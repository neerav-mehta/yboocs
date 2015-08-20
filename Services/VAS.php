<?php
include_once '../ServiceController.php';

PubSub::subscribe('VAS', function(){
    $moduleName = 'VAS';
    $params = func_get_args();
    $callBackMethod = $params[0];
    array_shift($params);
    
    echo $callBackMethod;
    call_user_func_array(array(__NAMESPACE__ .'\VAS',$callBackMethod),$params);
});

class VAS
{
    public static $omdbURL = 'http://www.omdbapi.com/?t=';
    public static $askForMovieName = 'So which movie you want to get the information of ?';
    public static $VAS_WELCOME_TEXT = 'Value Added Services Menu
                                       1. Movie Reviews
                                       2. Cricket Score';
    
    public static $cricInfoURL = 'http://cricscore-api.appspot.com/csa';
    
    public static $homeCountry = 'India';
    public static $askForLiveScoreString = 'Between which two team you want to get the live match score information?(team1,team2)';

    public static function initializeService($requester)
    {
        MessaggingController::sendMessage ($requester, self::$VAS_WELCOME_TEXT);
    }
    
    
    public static function askForMatchInformation($requester)
    {
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
                        MessaggingController::sendMessage($requester, $score);
                        $message =  self::$askForLiveScoreString;
                        MessaggingController::sendMessage($requester,$message);  
                        return;
                    }
                }
                $message = "No cricket match of ".self::$homeCountry." happening right now.\n";
                MessaggingController::sendMessage($requester, $message);
                $liveScoreAskMessage =  self::$askForLiveScoreString;
                MessaggingController::sendMessage($requester,$liveScoreAskMessage); 
            }
            else
            {
                $message = GenieConstants::$SERVICE_UNAVAILABLE;
                MessaggingController::sendMessage($requester, $message);
            }
    }
    
     public static function getLiveScore($requester,$request){
        $requestParams = explode(",", $request);
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
            
    }
    
    
    public static function askForMovieInformation($requester)
    {
        MessaggingController::sendMessage ($requester, self::$askForMovieName);
    }
    
    public static function getMovieReview($requester,$movieTitle){
        $movieTitle = str_replace(' ', '+', $movieTitle);
        $url = self::$omdbURL.$movieTitle.'&y=&plot=short&r=json';
        $movieReview = file_get_contents($url);
        if($movieReview)
            {
                $json = json_decode($movieReview, true);
                echo $json;
                if($json['Response'] == 'false')
                    MessaggingController::sendMessage ($requester, $json['Error']);
                else
                {
                                $response = "Tittle: ".$json['Title']."\n" .
                                "imdb Rating: ".$json['imdbRating']."\n" .
                                "Year: ".$json['Year']."\n" .
                                "Released: ".$json['Released']."\n" .
                                "Actors: ".$json['Actors']."\n" .
                                "Plot: ".$json['Plot']."\n";
                     MessaggingController::sendMessage ($requester, $response);
                }
            }
            else
            {
                 MessaggingController::sendMessage ($requester, GenieConstants::$SERVICE_UNAVAILABLE);
            }
            PubSub::publish(GenieConstants::$SERVICE_REQUEST_COMPLETE,$requester);        
    }
}

?>
