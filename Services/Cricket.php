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
    public static $CRICKET_OPTION_MESSAGE = "Do you want to get score of any other match
        1 for Yes
        2 for No";
    
    public static function initializeService($requester){
       
        $matchList = file_get_contents(self::$cricInfoURL);
        $scoreAvailable = false;
        if($matchList)
            {
                $json = json_decode($matchList, true);
                foreach ($json as $value) {
                    echo $value['t1'].'/n';
                    echo $value['t2'].'/n/n';

                    if(((stripos($value['t1'],self::$homeCountry) > -1) ||stripos($value['t2'],  self::$homeCountry) > -1))
                    {
                        $matchScoreURL = self::$cricInfoURL.'?id='.$value['id'];
                        $matchScore = file_get_contents($matchScoreURL);
                        $matchScore = json_decode($matchScore, true);
                        $score =  $matchScore['0']['de'];
                        MessaggingController::sendMessage($requester, $score);
                        $scoreAvailable = true;
                    }
                }
                if(!$scoreAvailable)
                {
                    $message = "No cricket match of ".self::$homeCountry." happening right now.\n";
                    MessaggingController::sendMessage($requester, $message);
                }
            }
            else
            {
                $message = GenieConstants::$SERVICE_UNAVAILABLE;
                MessaggingController::sendMessage($requester, $message);
            }
            MessaggingController::sendMessage($requester, self::$CRICKET_OPTION_MESSAGE);
    }

    public static function askForLiveScore($requester)
    {
        $message = self::$askForLiveScoreString;
        MessaggingController::sendMessage($requester,$message);      
    }
    
    
    public static function getLiveScore($requester,$request){
      $requestParams = explode(",", $request);
      $scoreAvailable = false;
        $team1 = $requestParams[0];
        $team2 = $requestParams[1];
        $matchList = file_get_contents(self::$cricInfoURL);
        $message =  "Sorry, this match information is not available.";
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
                        $scoreAvailable = true;
                        MessaggingController::sendMessage($requester, $score);
                    }
                }               
            }
            else
            {
                $message =  "Service temporarily not available, please try after some time";
            }
            if(!$scoreAvailable)
            MessaggingController::sendMessage($requester, $message);
            PubSub::publish(GenieConstants::$SERVICE_REQUEST_COMPLETE,$requester);

    }
    public static function exitMenu($requester)
    {
        PubSub::publish(GenieConstants::$SERVICE_REQUEST_COMPLETE,$requester);
    }
}

?>
