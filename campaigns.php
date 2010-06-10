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
$pagetitle = "Campaigns";

mysql_select_db($database_contacts, $contacts);
if($_POST['campaign_name'] && is_numeric($_POST['campaign_target'])) {
  $result = mysql_query(
    "INSERT INTO campaigns (`campaign_name`, `campaign_target`) VALUES ('" .
    mysql_real_escape_string($_POST['campaign_name']) . "', '" .
    mysql_real_escape_string($_POST['campaign_target']) . "')");
  if(mysql_affected_rows() == 1) {
    set_msg("Campaign Added.");
    if(is_numeric($_POST['expectation'])) {
      $new_campaign_id = mysql_insert_id();
      $copied_donations = mysql_query("INSERT INTO donations (campaign_id, contact_id, donation_is_cash, donation_value, donation_status, donation_description) 
                                       SELECT $new_campaign_id, d.contact_id, d.donation_is_cash, d.donation_value, 'expected', d.donation_description 
                                       FROM donations AS d 
                                       WHERE d.campaign_id = " . $_POST['expectation'] . " AND d.donation_status = 'received'");
      $copied = mysql_affected_rows();
      set_msg("Campaign Added. $copied donation" . ($copied == 1 ? " is" : "s are") . " expected.");
      $copied_targets = mysql_query("INSERT INTO targets (campaign_id, contact_id)
                                       SELECT $new_campaign_id, t.contact_id
                                       FROM targets AS t LEFT JOIN donations AS d USING (contact_id, campaign_id)
                                       WHERE t.campaign_id = " . $_POST['expectation'] . " AND d.donation_status = 'received'");
    }
  } else {
    set_msg("Failed to add campaign.");
  }
  header('Location: campaigns.php'); die;
} else if ($_POST['campaign_target'] && !is_numeric($_POST['campaign_target'])) {
  set_msg("Campaign target must be a number");
  header('Location: campaigns.php'); die;
}

$query_campaigns = "SELECT 
    campaign_id,
    campaign_name,
    campaign_target,
    ifnull((SELECT sum(donation_value) 
     FROM donations 
     WHERE donations.campaign_id = campaigns.campaign_id AND
           donations.donation_status = 'received' AND
           donations.donation_is_cash = false), 0) AS in_kind_received,
    ifnull((SELECT sum(donation_value) 
     FROM donations 
     WHERE donations.campaign_id = campaigns.campaign_id AND
           donations.donation_status = 'received' AND
           donations.donation_is_cash = true), 0) AS cash_received
  FROM campaigns 
  ORDER BY campaign_id desc";
$campaigns = mysql_query($query_campaigns, $contacts) or die(mysql_error());
$row_campaigns = mysql_fetch_assoc($campaigns);
$totalRows_campaigns = mysql_num_rows($campaigns);


?>

<?php include('includes/header.php'); ?>
  
  <div class="container">
  <div class="leftcolumn">
    <span class="notices" style="display:<?php echo $dis; ?>">
      <?php display_msg(); ?>
    </span>
    <h2>Campaigns</h2>
      <table class="sortable">
      <thead>
        <tr>
          <th>Name</th>
          <th class="right-cell">Target</th>
          <th class="right-cell">Cash Received</th>
          <th class="right-cell">In Kind Received</th>
          <th class="right-cell">Actual</th>
        </tr>
      </thead>
      <tbody>
        <?php if($row_campaigns) { ?>
        <?php do { ?>
	  <tr>
            <td>
              <a href="campaign-details.php?campaign=<?php echo $row_campaigns['campaign_id']; ?>"><?php echo $row_campaigns['campaign_name']; ?></a>
            </td>
            <td class="right-cell"><?php echo money_format("%n", $row_campaigns['campaign_target']); ?></td>
            <td class="right-cell"><?php echo money_format("%n", $row_campaigns['cash_received']); ?></td>
            <td class="right-cell"><?php echo money_format("%n", $row_campaigns['in_kind_received']); ?></td>
            <td class="right-cell">
              <?php echo money_format("%n", $row_campaigns['in_kind_received'] + $row_campaigns['cash_received']); ?>
            </td>
          </tr>
        <?php } while ($row_campaigns = mysql_fetch_assoc($campaigns)); ?>
        <?php } else { ?>
          <tr><td style="text-align: center;" colspan="5">No Campaigns</td></tr>
        <?php } ?>
      </tbody>
      </table>
      <br />
      <a href="#" onclick="new Effect.toggle('add_campaign', 'slide', { afterFinish: function() { $('campaign_name').focus(); }}); return false;">+Add Campaign</a>
      <br />
      <div id="add_campaign" style="display:none">
        <form name="form1" id="form1" method="post" action="campaigns.php">
          <fieldset>
          <label class="first width1">
            Name 
            <input name="campaign_name" id="campaign_name" class="required" type="text" value="" />
          </label>
          <label class="unitx1">
            Target 
            <input name="campaign_target" id="campaign_target" class="validate-number" type="text" value="" />
          </label>
          <label class="unitx2">
            Expectations From
            <select id="expectation" name="expectation">
              <?php echo_campaign_options(); ?>
              <option>None</option>
            </select>
          </label>
          <label class="unitx1 inlinebutton">
            <input name="submit" type="submit" value="Add Campaign" />
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
