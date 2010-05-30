<?php require_once('includes/config.php'); 
include('includes/sc-includes.php');

mysql_select_db($database_contacts, $contacts);

//DELETE CONTACT
if (isset($_GET['contact'])) {
mysql_query("DELETE FROM contacts WHERE contact_id = ".$_GET['contact']."");
mysql_query("DELETE FROM history WHERE history_contact = ".$_GET['contact']."");
mysql_query("DELETE FROM notes WHERE note_contact = ".$_GET['contact']."");
set_msg('Contact Deleted');
header('Location: contacts.php'); die;
}
//

//DELETE NOTE
if (isset($_GET['note'])) {
mysql_query("DELETE FROM notes WHERE note_id = ".$_GET['note']."");
set_msg('Note Deleted');
$cid = $_GET['id'];
$redirect = "contact-details.php?id=$cid";
header(sprintf('Location: %s', $redirect)); die;
}
//
?>