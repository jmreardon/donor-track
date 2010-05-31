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

$pagetitle = "Donations";
$campaign = get_default_campaign();

//Set Year
if($_GET['campaign']) {
  $campaign = $_GET['campaign'];
}

mysql_select_db($database_contacts, $contacts);
$campaigns_result = mysql_query("SELECT * FROM campaigns ORDER BY campaign_id desc", $contacts) or die(mysql_error());
$campaigns = array();
$found_campaign = false;
while($row_campaigns = mysql_fetch_assoc($campaigns_result)) {
  array_push($campaigns, $row_campaigns);
  if($row_campaigns['campaign_id'] == $campaign) {
    $found_campaign = true;
    $campaign = $row_campaigns;
  }
}
if(!$found_campaign) {
  $campaign = $campaigns[0];
}

$query_donations = "SELECT 
    donation_id, 
    donation_year, 
    donation_value, 
    donation_status, 
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

?>
<?php include('includes/header.php'); ?>
  
  <div class="container">
  <div class="leftcolumn">
    <h2>Donations</h2>
    <span class="notices" id="notice" style="display:<?php echo $dis; ?>">
      <?php display_msg(); ?>
    </span>
    <form class="width2" id="form1" name="form1" method="get" action="">
        <select style="float: left" name="campaign" id="campaign">
          <?php foreach ($campaigns as $curr) { ?>
            <option value="<?php echo $curr['campaign_id']; ?>" <?php echo ($curr == $campaign) ? "selected='selected'" : "" ?>>
              <?php echo $curr['campaign_name']; ?>
            </option>
          <?php } ?>
        </select>
        <input style="top: 0px; margin: 0px 0px 0px 5px" type="submit" value="Show">
    </form>
      <table class="sortable">
        <thead>
        <tr>
          <th class="text">Donor</th>
          <th class="text">Status</th>
          <th class="currency">Value</th>
          <th class="text">Type</th>
          <th>Pledged</a></th>
          <th>Received</a></th>
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
            <?php
              if($row_donations['donation_is_cash']) {
                echo "Cash";
              } else {
                echo "In Kind";
              }
            ?>
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
            Details
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
