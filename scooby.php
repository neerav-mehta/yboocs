<?php ;
/*************************************
 * Autor: mgp25                      *
 * Github: https://github.com/mgp25  *require_once 'Bot-API/spontena/pbphp/PBClient.php';

 *************************************/
//require_once 'Chat-API/src/whatsprot.class.php';
//require_once 'Bot-API/spontena/pbphp/PBClient.php';
//
//require "Bot-API/vendor/autoload.php";
//include 'Services/railways.php';
//include 'dbManager.php';
//include 'Services/movieReviews.php';
//include 'Services/cricketScore.php';
//include 'userRegistration.php';
//include 'GenieConstants.php';
include 'MessaggingController.php';
use spontena\pbphp\PBClient;

	# Configuration
//Change the time zone if you are in a different country
date_default_timezone_set('Europe/Madrid');

echo "####################################\n";
echo "#                                  #\n";
echo "#           WA CLI CLIENT          #\n";
echo "#                                  #\n";
echo "####################################\n\n";
echo "====================================\n";



//$baseURL = 'https://aiaas.pandorabots.com';
//$app_id = '1409612034562';
//$botname = 'dexter';
//$user_key = '0dfb3beeb74a59726d0c9ca6a1414e41';
//$pbc = new PBClient($baseURL,$app_id,$user_key);



//$boturl = 'http://www.botlibre.com/rest/botlibre/form-chat?instance=1121773&user=neerav.mehta@hotmail.com&password=Jgd@2421&message=';



$msgController = new MessaggingController();
$msgController->configureWhatsAppService();
$msgController->startPollingMessage();





?>