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
$pagetitle = "Contact";

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
  if(trim($_GET['targeted'] == "Any")) {
    $where_clauses[] = "(SELECT COUNT(*) FROM targets WHERE targets.contact_id=contacts.contact_id) > 0";
  } else if(is_numeric(trim($_GET['targeted']))) {
    $where_clauses[] = "(SELECT COUNT(*) FROM targets WHERE targets.contact_id=contacts.contact_id && targets.campaign_id=" . $_GET['targeted'] . ") > 0";
  }
  if(trim($_GET['donated'] == "Any")) {
    $where_clauses[] = "campaign_id IS NOT NULL";
  } else if(is_numeric(trim($_GET['donated']))) {
    $where_clauses[] = "campaign_id = " . $_GET['donated'] . "";
  }
  if(in_array(trim($_GET['donation_status']), array("Expected", "Pledged", "Received"))) {
    $where_clauses[] = "donation_status = '" . strtolower(trim($_GET['donation_status'])) . "'";
  }
}

$where = (count($where_clauses) > 0 ? "WHERE " . implode(" AND ", $where_clauses) : "");

mysql_select_db($database_contacts, $contacts);
$query_contacts = "SELECT 
    contacts.*,
    contact_company IS NULL AS isnull,
    GROUP_CONCAT(donation_description) as donation_descriptions,
    (SELECT campaign_name
     FROM targets 
     LEFT JOIN campaigns USING (campaign_id)
     WHERE targets.contact_id = contacts.contact_id
     ORDER BY campaign_id DESC
     LIMIT 1) as recent_targets,
    (SELECT COUNT(*)
     FROM targets 
     WHERE targets.contact_id = contacts.contact_id) > 1 as targeted_multiple,
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
  LEFT JOIN donations USING (contact_id)
  $where
  GROUP BY contact_id
  ORDER BY isnull, contact_company, contact_first, contact_last";
$contacts = mysql_query($query_contacts, $contacts) or die(mysql_error());
$totalRows_contacts = mysql_num_rows($contacts);

if (!$where && $totalRows_contacts < 1) { 
header('Location: contact.php');
}

