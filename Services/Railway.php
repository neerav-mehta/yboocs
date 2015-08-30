<?php
include_once '../ServiceController.php';

PubSub::subscribe('Railway', function(){
    $moduleName = 'Railway';
    $params = func_get_args();
    $callBackMethod = $params[0];
    array_shift($params);
    
    echo $callBackMethod;
    call_user_func_array(array(__NAMESPACE__ .'\Railway',$callBackMethod),$params);
});

class Railway
{
    public static $pnrURL = 'http://api.railwayapi.com/pnr_status/pnr/';
    public static $liveStatusURL = 'http://api.railwayapi.com/live/train/';
    public static $seatAvailabilityURL = 'http://api.railwayapi.com/check_seat/train/';
    public static $railwayApikey = "cbsym1545";
    
    public static $askForPnrStatusString = "May I have your Pnr number please?";
    
    public static $askForLiveStatusQuestion = "Please enter the train number and date for live running status in following format.\n<train-number>,<date(dd/mm/yyyy)>";
    public static $liveStatusFormat = 'For ex. if you want live status for train number 12201 on 2nd July 2015, you need to enter n "12201,02/07/2015"';
    public static $RAILWAY_WELCOME_MESSAGE = "Welcome to the Railway helpline.";
    public static $RAILWAY_OPTIONS_MENU = "Please choose the desired option:
        1 for PNR Status
        2 for Train Live Running Status";
    public static $mainMenuRedirectString = "Anytime you can type #mainmenu to go to the main menu";


    public static function initializeService($requester)
    {
        $welcomeMessage = self::$RAILWAY_WELCOME_MESSAGE;
        MessaggingController::sendMessage($requester, $welcomeMessage);

        $menu = self::$RAILWAY_OPTIONS_MENU;
        MessaggingController::sendMessage($requester, $menu);

        $mainMenuRedirectString = self::$mainMenuRedirectString;
        MessaggingController::sendMessage($requester, $mainMenuRedirectString); 
    }
    
    public static function askForPnrStatus($requester)
    {
        $welcomeMessage = self::$askForPnrStatusString;
        MessaggingController::sendMessage($requester, $welcomeMessage);
    }
    
    public static function getPnrStatus($requester, $pnrNumber)
    {
        $url = self::$pnrURL.$pnrNumber.'/apikey/'.self::$railwayApikey.'/';
        
        echo $url;
        $pnrStatus = file_get_contents($url);
        if($pnrStatus)
        {
            $json = json_decode($pnrStatus, true);
            if($json['total_passengers'] == 0)
            {
                $errorMessage =  "PNR number expired or not yet generated";
                MessaggingController::sendMessage($requester, $errorMessage);
            }
            else
            {
                $isChartPrepd = ($json['chart_prepared']=='N') ? 'Chart Not Prepared' : 'Chart Prepared';                
                $response = "PNR: ".$json['pnr']."\n" .
                            "Train: ".$json['train_num']. " ".$json['train_name'] ."\n" .
                            "From Station: ".$json['from_station']['name']."\n" .
                            "To Station: ".$json['to_station']['name']."\n" .
                            "DOJ: ".$json['doj']."\n" .
                            "Current Status: ".$json['passengers'][0]['current_status']."\n" .
                            "Booking Status: ".$json['passengers'][0]['booking_status']."\n" .
                             $isChartPrepd."\n";
                MessaggingController::sendMessage($requester, $response);
                
            }
        }
        else
        {
            MessaggingController::sendMessage($requester, GenieConstants::$SERVICE_UNAVAILABLE);
        }
        
        PubSub::publish(GenieConstants::$SERVICE_REQUEST_COMPLETE,$requester);  
    }

    public static function askForLiveRunningStatus($requester)
    {
        $welcomeMessage = self::$askForLiveStatusQuestion;
        MessaggingController::sendMessage($requester, $welcomeMessage);
        $welcomeMessage = self::$liveStatusFormat;
        MessaggingController::sendMessage($requester, $welcomeMessage);
    }
    
    public static function getLiveRunningStatus($requester,$request,$isDetailedStatusRequired=false)
    {
            $requestParams = explode(",", $request);
            $trainNumber = $requestParams[0];
            $doj = $requestParams[1];
            
            $date = DateTime::createFromFormat('d/m/Y', $doj);
            $doj = date_format($date, 'Ymd');
            $url = self::$liveStatusURL.$trainNumber.'/doj/'.$doj.'/apikey/'.self::$railwayApikey.'/';
            echo "************\n\n\n\n".$url;
            $liveStatus = file_get_contents($url);
            $json = json_decode($liveStatus, true);
            if($json['total'] == 0)
            {
                echo $json;
                $invalidDataMsg = "Invalid train number or journey date";
                MessaggingController::sendMessage($requester,$invalidDataMsg);
            }
            else if($liveStatus)
            {
//                if(!$isDetailedStatusRequired)
//                {
//                    $response = $json['position'];
//                    MessaggingController::sendMessage($requester, $response);
//                }
//                else
//                {
                    $response = "Current Status \n";
                    for($count = 0;$count < $json['total'];$count ++)
                    {
                        if($json['route'][$count+1]['status'] == '-')
                        {
                            
                            $response.= "Station: ".$json['route'][$count]['station']."\nScheduled Dep: ".$json['route'][$count]['schdep']."\nActual Dep: ".$json['route'][$count]['actdep']."\nStatus: ".$json['route'][$count]['status'];         
                        
                            break;
                        }
                    }
    //                $response+=$json['position'];
                    MessaggingController::sendMessage($requester, $response);
                //}
            }
            else
            {
                MessaggingController::sendMessage($requester, GenieConstants::$SERVICE_UNAVAILABLE);
            }
            PubSub::publish(GenieConstants::$SERVICE_REQUEST_COMPLETE,$requester); 
    }

    public static function getSeatAvailability($trainNumber,$doj,$sourceStation,$destStation,$class)
    {
            $url = $GLOBALS['seatAvailabilityURL'].$trainNumber.'/source/'.$sourceStation.'/dest/'.$destStation.'/date/'.$doj.'/class/'.$class.'/quota/GN/apikey/'.$GLOBALS['railwayApikey'].'/';
            $seatAvailability = file_get_contents($url);
            $json = json_decode($seatAvailability, true);
            if($json['error'])
            {
                echo $json;
                return  "Invalid information, please check again";
            }
            if($seatAvailability)
            {
                    $response = $json['availability'][0]['date'] . " " . $json['availability'][0]['status'];
                    echo $response;
                    return $response;
            }
            else
            {
                return "Service temporarily not available, please try after some time";
            }
    }


}
?>
