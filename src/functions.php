<?php
use Sunra\PhpSimple\HtmlDomParser;
use Eluceo\iCal\Component\Calendar;
use Eluceo\iCal\Component\Event;

function getCalendar($sid, $lang, $startdate, $enddate) {
    if(!is_int($sid)) {
        return false;
    }

    $url = sprintf("https://www.moodle.aau.dk/calmoodle/public/?sid=%d&lang=%s&display=agenda&startdate=%s&enddate=%s", $sid, $lang, $startdate, $enddate);

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => $url,
    ));
    $resp = curl_exec($curl);
    curl_close($curl);
    return $resp;
}

function getCalendarEvents($calBody) {
    $dom = HtmlDomParser::str_get_html($calBody);
            
    #find main table
    $table = $dom->find('table[class=maintable]', 0);

    if(!$table) {
        return false;
    }

    #array to store calendar name and events
    $events = [];

    #ignore first
    $first = true;

    foreach($table->find('tr') as $event) {
        if($first == true) {
            $first = false;
            continue;
        }

        # could be done inside the array, but need to calculate sha1
        $date     = str_replace("/", "-", $event->find("td", 2)->plaintext);
        $link     = "https://www.moodle.aau.dk".$event->find("a", 0)->href;
        $name     = str_replace("\n", "", $event->find("a", 0)->plaintext);
        $teacher  = $event->find("td", 7)->plaintext;
        $time     = str_replace(" - ", "-", $event->find("td", 3)->plaintext);
        $location = $event->find("td", 4)->plaintext;
        $note     = $event->find("td", 6)->plaintext;
        $sha1     = sha1($date.$link.$name.$teacher.$time.$location.$note);

        $events[] = [
            'date'     => $date,
            'link'     => $link,
            'name'     => $name,
            'teacher'  => $teacher,
            'time'     => $time,
            'location' => $location,
            'note'     => $note,
            'sha1'     => $sha1,
        ];
    }

    return $events;
}

function generateICalObject($calendarName, $events) {
    $vCal = new Calendar('calmoodle.greenmoon.dk');
    $vCal->setName($calendarName);

    foreach($events as $event) {
        #split time
        $timeSplit  = explode('-', $event['time']);
        $startDate  = new \DateTime($event['date'].' '.$timeSplit[0]);
        $endDate    = new \DateTime($event['date'].' .'.$timeSplit[1]);
        
        $hash = hash('sha256',  $startDate->format('Y-m-d H:i:s').
            $endDate->format('Y-m-d H:i:s').
            $event['name'].
            $event['location'].
            $event['teacher'].
            $event['note']);

        #create event
        $vEvent = new Event();
        $vEvent
        ->setDtStart($startDate)
        ->setDtEnd($endDate)
        ->setUniqueId($hash)
        ->setUrl($event['link'])
        ->setSummary($event['name'])
        ->setDescription(
            "Location: ".$event['location']."\n".
            "Teacher: ".$event['teacher']."\n".
            "Notes: ".$event['note']."\n".
            $event['link']
        );

        $vEvent->setUseTimezone(true);
        $vCal->addComponent($vEvent);
    } 

    #return response with headers
    return $vCal;
    /* print_r($vCal->render()) */
}
?>
