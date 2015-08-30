<?php
include_once '../ServiceController.php';
include_once '../dbManager.php';

PubSub::subscribe('Feedback', function(){
    $moduleName = 'Feedback';
    $params = func_get_args();
    $callBackMethod = $params[0];
    array_shift($params);
    
    echo $callBackMethod;
    call_user_func_array(array(__NAMESPACE__ .'\Feedback',$callBackMethod),$params);
});

class Feedback
{
   public static $FEEDBACK_MESSAGE = "Your feedback is really important to us. Please enter your feedback to help us improve our services:";


    public static function initializeService($requester)
    {
        
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
