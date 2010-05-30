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
$pagetitle = "Admin";

mysql_select_db($database_contacts, $contacts);
if($_POST['year'] && is_numeric($_POST['year'])) {
  $result = mysql_query("UPDATE config SET value = " . $_POST['year'] . " WHERE name = 'fiscal_year'");
  if(mysql_affected_rows() == 1) {
    set_msg("Fiscal year updated.");
  } else {
    set_msg("Failed to update fiscal year.");
  }
  header('Location: admin.php'); die;
} else if ($_POST['year'] && !is_numeric($_POST['year'])) {
  set_msg("Year must be a number");
  header('Location: admin.php'); die;
}

?>

<?php include('includes/header.php'); ?>
  
  <div class="container">
  <div class="leftcolumn">
    <span class="notices" style="display:<?php echo $dis; ?>">
      <?php display_msg(); ?>
    </span>
    <h2>Admin</h2>
      <form name="form1" id="form1" method="post" action="admin.php">
        <fieldset>
        <label class="first width1">
          Fiscal Year 
          <input name="year" id="year" class="validate-digits" type="text" value="<?php echo get_fiscal_year(); ?>" />
        </label>
        </fieldset>
        <fieldset>
          <input class="first" name="submit" type="submit" value="Update" />
        <fieldset>
      </form>
  </div>
  <?php include('includes/right-column.php'); ?>
  <br clear="all" />
</div>

<?php include('includes/footer.php'); ?>
</body>
</html>
