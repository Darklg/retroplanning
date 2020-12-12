<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include PROJ_DIR . '/inc/retroplanning.class.php';

$_settings = array();

/* Load from XML */
if (file_exists(PROJ_DIR . '/local-planning.xml')) {
    $settings = simplexml_load_file(PROJ_DIR . '/local-planning.xml');
    if (is_object($settings)) {
        $_settings = json_decode(json_encode($settings), true);
    }
}

/* Load from JSON */
if (file_exists(PROJ_DIR . '/local-planning.json')) {
    $settings = file_get_contents(PROJ_DIR . '/local-planning.json');

    /* Fix invalid JSON */
    $settings = preg_replace("/,([\s]+)}/isU", "}", $settings);
    $settings = preg_replace("/,([\s]+)\]/isU", "]", $settings);

    /* Remove comments */
    /* https://stackoverflow.com/a/5419241 */
    $settings = preg_replace('#^\s*//.+$#m', "", $settings);

    $settings = json_decode($settings);
    if (is_object($settings)) {
        $_settings = json_decode(json_encode($settings), true);
    }
}

$retroPlanning = new retroPlanning($_settings);
