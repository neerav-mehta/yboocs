<?php
include_once '../ServiceController.php';
include_once '../dbManager.php';

PubSub::subscribe('ServiceAgent', function(){
    $moduleName = 'ServiceAgent';
    $params = func_get_args();
    $callBackMethod = $params[0];
    array_shift($params);
    
    echo $callBackMethod;
    call_user_func_array(array(__NAMESPACE__ .'\ServiceAgent',$callBackMethod),$params);
});

class ServiceAgent
{
   public static $FEEDBACK_MESSAGE = "Hi, This is ";

   
   public static function getServiceAgent()
   {
       $serviceAgentLoad = [];
       foreach (GenieConstants::$serviceAgentList as $value)
       {
           $agentLoad['agentId'] = $value->agentNumber;
           $agentLoad['load'] = count($value->agentServiceRequests);
           array_push(serviceAgentLoad,$agentLoad);
            sort($agentLoad);
            
       }
   }

    public static function initializeService($requester)
    {
        self::getServiceAgent(Genie);
        MessaggingController::sendMessage($requester, self::$FEEDBACK_MESSAGE);
        updateMessageContext(GenieConstants::searchElement(GenieConstants::$MAIN_MENU_CONTEXT, GenieConstants::$FEEDBACK_MENU_CONTEXT,'subMenu'), '1', $requester['phone']);
    }
    
    public static function submitFeedback($requester,$body)
    {
        addFeedBack($requester['phone'], $body);
        PubSub::publish(GenieConstants::$SERVICE_REQUEST_COMPLETE,$requester); 
    }
    
    public static function getWeatherInformation($requester,$body)
    {
        PubSub::publish(GenieConstants::$SERVICE_REQUEST_COMPLETE,$requester); 
    }

    
}
?>
