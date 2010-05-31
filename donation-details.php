<?php require_once('includes/config.php'); ?>
<?php

//   Copyright 2008 johnboyproductions.com
//   Copyright 2010 Justin Reardon
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

include('includes/donation.inc.php');
include('includes/sc-includes.php');
$pagetitle = "Campaigns";

mysql_select_db($database_contacts, $contacts);

if($_POST["action"] == "delete" && is_numeric($_POST["donation"]) && is_numeric($_POST["campaign"])) {
  mysql_query("DELETE FROM donations WHERE donation_id = " . $_POST["donation"], $contacts) or die(mysql_error());
  if(mysql_affected_rows($contacts) == 1) {
    set_msg('Donation Deleted.');
    header("Location: campaign-details.php?campaign=" . $_POST["campaign"]); die;
  } else {
    set_msg('Failed to delete donation.');
    header("Location: donation-details.php?id=" . $_POST["donation"]); die;
  }
} else if($_POST["action"] == "updatestatus" && is_numeric($_POST["donation"])) {
  if (in_array($_POST['status'], array("Expected", "Pledged", "Received")) &&
      preg_match("/(\d\d)\/(\d\d)\/(\d\d\d\d)/", trim($_POST['update_date']), $date)) {
    $status = strtolower($_POST['status']);
    $pledged_date = 'NULL';
    $received_date = 'NULL';

    if($status == 'pledged') {
      $pledged_date = "'" . sprintf("%s-%s-%s", $date[3], $date[2], $date[1]) . "'";
    }

    if($status == 'received') {
      $received_date = "'" . sprintf("%s-%s-%s", $date[3], $date[2], $date[1]) . "'";
      $pledged_date = null;
    }

    $update_query = "UPDATE donations SET 
      donation_status = '$status', " . 
      ($pledged_date ? "donation_pledge_date = $pledged_date, " : "") .
     "donation_received_date = $received_date
      WHERE donation_id = " . $_POST['donation'];
    mysql_query($update_query, $contacts);
    set_msg('Donation Updated.');
  } else {
    set_msg('Failed to update donation.');
  }
  header("Location: donation-details.php?id=" . $_POST["donation"]); die;
} else if($_POST["action"] == "updatedonation" && is_numeric($_POST["donation"])) {
  if (in_array($_POST['type'], array("Cash", "In Kind")) && 
      is_numeric($_POST['amount']) &&
      ((float)$_POST['amount']) > 0) {
    $value = (int)$_POST['amount']; 
    $is_cash = $_POST['type'] == "Cash" ? 'true' : 'false';
    $update_query = "UPDATE donations SET 
      donation_is_cash = $is_cash, 
      donation_value = " . $_POST['amount'] . "
      WHERE donation_id = " . $_POST['donation'];
    mysql_query($update_query, $contacts);
    set_msg('Donation Updated.');
  } else {
    set_msg('Failed to update donation.');
  }
  header("Location: donation-details.php?id=" . $_POST["donation"]); die;
}

if(!($_GET['id'] && is_numeric($_GET['id']))) {
  header("Location: campaigns.php"); die;
}

$query_donation = "SELECT 
    donation_id, 
    campaign_id, 
    contact_id, 
    donation_value, 
    donation_status, 
    donation_is_cash, 
    donation_pledge_date,
    donation_received_date,
    donation_description,
    campaign_name, 
    contact_company, 
    contact_first, 
    contact_last 
  FROM donations 
  LEFT JOIN contacts USING (contact_id) 
  LEFT JOIN campaigns USING (campaign_id)
  WHERE donation_id = ".$_GET['id']."";
$donation = mysql_query($query_donation, $contacts) or die(mysql_error());
$row_donation = mysql_fetch_assoc($donation);
$totalRows_donation = mysql_num_rows($donation);

