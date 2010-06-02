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

include('includes/sc-includes.php');
$pagetitle = "ContactDetails";

$update = 0;
if (isset($_GET['note'])) {
$update = 1;
}

mysql_select_db($database_contacts, $contacts);
$query_contact = "SELECT * FROM contacts WHERE contact_id = ".$_GET['id']."";
$contact = mysql_query($query_contact, $contacts) or die(mysql_error());
$row_contact = mysql_fetch_assoc($contact);
$totalRows_contact = mysql_num_rows($contact);

$query_donations = "SELECT 
    donation_id, 
    campaign_id, 
    campaign_name,
    donation_value, 
    donation_status, 
    donation_is_cash, 
    donation_pledge_date, 
    donation_received_date,
    GREATEST(COALESCE(donation_pledge_date,0), COALESCE(donation_received_date,0)) as last_update 
  FROM donations 
  LEFT JOIN campaigns USING (`campaign_id`) 
  WHERE contact_id = " . $row_contact['contact_id'] . "
  ORDER BY last_update desc";

$donations = mysql_query($query_donations, $contacts) or die(mysql_error());
$row_donations = mysql_fetch_assoc($donations);
$totalRows_donations = mysql_num_rows($donations);

$query_campaigns = "SELECT campaign_id, campaign_name FROM campaigns ORDER BY campaign_id DESC";
$campaigns = mysql_query($query_campaigns, $contacts) or die(mysql_error());
$row_campaigns = mysql_fetch_assoc($campaigns);
$totalRows_campaigns = mysql_num_rows($campaigns);

$query_notes = "SELECT * FROM notes WHERE note_contact = ".$_GET['id']." ORDER BY note_date DESC";
$notes = mysql_query($query_notes, $contacts) or die(mysql_error());
$row_notes = mysql_fetch_assoc($notes);
$totalRows_notes = mysql_num_rows($notes);

if ($update==1) {
$query_note = "SELECT * FROM notes WHERE note_id = ".$_GET['note']."";
$note = mysql_query($query_note, $contacts) or die(mysql_error());
$row_note = mysql_fetch_assoc($note);
$totalRows_note = mysql_num_rows($note);
}

