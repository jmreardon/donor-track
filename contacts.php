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
    contact_company,
    contact_first,
    contact_last,
    contact_title,
    contact_phone,
    contact_email,
    contact_company IS NULL AS isnull,
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
  ORDER BY isnull, contact_company, contact_first, contact_last";
$contacts = mysql_query($query_contacts, $contacts) or die(mysql_error());
$row_contacts = mysql_fetch_assoc($contacts);
$totalRows_contacts = mysql_num_rows($contacts);

if ($totalRows_contacts < 1) { 
header('Location: contact.php');
}

//act on multiple contacts
if (isset($_POST['d'])) {
  foreach($_POST['d'] as $key => $value) {
    if (is_numeric($value)) {
      if($_POST['action'] == "Delete") {
        mysql_query("DELETE FROM contacts WHERE contact_id = ".$value."");
      }
      if($_POST['action'] == "Target" && is_numeric($_POST['campaign'])) {
        mysql_query("INSERT INTO targets (`contact_id`, `campaign_id`) VALUES ($value, " . $_POST['campaign'] . ")");
      }
      if($_POST['action'] == "Untarget" && is_numeric($_POST['campaign'])) {
        mysql_query("DELETE FROM targets WHERE contact_id = $value AND campaign_id = " . $_POST['campaign']);
      }
    }
  }
  if($_POST['action'] == "Delete") {
    set_msg('Contacts Deleted');
  } else if($_POST['action'] == "Untarget") {
    set_msg('Contacts Untargeted');
  } else if($_POST['action'] == "Target") {
    set_msg('Contacts Targeted');
  }
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
    <a href="csv.php"><strong>Export</strong></a><strong> | </strong>
    <a href="batch.php"><strong>Import</strong></a><strong> | </strong>
    <a href="contact.php"><strong>Create</strong></a>
    <form id="form1" name="form1" method="post" action="">
      <fieldset>
      <label style="margin-bottom: 0px" class="first column unitx1">
        Action
        <select id="action" name="action">
          <option>Delete</option>
          <option>Target</option>
          <option>Untarget</option>
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
            if($("action").getValue() != "Delete") {
              $("target_value_label").show();
            } else if($("action").getValue() == "Delete") {
              $("target_value_label").hide();
            }
          });
        });
      </script>
      <label style="margin-bottom: 0px" class="column width1 inlinebutton">
        <input type="submit" name="Submit" value="Submit" />
      </label>
      </fieldset>
      <p style="text-align: right;">
        Select <a href="#" onclick="$$('#form1 input.action_check').each(function(box){box.checked=true});return false;">All</a> | 
               <a href="#" onclick="$$('#form1 input.action_check').each(function(box){box.checked=false});return false;">None</a>
      </p>
      <table class="sortable" style="width: 100%; margin-top: 5px">
        <thead>
        <tr>
          <th class="nosort"></th>
          <th>Company</th>
          <th>Name</th>
          <th class="nosort">Phone</th>
          <th class="nosort">Email</th>
          <th class="date one-line">Last Gave</th>
          <th class="nosort" width="7%" style="text-align: center">Select</th>
        </tr>
        </thead>
        <tbody>
  <?php do { $row_count++; ?>
        <tr>
          <td style="padding-right: 10px"><a href="contact-details.php?id=<?php echo $row_contacts['contact_id']; ?>">
            Details
          </a></td>
          <td>
            <?php echo $row_contacts['contact_company'] ? $row_contacts['contact_company'] : $na; ?>
          </td>
          <td class="one-line">
            <?php printf("%s %s", $row_contacts['contact_first'] ? $row_contacts['contact_first'] : $row_contacts['contact_title'], 
                                  $row_contacts['contact_last']); ?>
          </td>
          <td class="one-line"><?php echo $row_contacts['contact_phone'] ? $row_contacts['contact_phone'] : $na; ?></td>
          <td class="one-line"><a href="mailto:<?php echo $row_contacts['contact_email']; ?>"><?php echo $row_contacts['contact_email']; ?></a></td>
          <td class="one-line"><?php if($row_contacts['last_donation']) {
            echo date("M j, Y", strtotime($row_contacts['last_donation'])) ?>
          <?php } else { echo $na; } ?>
          <td>
            <input name="d[<?php echo $row_contacts['contact_id']; ?>]" type="checkbox" class="action_check" id="d[<?php echo $row_contacts['contact_id']; ?>]" value="<?php echo $row_contacts['contact_id']; ?>" />
          </td>
        </tr>
        <?php } while ($row_contacts = mysql_fetch_assoc($contacts)); ?>
        </tbody>
      </table>
    </form>
    <?php } ?>



  </div>
  <?php include('includes/right-column.php'); ?>
  <br clear="all" />
</div>

<?php include('includes/footer.php'); ?>

</body>
</html>