//act on multiple contacts
if (isset($_POST['d'])) {
  foreach($_POST['d'] as $key => $value) {
    if (is_numeric($value)) {
      if($_POST['action'] == "Delete") {
        mysql_query("DELETE FROM contacts WHERE contact_id = ".$value."");
        mysql_query("DELETE FROM donations WHERE contact_id = ".$value."");
        mysql_query("DELETE FROM targets WHERE contact_id = ".$value."");
      }
      if($_POST['action'] == "Target" && is_numeric($_POST['campaign'])) {
        mysql_query("INSERT INTO targets (`contact_id`, `campaign_id`) VALUES ($value, " . $_POST['campaign'] . ")");
      }
      if($_POST['action'] == "Untarget" && is_numeric($_POST['campaign'])) {
        mysql_query("DELETE FROM targets WHERE contact_id = $value AND campaign_id = " . $_POST['campaign']);
      } else {
        set_msg('You must select an action');
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

$display = isset($_GET['display']) ? $_GET['display'] : "html";
if (!in_array($display, array("csv", "html"))) {
  $display = "html";
}

switch ($display) {
  case "csv":
    $out = fopen('php://output', 'w');

    header('Content-type: text/csv');
    header('Content-Disposition: attachment; filename="contacts.csv"');
    header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
    $fields = array("first name", "last name", "title", "company", 
                    "street", "city", "state/province", "postal code", 
                    "phone", "cell", "fax", "email", "website", 
                    "is individual", "last donation", "last campaign");
    fputcsv($out, $fields);
    while ($row_contacts = mysql_fetch_assoc($contacts)) {
      fputcsv($out, array($row_contacts["contact_first"], $row_contacts["contact_last"],
                          $row_contacts["contact_title"], $row_contacts["contact_company"],
                          $row_contacts["contact_street"], $row_contacts["contact_city"],
                          $row_contacts["contact_state"], $row_contacts["contact_zip"],
                          $row_contacts["contact_phone"], $row_contacts["contact_cell"],
                          $row_contacts["contact_fax"], $row_contacts["contact_email"],
                          $row_contacts["contact_web"], $row_contacts["isnull"],
                          $row_contacts["last_donation"], $row_contacts["last_donation_campaign"]));
    }
    break;
  case "html":
?>
<?php include('includes/header.php'); ?>
  <div class="container">
  <div class="leftcolumn">
    <h2>Contacts</h2>
    <span class="notices" id="notice" style="display:<?php echo $dis; ?>">
      <?php display_msg(); ?>
    </span>
    <a href="<?php echo $_SERVER['REQUEST_URI'] . (isset($_GET['search']) ? "&" : "?") . "display=csv"; ?>"><strong>Export</strong></a><strong> | </strong>
    <a href="batch.php"><strong>Import</strong></a><strong> | </strong>
    <a href="contact.php"><strong>Create</strong></a>
    <br class="first"/><br /><a href="#" onclick="new Effect.toggle('search_pane', 'slide', { afterFinish: function() { $('name').focus(); }}); return false;">+Search</a>
    
    <div id="search_pane" style="display:<?php echo $where ? "block" : "none"; ?>">
      <form id="search_form" name="search_form" method="get" action="">
      <fieldset class="width3">
        <input type="hidden" name="search" id="search" value="search" />
        <label class="first column width2">
          Name/Organization
          <input type="text" id="name" name="name" value="<?php echo $_GET['name']; ?>"/>
        </label>
        <label class="column unitx2">
          Targeted In 
          <select id="targeted" name="targeted">
            <option <?php if ($_GET['targeted'] == "Ignore") { echo "selected='selected'"; } ?>>Ignore</option>
            <option <?php if ($_GET['targeted'] == "Any") { echo "selected='selected'"; } ?>>Any</option>
            <?php echo_campaign_options($_GET['targeted']); ?>
          </select>
        </label>
        <label class="first column unitx4">
          Donation Descriptions
          <input type="text" id="donation_desc" name="donation_desc" value="<?php echo $_GET['donation_desc']; ?>"/>
        </label>
        <label class="column unitx1">
          Donations In
          <select id="donated" name="donated">
            <option <?php if ($_GET['donated'] == "Ignore") { echo "selected='selected'"; } ?>>Ignore</option>
            <option <?php if ($_GET['donated'] == "Any") { echo "selected='selected'"; } ?>>Any</option>
            <?php echo_campaign_options($_GET['donated']); ?>
          </select>
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
    <hr />
    <form id="form1" name="form1" method="post" action="">
      <fieldset>
      <label style="margin-bottom: 0px" class="first column unitx1">
        Action
        <select id="action" name="action">
          <option>Select action</option>
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
            if($("action").getValue() == "Target" || $("action").getValue() == "Untarget") {
              $("target_value_label").show();
            } else {
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
          <th>Organization</th>
          <th>Name</th>
          <th>Targeted</th>
          <th class="date one-line">Last Gave</th>
          <th class="nosort" width="7%" style="text-align: center">Select</th>
        </tr>
        </thead>
        <tbody>
<?php if ($totalRows_contacts > 0) { ?>
        <?php while ($row_contacts = mysql_fetch_assoc($contacts)) { ?>
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
          <td class="one-line"><?php echo $row_contacts['recent_targets'] ? $row_contacts['recent_targets'] . 
                                     ($row_contacts['targeted_multiple'] ? ", ..." : "") : $na; ?></td>
          <td class="one-line"><?php if($row_contacts['last_donation']) {
            echo date("M j, Y", strtotime($row_contacts['last_donation'])) ?>
          <?php } else { echo $na; } ?>
          <td>
            <input name="d[<?php echo $row_contacts['contact_id']; ?>]" type="checkbox" class="action_check" id="d[<?php echo $row_contacts['contact_id']; ?>]" value="<?php echo $row_contacts['contact_id']; ?>" />
          </td>
        </tr>
        <?php if($show_donations) { ?>
          <tr><td colspan="7">
          <?php
            $matching = array();
            foreach(explode(",", $_GET['donation_desc']) as $term) {
              if(trim($term) && !(strpos($row_contacts['donation_descriptions'], trim($term)) === false)) {
                $matching[] = trim($term);
              }
            }
            echo "Donation Matches: " . implode(", ", $matching);
          ?>
          </td></tr>
        <?php }
              } ?>
    <?php } else { ?>
        <tr><td style="text-align: center;" colspan="7">No Results</td></tr>
    <?php } ?>
        </tbody>
      </table>
    </form>



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
