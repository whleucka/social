<?php 

require_once __DIR__.'/vendor/autoload.php';

use GO\Scheduler;

// Create a new scheduler
$scheduler = new Scheduler();

$jobs = config("paths.jobs");
$logs = config("paths.logs");

// Jobs
$scheduler->php($jobs . "/ping.php")
    ->everyMinute()
    ->output($logs . date("Y-m-d") . "_ping.log", true);

// Let the scheduler execute jobs which are due.
$scheduler->run();
