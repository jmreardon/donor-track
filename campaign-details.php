<?php require_once('includes/config.php');

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

include('includes/sc-includes.php');
include('includes/donation.inc.php');

$pagetitle = "Campaigns";
//Set Campaign
if($_GET['campaign'] && is_numeric($_GET['campaign'])) {
  $campaign = $_GET['campaign'];
} else {
  header("Location: campaigns.php"); die;
}

mysql_select_db($database_contacts, $contacts);
$campaigns_result = mysql_query("SELECT * FROM campaigns WHERE campaign_id = $campaign", $contacts) or die(mysql_error());
$campaign = mysql_fetch_assoc($campaigns_result);
$campaign_row_count = mysql_num_rows($campaigns_result);
if($campaign_row_count == 0) {
  header("Location: campaigns.php?"); die;
}

$query_donations = "SELECT 
    donation_id, 
    donation_value, 
    donation_status, 
    donation_is_cash, 
    donation_pledge_date, 
    donation_received_date, 
    contact_id, 
    contact_first, 
    contact_last, 
    contact_title, 
    contact_company 
  FROM donations 
  LEFT JOIN contacts USING (`contact_id`) 
  WHERE campaign_id = " . $campaign['campaign_id'];

$donations = mysql_query($query_donations, $contacts) or die(mysql_error());
$row_donations = mysql_fetch_assoc($donations);
$totalRows_donations = mysql_num_rows($donations);

$query_targets = "SELECT
    contact_id,
    contact_first,
    contact_last,
    contact_title,
    contact_company
  FROM targets
  LEFT JOIN contacts using (contact_id)
  WHERE campaign_id = " . $campaign['campaign_id'] ." AND
        (SELECT COUNT(*) 
         FROM donations 
         WHERE campaign_id = " . $campaign['campaign_id'] ." AND 
               donations.contact_id = targets.contact_id
        ) = 0";
$targets = mysql_query($query_targets, $contacts) or die(mysql_error());
$row_targets = mysql_fetch_assoc($targets);
$totalRows_targets = mysql_num_rows($targets);

$stats = donation_stats($campaign['campaign_id']);
$title_text = "Campaign - ". $campaign['campaign_name'];
$back_track = array('title' => "Campaigns", 'url' => "campaigns.php");
?>
<?php include('includes/header.php'); ?>
  
  <div class="container">
  <div class="leftcolumn">
    <span class="notices" id="notice" style="display:<?php echo $dis; ?>">
      <?php display_msg(); ?>
    </span>
    <h2>Campaign - <?php echo $campaign['campaign_name']; ?></h2>
      <table class="sortable">
        <thead>
        <tr>
          <th class="nosort"></th>
          <th class="sortfirstasc text">Donor</th>
          <th class="text">Status</th>
          <th class="currency">Value</th>
          <th class="text centre-cell">Type</th>
          <th>Pledged</th>
          <th>Received</th>
        </tr>
        </thead>
        <tfoot>
        <tr>
          <td colspan="6" class="right-cell">Expected</td>
          <td class="right-cell"><?php echo money_format("%n", $stats->expected); ?></td>
        </tr>
        <tr>
          <td colspan="6" class="right-cell">Pledged</td>
          <td class="right-cell"><?php echo money_format("%n", $stats->pledged); ?></td>
        </tr>
        <tr>
          <td colspan="6" class="right-cell">Received</td>
          <td class="right-cell"><?php echo money_format("%n", $stats->received); ?></td>
        </tr>
        <tr>
          <td colspan="6" class="right-cell">Total</td>
          <td class="right-cell"><?php echo money_format("%n", $stats->total); ?></td>
        </tr>
        </tfoot>
        <tbody>
<?php if ($totalRows_donations > 0) { ?>
  <?php do { $row_count++; ?>
        <tr>
          <td style="padding-right: 10px">
            <a href="donation-details.php?id=<?php echo $row_donations['donation_id']; ?>">Details</a>
          </td>
          <td><a href="contact-details.php?id=<?php echo $row_donations['contact_id']; ?>">
            <?php echo display_name($row_donations); ?>
          </a></td>
          <td><?php echo ucwords($row_donations['donation_status']) ?></td>
          <td class="one-line"><?php printf("$%.2f", $row_donations['donation_value']); ?></td>
          <td class="one-line centre-cell">
            <?php echo donation_kind_text($row_donations['donation_is_cash']); ?>
          </td>
          <td class="one-line">
            <?php echo $row_donations['donation_pledge_date']
                         ? date("M j, Y", strtotime($row_donations['donation_pledge_date'])) 
                         : $na; 
            ?> 
          </td>
          <td class="one-line">
            <?php echo $row_donations['donation_received_date'] 
                         ? date("M j, Y", strtotime($row_donations['donation_received_date'])) 
                         : $na; 
            ?> 
          </td>
        </tr>
        <?php } while ($row_donations = mysql_fetch_assoc($donations)); ?>
    <?php } else { ?>
        <tr><td style="text-align: center;" colspan="7">No Donations for <?php echo $campaign['campaign_name']; ?></td></tr>
    <?php } ?>
        </tbody>
      </table>
      <br />
<?php if ($totalRows_targets > 0) { ?>
      <h3>Other Targets</h3>
      <ul>
        <?php do { ?>
          <li><a href="contact-details.php?id=<?php echo $row_targets['contact_id']; ?>">
              <?php echo display_name($row_targets); ?>
          </a></li>
        <?php } while ($row_targets = mysql_fetch_assoc($targets)); ?>
      </ul>
<?php } ?>
  </div>
  <?php include('includes/right-column.php'); ?>
  <br clear="all" />
</div>

<?php include('includes/footer.php'); ?>

</body>
</html>
