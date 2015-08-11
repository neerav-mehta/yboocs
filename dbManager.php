<?php

$userFields = array('gender','age','location','email');
$genderQuestion = "Alright, May I know your gender please?\n1 for Male\n2 for Female";
$ageQuestion = 'Tell me which age group do you belong to:
    1 for <18 yrs
    2 for 18-25 yrs
    3 for 26-39 yrs
    4 for 40-60 yrs
    5 for >60 yrs';
$cityQuestion = 'May I know which city you belong to?';
$emailQuestion = 'One last question! Tell me your email address.';
$fieldQuestions = array($genderQuestion,$ageQuestion,$cityQuestion,$emailQuestion);




$genderRegex = '/[1-2]/';
$ageRegex = '/[1-5]/';
$locationRegex = '/^[a-zA-Z ]*$/';
$emailRegex = "/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/";
$regex = array($genderRegex,$ageRegex,$locationRegex,$emailRegex);


function addContact($number, $name)
{
  $contactsDB = __DIR__ . DIRECTORY_SEPARATOR . 'contacts.db';
  if (!file_exists($contactsDB))
  {
    $db = new \PDO("sqlite:" . $contactsDB, null, null, array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    $db->exec('CREATE TABLE contacts (`phone` TEXT, `nickname` TEXT, `gender` TEXT, `age` TEXT, `location` TEXT, `email` TEXT , `registered` TEXT)');
  }
  else {
    $db = new \PDO("sqlite:" . $contactsDB, null, null, array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
  }
  $sql = 'INSERT INTO contacts (`phone`, `nickname`,`registered`) VALUES (:phone, :nickname, :registered)';
  $query = $db->prepare($sql);

  $query->execute(
      array(
          ':phone' => $number,
          ':nickname' => $name,
          ':registered' => 'false'
      )
  );

}

function updateContact($field,$value,$number)
{
  $contactsDB = __DIR__ . DIRECTORY_SEPARATOR . 'contacts.db';
  $sql = 'UPDATE contacts SET '.$field.' = :value WHERE phone = :number';
   $db = new \PDO("sqlite:" . $contactsDB, null, null, array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
  $query = $db->prepare($sql);
  echo $number;
  $query->execute(   array(
          ':number' => $number,
          ':value' => $value
      ));

}


function getUserInfo($contact)
{
  $contactsDB = __DIR__ . DIRECTORY_SEPARATOR . 'contacts.db';
  if (file_exists($contactsDB))
  {
      $cDB = new \PDO("sqlite:" . $contactsDB, null, null, array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
      $sql = 'SELECT * FROM contacts WHERE phone = :number';
      $query = $cDB->prepare($sql);
      $query->execute(array(':number' => $contact));
      $contact = $query->fetchAll();
      $contact = $contact[0];
      print_r($contact);
      return $contact;
  }
}

function findPhoneByNickname($contact)
{
  $contactsDB = __DIR__ . DIRECTORY_SEPARATOR . 'contacts.db';
  if (file_exists($contactsDB))
  {
      $cDB = new \PDO("sqlite:" . $contactsDB, null, null, array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
      $sql = 'SELECT phone FROM contacts WHERE nickname = :nickname';
      $query = $cDB->prepare($sql);
      $query->execute(array(':nickname' => $contact));
      $contact = $query->fetchAll();
      $contact = $contact[0]['phone'];

      return $contact;
  }
}

function findNicknameByPhone($phone)
{
  $contactsDB = __DIR__ . DIRECTORY_SEPARATOR . 'contacts.db';
  if (file_exists($contactsDB))
  {
      $cDB = new \PDO("sqlite:" . $contactsDB, null, null, array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
      $sql = 'SELECT nickname FROM contacts WHERE phone = :phone';
      $query = $cDB->prepare($sql);
      $query->execute(array(':phone' => $phone));
      $contact = $query->fetchAll();
      $contact = $contact[0]['nickname'];

      return $contact;
  }
}

function getLatestMessages($phone)
{
  $msgDB = $GLOBALS["msg_db"];
  if (file_exists($msgDB))
  {
      $cDB = new \PDO("sqlite:" . $msgDB, null, null, array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
      $sql = 'SELECT message, t FROM messages WHERE `from` = :phone LIMIT 20';
      $query = $cDB->prepare($sql);
      $query->execute(array(':phone' => $phone));
      $messages = $query->fetchAll();

      return $messages;
  }
}

?>
