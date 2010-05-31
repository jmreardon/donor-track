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
    contact_company 
  FROM donations 
  LEFT JOIN contacts USING (`contact_id`) 
  WHERE campaign_id = " . $campaign['campaign_id'] . "
  ORDER BY contact_last, contact_first";

$donations = mysql_query($query_donations, $contacts) or die(mysql_error());
$row_donations = mysql_fetch_assoc($donations);
$totalRows_donations = mysql_num_rows($donations);

$stats = donation_stats($campaign['campaign_id']);
$title_text = "Campaign - ". $campaign['campaign_name'];
$back_track = array('title' => "Campaigns", 'url' => "campaigns.php");
?>
<?php include('includes/header.php'); ?>
  
  <div class="container">
  <div class="leftcolumn">
    <h2>Campaign - <?php echo $campaign['campaign_name']; ?></h2>
    <span class="notices" id="notice" style="display:<?php echo $dis; ?>">
      <?php display_msg(); ?>
    </span>
      <table class="sortable">
        <thead>
        <tr>
          <th class="text">Donor</th>
          <th class="text">Status</th>
          <th class="currency">Value</th>
          <th class="text">Type</th>
          <th>Pledged</th>
          <th>Received</th>
          <th class="nosort"></th>
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
          <td><a href="contact-details.php?id=<?php echo $row_donations['contact_id']; ?>">
            <?php 
              if($row_donations['contact_company']) {
	        printf("%s (%s %s)", $row_donations['contact_company'], $row_donations['contact_first'], $row_donations['contact_last']); 
              } else {
	        printf("%s, %s", $row_donations['contact_last'], $row_donations['contact_first']); 
              }
            ?>
          </a></td>
          <td><?php echo ucwords($row_donations['donation_status']) ?></td>
          <td><?php printf("$%.2f", $row_donations['donation_value']); ?></td>
          <td>
            <?php echo donation_kind_text($row_donations['donation_is_cash']); ?>
          </td>
          <td>
            <?php echo $row_donations['donation_pledge_date']
                         ? date("M j, Y", strtotime($row_donations['donation_pledge_date'])) 
                         : $na; 
            ?> 
          </td>
          <td>
            <?php echo $row_donations['donation_received_date'] 
                         ? date("M j, Y", strtotime($row_donations['donation_received_date'])) 
                         : $na; 
            ?> 
          </td>
          <td>
            <a href="donation-details.php?id=<?php echo $row_donations['donation_id']; ?>">Details</a>
          </td>
        </tr>
        <?php } while ($row_donations = mysql_fetch_assoc($donations)); ?>
    <?php } else { ?>
        <tr><td style="text-align: center;" colspan="7">No Donations for <?php echo $campaign['campaign_name']; ?></td></tr>
    <?php } ?>
        </tbody>
      </table>
      <br />
  </div>
  <?php include('includes/right-column.php'); ?>
  <br clear="all" />
</div>

<?php include('includes/footer.php'); ?>

</body>
</html>
