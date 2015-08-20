<?php
PubSub::subscribe('Cricket', function(){
    $moduleName = 'Cricket';
    $params = func_get_args();
    $callBackMethod = $params[0];
    array_shift($params);
    
    echo $callBackMethod;
    call_user_func_array(array(__NAMESPACE__ .'\Cricket',$callBackMethod),$params);
});

class Cricket
{
    public static $cricInfoURL = 'http://cricscore-api.appspot.com/csa';
    
    public static $homeCountry = 'India';
    public static $askForLiveScoreString = 'Between which two team you want to get the live match score information?(team1,team2)';
    
    public static function initializeService($requester,$request){
        $matchList = file_get_contents(self::$cricInfoURL);
        if($matchList)
            {
                $json = json_decode($matchList, true);
                echo $json;
                foreach ($json as $value) {
                    echo $value['t1'].'/n';
                    echo $value['t2'].'/n/n';

                    if(((stripos($value['t1'],self::$homeCountry) > -1) ||stripos($value['t1'],  self::$homeCountry) > -1))
                    {
                        $matchScoreURL =  self::$homeCountry . '?id='.$value['id'];
                        $matchScore = file_get_contents($matchScoreURL);
                        $matchScore = json_decode($matchScore, true);
                        $score =  $matchScore['0']['de'];
                        MessaggingController::sendMessage($requester, $message);
                        $message = "Do you want to know the score of any other match?\n
                                    1 For Yes\n
                                    2 for No";
                        MessaggingController::sendMessage($requester, $message);
                        return;
                    }
                }
                $message = "No cricket match of ".self::$homeCountry." happening right now.\n
                            Do you want to know the score of any other match?\n
                            1 For Yes\n
                            2 for No";
               
            }
            else
            {
                $message =  "Service temporarily not available, please try after some time";
            }
            MessaggingController::sendMessage($requester, $message);
            PubSub::publish(GenieConstants::$SERVICE_REQUEST_COMPLETE,$requester);  
    }

    public static function askForLiveScore($requester)
    {
        $message = self::$askForLiveScoreString;
        MessaggingController::sendMessage($requester,$message);      
    }
    
    
    public static function getLiveScore($requester,$request){
        $requestParams = explode(",", $request);
        $team1 = $requestParams[0];
        $team2 = $requestParams[1];
        $matchList = file_get_contents(self::$cricInfoURL);
        $message =  "Service temporarily not available, please try after some time";
        if($matchList)
            {
                $json = json_decode($matchList, true);
                foreach ($json as $value) {
                    echo $value['t1'].'/n';
                    echo $value['t2'].'/n/n';

                    if(((stripos($value['t1'],$team1) > -1) ||stripos($value['t1'],$team2) > -1) &&
                           ((stripos($value['t2'],$team1) > -1) ||stripos($value['t2'],$team2) > -1))

                    {
                        $matchScoreURL = self::$cricInfoURL. '?id='.$value['id'];
                        $matchScore = file_get_contents($matchScoreURL);
                        $matchScore = json_decode($matchScore, true);
                        $score =  $matchScore['0']['de'];
                        MessaggingController::sendMessage($requester, $score);
                        $message = "Do you want to know the score of any other match?\n
                                    1 For Yes\n
                                    2 for No";
                        setMessageContext(GenieConstants::searchElement(GenieConstants::$MAIN_MENU_CONTEXT,GenieConstants::$CRICKET_MENU_CONTEXT,'subMenu'),NULL, $requester['phone']);
                        
                    }
                }          
               
            }
            else
            {
                $message =  "Service temporarily not available, please try after some time";
            }
            MessaggingController::sendMessage($requester, $message);
            $otherQuery = "Do you want to know the score of any other match?\n
                                    1 For Yes\n
                                    2 for No";
            MessaggingController::sendMessage($requester, $otherQuery);
            updateMessageContext(GenieConstants::searchElement(GenieConstants::$MAIN_MENU_CONTEXT,GenieConstants::$CRICKET_MENU_CONTEXT,'subMenu'),NULL, $requester['phone']);

    }
}

?>
