<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include dirname(__FILE__) . '/retroplanning.class.php';

$_settings = array();

/* Load from XML */
if (file_exists('local-planning.xml')) {
    $settings = simplexml_load_file('local-planning.xml');
    if (is_object($settings)) {
        $_settings = json_decode(json_encode($settings), true);
    }
}

/* Load from JSON */
if (file_exists('local-planning.json')) {
    $settings = file_get_contents('local-planning.json');
    $settings = json_decode($settings);
    if (is_object($settings)) {
        $_settings = json_decode(json_encode($settings), true);
    }
}

$retroPlanning = new retroPlanning($_settings);
