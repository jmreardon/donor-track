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

<script type="text/JavaScript">
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
<?php if ($contactcount > 0) { ?><br />

<?php if ($totalRows_tags) { ?>
Tags:
<?php 
$i = 1;
$comma = ",";
do { 
if ($i==$totalRows_tags) {
$comma = "";
}
?>
<a href="index.php?s=<?php echo $row_tags['tag_description']; ?>"><?php echo $row_tags['tag_description'].$comma; ?></a>
<?php 
$i++;
} while ($row_tags = mysql_fetch_assoc($tags)); ?>
<br />
<br />
<?php } ?>

<form id="form3" name="form3" method="GET" action="index.php">
      <input name="s" type="text" id="s" onfocus="MM_setTextOfTextfield('s','','')" value="Search" size="15" />
        <input type="submit" name="Submitf" value="Go" />
  </form>
<?php } ?>
    <p>&nbsp;</p>
    <p><a class="addcontact" href="contact.php">+ Add Contact</a></p>
    <?php if ($pagetitle==ContactDetails) { ?>
    <hr />
    <p><strong>Contact Information</strong><br />
      <?php if ($row_contact['contact_company']) { echo $row_contact['contact_company'] ."<br>"; } ?>
      <?php if ($row_contact['contact_street']) { echo $row_contact['contact_street']  ."<br>"; } ?>
    <?php if ($row_contact['contact_city']) { echo $row_contact['contact_city'] .","; } ?> <?php if ($row_contact['contact_state']) { echo $row_contact['contact_state']; } ?> <?php if ($row_contact['contact_zip']) { echo $row_contact['contact_zip']; } ?></p>
    <?php if ($row_contact['contact_street'] && $row_contact['contact_city'] && $row_contact['contact_state']) { ?><p><a href="http://maps.google.com/maps?f=q&amp;hl=en&amp;q=<?php echo $row_contact['contact_street']; ?>,+<?php echo $row_contact['contact_city']; ?>,+<?php echo $row_contact['contact_state']; ?>+<?php echo $row_contact['contact_zip']; ?>&gt;" target="_blank">+ View Map </a></p>
    <?php } ?>
    <hr />
    <p>      <?php if ($row_contact['contact_phone']) { ?>Phone: <?php echo $row_contact['contact_phone']; ?><br /><?php } ?>

<?php if ($row_contact['contact_web']) { ?>
      <a href="<?php echo $row_contact['contact_web']; ?>" target="_blank"><?php echo $row_contact['contact_web']; ?></a>        
<?php } ?>

<?php if ($row_contact['contact_email']) { ?>
      <a href="mailto:<?php echo $row_contact['contact_email']; ?>"><?php echo $row_contact['contact_email']; ?></a>        
<?php } ?>

</p>
<?php if ($row_contact['contact_profile']) { ?>   
 <hr />
  <strong>Background</strong><br />
  <?php echo $row_contact['contact_profile']; ?>
<?php } ?>
<?php } ?>  </div>
