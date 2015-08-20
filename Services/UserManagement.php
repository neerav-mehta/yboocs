<?php
include_once '../dbManager.php';
include_once '../ServiceController.php';

PubSub::subscribe('UserManagement', function(){
    $moduleName = 'UserManagement';
    $params = func_get_args();
    $callBackMethod = $params[0];
    array_shift($params);
    call_user_func_array(array(__NAMESPACE__ .'\UserManagement',$callBackMethod),$params);
});

class UserManagement{
    
    
   public static function initializeService($receiver,$response)
   {
        $replyMsg = "Hi ".$receiver['nickname']. GenieConstants::$WELCOME_MESSAGE;
        MessaggingController::sendMessage($receiver, $replyMsg);
        self::askForGenderInformation($receiver, $response);
        $menu_Context = GenieConstants::searchElement(GenieConstants::$MAIN_MENU_CONTEXT, GenieConstants::$REGISTRATION_MENU_CONTEXT,'subMenu');
        $subMenu_Context = GenieConstants::searchElement(GenieConstants::$REGISTRATION_MENU_CONTEXT, GenieConstants::$GENDER_SUB_MENU_STRING,'menuItem');
        updateMessageContext($menu_Context,$subMenu_Context,$receiver['phone']);

   }
    
   public static function processGenderInformation($receiver,$response)
   {
     $regex = GenieConstants::$genderRegex;
     if(preg_match($regex, $response) == 0)
     {
          $errorMessage = GenieConstants::$ERROR_MESSAGE;
          MessaggingController::sendMessage($receiver, $errorMessage);
          self::askForGenderInformation($receiver, $response);
          return;
      }
     else 
     {
          updateContact(GenieConstants::$GENDER_SUB_MENU_STRING, $response, $receiver['phone']);
          self::askForAgeInformation($receiver,$response);
          $menu_Context = GenieConstants::searchElement(GenieConstants::$MAIN_MENU_CONTEXT, GenieConstants::$REGISTRATION_MENU_CONTEXT,'subMenu');
          $subMenu_Context = GenieConstants::searchElement(GenieConstants::$REGISTRATION_MENU_CONTEXT, GenieConstants::$AGE_SUB_MENU_STRING,'menuItem');
          updateMessageContext($menu_Context,$subMenu_Context, $receiver['phone']);
     }
   }
   
   public static function askForGenderInformation($receiver,$response)
   {
     $fieldQuestions =  GenieConstants::$genderQuestion;
     MessaggingController::sendMessage($receiver, $fieldQuestions);
   }
   
   public static function processAgeInformation($receiver,$response)
   {
     $regex = GenieConstants::$ageRegex;
     if(preg_match($regex, $response) == 0)
     {
          $errorMessage = GenieConstants::$ERROR_MESSAGE;
          MessaggingController::sendMessage($receiver, $errorMessage);
          $this->askForAgeInformation($receiver, $response);
          return;
      }
     else 
     {
          updateContact(GenieConstants::$AGE_SUB_MENU_STRING, $response, $receiver['phone']);
          self::askForCityInformation($receiver,$response);
           $menu_Context = GenieConstants::searchElement(GenieConstants::$MAIN_MENU_CONTEXT, GenieConstants::$REGISTRATION_MENU_CONTEXT,'subMenu');
           $subMenu_Context = GenieConstants::searchElement(GenieConstants::$REGISTRATION_MENU_CONTEXT, GenieConstants::$CITY_SUB_MENU_STRING,'menuItem');
           updateMessageContext($menu_Context,$subMenu_Context, $receiver['phone']);
     }
   }
   
   public static function askForAgeInformation($receiver,$response)
   {
     $fieldQuestions =  GenieConstants::$ageQuestion;
     MessaggingController::sendMessage($receiver, $fieldQuestions);
   }
   
