<?php require_once('includes/config.php'); 
require_once('includes/user.inc.php'); 

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
$pagetitle = "Profile";

//UPDATE PROFILE
if ($_POST['email']) {
update_profile($_POST['email'], $_POST['password'], $_POST['home']);
set_msg('Profile Updated');
$_SESSION['user'] = $_POST['email'];
header('Location: profile.php'); die;
}

mysql_select_db($database_contacts, $contacts);
$query_profile = "SELECT * FROM users WHERE user_id = " . $row_userinfo['user_id'];
$profile = mysql_query($query_profile, $contacts) or die(mysql_error());
$row_profile = mysql_fetch_assoc($profile);
$totalRows_profile = mysql_num_rows($profile);

$title_text = "Update Profile";
?>
<?php include('includes/header.php'); ?>
<div class="container">
  <div class="leftcolumn">
    <h2>Profile</h2>
    <span class="notices" style="display:<?php echo $dis; ?>">
    <?php display_msg(); ?>
    </span>
    <form id="form1" name="form1" method="post" action="">
    <fieldset class="width2">
      <label class="first column width2">
        Email
        <input name="email" type="text" id="email" value="<?php echo $row_profile['user_email']; ?>" class="required validate-email" size="35" />
      </label>
      <label class="first column width2">
        Password (leave blank to keep current password) <br />
        <input name="password" type="password" id="password" />
      </label>
      <label class="first column unitx1">
        Home Page<br />
      </label>
      <label class="column unitx1">
        <input name="home" type="radio" value="index.php" <?php if ($row_profile['user_home']=="index.php") { ?>checked="checked"<?php } ?> />
        Dashboard<br />
      </label>
      <label class="column unitx1">
        <input name="home" type="radio" value="contacts.php" <?php if ($row_profile['user_home']=="contacts.php") { ?>checked="checked"<?php } ?> />
        Contacts
      </label>
      <label class="first column unitx1">
        <input name="Submit2" type="submit" id="Submit2" value="Update" /> 
      </label>
    </fieldset>
    </form>
    
    <p>&nbsp;</p>
  </div>
  <?php include('includes/right-column.php'); ?>
  <br clear="all" />
</div>

<?php include('includes/footer.php'); ?>

</body>
</html>
