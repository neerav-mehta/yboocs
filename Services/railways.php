<?php

$pnrURL = 'http://api.railwayapi.com/pnr_status/pnr/';
$liveStatusURL = 'http://api.railwayapi.com/live/train/';
$seatAvailabilityURL = 'http://api.railwayapi.com/check_seat/train/';
$railwayApikey = "cbsym1545";

function getPnrStatus($pnrNumber)
{
        $url = $GLOBALS['pnrURL'].$pnrNumber.'/apikey/'.$GLOBALS['railwayApikey'].'/';
        $pnrStatus = file_get_contents($url);
        if($pnrStatus)
        {
            $json = json_decode($pnrStatus, true);
            if($json['total_passengers'] == 0)
                return "PNR number expired or not yet generated";
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
                return $response;
            }
        }
        else
        {
            return "Service temporarily not available, please try after some time";
        }
}

function getLiveRunningStatus($trainNumber,$doj,$isDetailedStatusRequired=false)
{
        $url = $GLOBALS['liveStatusURL'].$trainNumber.'/doj/'.$doj.'/apikey/'.$GLOBALS['railwayApikey'].'/';
        $liveStatus = file_get_contents($url);
        $json = json_decode($liveStatus, true);
        if($json['total'] == 0)
        {
            echo $json;
            return  "Invalid train number or journey date";
        }
        if($liveStatus)
        {
            if(!$isDetailedStatusRequired)
            {
                $response = $json['position'];
                return $response;
            }
            else
            {
                $response = "Station: Act Dep   Sch Dep";
                for($count = 0;$count < $json['total'];$count ++)
                {
                    $response+= $json['route'][$count]['station'].":".$json['route'][$count]['actdep']." ".$json['route'][$count]['schdep'].'\n';         
                }
//                $response+=$json['position'];
                return $response;
            }
        }
        else
        {
            return "Service temporarily not available, please try after some time";
        }
}

function getSeatAvailability($trainNumber,$doj,$sourceStation,$destStation,$class)
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
?>
