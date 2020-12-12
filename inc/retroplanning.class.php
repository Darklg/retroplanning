<?php

class retroPlanning {

    public $contents;
    public $empty_after;
    public $holidays;
    public $vacations;
    public $max_hours_per_day;
    public $now;
    public $planning_end;
    public $planning_start;
    public $settings;
    public $today_date;

    public function __construct($datas) {

        /* Now is today at 13:00 */
        $this->now = mktime(13, 0, 0, date('n', time()), date('d', time()), date('Y', time()));

        $this->working_days = array(1, 2, 3, 4, 5);
        $this->max_hours_per_day = 7;
        $this->max_days_displayed = 60;
        $this->vacations = array();
        $this->holidays = array(
            '01/01',
            '25/12'
        );

        /* Init settings */
        $settings = array();
        if (isset($datas['settings']) && is_array($datas['settings'])) {
            $settings = $datas['settings'];
        }
        $this->initSettings($settings);

        /* Start previous monday */
        $this->planning_start = strtotime("next monday", $this->now) - 7 * 86400;

        /* End in n months */
        $planning_end_days = $this->now + 86400 * $this->max_days_displayed;
        $this->planning_end = mktime(12, 0, 0, date('n', $planning_end_days), date('t', $planning_end_days), date('Y', $planning_end_days));
        $this->today_date = date('d/m/Y', $this->now);

        /* Set projects */
        $projects = array();
        if (isset($datas['projects']) && is_array($datas['projects'])) {
            $projects = $datas['projects'];
        }
        $this->initProjects($projects);

        /* Clients */
        $clients = array();
        if (isset($datas['clients']) && is_array($datas['clients'])) {
            $clients = $datas['clients'];
        }
        $this->initClients($clients);

        /* Set contents */
        $this->setContents();
    }

    public function initSettings($settings) {

        /* Max hours per day */
        if (isset($settings['max_hours_per_day']) && is_numeric($settings['max_hours_per_day'])) {
            $this->max_hours_per_day = intval($settings['max_hours_per_day'], 10);
        }
        /* Max days displayed */
        if (isset($settings['max_days_displayed']) && is_numeric($settings['max_days_displayed'])) {
            $this->max_days_displayed = intval($settings['max_days_displayed'], 10);
        }
        /* Working days */
        if (isset($settings['working_days']) && is_array($settings['working_days'])) {
            $this->working_days = array();
            $working_days = $settings['working_days'];
            if (isset($settings['working_days']['day'])) {
                $working_days = $settings['working_days']['day'];
            }
            foreach ($working_days as $day) {
                if (is_numeric($day)) {
                    $this->working_days[] = intval($day, 10);
                }
            }
        }
        /* Vacations */
        if (isset($settings['vacations']) && is_array($settings['vacations'])) {
            $this->vacations = $settings['vacations'];
            if (isset($settings['vacations']['date'])) {
                $this->vacations = $settings['vacations']['date'];
            }
        }

        /* Holidays */
        if (isset($settings['holidays']) && is_array($settings['holidays'])) {
            $this->holidays = $settings['holidays'];
            if (isset($settings['holidays']['date'])) {
                $this->holidays = $settings['holidays']['date'];
            }
        }

    }

    public function initClients($clients = array()) {
        $_clients = array();
        foreach ($this->projects as $_project) {
            if (isset($_project['client_id']) && !isset($clients[$_project['client_id']])) {
                $clients[$_project['client_id']] = array();
            }
        }
        foreach ($clients as $_id => $_client) {
            if (!is_array($_client)) {
                $_client = array();
            }
            if (!isset($_client['color'])) {
                $_client['color'] = "#999";
            }
            $_clients[$_id] = $_client;
        }
        ksort($_clients);
        $this->clients = $_clients;
    }

    public function initProjects($projects = array()) {
        $_projects = array();
        foreach ($projects as $id => $proj) {
            $_project = $this->initProject($id, $proj);
            if ($_project) {
                $_projects[$id] = $_project;
            }
        }
        $this->projects = $_projects;
    }

