<?php

class retroPlanning {

    public $contents;
    public $empty_after;
    public $holidays;
    public $max_hours_per_day;
    public $now;
    public $planning_end;
    public $planning_start;
    public $projects;
    public $today_date;

    public function __construct($projects) {

        /* Now is today at 13:00 */
        $this->now = mktime(13, 0, 0, date('n', time()), date('d', time()), date('Y', time()));

        $this->working_days = array(1, 2, 3, 4, 5);
        $this->max_hours_per_day = 7;
        $this->holidays = array(
            '01/01',
            '01/05',
            '08/05',
            '14/07',
            '15/08',
            '01/11',
            '11/11',
            '25/12'
        );

        /* Start previous monday */
        $this->planning_start = strtotime("next monday", $this->now) - 7 * 86400;

        /* End in two months */
        $two_months = $this->now + 86400 * 60;
        $this->planning_end = mktime(12, 0, 0, date('n', $two_months), date('t', $two_months), date('Y', $two_months));
        $this->today_date = date('d/m/Y', $this->now);

        /* Set projects */
        $this->initProjects($projects);

        /* Set contents */
        $this->setContents();
    }

    public function initProjects($projects) {
        $this->projects = $projects;
        foreach ($this->projects as $id => $proj) {
            $this->projects[$id] = $this->initProject($id, $proj);
        }
    }

    public function initProject($id, $proj) {

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
        /* Init hours per day */
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
        if (!in_array($weekday, $this->working_days) || !$today_or_later || in_array($day_id, $this->holidays)) {
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
