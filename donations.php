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
$year = get_fiscal_year();

//Set Year
if(is_numeric($_GET['year'])) {
  $year = $_GET['year'];
}
//SORTING
$name = "name_up";
if (isset($_GET['name_up'])) {
$sorder = "ORDER BY contact_last ASC";
$name = "name_down";
} elseif (isset($_GET['name_down'])) {
$sorder = "ORDER BY contact_last DESC";
}

$email = "email_up";
if (isset($_GET['email_up'])) {
$sorder = "ORDER BY contact_email ASC";
$email = "email_down";
} elseif (isset($_GET['email_down'])) {
$sorder = "ORDER BY contact_email DESC";
}

$phone = "phone_up";
if (isset($_GET['phone_up'])) {
$sorder = "ORDER BY contact_phone ASC";
$phone = "phone_down";
} elseif (isset($_GET['email_phone'])) {
$sorder = "ORDER BY contact_phone DESC";
}
//END SORTING

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
  WHERE donation_year = $year
  ORDER BY contact_last, contact_first";

mysql_select_db($database_contacts, $contacts);
$donations = mysql_query($query_donations, $contacts) or die(mysql_error());
$row_donations = mysql_fetch_assoc($donations);
$totalRows_donations = mysql_num_rows($donations);

$years_result = mysql_query("SELECT DISTINCT donation_year AS year FROM donations ORDER BY year", $contacts) or die(mysql_error());
$years = array();
while($row_years = mysql_fetch_row($years_result)) {
  array_push($years, $row_years[0]);
}
array_push($years, $year);
$years = array_unique($years, SORT_NUMERIC);
$stats = donation_stats($year);

?>
<?php include('includes/header.php'); ?>
  
  <div class="container">
  <div class="leftcolumn">
    <h2>Donations</h2>
    <span class="notices" id="notice" style="display:<?php echo $dis; ?>">
      <?php display_msg(); ?>
    </span>
    <form class="width2" id="form1" name="form1" method="get" action="">
        <select style="float: left" name="year" id="year">
          <?php foreach ($years as $curr) { ?>
            <option <?php echo ($curr == $year) ? "selected='selected'" : "" ?>><?php echo $curr; ?></option>
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
        <tr><td style="text-align: center;" colspan="7">No Donations for <?php echo $year; ?></td></tr>
    <?php } ?>
        </tbody>
      </table>
      <br />
      <h2>Statistics</h2>
      <div class="unitx2">
        <div class="unitx1 column first">Expected</div>
        <div class="unitx1 column align-right">$<?php echo $stats->expected ?></div>
        <div class="unitx1 column first">Pledged</div>
        <div class="unitx1 column align-right">$<?php echo $stats->pledged ?></div>
        <div class="unitx1 column first">Received</div>
        <div class="unitx1 column align-right">$<?php echo $stats->received ?></div>
        <div class="unitx1 column first">Total</div>
        <div class="unitx1 column align-right">$<?php echo $stats->total ?></div>
      </div>
  </div>
  <?php include('includes/right-column.php'); ?>
  <br clear="all" />
</div>

<?php include('includes/footer.php'); ?>

</body>
</html>
