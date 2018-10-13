<?php
require(__DIR__ . "/../vendor/autoload.php");
require("functions.php");

use Valitron\Validator;

# validator 
## base information
$v = new Validator($_GET);
$v->rule("required", "sid");
$v->rule("integer", "sid");
$v->rule("ascii", "name");
$v->rule("in", "lang", array("da","en"));
$v->rule("dateFormat", ["startdate", "enddate"], "Y-m-d");
## output format
$v->rule("in", "output", array("json", "ical"));

if(!$v->validate()) {
    $errors = $v->errors();
    $errors["error"] = "validation error";
    exit(json_encode($errors));
}

# get our vars
$sid       = (int)$_GET["sid"];
$name      = (isset($_GET["name"]) ? $_GET["name"] : "Calmoodle");
$lang      = (isset($_GET["lang"]) ? $_GET["lang"] : "en");
$startdate = (isset($_GET["startdate"]) ? $_GET["startdate"] : date('Y-m-d', strtotime('-1 month')));
$enddate   = (isset($_GET["startdate"]) ? $_GET["startdate"] : date('Y-m-d', strtotime('+6 months')));
$output   = (isset($_GET["output"]) ? $_GET["output"] : "ical");

$calBody = getCalendar($sid, $lang, $startdate, $enddate);
if(!$calBody) {
	exit(json_encode(["error" => "could not get calendar"]));
}

$events = getCalendarEvents($calBody);
if(!$events) {
	exit(json_encode(["error" => "could not parse events"]));
}

# if statement our output - to make things clear
if($output == "json") {
    exit(json_encode($events));
} elseif($output == "ical") {
    $ical = generateICalObject($name, $events);
    # currently cannot happen...
    if(!$ical) {
        exit(json_encode(["error" => "could not generate ical"]));
    }

    header("content-type: text/calendar; charset=utf-8");
    header("content-disposition: attachment; filename=\"cal.ics\"");
    exit($ical->render());
}

# should never reach this
exit(json_encode(["error" => "unknown error"]));
?>
