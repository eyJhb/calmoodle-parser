<?php
require("../vendor/autoload.php");
require("functions.php");

use Valitron\Validator;

# validator 
$v = new Validator($_GET);
$v->rule("required", "sid");
$v->rule("integer", "sid");
$v->rule("alphaNum", "name");
$v->rule("in", "lang", array("da","en"));
$v->rule("dateFormat", ["startdate", "enddate"], "Y-m-d");

if(!$v->validate()) {
    print_r($v->errors());
    exit("Failed validation...\n");

}

# get our vars
$sid       = (int)$_GET["sid"];
$name      = $_GET["name"] ?? "Calmoodle";
$lang      = $_GET["lang"] ?? "en";
$startdate = $_GET["startdate"] ?? date('Y-m-d', strtotime('-1 month'));
$enddate   = $_GET["startdate"] ?? date('Y-m-d', strtotime('+6 months'));

$calBody = getCalendar($sid, $lang, $startdate, $enddate);
if(!$calBody) {
	exit("Could not get calendar..");
}

$events = getCalendarEvents($calBody);
if(!$events) {
	exit("Could not parse events");
}


$ical = generateICalObject($name, $events);

# currently cannot happen...
if(!$ical) {
	exit("Could not generate ical");
}

header("content-type: text/calendar; charset=utf-8");
header("content-disposition: attachment; filename=\"cal.ics\"");
print_r($ical->render());
?>
