<?php

class retroPlanning {

    public $contents;
    public $empty_after;
    public $holidays;
    public $max_hours_per_day;
    public $now;
    public $projects;

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

        /* Set projects */
        $this->setProjects($projects);

        /* Set contents */
        $this->setContents();
    }

    public function setProjects($projects) {
        $this->projects = $projects;
        foreach ($this->projects as $id => $proj) {
            /* Default name : id */
            if (!isset($proj['name'])) {
                $this->projects[$id]['name'] = $id;
            }

            /* Default start time : today at 11:00 */
            if (!isset($proj['start_time'])) {
                $this->projects[$id]['start_time'] = mktime(11, 0, 0, date('n', $this->now), date('d', $this->now), date('Y', $this->now));
            }
            if (!is_numeric($this->projects[$id]['start_time'])) {
                $this->projects[$id]['start_time'] = strtotime($this->projects[$id]['start_time']);
            }
            /* Init hours per day */
            if (!isset($proj['hours_per_day'])) {
                $this->projects[$id]['hours_per_day'] = 2;
            }
            /* Init hours per day */
            if (!isset($proj['color'])) {
                $this->projects[$id]['color'] = '#777';
            }
            /* Init time remaining */
            if (!isset($proj['time_remaining'])) {
                $proj['time_remaining'] = 2;
                $this->projects[$id]['time_remaining'] = 2;
            }
            /* Init total time */
            if (!isset($proj['total_time'])) {
                $this->projects[$id]['total_time'] = $proj['time_remaining'];
            }
        }
    }

    public function setContents() {

        /* Start previous monday */
        $planning_start = strtotime("next monday", $this->now) - 7 * 86400;
        $current_time = $planning_start;

        /* End in two months */
        $two_months = $this->now + 86400 * 60;
        $planning_end = mktime(12, 0, 0, date('n', $two_months), date('t', $two_months), date('Y', $two_months));
        $today_date = date('d/m/Y', $this->now);

        /* ----------------------------------------------------------
          Contents
        ---------------------------------------------------------- */

        $this->empty_after = false;
        $this->contents = array();
        while ($planning_end > $current_time) {
            $date_id = date('Y-m-d', $current_time);
            $day_id = date('d/m', $current_time);
            $weekday = date('w', $current_time);
            $this->contents[$date_id] = array(
                'current_time' => $current_time,
                'date' => date('d/m/Y', $current_time),
                'day_id' => $day_id,
                'graph' => array(),
                'is_workday' => true,
                'works' => array()
            );
            $today_or_later = $this->contents[$date_id]['date'] == $today_date || $this->now <= $current_time;
            $current_time += 86400;

            /* If day is not worked */
            if (!in_array($weekday, $this->working_days) || !$today_or_later || in_array($day_id, $this->holidays)) {
                $this->contents[$date_id]['is_workday'] = false;
                continue;
            }

            $_remaining_hours_today = $this->max_hours_per_day;
            foreach ($this->projects as $id => $proj) {
                $work = array(
                    'id' => $id,
                    'proj' => $proj,
                    'today' => 0
                );

                /* No time available on this day */
                if ($proj['start_time'] >= $current_time) {
                    continue;
                }

                /* No time available on this day */
                if ($_remaining_hours_today <= 0) {
                    continue;
                }

                /* If time needed on project */
                if ($proj['time_remaining'] > 0) {
                    $can_work_on_this_for = min($_remaining_hours_today, $proj['hours_per_day']);
                    $can_work_on_this_for = min($can_work_on_this_for, $proj['time_remaining']);
                    $this->projects[$id]['time_remaining'] -= $can_work_on_this_for;
                    $work['today'] = min($can_work_on_this_for, $proj['time_remaining']);
                    $work['proj'] = $this->projects[$id];
                    $_remaining_hours_today -= $can_work_on_this_for;
                    $this->contents[$date_id]['works'][] = $work;
                }
            }

            $_works_this_day = count($this->contents[$date_id]['works']);

            /* No work this day */
            if (!$_works_this_day) {
                continue;
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

    }

    public function divide_remaining_hours($_remaining_hours_today, $date_id) {

        $_works_can_add = 0;
        foreach ($this->contents[$date_id]['works'] as $work) {
            if ($work['proj']['time_remaining'] > 0) {
                $_works_can_add++;
            }
        }

        /* More time available on this day */
        if ($_remaining_hours_today > 0 && $_works_can_add > 0) {

            /* Divide time between active projects */
            $_bonus_time = round($_remaining_hours_today / $_works_can_add, 4);

            /* Add time to each project worked on this day */
            foreach ($this->contents[$date_id]['works'] as $id => $work) {
                if ($work['proj']['time_remaining'] <= 0) {
                    continue;
                }
                $_bonus_work = min($work['proj']['time_remaining'], $_bonus_time);
                $this->contents[$date_id]['works'][$id]['today'] += $_bonus_work;
                $this->projects[$work['id']]['time_remaining'] -= $_bonus_time;
                $_remaining_hours_today -= $_bonus_work;
            }
        }

        return $_remaining_hours_today;

    }

}