$title_text = "Donation for Campaign " . $row_donation['campaign_name'];
$back_track = array('title' => "Campaign " . $row_donation['campaign_name'], 'url' => "campaign-details.php?campaign=" . $row_donation['campaign_id']);
?>
<?php include('includes/header.php'); ?>
<div class="container">
  <div class="leftcolumn">
    <span class="notices" style="display:<?php echo $dis; ?>">
      <?php display_msg(); ?>
    </span>
    <div style="display:block; margin-bottom:5px">
      <h2><?php echo $title_text; ?></h2>
      <br clear="all" />
    </div>
    <dl>
      <dt class="unitx1">Donor</dt>
      <dd><a href="contact-details.php?id=<?php echo $row_donation['contact_id']; ?>">
        <?php if($row_donation['contact_company']) {
            printf("%s (%s %s)", $row_donation['contact_company'], $row_donation['contact_first'], $row_donation['contact_last']); 
          } else {
            printf("%s %s", $row_donation['contact_first'], $row_donation['contact_last']); 
          } ?></a>
      </dd>
      <dt class="unitx1">Status</dt>
      <dd><?php echo ucwords($row_donation['donation_status']); ?></dd>
      <dt class="unitx1">Amount</dt>
      <dd>
        <?php echo money_format("%n", $row_donation['donation_value']) . " " . donation_kind_text($row_donation['donation_is_cash']); ?>
      </dd>
      <dt class="unitx1">Pledged</dt>
      <dd><?php echo $row_donation['donation_pledge_date'] ? date("M j, Y", strtotime($row_donation['donation_pledge_date'])) : $na; ?></dd>
      <dt class="unitx1">Received</dt>
      <dd><?php echo $row_donation['donation_received_date'] ? date("M j, Y", strtotime($row_donation['donation_received_date'])) : $na; ?></dd>
      <dt class="unitx1">Description</dt>
      <dd class="width1"><?php echo $row_donation['donation_description'] ? $row_donation['donation_description'] : $na; ?></dd>
    </dl>
    <br class="first"/><br /><a href="#" onclick="new Effect.toggle('update_status', 'slide'); return false;">+Update Status</a>
    <br />
    <div id="update_status" style="display:none">
      <form id="form1" name="form1" method="post" action="">
      <input type="hidden" name="donation" id="donation" value="<?php echo $row_donation['donation_id']; ?>" />
      <input type="hidden" name="campaign" id="campaign" value="<?php echo $row_donation['campaign_id']; ?>" />
      <input type="hidden" name="action" id="action" value="updatestatus" />
        <fieldset class="width2">
            <label class="first column unitx1">
              Status
              <select id="status" name="status">
                <option <?php if ($row_donation['donation_status'] == 'expected') { echo "selected='selected'"; } ?>>Expected</option>
                <option <?php if ($row_donation['donation_status'] == 'pledged') { echo "selected='selected'"; } ?>>Pledged</option>
                <option <?php if ($row_donation['donation_status'] == 'received') { echo "selected='selected'"; } ?>>Received</option>
              </select>
            </label>
            <label class="column unitx1">
              On
              <input type="text" id="update_date" name="update_date" class="validate-date-au" value="<?php echo date("d/m/Y"); ?>" />
            </label>
            <label class="column inlinebutton width1">
              <input name="submit" type="submit" value="Update" />
            </label>
        <fieldset>
      </form>
    </div>
    <a href="#" onclick="new Effect.toggle('update_donation', 'slide'); return false;">+Update Donation </a>
    <br />
    <div id="update_donation" style="display:none">
      <form id="form3" name="form3" method="post" action="">
      <input type="hidden" name="donation" id="donation" value="<?php echo $row_donation['donation_id']; ?>" />
      <input type="hidden" name="campaign" id="campaign" value="<?php echo $row_donation['campaign_id']; ?>" />
      <input type="hidden" name="action" id="action" value="updatedonation" />
        <fieldset class="width2">
          <label class="first column unitx1">
            Type
            <select id="type" name="type">
              <option <?php if($row_donation['donation_is_cash']) { echo "selected='selected'"; } ?>>Cash</option>
              <option <?php if(!$row_donation['donation_is_cash']) { echo "selected='selected'"; } ?>>In Kind</option>
            </select>
          </label>
          <label class="column unitx1">
            Value
            <input type="text" id="amount" name="amount" class="validate-number" value="<?php echo $row_donation['donation_value']; ?>" />
          </label>
            <label class="column inlinebutton width1">
              <input name="submit" type="submit" value="Update" />
            </label>
        <fieldset>
      </form>
    </div>
    <a href="#" onclick="new Effect.toggle('update_description', 'slide'); return false;">+Update Description</a>
    <br />
    <div id="update_description" style="display:none">
      <form id="form4" name="form4" method="post" action="">
      <input type="hidden" name="donation" id="donation" value="<?php echo $row_donation['donation_id']; ?>" />
        <fieldset class="width2">
        <label class="column first width1">
          Description
          <textarea name="description" id="description" class="width1"><?php echo $row_donation['donation_description'] ?></textarea>
        </label>
        <label class="column first width1">
          <input name="submit" type="submit" value="Update" />
        </label>
        </fieldset>
      </form>
    </div>
    <hr />
    <form class="width2" id="form2" name="form2" method="post" action="">
      <input type="hidden" name="donation" id="donation" value="<?php echo $row_donation['donation_id']; ?>" />
      <input type="hidden" name="campaign" id="campaign" value="<?php echo $row_donation['campaign_id']; ?>" />
      <input type="hidden" name="action" id="action" value="delete" />
      <fieldset class="width2">
        <label class="column first width1">
          <input name="delete" type="submit" value="Delete" />
        </label>
      </fieldset>
    </form>
  </div>
  <?php include('includes/right-column.php'); ?>
  
  <br clear="all" />
</div>

<?php include('includes/footer.php'); ?>

</body>
</html>
