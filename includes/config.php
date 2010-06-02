<?php
$hostname_contacts = "localhost";  
$database_contacts = "donor_track_1"; //The name of the database
$username_contacts = "donor"; //The username for the database
$password_contacts = "ifcs39"; // The password for the database
$contacts = mysql_connect($hostname_contacts, $username_contacts, $password_contacts) or trigger_error(mysql_error(),E_USER_ERROR); 
mysql_select_db($database_contacts, $contacts);

?>
