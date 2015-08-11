<?php

$cricInfoURL = 'http://cricscore-api.appspot.com/csa';
function getLiveScore($team1,$team2){
    $matchList = file_get_contents($GLOBALS['cricInfoURL']);
    if($matchList)
        {
            $json = json_decode($matchList, true);
            echo $json;
            foreach ($json as $value) {
                echo $value['t1'].'/n';
                echo $value['t2'].'/n/n';
                
                if(((stripos($value['t1'],$team1) > -1) ||stripos($value['t1'],$team2) > -1) &&
                       ((stripos($value['t2'],$team1) > -1) ||stripos($value['t2'],$team2) > -1))
            
                {
                    $matchScoreURL = $GLOBALS['cricInfoURL'] . '?id='.$value['id'];
                    $matchScore = file_get_contents($matchScoreURL);
                    $matchScore = json_decode($matchScore, true);
                    return $matchScore['0']['de'];
                }
            }          
            return "Sorry, this match information is not available.";
        }
        else
        {
            return "Service temporarily not available, please try after some time";
        }
}

?>
