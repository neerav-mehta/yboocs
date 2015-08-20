<?php ;

include 'Services/UserManagement.php';      
include 'Services/Railway.php'; 
include 'Services/Cricket.php'; 
include 'Services/VAS.php'; 
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
    function performServiceRequest($request,$requester){
            switch (strtolower($request[0])) {
                case "#railwayhelp":
                    $railwayHelpString = "For pnr related information: \nSend: #pnr <your-pnr-number> \nex:#pnr 12231122211 \n\n".
                                         "For train running status: \nSend: #livestatus <TrainNumber> <date(yyyymmdd)> \n\n".
                                         "For seat availability: \nSend: #seat <train-number> <date> <source-stn-code> <dest-stn-code> <class>";
                    $replyMessage = $railwayHelpString;
                case "#help":
                    $helpString = "";
                    $replyMessage = $helpString;
                    break;
                case "#pnr":
                    $pnrNumber = str_replace(' ', '', $request[1]);
                    $pnrStatus = getPnrStatus($pnrNumber);
                    echo $pnrStatus . "\n";
                    $replyMessage = $pnrStatus;
                    break;
                case "#livestatus":
                    $trainNumber = str_replace(' ', '', $request[1]);
                    $journeyDate = str_replace(' ', '', $request[2]);
                    $runningStatus = getLiveRunningStatus($trainNumber,$journeyDate);
                    echo $runningStatus . "\n";
                    $replyMessage = $runningStatus;
                    break;
                case "#seat":
                    $trainNumber = str_replace(' ', '', $request[1]);
                    $journeyDate = str_replace(' ', '', $request[2]);
                    $sourceStation = str_replace(' ', '', $request[3]);
                    $destStation = str_replace(' ', '', $request[4]);
                    $class = str_replace(' ', '', $request[5]);
                    $seatAvailability = getSeatAvailability($trainNumber,$journeyDate,$sourceStation,$destStation,$class);
                    echo $seatAvailability . "\n";
                    $replyMessage = $seatAvailability;
                    break;
                 case "#movie":
                     unset($request[0]);
                    $movieTittle = join(" ", $request);
                    echo $movieTittle;
                    $review = getReview($movieTittle);           
                    echo $review . "\n";
                    $replyMessage = $review;
                    break;
                 case "#cricket":
                    $matchList = getMatchesList();           
                    echo $matchList . "\n";
                    $replyMessage = $matchList;
                    break;
                 case "#livescore":
                    $team1 = $request[1];
                    $team2 = $request[2];
                    $score = getliveScore($team1,$team2);
                    echo $score.'\n';
                    $replyMessage = $score;
                    break;
                default:
                    $defaultResponse = $GLOBALS['INVALID_SERVICE'];
                    echo $defaultResponse . "\n";
                    $replyMessage = $defaultResponse;
                    break;
            } 
            addMessage('Genie',$requester, $replyMessage);
            $GLOBALS['w']->sendMessage($requester, $replyMessage);
    }
    

    static function handleServiceRequestComplete($requester)
    {
        $context = getMessageContext($requester['phone']); 
        $SubMenuDict = GenieConstants::$MAIN_MENU_CONTEXT[$context['main_menu']]->subMenu;                    
        $postServiceMessage = $SubMenuDict[$context['sub_menu']]->postServiceMessage;
        MessaggingController::sendMessage($requester, $postServiceMessage);
        $anythingElse = GenieConstants::$anyThingElse;
        MessaggingController::sendMessage($requester, $anythingElse);  
        updateMessageContext('-1', NULL, $requester['phone'],$anythingElse);
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
                        PubSub::publish(GenieConstants::$MAIN_MENU_CONTEXT[$body]->menuItem,$callBackFunction,$contact); 
                        updateMessageContext($body, NULL, $contact['phone']);
                    }
                    else {
                        MessaggingController::sendMessage($contact, GenieConstants::$INVALID_SERVICE);
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