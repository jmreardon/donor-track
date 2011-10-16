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

$where_clauses = array();
$show_donations = false;

if(isset($_GET['search'])) {
  if(trim($_GET['name'])) {
    $names = explode(",", $_GET['name']);
    $clauses = array();
    foreach($names as $name) { 
      if(trim($name)) { 
        $t = mysql_real_escape_string(trim($name)); 
        $clauses[] = "(contact_first LIKE '%$t%' OR 
                       contact_last LIKE '%$t%' OR 
                       contact_company LIKE '%$t%' OR
                       CONCAT(contact_first, ' ', contact_last) LIKE '%$t%')";
      } 
    }  
    if(count($clauses) > 0) {
      $where_clauses[] = "(" . implode(" OR ", $clauses) . ")";
    }
  }
  if(trim($_GET['donation_desc'])) {
    $terms = explode(",", $_GET['donation_desc']);
    $clauses = array();
    foreach($terms as $term) { 
      if(trim($term)) { 
        $t = mysql_real_escape_string(trim($term)); 
        $clauses[] = "(donation_description LIKE '%$t%')";
      } 
    }  
    if(count($clauses) > 0) {
      $where_clauses[] = implode(" OR ", $clauses);
      $show_donations = true;
    }
  }
  if(in_array(trim($_GET['donation_status']), array("Expected", "Pledged", "Received"))) {
    $where_clauses[] = "donation_status = '" . strtolower(trim($_GET['donation_status'])) . "'";
  }
}

$where = (count($where_clauses) > 0 ? " AND " . implode(" AND ", $where_clauses) : "");

$query_donations = "SELECT 
    donation_id, 
    donation_value, 
    donation_status, 
    donation_is_cash, 
    donation_pledge_date, 
    donation_received_date,
    donation_description,
    contacts.*,
    contact_company IS NULL AS isnull
  FROM donations 
  LEFT JOIN contacts USING (`contact_id`) 
  WHERE campaign_id = " . $campaign['campaign_id'] . $where .
  " ORDER BY isnull, contact_company, contact_first, contact_last";

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

$stats = donation_stats($campaign['campaign_id'], $where);
$title_text = "Campaign - ". $campaign['campaign_name'];
$back_track = array('title' => "Campaigns", 'url' => "campaigns.php");

$display = isset($_GET['display']) ? $_GET['display'] : "html";
if (!in_array($display, array("csv", "html"))) {
  $display = "html";
}

switch ($display) {
  case "csv":
    $out = fopen('php://output', 'w');

    header('Content-type: text/csv');
    header('Content-Disposition: attachment; filename="donations.csv"');
    header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
    $fields = array("first name", "last name", "title", "company", 
                    "street", "city", "state/province", "postal code", 
                    "phone", "cell", "fax", "email", "website", 
                    "is individual", "donation value", "donation description", "status", "received", "pledged");
    fputcsv($out, $fields);
    if(!$row_donations) {
      break;
    }
    do {
      fputcsv($out, array($row_donations["contact_first"], $row_donations["contact_last"],
                          $row_donations["contact_title"], $row_donations["contact_company"],
                          $row_donations["contact_street"], $row_donations["contact_city"],
                          $row_donations["contact_state"], $row_donations["contact_zip"],
                          $row_donations["contact_phone"], $row_donations["contact_cell"],
                          $row_donations["contact_fax"], $row_donations["contact_email"],
                          $row_donations["contact_web"], $row_donations["isnull"],
                          $row_donations["donation_value"], $row_donations["donation_description"],
                          $row_donations["donation_status"], $row_donations["donation_received_date"],
                          $row_donations["donation_pledge_date"]));
    } while ($row_donations = mysql_fetch_assoc($donations));
    break;
  case "html":
?>
<?php include('includes/header.php'); ?>
  
  <div class="container">
  <div class="leftcolumn">
    <span class="notices" id="notice" style="display:<?php echo $dis; ?>">
      <?php display_msg(); ?>
    </span>
    <h2>Campaign - <?php echo $campaign['campaign_name']; ?></h2>
    <a href="<?php echo $_SERVER['REQUEST_URI'] . "&display=csv"; ?>"><strong>Export</strong></a>
    <br class="first"/><br /><a href="#" onclick="new Effect.toggle('search_pane', 'slide', { afterFinish: function() { $('name').focus(); }}); return false;">+Search</a>
    
    <div id="search_pane" style="display:<?php echo isset($_GET['search']) ? "block" : "none"; ?>">
      <form id="search_form" name="search_form" method="get" action="">
      <fieldset class="width3">
      <input type="hidden" name="campaign" id="campaign_id" value="<?php echo $campaign['campaign_id'] ?>" />
      <input type="hidden" name="search" id="search" value="search" />
      <label class="first column unitx2">
      Name/Organization
      <input type="text" id="name" name="name" value="<?php echo $_GET['name']; ?>"/>
      </label>
      <label class=" column unitx3">
      Donation Description
      <input type="text" id="donation_desc" name="donation_desc" value="<?php echo $_GET['donation_desc']; ?>"/>
      </label>
      <label class="column unitx1">
      With Status
      <select id="donation_status" name="donation_status">
	<option <?php if ($_GET['donation_status'] == "Any") { echo "selected='selected'"; } ?>>Any</option>
	<option <?php if ($_GET['donation_status'] == "Expected") { echo "selected='selected'"; } ?>>Expected</option>
	<option <?php if ($_GET['donation_status'] == "Pledged") { echo "selected='selected'"; } ?>>Pledged</option>
	<option <?php if ($_GET['donation_status'] == "Received") { echo "selected='selected'"; } ?>>Received</option>
      </select>
      </label>
      <label class="first column unitx3">
      <input type="submit" id="submit_search" name="submit_search" value="Search"/>
      </label>
      </fieldset>
      </form>
    </div>
    
    <table class="sortable" style="width: 100%;">
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
            <a href="donation-details.php?id=<?php echo $row_donations['donation_id']; ?>" title="<?php echo htmlspecialchars($row_donations['donation_description']) ?>">Details</a>
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
    <?php } else {
	if(isset($_GET['search'])) {
	?>
        <tr><td style="text-align: center;" colspan="7">No Donations in <?php echo $campaign['campaign_name']; ?> match.</td></tr>
	<?php } else { ?>
        <tr><td style="text-align: center;" colspan="7">No Donations for <?php echo $campaign['campaign_name']; ?></td></tr>
    <?php } } ?>
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
<?php
    break;
}
