<?php require_once('includes/config.php'); 

//   Copyright 2008 johnboyproductions.com
//
//   Licensed under the Apache License, Version 2.0 (the "License");
//   you may not use this file except in compliance with the License.
//   You may obtain a copy of the License at
//
//       http://www.apache.org/licenses/LICENSE-2.0
//
//   Unless required by applicable law or agreed to in writing, software
//   distributed under the License is distributed on an "AS IS" BASIS,
//   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
//   See the License for the specific language governing permissions and
//   limitations under the License.

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
