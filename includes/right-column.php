<?php

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

?>

<script type="text/javascript">
<!--
function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_setTextOfTextfield(objName,x,newText) { //v3.0
  var obj = MM_findObj(objName); if (obj) obj.value = newText;
}
//-->
</script>
<div class="rightcolumn">
    <?php if ($row_contact) { ?>
    <h3>Contact Information</h3>
      <?php if ($row_contact['contact_company']) { echo $row_contact['contact_company'] ."<br>"; } ?>
      <?php if ($row_contact['contact_street']) { echo $row_contact['contact_street']  ."<br>"; } ?>
    <?php if ($row_contact['contact_city']) { echo $row_contact['contact_city'] .","; } ?> <?php if ($row_contact['contact_state']) { echo $row_contact['contact_state']; } ?> <?php if ($row_contact['contact_zip']) { echo $row_contact['contact_zip']; } ?></p>
    <?php if ($row_contact['contact_street'] && $row_contact['contact_city'] && $row_contact['contact_state']) { ?><a href="http://maps.google.com/maps?f=q&amp;hl=en&amp;q=<?php echo $row_contact['contact_street']; ?>,+<?php echo $row_contact['contact_city']; ?>,+<?php echo $row_contact['contact_state']; ?>+<?php echo $row_contact['contact_zip']; ?>" target="_blank">+ View Map </a>
    <?php } ?>
    <p>      <?php if ($row_contact['contact_phone']) { ?>Phone: <?php echo $row_contact['contact_phone']; ?><br /><?php } ?>

<?php if ($row_contact['contact_web']) { ?>
      <a href="<?php echo $row_contact['contact_web']; ?>" target="_blank"><?php echo $row_contact['contact_web']; ?></a>        
<?php } ?>

<?php if ($row_contact['contact_email']) { ?>
      <a href="mailto:<?php echo $row_contact['contact_email']; ?>"><?php echo $row_contact['contact_email']; ?></a>        
<?php } ?>

</p>
<?php if ($row_contact['contact_profile']) { ?>   
  <strong>Background</strong><br />
  <?php echo $row_contact['contact_profile']; ?>
<?php } ?>
<hr />
<?php } ?>

<?php
$query_history = "SELECT 
    contact_id, 
    contact_first, 
    contact_last, 
    contact_company, 
    contact_title 
  FROM history 
  LEFT JOIN contacts ON history_contact=contact_id 
  WHERE history_status = 1 
  ORDER BY history_date DESC 
  LIMIT 0, 6";
$history = mysql_query($query_history) or die(mysql_error());
$row_history = mysql_fetch_assoc($history);
$totalRows_history = mysql_num_rows($history);

if ($totalRows_history > 0) { ?>
<h3>Recent Contacts</h3>
<ul class="blocklist">
<?php do { 
?>
  <li>
    <a href="contact-details.php?id=<?php echo $row_history['contact_id']; ?>">
      <?php echo display_name($row_history); ?>
    </a>
  </li>
<?php } while ($row_history = mysql_fetch_assoc($history)); ?>
</ul>
<?php } ?>

  </div>
