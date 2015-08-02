<?php
$omdbURL = 'http://www.omdbapi.com/?t=';
function getReview($movieTitle){
    $movieTitle = str_replace(' ', '+', $movieTitle);
    $url = $GLOBALS['omdbURL'].$movieTitle.'&y=&plot=short&r=json';
    $movieReview = file_get_contents($url);
    if($movieReview)
        {
            $json = json_decode($movieReview, true);
            echo $json;
            if($json['Response'] == 'false')
                return $json['Error'];
            else
            {
                            $response = "Tittle: ".$json['Title']."\n" .
                            "imdb Rating: ".$json['imdbRating']."\n" .
                            "Year: ".$json['Year']."\n" .
                            "Released: ".$json['Released']."\n" .
                            "Actors: ".$json['Actors']."\n" .
                            "Plot: ".$json['Plot']."\n";
                return $response;
            }
        }
        else
        {
            return "Service temporarily not available, please try after some time";
        }
}

?>
