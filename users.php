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

ob_start();

include('includes/sc-includes.php');
require_once('includes/user.inc.php'); 
$pagetitle = "Users";

if(!isSuperUser($row_userinfo)) {
  header("HTTP/1.0 403 Forbidden"); die;
}


mysql_select_db($database_contacts, $contacts);
$query_users = "SELECT user_id,
                       user_email,
                       user_level
                FROM users";
$users = mysql_query($query_users, $contacts) or die(mysql_error());
$row_users = mysql_fetch_assoc($users);
$totalRows_users = mysql_num_rows($users);

if(isset($_POST["user_email"]) && isset($_POST["user_type"])) {
  $email = $_POST["user_email"];
  $type = $_POST["user_type"];
  $users_emails = array();
  if($type != "0" && $type != "1") {
    set_msg("Invalid User Type");
  } else {
    do {
      $users_emails []= $row_users["user_email"];
    } while ($row_users = mysql_fetch_assoc($users));
    if(in_array($email, $users_emails)) {
      set_msg("This email address is already in use. The account was not created.");
    } else {
      mysql_query("INSERT INTO users (`user_email`, `user_level`) VALUES ('" . 
        mysql_real_escape_string($email) . "', '" . mysql_real_escape_string($type) . "')");
      if(send_password($email)) {
        set_msg("Account created");
      } else {
        set_msg("Account created but email failed to send.");
      }
    }
  }
  header('Location: users.php'); die;
} else if (isset($_POST["user"]) && isset($_POST["action"])) {
  $target = $_POST["user"];
  if(!is_numeric($target)) {
      set_msg("Invalid user." . $target);
  } else if($target == $row_userinfo["user_id"]) {
      set_msg("You may not modify your own user account.");
  } else {
    switch ($_POST["action"]) {
      case "Make Admin":
        mysql_query("UPDATE users SET user_level = 1 WHERE user_id = " . $target);
        set_msg("User has been granted administrative privileges");
        break;
      case "Make User":
        mysql_query("UPDATE users SET user_level = 0 WHERE user_id = " . $target);
        set_msg("User's administrative privileges have been revoked");
        break;
      case "Delete":
        mysql_query("DELETE FROM users WHERE user_id = " . $target);
        set_msg("User deleted");
        break;
      default:
        set_msg("You must select an action");
    }
  }
  header('Location: users.php'); die;
}

?>

<?php include('includes/header.php'); ?>
  
  <div class="container">
  <div class="leftcolumn">
    <span class="notices" style="display:<?php echo $dis; ?>">
      <?php display_msg(); ?>
    </span>
    <h2>Campaigns</h2>
    <hr />
    <form id="form1" name="form1" method="post" action="">
      <fieldset>
      <label style="margin-bottom: 0px" class="first column unitx1">
        Action
        <select id="action" name="action">
          <option>Select action</option>
          <option>Make Admin</option>
          <option>Make User</option>
          <option>Delete</option>
        </select>
      </label>
      <label id="target_value_label" style="margin-bottom: 0px; display: none;" class="column unitx1">
        Campaign
        <select id="campaign" name="campaign">
          <?php echo_campaign_options(); ?>
        </select>
      </label>
      <script type="text/javascript">
        Event.observe(window,'load',function( ) {
          Event.observe('action','change', function() {
            if($("action").getValue() == "Target" || $("action").getValue() == "Untarget") {
              $("target_value_label").show();
            } else {
              $("target_value_label").hide();
            }
          });
        });
      </script>
      <label style="margin-bottom: 0px" class="column width1 inlinebutton">
        <input type="submit" name="Submit" value="Submit" />
      </label>
      </fieldset>  <table class="sortable">
      <thead>
        <tr>
          <th>Email</th>
          <th>User Type</th>
          <th class="nosort" width="7%" style="text-align: center">Select</th>
        </tr>
      </thead>
      <tbody>
        <?php if($row_users) { ?>
        <?php do { ?>
	  <tr>
            <td>
              <?php echo $row_users['user_email']; ?>
            </td>
            <td>
              <?php if(isSuperUser($row_users)) { ?>
                Admin
              <?php } else { ?>
                User
              <?php } ?>
            </td>
            <td>
              <input name="user" type="radio" class="action_radio" value="<?php echo $row_users['user_id']; ?>" />
            </td>
          </tr>
        <?php } while ($row_users = mysql_fetch_assoc($users)); ?>
        <?php } else { ?>
          <tr><td style="text-align: center;" colspan="5">No Campaigns</td></tr>
        <?php } ?>
      </tbody>
      </table>
      </form>
      <br />
      <a href="#" onclick="new Effect.toggle('add_user', 'slide', { afterFinish: function() { $('user_email').focus(); }}); return false;">+Add User</a>
      <br />
      <div id="add_user" style="display:none">
        <form name="form2" id="form2" method="post" action="users.php">
          <fieldset>
          <label class="first width1">
            Email 
            <input name="user_email" id="user_email" class="required" type="text" value="" />
          </label>
          <label class="unitx1">
            User Type 
            <select id="user_type" name="user_type">
              <option selected='selected' value="0">User</option>
              <option value="1">Admin</option>
            </select>
          </label>
          <label class="unitx1 inlinebutton">
            <input name="submit" type="submit" value="Add User" />
          </label>
          </fieldset>
        </form>
      </div>
  </div>
  <?php include('includes/right-column.php'); ?>
  <br clear="all" />
</div>

<?php include('includes/footer.php'); ?>
</body>
</html>
