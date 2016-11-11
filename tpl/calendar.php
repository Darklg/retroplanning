<?php

$_projects = $retroPlanning->projects;

echo '<div class="block-calendar">';
foreach ($retroPlanning->contents as $content) {
    $classnames = array('block-date');
    $classnames[] = $content['is_workday'] ? 'is-workday' : 'not-workday';
    $classnames[] = 'day-nb-' . date('d', $content['current_time']);
    $classnames[] = 'day-' . strtolower(date('l', $content['current_time']));

    echo '<div class="' . implode(' ', $classnames) . '">';
    echo '<h3>' . $content['date'] . '</h3>';
    foreach ($content['works'] as $work) {
        echo '<div>';
        echo '<span class="dot" style="color: ' . $_projects[$work['id']]['color'] . '"></span> ';
        echo '<strong>' . $_projects[$work['id']]['name'] . ' : ' . number_format($work['today'], 1) . 'h</strong>';
        echo '</div>';
    }

    if (!empty($content['works'])) {
        echo '<div class="graph">';
        foreach ($content['graph'] as $line) {
            $work = $_projects[$line['work']['id']];
            echo '<div title="' . $work['name'] . '" style="background-color:' . $work['color'] . ';width:' . $line['percent'] . '%"></div>';
        }
        echo '</div>';
    }

    echo '</div>';
    if ($retroPlanning->empty_after == $content['day_id']) {
        break;
    }
}
echo '</div>';
