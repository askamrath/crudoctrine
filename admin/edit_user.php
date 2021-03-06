<?php
/*
 * Cru Doctrine
 * Admin - Edit User
 * Campus Crusade for Christ
 */

try {
  //get values
  $id         = isset($_POST['id'])            ? $_POST['id']          : '';
  $email      = isset($_POST['email'])         ? $_POST['email']       : '';
  $firstName  = isset($_POST['firstName'])     ? $_POST['firstName']   : '';
  $lastName   = isset($_POST['lastName'])      ? $_POST['lastName']    : '';
  $password   = isset($_POST['password'])      ? $_POST['password']    : '';
  $type       = isset($_POST['type'])          ? $_POST['type']        : '';
  $region     = isset($_POST['region'])        ? $_POST['region']      : '';
  //$location   = isset($_POST['location'])      ? $_POST['location']    : '';
  $regDate    = isset($_POST['regDate'])       ? $_POST['regDate']     : '';
  $status     = isset($_POST['status'])        ? $_POST['status']      : '';
  //$progress   = isset($_POST['progress'])      ? $_POST['progress']    : '';
  $coach      = isset($_POST['coach'])         ? $_POST['coach']       : '';

  require_once("../config.inc.php"); 
  require_once("../Database.singleton.php");

  $password = stripslashes($password);
  $firstName = stripslashes($firstName);
  $lastName = stripslashes($lastName);
  //trim whitespaces from the email
  $id = trim($id);

  //initialize the database object
  $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE); 
  $db->connect();

  //update user
  //prepare query
  //$data['Email']      = $email;
  $data['FName']      = $firstName;
  $data['LName']      = $lastName;
  //hash the supplied password with some salt
  $passwordHash = null;
  $passwordHash = hash("sha512", $password.$id);
  $data['Password']   = $passwordHash;
  $data['Type']       = $type;
  $data['Region']     = $region;
  //$data['Loc']        = $location;
  $data['Reg_Date']   = $regDate;
  $data['Status']     = $status;

  //execute query
  $db->update("user", $data, "Email = '".$db->escape($id)."'");

  //update progress
  //prepare query
  //execute query

  //determine whether this student already has a coach
  $sql     = "SELECT COUNT(*) from coach where Student = '".$db->escape($email)."'";
  $result  = $db->query_first($sql);
  if ($result['COUNT(*)'] > 0) {
    if ($coach != '') {
      //update coach
      //prepare query
      $data = array();
      $data['Coach'] = $coach;

      //execute query
      $db->update("coach", $data, "Student = '".$db->escape($email)."'");
    }
    else {
      //delete coach record(s)
      $sql = "DELETE FROM coach WHERE Student = '".$db->escape($email)."'";
      $db->query($sql);
    }
  }
  else {
    if ($coach != '') {
      //create coach
      //prepare query
      $data = array();
      $data['Coach'] = $coach;
      $data['Student'] = $email;
      $data['Year'] = date('Y');
      $data['Type'] = COACH;

      //execute query
      $db->insert("coach", $data);
    }
  }

  $db->close();
} 
catch (PDOException $e) {
  echo $e->getMessage();
  exit();
}
?>