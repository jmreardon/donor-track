<?php

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

$query_history = "SELECT * FROM history WHERE history_status = 1 ORDER BY history_date DESC LIMIT 0, 4";
$history = mysql_query($query_history) or die(mysql_error());
$row_history = mysql_fetch_assoc($history);
$totalRows_history = mysql_num_rows($history);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php if($title_text) { echo $title_text; } else { echo $pagetitle; } ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<script src="includes/lib/prototype.js" type="text/javascript"></script>
<script src="includes/src/effects.js" type="text/javascript"></script>
<script src="includes/validation.js" type="text/javascript"></script>
<script src="includes/src/scriptaculous.js" type="text/javascript"></script>

<link href="includes/style.css" rel="stylesheet" type="text/css" />
<link href="includes/simplecustomer.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div class="headercontainer"> 
  <div class="header">
    <h1>Simple Customer</h1>
  <a href="index.php" class="menubuttons <?php if ($pagetitle==Dashboard) { echo menubuttonsactive; } ?>">Dashboard</a><a href="contacts.php" class="menubuttons <?php if ($pagetitle==Contact || $pagetitle==ContactDetails) { echo menubuttonsactive; } ?>">Contacts</a><span class="headerright">Logged in as <?php echo $row_userinfo['user_email']; ?> | <a href="logout.php">Log Out</a> | <a href="profile.php">Update Profile</a> </span><br clear="all" />
  </div>
  </div>

<?php if ($totalRows_history > 0) { ?>
<div class="historycontainer">Recent: 
    <?php $ih = 1; do { 
//GET CONTACT INFO FROM HISTORY
mysql_select_db($database_contacts);
$query_histcont = "SELECT * FROM contacts WHERE contact_id = ".$row_history['history_contact']."";
$histcont = mysql_query($query_histcont) or die(mysql_error());
$row_histcont = mysql_fetch_assoc($histcont);
//
?>
    <a href="contact-details.php?id=<?php echo $row_histcont['contact_id']; ?>"><?php echo $row_histcont['contact_first']; ?> <?php echo $row_histcont['contact_last']; ?></a> <?php if ($totalRows_history!=$ih) {?> &middot; <?php } ?>
      <?php $ih++; } while ($row_history = mysql_fetch_assoc($history)); ?></div>
<?php } ?>
