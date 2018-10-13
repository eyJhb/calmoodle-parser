<?php
if(strpos($_SERVER["REQUEST_URI"], "/ical") !== false) {
    exit(require('ical.php'));

} else {
    exit(require('index.php'));
}
?>
