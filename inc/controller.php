<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include dirname(__FILE__) . '/retroplanning.class.php';

$_projects = array();

/* Load from XML */
if (file_exists('local-planning.xml')) {
    $projects = simplexml_load_file('local-planning.xml');
    if (is_object($projects)) {
        $_projects = json_decode(json_encode($projects), true);
    }
}

/* Load from JSON */
if (file_exists('local-planning.json')) {
    $projects = file_get_contents('local-planning.json');
    $projects = json_decode($projects);
    if (is_object($projects)) {
        $_projects = json_decode(json_encode($projects), true);
    }
}

$retroPlanning = new retroPlanning($_projects);
