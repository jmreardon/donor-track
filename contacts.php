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
$pagetitle = "Contact";

mysql_select_db($database_contacts, $contacts);
$query_contacts = "SELECT 
    contact_id,
    contact_first,
    contact_last,
    contact_phone,
    contact_email,
    (SELECT campaign_id 
     FROM campaigns 
     WHERE campaigns.campaign_id = (SELECT campaign_id FROM donations WHERE donations.contact_id = contacts.contact_id AND
           donations.donation_status = 'received' 
     ORDER BY donation_received_date DESC LIMIT 1)) AS last_donation_campaign_id,
    (SELECT campaign_name FROM campaigns WHERE campaign_id = last_donation_campaign_id) AS last_donation_campaign,
    (SELECT donation_received_date FROM donations WHERE donations.contact_id = contacts.contact_id AND
           donations.donation_status = 'received' 
     ORDER BY donation_received_date DESC LIMIT 1) AS last_donation
  FROM contacts 
  ORDER BY contact_last, contact_first";
$contacts = mysql_query($query_contacts, $contacts) or die(mysql_error());
$row_contacts = mysql_fetch_assoc($contacts);
$totalRows_contacts = mysql_num_rows($contacts);

if ($totalRows_contacts < 1) { 
header('Location: contact.php');
}

//delete multiple contacts
if (isset($_POST['d'])) {
	foreach($_POST['d'] as $key => $value) {
		if ($value) {
			mysql_query("DELETE FROM contacts WHERE contact_id = ".$value."");
		}
		
	}
set_msg('Contacts Deleted');
header('Location: contacts.php'); die;
}
//
?>
<?php include('includes/header.php'); ?>
  
  <div class="container">
  <div class="leftcolumn">
    <h2>Contacts</h2>
<?php if ($totalRows_contacts > 0) { ?>
    <span class="notices" id="notice" style="display:<?php echo $dis; ?>">
      <?php display_msg(); ?>
    </span>
    <a href="csv.php"><strong>Export</strong></a><strong> | </strong><a href="batch.php"><strong>Import</strong></a>
    <form id="form1" name="form1" method="post" action="">
      <table class="sortable">
        <thead>
        <tr>
          <th>Name</th>
          <th class="nosort">Phone</th>
          <th class="nosort">Email</th>
          <th>Last Donation</th>
          <th class="nosort" width="7%">Delete</th>
        </tr>
        </thead>
        <tbody>
  <?php do { $row_count++; ?>
        <tr>
          <td><a href="contact-details.php?id=<?php echo $row_contacts['contact_id']; ?>">
            <?php printf("%s, %s", $row_contacts['contact_last'], $row_contacts['contact_first']); ?>
          </a></td>
          <td><?php echo $row_contacts['contact_phone'] ? $row_contacts['contact_phone'] : $na; ?></td>
          <td><a href="mailto:<?php echo $row_contacts['contact_email']; ?>"><?php echo $row_contacts['contact_email']; ?></a></td>
          <td><?php if($row_contacts['last_donation']) {
            echo date("M j, Y", strtotime($row_contacts['last_donation'])) ?>
            For <a href="campaign-details.php?campaign=<?php echo $row_contacts['last_donation_campaign_id']; ?>"><?php echo $row_contacts['last_donation_campaign'] ?>
          <?php } else { echo $na; } ?>
          <td>
            <input name="d[<?php echo $row_contacts['contact_id']; ?>]" type="checkbox" id="d[<?php echo $row_contacts['contact_id']; ?>]" value="<?php echo $row_contacts['contact_id']; ?>" />
          </td>
        </tr>
        <?php } while ($row_contacts = mysql_fetch_assoc($contacts)); ?>
        </tbody>
      </table>
      <input type="submit" name="Submit" value="Submit" />
    </form>
    <?php } ?>



  </div>
  <?php include('includes/right-column.php'); ?>
  <br clear="all" />
</div>

<?php include('includes/footer.php'); ?>

</body>
</html>