//INSERT NOTE FOR CONTACT
if ($update==0) {
if ($_POST['note_text']) {
mysql_query("INSERT INTO notes (note_contact, note_text, note_date, note_status) VALUES 
	(
	".$row_contact['contact_id'].",
	'".mysql_real_escape_string($_POST['note_text'])."',
	".time().",
	1
	)
");
set_msg('Note Added');
$cid = $_GET['id'];
$goto = "contact-details.php?id=$cid";
header(sprintf('Location: %s', $goto)); die;
}
}
//

//UPDATE NOTE
if ($update==1) {
if ($_POST['note_text']) {
mysql_query("UPDATE notes SET note_text = '".mysql_real_escape_string($_POST['note_text'])."' WHERE note_id = ".$_GET['note']."");
$cid = $_GET['id'];
$goto = "contact-details.php?id=$cid";
set_msg('Note Updated');
header(sprintf('Location: %s', $goto)); die;
}
}
//


//UPDATE HISTORY

$query_checkhistory = "SELECT history_contact FROM history WHERE history_contact = ".$_GET['id']."";
$checkhistory = mysql_query($query_checkhistory, $contacts) or die(mysql_error());
$row_checkhistory = mysql_fetch_assoc($checkhistory);
$totalRows_checkhistory = mysql_num_rows($checkhistory);


if ($totalRows_checkhistory > 0) { 
mysql_query("UPDATE history SET history_status = 2 WHERE history_contact = ".$_GET['id']."");
}

mysql_query("INSERT INTO history (history_contact, history_date, history_status) VALUES
(
	".$row_contact['contact_id'].",
	".time().",
	1
)
");

//
$title_text = display_name($row_contact);
$back_track = array('title' => "Contacts", 'url' => "contacts.php");
?>
<?php include('includes/header.php'); ?>
<?php if ($row_notes['note_date'] > time()-1) { ?>
<script type="text/javascript">
  Event.observe(window, 'load', function() {
    new Effect.Highlight('newnote'); 
    return false;
  });
</script>
<?php } ?>
<div class="container">
  <div class="leftcolumn">
<span class="notices" style="display:<?php echo $dis; ?>">
    <?php display_msg(); ?>
    </span>
<div style="display:block; margin-bottom:5px">
<?php if ($row_contact['contact_image']) { ?><img src="images/<?php echo $row_contact['contact_image']; ?>" width="95" height="71" class="contactimage" /><?php } ?>
<h2>
  <?php echo display_name($row_contact); ?>
  <a style="font-size:12px; font-weight:normal" href="contact.php?id=<?php echo $row_contact['contact_id']; ?>">&nbsp;&nbsp;+ Edit contact </a>
</h2>
<br clear="all" />
</div>

<p><br />
    </p>



    <form id="form1" name="form1" method="post" action="">
<?php if ($update==0) { echo "Add a new note <br>"; } ?>
<textarea name="note_text" style="width:95% "rows="3" id="note_text" class="required"><?php echo $row_note['note_text']; ?></textarea>
        <br />
        <input type="submit" name="Submit2" value="<?php if ($update==1) { echo 'Update'; } else { echo 'Add'; } ?> note" />
      <?php if ($update==1) { ?>  <a href="delete.php?note=<?php echo $row_note['note_id']; ?>&amp;id=<?php echo $row_note['note_contact']; ?>" onclick="javascript:return confirm('Are you sure you want to delete this note?')">Delete Note</a><?php } ?>
<?php if ($totalRows_notes > 0) { ?>
        <hr />
        <?php do { ?>
<div <?php if ($row_notes['note_date'] > time()-1) { ?>id="newnote"<?php } ?>>
        <span class="datedisplay"><a href="?id=<?php echo $row_contact['contact_id']; ?>&note=<?php echo $row_notes['note_id']; ?>"><?php echo date('F d, Y g:mA', $row_notes['note_date']); ?></a></span><br />
          <?php echo $row_notes['note_text']; ?>
</div>
          <hr />
              <?php } while ($row_notes = mysql_fetch_assoc($notes)); ?>
<?php } ?></form>
      <a href="#" onclick="new Effect.toggle('add_campaign', 'slide'); return false;">+Add Donation</a>
      <br />
      <div id="add_campaign" style="display:none">
<?php if($totalRows_campaigns == 0) { ?>
        <p>You must add a campaign before entering donations.</p>
<?php } else { ?>
        <form name="form1" id="form1" method="post" action="donation-details.php">
          <input type="hidden" name="action" id="action" value="create" />
          <input type="hidden" name="contact" id="contact" value="<?php echo $row_contact['contact_id']; ?>" />
          <fieldset>
          <label class="first unitx1">
            Campaign 
            <select id="campaign" name="campaign">
            <?php do { ?>
              <option value="<?php echo $row_campaigns['campaign_id']; ?>"><?php echo $row_campaigns['campaign_name']; ?></option>
            <?php } while($row_campaigns = mysql_fetch_assoc($campaigns)); ?>
            </select>
          </label>
          <label class="column unitx1">
            Type
            <select id="type" name="type">
              <option>Cash</option>
              <option>In Kind</option>
            </select>
          </label>
          <label class="column unitx1">
            Value
            <input type="text" id="amount" name="amount" class="validate-number" value="<?php echo $row_donation['donation_value']; ?>" />
          </label>
          <label class="unitx1 inlinebutton">
            <input name="submit" type="submit" value="Add" />
          </label>
          </fieldset>
        </form>
<?php } ?>
      </div>
 
      <table class="sortable">
        <thead>
        <tr>
          <th class="nosort"></th>
          <th class="text">Campaign</th>
          <th class="text">Status</th>
          <th class="currency">Value</th>
          <th class="text">Type</th>
          <th>Pledged</th>
          <th class="date">Received</th>
        </tr>
        </thead>
        <tbody>
<?php if ($totalRows_donations > 0) { ?>
  <?php do { $row_count++; ?>
        <tr>
          <td>
            <a href="donation-details.php?id=<?php echo $row_donations['donation_id']; ?>">Details</a>
          </td>
          <td><a href="campaign-details.php?campaign=<?php echo $row_donations['campaign_id']; ?>">
            <?php echo $row_donations['campaign_name']; ?>
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
        </tr>
        <?php } while ($row_donations = mysql_fetch_assoc($donations)); ?>
    <?php } else { ?>
        <tr><td style="text-align: center;" colspan="7">No Donations</td></tr>
    <?php } ?>
        </tbody>
      </table>
 

    <p>&nbsp;</p>
    <p>&nbsp;</p>
  </div>
  <?php include('includes/right-column.php'); ?>
  
  <br clear="all" />
</div>

<?php include('includes/footer.php'); ?>

</body>
</html>
