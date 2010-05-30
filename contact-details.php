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

mysql_select_db($database_contacts, $contacts);
$query_notes = "SELECT * FROM notes WHERE note_contact = ".$_GET['id']." ORDER BY note_date DESC";
$notes = mysql_query($query_notes, $contacts) or die(mysql_error());
$row_notes = mysql_fetch_assoc($notes);
$totalRows_notes = mysql_num_rows($notes);

if ($update==1) {
mysql_select_db($database_contacts, $contacts);
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
$title_text = $row_contact['contact_first'] . " " . $row_contact['contact_last']; 
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
<h2><?php echo $row_contact['contact_first']; ?> <?php echo $row_contact['contact_last']; ?><?php if ($row_contact['contact_company']) { ?><span style="color:#999999"> with <?php echo $row_contact['contact_company']; ?><?php } ?></span><a style="font-size:12px; font-weight:normal" href="contact.php?id=<?php echo $row_contact['contact_id']; ?>">&nbsp;&nbsp;+ Edit contact </a>    </h2>
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
              <?php } while ($row_notes = mysql_fetch_assoc($notes)); ?></form>
<?php } ?>


    <p>&nbsp;</p>
    <p>&nbsp;</p>
  </div>
  <?php include('includes/right-column.php'); ?>
  
  <br clear="all" />
</div>

<?php include('includes/footer.php'); ?>

</body>
</html>
