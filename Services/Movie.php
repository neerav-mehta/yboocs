<?php
include_once '../ServiceController.php';

PubSub::subscribe('Movie', function(){
    $moduleName = 'Movie';
    $params = func_get_args();
    $callBackMethod = $params[0];
    array_shift($params);
    
    echo $callBackMethod;
    call_user_func_array(array(__NAMESPACE__ .'\Movie',$callBackMethod),$params);
});

class Movie
{
    public static $omdbURL = 'http://www.omdbapi.com/?t=';
    public static $askForMovieName = 'So which movie you want to get the information of ?';
    public static function initializeService($requester)
    { 
        MessaggingController::sendMessage ($requester, self::$askForMovieName);
        updateMessageContext(GenieConstants::searchElement(GenieConstants::$MAIN_MENU_CONTEXT, GenieConstants::$MOVIE_MENU_CONTEXT, 'subMenu'), '1', $requester['phone']);
    }
    

    
    public static function getMovieReview($requester,$movieTitle){
        
        echo "\n\n\n\n\n";
        var_dump($movieTitle);
        echo "\n\n\n\n\n";
        $movieTitle = str_replace(' ', '+', $movieTitle);
        $url = self::$omdbURL.$movieTitle.'&y=&plot=short&r=json';
        $movieReview = file_get_contents($url);
        if($movieReview)
            {
                $json = json_decode($movieReview, true);
             
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
