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

function generate_password($password, $salt) {
  return hash_hmac("sha256", $password, $salt);
}

function check_password($password, $hash, $salt) {
  return $hash == hash_hmac("sha256", $password, $salt);
}

function update_profile($email, $password = NULL, $home = NULL) {
  $query_profile = "SELECT * FROM users";
  $profile = mysql_query($query_profile) or die(mysql_error());
  $row_profile = mysql_fetch_assoc($profile);

  if ($home == NULL) {
    $home = $row_profile['user_home'];
  }

  if ($password) {
    $password_salt = time();
    $password = generate_password($password, $password_salt);
  } else {
    $password = $row_profile['user_password'];
    $password_salt = $row_profile['user_salt'];
  }

  mysql_query("UPDATE users SET 
    user_email = '".mysql_real_escape_string(trim($email))."', 
    user_password = '".mysql_real_escape_string(trim($password))."', 
    user_salt = '".mysql_real_escape_string(trim($password_salt))."', 
    user_home = '".mysql_real_escape_string(trim($home))."' 
    WHERE user_email = '" . mysql_real_escape_string(trim($email)) . "'");
  if(mysql_error()) {
    die(mysql_error());
  }
  return mysql_affected_rows();
}

function send_password($email) {
  $query_passwordcheck = "SELECT * FROM users WHERE user_email = '".$email."'";
  $passwordcheck = mysql_query($query_passwordcheck) or die(mysql_error());
  $row_passwordcheck = mysql_fetch_assoc($passwordcheck);
  $totalRows_passwordcheck = mysql_num_rows($passwordcheck);

  $new_password = gen_password(8);

  update_profile($email, $new_password);

  if ($totalRows_passwordcheck==1) { 
    error_log("Sending password email to: " . $email);
    //SEND EMAIL WITH PASSWORD
    $password = $row_passwordcheck['user_password'];
    $name = "Donor Track";
    $subject = "Your New Password";
    $message = "Your password is $new_password.";
    $emailto = $row_passwordcheck['user_email'];

    return mail($emailto, $subject, $message, 
      "From: $name <$email_from_address>\n" .
      "MIME-Version: 1.0\n" .
      "Content-type: text/html; charset=iso-8859-1");
    //END SEND EMAIL
  } else if ($totalRows_passwordcheck < 1) {
    return false;
  }
}

function gen_password($len = 6)
{
  $r = '';
  for($i=0; $i<$len; $i++) {
    $r .= chr(rand(0, 25) + ord('a'));
  }
  return $r;
}
?>
