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

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Donor Track &raquo; <?php if($title_text) { echo $title_text; } else { echo $pagetitle; } ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<script src="includes/lib/prototype.js" type="text/javascript"></script>
<script src="includes/lib/tablekit.js" type="text/javascript"></script>
<script src="includes/src/effects.js" type="text/javascript"></script>
<script src="includes/validation.js" type="text/javascript"></script>
<script src="includes/src/scriptaculous.js" type="text/javascript"></script>

<link href="includes/baseline.reset.css" rel="stylesheet" type="text/css" />
<link href="includes/baseline.base.css" rel="stylesheet" type="text/css" />
<link href="includes/baseline.type.css" rel="stylesheet" type="text/css" />
<link href="includes/baseline.grid.css" rel="stylesheet" type="text/css" />
<link href="includes/baseline.form.css" rel="stylesheet" type="text/css" />
<link href="includes/baseline.table.css" rel="stylesheet" type="text/css" />
<link href="includes/style.css" rel="stylesheet" type="text/css" />
<link href="includes/simplecustomer.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div class="headercontainer"> 
  <div class="header">
    <h1>Donor Track</h1>
    <a href="index.php" class="menubuttons <?php if ($pagetitle=="Dashboard") { echo "menubuttonsactive"; } ?>">Dashboard</a>
    <a href="contacts.php" class="menubuttons <?php if ($pagetitle=="Contact" || $pagetitle=="ContactDetails") { echo "menubuttonsactive"; } ?>">Contacts</a>
    <a href="campaigns.php" class="menubuttons <?php if ($pagetitle=="Campaigns") { echo "menubuttonsactive"; } ?>">Campaigns</a>
<?php if(isSuperUser($row_userinfo)) { ?>
    <a href="users.php" class="menubuttons <?php if ($pagetitle=="Users") { echo "menubuttonsactive"; } ?>">Users</a>
<?php } ?>

    <span class="headerright">Logged in as <?php echo $row_userinfo['user_email']; ?> | <a href="logout.php">Log Out</a> | <a href="profile.php">Update Profile</a> </span><br clear="all" />
  </div>
  </div>

<div class="historycontainer">
<?php if($back_track) { printf("<a href='%s'>&laquo;%s</a>", $back_track['url'], $back_track['title']); } ?>

</div>
