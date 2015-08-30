<?php ;

include 'Services/UserManagement.php';      
include 'Services/Railway.php'; 
include 'Services/Cricket.php'; 
include 'Services/Shopping.php'; 
include 'Services/VAS.php'; 
include 'Services/Weather.php';
include 'Services/Feedback.php';
include 'Services/Movie.php';
function searchElement($from, $searchElement,$property)
   {
      foreach ($from as $key=>$value)
     {
         if($value->$property == $searchElement)
         {
             return $key;
         }
     }
     return -1;
  }

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
    
    
// Basic usage
PubSub::subscribe('message_received', function(){

    $params = func_get_args();
    call_user_func_array(__NAMESPACE__ .'\ServiceController::handleMessageReceived', $params);
});


PubSub::subscribe('SERVICE_REQUEST_COMPLETE', function(){
    $params = func_get_args();    
    call_user_func_array(array(__NAMESPACE__ .'\ServiceController','handleServiceRequestComplete'),$params);
});
class ServiceController{

    static function handleServiceRequestComplete($requester)
    {
        $context = getMessageContext($requester['phone']); 
        if($context['main_menu'] !=0 && $context['sub_menu'] !=NULL )
        {
            $SubMenuDict = GenieConstants::$MAIN_MENU_CONTEXT[$context['main_menu']]->subMenu;                    
            $postServiceMessage = $SubMenuDict[$context['sub_menu']]->postServiceMessage;
            echo "\n\n\n";
            var_dump($SubMenuDict);
            var_dump($context);
            MessaggingController::sendMessage($requester, $postServiceMessage);
            $anythingElse = GenieConstants::$anyThingElse;
            MessaggingController::sendMessage($requester, $anythingElse);  
            updateMessageContext('-1', NULL, $requester['phone'],$anythingElse);
        }
    }
    
    static function handleGeneralConversation($body,$context,$requester)
    {
           echo "****************".$context['last_message'];
          if($context['last_message'] == GenieConstants::$anyThingElse)
          {
              switch ($body) {
                  case 1:
                      updateMessageContext('0', NULL, $requester['phone']);
                      MessaggingController::sendMessage($requester, GenieConstants::$MAIN_MENU_STRING); 
                      break;
                  default:
                      updateMessageContext('-1', NULL, $requester['phone'],GenieConstants::$THANKYOU_SERVICE_MESSAGE);
                      MessaggingController::sendMessage($requester, GenieConstants::$THANKYOU_SERVICE_MESSAGE); 
                      break;
              }
          }
          if($context['last_message'] == GenieConstants::$THANKYOU_SERVICE_MESSAGE)
          {
                      updateMessageContext('0', NULL, $requester['phone']);
                      MessaggingController::sendMessage($requester, GenieConstants::$MAIN_MENU_STRING); 
          }
    }
    
