<?php
if($_SERVER['REQUEST_URI'] == "") {
   header("Location: https://elektro-kolesa.ru/",TRUE,301);
   exit();
}


if(stristr($_SERVER['REQUEST_URI'], '//')) {
   header("Location: https://elektro-kolesa.ru/",TRUE,301);
   exit();
}

if($_SERVER['REQUEST_URI'] == "/index.html") {
    header("Location: https://elektro-kolesa.ru/",TRUE,301);
    exit();
}

if($_SERVER['REQUEST_URI'] == "/home.html") {
    header("Location: https://elektro-kolesa.ru/",TRUE,301);
    exit();
}

if($_SERVER['REQUEST_URI'] == "/home.php") {
    header("Location: https://elektro-kolesa.ru/",TRUE,301);
    exit();
}

if($_SERVER['REQUEST_URI'] == "/index.htm") {
    header("Location: https://elektro-kolesa.ru/",TRUE,301);
    exit();
}

if($_SERVER['REQUEST_URI'] == "/home.htm") {
    header("Location: https://elektro-kolesa.ru/",TRUE,301);
    exit();
}

if($_SERVER['REQUEST_URI'] == "/home") {
    header("Location: https://elektro-kolesa.ru/",TRUE,301);
    exit();
}

if($_SERVER['REQUEST_URI'] == "/index.php") {
    header("Location: https://elektro-kolesa.ru/",TRUE,301);
    exit();
}

ini_set('session.cookie_domain', '.elektro-kolesa.ru');
// Version
define('VERSION', '3.0.2.0');

// Configuration
if (is_file('config.php')) {
	require_once('config.php');
}

// Install
if (!defined('DIR_APPLICATION')) {
	header('Location: install/index.php');
	exit;
}

// Startup
require_once(DIR_SYSTEM . 'startup.php');

start('catalog');