    public function initProject($id, $proj) {
        if (isset($proj['disabled']) && $proj['disabled'] == '1') {
            return false;
        }
        /* Default name : id */
        if (!isset($proj['name'])) {
            $proj['name'] = $id;
        }
        /* Default start time : today at 11:00 */
        if (!isset($proj['start_time'])) {
            $proj['start_time'] = mktime(11, 0, 0, date('n', $this->now), date('d', $this->now), date('Y', $this->now));
        }
        if (!is_numeric($proj['start_time'])) {
            $proj['start_time'] = strtotime($proj['start_time']);
        }
        $proj['end_time'] = $proj['start_time'];
        /* Init hours per day */
        if (!isset($proj['hours_per_day'])) {
            $proj['hours_per_day'] = 2;
        }
        /* Init Client */
        if (!isset($proj['client_id'])) {
            $proj['client_id'] = 'default';
        }
        /* Init Colors */
        if (!isset($proj['color'])) {
            $proj['color'] = '#777';
        }
        /* Init time remaining */
        if (!isset($proj['time_remaining'])) {
            $proj['time_remaining'] = 2;
        }
        /* Init total time */
        if (!isset($proj['total_time'])) {
            $proj['total_time'] = $proj['time_remaining'];
        }
        /* Project can use bonus time */
        if (!isset($proj['can_bonus'])) {
            $proj['can_bonus'] = true;
        } else {
            if (is_numeric($proj['can_bonus'])) {
                $proj['can_bonus'] = intval($proj['can_bonus'], 10);
            }
        }

        return $proj;
    }

    public function setContents() {
        $this->current_time = $this->planning_start;
        $this->empty_after = false;
        $this->contents = array();
        while ($this->planning_end > $this->current_time) {
            $this->setContentDay();
        }
    }

    public function setContentDay() {
        $day_time = $this->current_time;
        $this->current_time += 86400;
        $date_id = date('Y-m-d', $day_time);
        $day_id = date('d/m', $day_time);
        $weekday = date('w', $day_time);
        $this->contents[$date_id] = array(
            'current_time' => $day_time,
            'date' => date('d/m/Y', $day_time),
            'day_id' => $day_id,
            'graph' => array(),
            'is_workday' => true,
            'works' => array()
        );
        $today_or_later = $this->contents[$date_id]['date'] == $this->today_date || $this->now <= $day_time;

        /* If day is not worked */
        if (!in_array($weekday, $this->working_days) || !$today_or_later || in_array($day_id, $this->holidays) || in_array($day_id, $this->vacations)) {
            $this->contents[$date_id]['is_workday'] = false;
            return;
        }

        $_remaining_hours_today = $this->max_hours_per_day;
        foreach ($this->projects as $id => $proj) {
            $work = array(
                'id' => $id,
                'today' => 0
            );

            /* No time available on this day */
            if ($proj['start_time'] >= $this->current_time) {
                continue;
            }

            /* No time available on this day */
            if ($_remaining_hours_today <= 0) {
                continue;
            }

            /* If time needed on project */
            if ($proj['time_remaining'] > 0.001) {
                $can_work_on_this_for = min($_remaining_hours_today, $proj['hours_per_day']);
                $can_work_on_this_for = min($can_work_on_this_for, $proj['time_remaining']);
                $this->projects[$id]['time_remaining'] -= $can_work_on_this_for;
                $this->projects[$id]['end_time'] = $day_time;
                $work['today'] = min($can_work_on_this_for, $proj['time_remaining']);
                $_remaining_hours_today -= $can_work_on_this_for;
                $this->contents[$date_id]['works'][] = $work;
            }
        }

        $_works_this_day = count($this->contents[$date_id]['works']);

        /* No work this day */
        if (!$_works_this_day) {
            return;
        }

        $this->divide_remaining_hours($_remaining_hours_today, $date_id);

        /* Generate graph */
        $this->contents[$date_id]['graph'] = array();
        foreach ($this->contents[$date_id]['works'] as $id => $work) {
            $this->contents[$date_id]['graph'][] = array(
                'percent' => floor($work['today'] / $this->max_hours_per_day * 100),
                'work' => $work
            );
        }

        /* Store last day with time used */
        $this->empty_after = $day_id;
    }

    public function divide_remaining_hours($_remaining_hours_today, $date_id) {

        $_works_can_add = 0;
        foreach ($this->contents[$date_id]['works'] as $work) {
            if ($this->projects[$work['id']]['time_remaining'] > 0 && $this->projects[$work['id']]['can_bonus']) {
                $_works_can_add++;
            }
        }

        /* More time available on this day */
        if ($_remaining_hours_today > 0 && $_works_can_add > 0) {

            /* Divide time between active projects */
            $_bonus_time = round($_remaining_hours_today / $_works_can_add, 4);

            /* Add time to each project worked on this day */
            foreach ($this->contents[$date_id]['works'] as $id => $work) {
                if ($this->projects[$work['id']]['time_remaining'] <= 0 || !$this->projects[$work['id']]['can_bonus']) {
                    continue;
                }
                $_bonus_work = min($this->projects[$work['id']]['time_remaining'], $_bonus_time);
                $this->contents[$date_id]['works'][$id]['today'] += $_bonus_work;
                $this->projects[$work['id']]['time_remaining'] -= $_bonus_time;
                $_remaining_hours_today -= $_bonus_work;
            }
        }

        return $_remaining_hours_today;

    }

}
