<?php

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

function donation_stats($campaign_id, $extra = "") {
  $stat_results = mysql_query("SELECT 
    ifnull((SELECT sum(donation_value) 
         FROM donations LEFT JOIN contacts using (contact_id)
         WHERE campaign_id = $campaign_id $extra 
         GROUP BY campaign_id), 0)
      AS total,
    ifnull((SELECT sum(donation_value) 
         FROM donations LEFT JOIN contacts using (contact_id)
         WHERE campaign_id = $campaign_id AND donation_status = 'expected' $extra 
         GROUP BY campaign_id), 0)
     AS expected,
    ifnull((SELECT sum(donation_value) 
         FROM donations LEFT JOIN contacts using (contact_id)
         WHERE campaign_id = $campaign_id AND donation_status = 'pledged' $extra 
         GROUP BY campaign_id), 0)
     AS pledged,
    ifnull((SELECT sum(donation_value) 
         FROM donations LEFT JOIN contacts using (contact_id)
         WHERE campaign_id = $campaign_id AND donation_status = 'received' $extra 
         GROUP BY campaign_id), 0) 
     AS received"
  ) or die(mysql_error());
  return mysql_fetch_object($stat_results);
}

function donation_kind_text($is_cash) {
  if($is_cash) {
    return "Cash";
  } else {
    return "In Kind";
  }
}
?>
