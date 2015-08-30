<?php
include_once '../ServiceController.php';

PubSub::subscribe('Weather', function(){
    $moduleName = 'Weather';
    $params = func_get_args();
    $callBackMethod = $params[0];
    array_shift($params);
    echo  "I AM HERE";
    
    echo $callBackMethod;
    call_user_func_array(array(__NAMESPACE__ .'\Weather',$callBackMethod),$params);
});

class Weather
{
    public static $url = 'http://apidev.accuweather.com/';
    public static $apiKey = '62de4c8da9934c4db7dd35d37dd0addd';
    public static $forecastURL = 'http://apidev.accuweather.com/forecasts/v1/daily/5day/188413?apikey=62de4c8da9934c4db7dd35d37dd0addd';
    public static $askForLocation = "May I know the city for which you want to get the weather information?";
    
    public static $WEATHER_OPTION_MESSAGE = "Do you want to know the weather conditions of any other place?\n
                         1 Yes \n
                         2 No \n";


    public static function initializeService($requester)
    {
        $locationURL = self::$url.'/locations/v1/In/search?q='.$requester['location'].'&apikey='.self::$apiKey;
        echo $locationURL;
        $location = file_get_contents($locationURL);
        $locationJson = json_decode($location, true);
        if($locationJson)
        {
            var_dump($locationJson);//$locationJson;
            $locationKey = $locationJson['0']['Key'];
            $forecastURL = self::$url.'/forecasts/v1/daily/5day/'.$locationKey.'?apikey='.self::$apiKey;
            $forecast = file_get_contents($forecastURL);
            $forecastJson = json_decode($forecast, true);
            $forecastMessage = "Next 5 days Weather Forcast for ".$requester['location'].' is 
'.$forecastJson['Headline']['Text'];
            MessaggingController::sendMessage($requester, $forecastMessage);
            $dailyForecast = "Daily Forecast
";           
            foreach ($forecastJson['DailyForecasts'] as $value)
            {
                $minCelsius = round(($value['Temperature']['Minimum']['Value'] - 32)/1.8);
                $maxCelsius = round(($value['Temperature']['Maximum']['Value'] - 32)/1.8);
                $dailyForecast= $dailyForecast.date('jS F', $value['EpochDate']).": "
                        .$minCelsius."C ".$maxCelsius."C ".$value['Day']['IconPhrase']."
";
            }
            MessaggingController::sendMessage($requester, $dailyForecast);
        }
        MessaggingController::sendMessage($requester, self::$WEATHER_OPTION_MESSAGE);
    }
    
    public static function askForWeatherLocation($requester)
    {
        MessaggingController::sendMessage($requester,  self::$askForLocation);
    }
    
    public static function getWeatherInformation($requester,$body)
    {
        $locationURL = self::$url.'/locations/v1/In/search?q='.$body.'&apikey='.self::$apiKey;
        echo $locationURL;
        $location = file_get_contents($locationURL);
        $locationJson = json_decode($location, true);
        if($locationJson)
        {
            var_dump($locationJson);//$locationJson;
            $locationKey = $locationJson['0']['Key'];
            $forecastURL = self::$url.'/forecasts/v1/daily/5day/'.$locationKey.'?apikey='.self::$apiKey;
            $forecast = file_get_contents($forecastURL);
            $forecastJson = json_decode($forecast, true);
            $forecastMessage = "Next 5 days Weather Forcast for ".$body.' is 
'.$forecastJson['Headline']['Text'];
            MessaggingController::sendMessage($requester, $forecastMessage);
            $dailyForecast = "Daily Forecast
";           
            foreach ($forecastJson['DailyForecasts'] as $value)
            {
                $minCelsius = round(($value['Temperature']['Minimum']['Value'] - 32)/1.8);
                $maxCelsius = round(($value['Temperature']['Maximum']['Value'] - 32)/1.8);
                $dailyForecast= $dailyForecast.date('jS F', $value['EpochDate']).": "
                        .$minCelsius."C ".$maxCelsius."C ".$value['Day']['IconPhrase']."
";
            }
            MessaggingController::sendMessage($requester, $dailyForecast);
        }
        PubSub::publish(GenieConstants::$SERVICE_REQUEST_COMPLETE,$requester); 
    }
    public static function exitMenu($requester)
    {
        PubSub::publish(GenieConstants::$SERVICE_REQUEST_COMPLETE,$requester);
    }
    
}
?>