    public static function processCityInformation($receiver,$response)
   {
     $regex = GenieConstants::$cityRegex;
     if(preg_match($regex, $response) == 0)
     {
          $errorMessage = GenieConstants::$ERROR_MESSAGE;
          MessaggingController::sendMessage($receiver, $errorMessage);
          self::askForCityInformation($receiver, $response);
          return;
      }
     else 
     {
          updateContact(GenieConstants::$CITY_SUB_MENU_STRING, $response, $receiver['phone']);
          self::askForEmailInformation($receiver,$response);
             $menu_Context = GenieConstants::searchElement(GenieConstants::$MAIN_MENU_CONTEXT, GenieConstants::$REGISTRATION_MENU_CONTEXT,'subMenu');
           $subMenu_Context = GenieConstants::searchElement(GenieConstants::$REGISTRATION_MENU_CONTEXT, GenieConstants::$EMAIL_SUB_MENU_STRING,'menuItem');
          updateMessageContext($menu_Context,$subMenu_Context, $receiver['phone']);
     }
   }
   
   
   public static function askForCityInformation($receiver,$response)
   {
     $fieldQuestions =  GenieConstants::$cityQuestion;
     MessaggingController::sendMessage($receiver, $fieldQuestions);
   }
   
   
   public static  function processEmailInformation($receiver,$response)
   {
     $regex = GenieConstants::$emailRegex;
     if(preg_match($regex, $response) == 0)
     {
          $errorMessage = GenieConstants::$ERROR_MESSAGE;
          MessaggingController::sendMessage($receiver, $errorMessage);
          self::askForEmailInformation($receiver, $response);
          return;
      }
     else 
     {
          updateContact(GenieConstants::$EMAIL_SUB_MENU_STRING, $response, $receiver['phone']);
          $menu_Context = GenieConstants::searchElement(GenieConstants::$MAIN_MENU_CONTEXT, GenieConstants::$PARENT_MENU_CONTEXT,'subMenu');
          $subMenu_Context = NULL;
          updateMessageContext($menu_Context,$subMenu_Context, $receiver['phone']);
          MessaggingController::sendMessage($receiver, GenieConstants::$INTRODUCTION); 
   //       PubSub::publish(GenieConstants::$SERVICE_REQUEST_COMPLETE,$receiver);  
          
     }
   }
   

   public static function askForEmailInformation($receiver,$response)
   {
     $fieldQuestions =  GenieConstants::$emailQuestion;
     MessaggingController::sendMessage($receiver, $fieldQuestions);
   }
    
//   function completePendingRegistration($contact,$response)
//    {
//            $userFields = $GLOBALS['userFields'];
//            $fieldQuestions = $GLOBALS['fieldQuestions'];
//            $regex = $GLOBALS['regex'];
//            for($i=0;$i< sizeof($userFields);$i++)
//            {
//                $field = $userFields[$i];
//                if(!$contact[$field])
//                {
//                    echo $regex[$i];
//                    if(preg_match($regex[$i], $response) == 0)
//                    {
//                         $errorMessage = $GLOBALS['ERROR_MESSAGE'];
//                         $GLOBALS['w']->sendMessage($contact['phone'], $errorMessage);
//                         $questionMessage = "$fieldQuestions[$i]";
//                         $GLOBALS['w']->sendMessage($contact['phone'], $questionMessage);
//                         return;
//                    }
//                    else
//                    {
//
//                        updateContact($field, $response, $contact['phone']);
//                        if($i < sizeof($userFields) -1)
//                        {
//                            $nextQuestion = $fieldQuestions[$i+1];
//                            $GLOBALS['w']->sendMessage($contact['phone'], $nextQuestion);
//                            return;
//                        }
//                        else
//                        {
//                            updateContact('registered', 'true', $contact['phone']);
//                            $thanksMessage = $GLOBALS['THANKYOU_MESSAGE'];
//                            $GLOBALS['w']->sendMessage($contact['phone'], $thanksMessage);
//                            $introduction = $GLOBALS['INTRODUCTION'];
//                            $GLOBALS['w']->sendMessage($contact['phone'], $introduction);
//                            $help = $GLOBALS['HELP'];
//                            $GLOBALS['w']->sendMessage($contact['phone'], $help);
//
//                            return;
//                        }
//                    }
//                }            
//            }
//        }
 
}


?>