    static function handleShortCuts($requester,$requestArray)
    {
        updateMessageContext('0', NULL, $requester['phone']);
        $isServiceFound = false;
        if($requestArray[0] == "#mainmenu")
        {
            MessaggingController::sendMessage($requester, GenieConstants::$MAIN_MENU_STRING);  
            return;
        }
        foreach (GenieConstants::$MAIN_MENU_CONTEXT as $key=>$value)
        {
            if($value->subMenu)
            {
                $subMenu = $value->subMenu;
                foreach ($subMenu as $subKey=>$subValue)
                {
               
    
                    
                    if($subValue->shortCutToken && $subValue->shortCutToken == $requestArray[0])
                    {
                        $callback = $subValue->requestServer;
                        array_shift($requestArray);
                                        echo "\n\n\n\n";
                    var_dump($requestArray);
                     echo "\n\n\n\n";
                        $requestString  = implode(" ",$requestArray);
                        PubSub::publish($value->menuItem,$callback,$requester,$requestString); 
                        $isServiceFound = true;
                        return;
                    }
                }
            }
        }
        if (!$isServiceFound) {
            MessaggingController::sendMessage($contact, GenieConstants::$INVALID_SERVICE);
        }
        

    }
    static function handleMessageReceived($mynumber, $from, $id, $type, $time, $name, $body)
    {
                $number = ExtractNumber($from);
                $contact = getUserInfo($number);
                if(!$contact)
                {
                    addContact ($number, $name);
                    $contact = getUserInfo($number);
                    $main_menu = GenieConstants::searchElement(GenieConstants::$MAIN_MENU_CONTEXT, GenieConstants::$REGISTRATION_MENU_CONTEXT,'subMenu');
                    
                    setMessageContext($main_menu, NULL, $contact['phone']);
                }

                
                
                //Handle ShortCut in request 
                $requestArray = $data   = preg_split('/\s+/', $body);
                if($requestArray[0][0] == '#')
                {
                    self::handleShortCuts ($contact, $requestArray);
                    return;
                }

                $context = getMessageContext($number); 
                $callBackFunction = 'initializeService';
                
                if($context['main_menu'] == -1)
                {
                    self::handleGeneralConversation($body,$context,$contact);
                    return;
                }
                if($context['main_menu'] == 0)
                {
                    if(GenieConstants::searchElement(GenieConstants::$MAIN_MENU_CONTEXT,$body,'id') != NULL)
                    {     
                        updateMessageContext($body, NULL, $contact['phone']);
                        PubSub::publish(GenieConstants::$MAIN_MENU_CONTEXT[$body]->menuItem,$callBackFunction,$contact); 
                   
                    }
                    else {
                        MessaggingController::sendMessage($contact, GenieConstants::$INVALID_SERVICE);
                        MessaggingController::sendMessage($contact, GenieConstants::$MAIN_MENU_STRING);
                    }
                    return;
                }
                
                if($context['main_menu'] != 0 && $context['main_menu'] != 8 && $context['sub_menu'] == NULL)
                {
                    $subMenuKey = GenieConstants::searchElement(GenieConstants::$MAIN_MENU_CONTEXT[$context['main_menu']]->subMenu,$body,'id');
                    $SubMenuDict = GenieConstants::$MAIN_MENU_CONTEXT[$context['main_menu']]->subMenu;
                    if($subMenuKey != NULL)
                    {     
                        updateMessageContext($context['main_menu'], $body, $contact['phone']);
                        $callBackFunction = $SubMenuDict[$subMenuKey]->callBackMethod;
                        PubSub::publish(GenieConstants::$MAIN_MENU_CONTEXT[$context['main_menu']]->menuItem,$callBackFunction,$contact); 
                    }
                    else {
                        MessaggingController::sendMessage($contact, GenieConstants::$INVALID_SERVICE);
                        PubSub::publish(GenieConstants::$MAIN_MENU_CONTEXT[$context['main_menu']]->menuItem,$callBackFunction,$contact); 
                    }
                    return;
                }
                
                else if($context['main_menu'] != 0 && $context['main_menu'] != 8 && $context['sub_menu'] != NULL)
                {
                        $SubMenuDict = GenieConstants::$MAIN_MENU_CONTEXT[$context['main_menu']]->subMenu;                    
                        $callBackFunction = $SubMenuDict[$context['sub_menu']]->requestServer;
                        PubSub::publish(GenieConstants::$MAIN_MENU_CONTEXT[$context['main_menu']]->menuItem,$callBackFunction,$contact,$body); 
                    return;
                }
                
                else if($context['sub_menu'] != NULL)
                {
                      $callBackFunction = GenieConstants::$MAIN_MENU_CONTEXT[$context['main_menu']]->subMenu[$context['sub_menu']]->callBackMethod;
                }
                else if(GenieConstants::$MAIN_MENU_CONTEXT[$context['main_menu']]->callBackMethod != NULL)
                {
                    $callBackFunction = GenieConstants::$MAIN_MENU_CONTEXT[$context['main_menu']]->callBackMethod;
                }

                PubSub::publish(GenieConstants::$MAIN_MENU_CONTEXT[$context['main_menu']]->menuItem,$callBackFunction,$contact,$body);  
    }
}
?>
