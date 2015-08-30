<?php





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

function addMessage($sender, $receiver, $message)
{
  $contactsDB = __DIR__ . DIRECTORY_SEPARATOR . 'contacts.db';
  if (!file_exists($contactsDB))
  {
    $db = new \PDO("sqlite:" . $contactsDB, null, null, array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    $db->exec('CREATE TABLE conversation(time TEXT, sender TEXT, receiver TEXT,message TEXT, FOREIGN KEY(sender) REFERENCES contact(phone), FOREIGN KEY(receiver) REFERENCES contact(phone), message TEXT)');
  }
  else {
    $db = new \PDO("sqlite:" . $contactsDB, null, null, array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
  }
  $sql = 'INSERT INTO conversation (`time`, `sender`,`receiver`,`message`) VALUES (:time, :sender, :receiver, :message)';
  $query = $db->prepare($sql);

  $query->execute(
      array(
          ':time' => date("Y-m-d H:i:s"),
          ':sender' => $sender,
          ':receiver' => $receiver,
          ':message' => $message
      )
  );

}

function setMessageContext($main_menu, $sub_menu, $user)
{

  $contactsDB = __DIR__ . DIRECTORY_SEPARATOR . 'contacts.db';
  if (!file_exists($contactsDB))
  {
    $db = new \PDO("sqlite:" . $contactsDB, null, null, array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    $db->exec('CREATE TABLE context(time TEXT, main_menu TEXT, sub_menu TEXT, user TEXT, FOREIGN KEY(user) REFERENCES contact(phone))');
  }
  else {
    $db = new \PDO("sqlite:" . $contactsDB, null, null, array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
  }
  $sql = 'INSERT INTO context (`time`, `main_menu`,`sub_menu`,`user`) VALUES (:time, :main_menu, :sub_menu, :user)';
  $query = $db->prepare($sql);

  $query->execute(
      array(
          ':time' => date("Y-m-d H:i:s"),
          ':main_menu' => $main_menu,
          ':sub_menu' => $sub_menu,
          ':user' => $user
      )
  );

}

function updateMessageContext($main_menu, $sub_menu, $user, $lastMessage = NULL)
{
  if($lastMessage != NULL)
  {  
      $sql = "UPDATE context SET time= '".date("Y-m-d H:i:s")."', main_menu= '".$main_menu."', sub_menu= '".$sub_menu."',  last_message= '".$lastMessage."' WHERE user = ".$user;      
  }
  else
  {
        $sql = "UPDATE context SET time= '".date("Y-m-d H:i:s")."', main_menu= '".$main_menu."', sub_menu= '".$sub_menu."' WHERE user = ".$user;
  }
  $contactsDB = __DIR__ . DIRECTORY_SEPARATOR . 'contacts.db';
    $db = new \PDO("sqlite:" . $contactsDB, null, null, array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

  
  echo "/n/n/n/n***********".$sql;
  $query = $db->prepare($sql);

  $query->execute();

}

function getMessageContext($contact)
{
  $contactsDB = __DIR__ . DIRECTORY_SEPARATOR . 'contacts.db';
  if (file_exists($contactsDB))
  {
      $cDB = new \PDO("sqlite:" . $contactsDB, null, null, array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
      $sql = 'SELECT * FROM context WHERE user = :contact';
      $query = $cDB->prepare($sql);
      $query->execute(array(':contact' => $contact));
      $contact = $query->fetchAll();
      $contact = $contact[0];
      print_r($contact);
      return $contact;
  }
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

function addFeedBack($contact,$feedback)
{
    
  $contactsDB = __DIR__ . DIRECTORY_SEPARATOR . 'contacts.db';
  if (!file_exists($contactsDB))
  {
    $db = new \PDO("sqlite:" . $contactsDB, null, null, array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    $db->exec('CREATE TABLE feedback(time TEXT, user TEXT, feedback TEXT, FOREIGN KEY(user) REFERENCES contact(phone))');
  }
  else {
    $db = new \PDO("sqlite:" . $contactsDB, null, null, array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
  }
  $sql = 'INSERT INTO feedback (`time`, `user`,`feedback`) VALUES (:time, :user, :feedback)';
  $query = $db->prepare($sql);

  $query->execute(
      array(
          ':time' => date("Y-m-d H:i:s"),
          ':user' => $contact,
          ':feedback' => $feedback
      )
  );
    
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
