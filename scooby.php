<?php
/*************************************
 * Autor: mgp25                      *
 * Github: https://github.com/mgp25  *require_once 'Bot-API/spontena/pbphp/PBClient.php';

 *************************************/
require_once 'Chat-API/src/whatsprot.class.php';
require_once 'Bot-API/spontena/pbphp/PBClient.php';

require "Bot-API/vendor/autoload.php";
include 'Services/railways.php';
include 'dbManager.php';
include 'Services/movieReviews.php';
include 'Services/cricketScore.php';
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

////////////////CONFIGURATION///////////////////////
////////////////////////////////////////////////////
$username = "918222828292";
$password = "gFQ6dv4Yf1IiiybNEcp56G+0KLI=";
$nickname = "Dexter";
$debug = true;


$baseURL = 'https://aiaas.pandorabots.com';
$app_id = '1409612034562';
$botname = 'dexter';
$user_key = '0dfb3beeb74a59726d0c9ca6a1414e41';
$pbc = new PBClient($baseURL,$app_id,$user_key);



$boturl = 'http://www.botlibre.com/rest/botlibre/form-chat?instance=1121773&user=neerav.mehta@hotmail.com&password=Jgd@2421&message=';

/////////////////////////////////////////////////////
//if ($_SERVER['argv'][1] == null) {
////    echo "USAGE: php ".$_SERVER['argv'][0]." <number> \n\nEj: php client.php 34123456789\n\n";
//    exit(1);
//}
$target = "";//$_SERVER['argv'][1];
function fgets_u($pStdn)
{
    $pArr = array($pStdn);
    $write = NULL;
    $except = NULL;
    $num_changed_streams = stream_select($pArr,$write , $except, 0);
    if (false === ($num_changed_streams)) {
        print("\$ 001 Socket Error : UNABLE TO WATCH STDIN.\n");

        return FALSE;
    } elseif ($num_changed_streams > 0) {
        return trim(fgets($pStdn, 1024));
    }
    return null;
}

function onPresenceAvailable($username, $from)
{
    $dFrom = str_replace(array("@s.whatsapp.net","@g.us"), "", $from);
 //   echo "<$dFrom is online>\n\n";
}

function onPresenceUnavailable($username, $from, $last)
{
    $dFrom = str_replace(array("@s.whatsapp.net","@g.us"), "", $from);
//    echo "<$dFrom is offline>\n\n";
}

echo "[] logging in as '$nickname' ($username)\n";
$w = new WhatsProt($username, $nickname, $debug);

$w->eventManager()->bind("onPresenceAvailable", "onPresenceAvailable");
$w->eventManager()->bind("onPresenceUnavailable", "onPresenceUnavailable");

$w->connect(); // Nos conectamos a la red de WhatsApp
$w->loginWithPassword($password); // Iniciamos sesion con nuestra contraseña
echo "[*]Conectado a WhatsApp\n\n";
$w->sendGetServerProperties(); // Obtenemos las propiedades del servidor
$w->sendClientConfig(); // Enviamos nuestra configuración al servidor
$sync = array($target);
$w->sendSync($sync); // Sincronizamos el contacto
$w->pollMessage(); // Volvemos a poner en cola mensajes
$w->sendPresenceSubscription($target); // Nos suscribimos a la presencia del usuario

$pn = new ProcessNode($w, $target);
$w->setNewMessageBind($pn);


function performServiceRequest($request,$requester){
        switch (strtolower($request[0])) {
            case "#railwayhelp":
                $railwayHelpString = "For pnr related information: \nSend: #pnr <your-pnr-number> \nex:#pnr 12231122211 \n\n".
                                     "For train running status: \nSend: #livestatus <TrainNumber> <date(yyyymmdd)> \n\n".
                                     "For seat availability: \nSend: #seat <train-number> <date> <source-stn-code> <dest-stn-code> <class>";
                $GLOBALS['w']->sendMessage($requester , $railwayHelpString);
            case "#help":
                $helpString = "";
                $GLOBALS['w']->sendMessage($requester , $helpString);
                break;
            case "#pnr":
                $pnrNumber = str_replace(' ', '', $request[1]);
                $pnrStatus = getPnrStatus($pnrNumber);
                echo $pnrStatus . "\n";
                $GLOBALS['w']->sendMessage($requester , $pnrStatus);
                break;
            case "#livestatus":
                $trainNumber = str_replace(' ', '', $request[1]);
                $journeyDate = str_replace(' ', '', $request[2]);
                $runningStatus = getLiveRunningStatus($trainNumber,$journeyDate);
                echo $runningStatus . "\n";
                $GLOBALS['w']->sendMessage($requester , $runningStatus);
                break;
            case "#seat":
                $trainNumber = str_replace(' ', '', $request[1]);
                $journeyDate = str_replace(' ', '', $request[2]);
                $sourceStation = str_replace(' ', '', $request[3]);
                $destStation = str_replace(' ', '', $request[4]);
                $class = str_replace(' ', '', $request[5]);
                $seatAvailability = getSeatAvailability($trainNumber,$journeyDate,$sourceStation,$destStation,$class);
                echo $seatAvailability . "\n";
                $GLOBALS['w']->sendMessage($requester , $seatAvailability);
                break;
             case "#movie":
                 unset($request[0]);
                $movieTittle = join(" ", $request);
                echo $movieTittle;
                $review = getReview($movieTittle);           
                echo $review . "\n";
                $GLOBALS['w']->sendMessage($requester , $review);
                break;
             case "#cricket":
                $matchList = getMatchesList();           
                echo $matchList . "\n";
                $GLOBALS['w']->sendMessage($requester , $review);
                break;
             case "#livescore":
                $team1 = $request[1];
                $team2 = $request[2];
                $score = getliveScore($team1,$team2);
                echo $score.'\n';
                $GLOBALS['w']->sendMessage($requester , $score);
                break;
//            case "#livedetailedstatus":
//                $trainNumber = str_replace(' ', '', $request[1]);
//                $journeyDate = str_replace(' ', '', $request[2]);
//                $runningStatus = getLiveRunningStatus($trainNumber,$journeyDate,true);
//                echo $runningStatus . "\n";
//                $GLOBALS['w']->sendMessage($GLOBALS['target'] , $runningStatus);
//                break;

            default:
                $defaultResponse = "There is no such service, Please enter a valid service name or #help for assistance";
                echo $defaultResponse . "\n";
                $GLOBALS['w']->sendMessage($requester , $defaultResponse);
                break;
        }        
}

function startsWith($haystack, $needle) {
    // search backwards starting from haystack length characters from the end
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
}

function processRegistrationAnswers($response,$question,$expectedAnswers)
{
    foreach ($expectedAnswers as $value) {
        if($expectedAnswers == $value)
        {
            
        }
    }
}
function completePendingRegistration($contact,$response)
{
    $userFields = $GLOBALS['userFields'];
    $fieldQuestions = $GLOBALS['fieldQuestions'];
    $regex = $GLOBALS['regex'];
    for($i=0;$i< sizeof($userFields);$i++)
    {
        $field = $userFields[$i];
        if(!$contact[$field])
        {
            echo $regex[$i];
            if(preg_match($regex[$i], $response) == 0)
            {
                 $errorMessage = "Sorry, I could not understand, Please try again";
                 $GLOBALS['w']->sendMessage($contact['phone'], $errorMessage);
                 $questionMessage = "$fieldQuestions[$i]";
                 $GLOBALS['w']->sendMessage($contact['phone'], $questionMessage);
                 return;
            }
            else
            {
                
                updateContact($field, $response, $contact['phone']);
                if($i < sizeof($userFields) -1)
                {
                    $nextQuestion = $fieldQuestions[$i+1];
                    $GLOBALS['w']->sendMessage($contact['phone'], $nextQuestion);
                    return;
                }
                else
                {
                    updateContact('registered', 'true', $contact['phone']);
                    $thanksMessage = "Thank you for the information.";
                    $GLOBALS['w']->sendMessage($contact['phone'], $thanksMessage);
                    $introduction = "I am Dexter, your personal assistant. I can assist you in variety of stuff like\n
                        Weather Information \n
                        Movie Reviews \n
                        Stock Quotes \n
                        Live Scores \n
                        Railway Enquiry \n";
                    $GLOBALS['w']->sendMessage($contact['phone'], $introduction);
                    $help = "Send #help for more information";
                    $GLOBALS['w']->sendMessage($contact['phone'], $help);
                    
                    return;
                }
            }
        }            
    }
}

function onMessage($mynumber, $from, $id, $type, $time, $name, $body)
{
    $number = ExtractNumber($from);
    $contact = getUserInfo($number);
    
    if(!$contact)
    {
        addContact ($number, $name);
        $contact = getUserInfo($number);
        $replyMsg = "Hi ".$name.", It seems we are meeting for the first time.\n".
        "To assist you better I will ask you a few questions. To start with,";
        $GLOBALS['w']->sendMessage($from, $replyMsg);
        $replyMsg = $GLOBALS[fieldQuestions][0];
        $GLOBALS['w']->sendMessage($from, $replyMsg);
        return;
    }
    if($contact['registered'] == 'false')
    {
        completePendingRegistration($contact,$body);
        return;
    }
    
        
        
        
    $requestArray = explode(" ",$body);
    $hashCode = str_replace(' ', '', $requestArray[0]);
    if(startsWith($hashCode, "#"))
    {
        performServiceRequest($requestArray,$from);
    }
    else
    {
        
        $talk = file_get_contents($GLOBALS['boturl']. str_replace(' ', '+', $body));
        $xml = simplexml_load_string($talk);
        $json = json_encode($xml);
        $array = json_decode($json,TRUE);
        echo $array;
        echo $json;
        $GLOBALS['w']->sendMessage($from,$array['message']);
//        $talk = $GLOBALS['pbc']->talk($body,$GLOBALS['botname']);
//        if($talk->status == "ok")
//            {
//                foreach ($talk->responses as $responce) 
//                {
//                    echo "****************".$from;
//                    echo $responce . "\n";
//                    $interruptedResponce = interruptMessage($responce).
//                        
//                    
//                    $GLOBALS['w']->sendMessage($from, $responce);
//                }
//            }
//        else
//            {
//             echo "****************".$from;
//                    echo "Talk: " . $talk->message . "\n";
//                     $GLOBALS['w']->sendMessage($from , $talk->message );
//            }
    }

    //echo "Message from $name:\n$body\n\n";
}

function interruptMessage($response)
{
    switch ($response) {
        case "#Salutation":
            

            break;

        default:
            break;
    }
}

//$w = new WhatsProt($username, $identity, $nickname, $debug);
//$events = new MyEvents($w);
$w->eventManager()->bind("onGetMessage", "onMessage");




while (1) {
    
    $time = 0;
    while (1) {
    $w->pollMessage();
    $time++;    
    if($time >= 110){
        echo "PING \n";

        $w->sendPing();
        $time = 0;
    }   
//    echo " $time \n";
    }
   // $w->pollMessage();
    $msgs = $w->getMessages();
    foreach ($msgs as $m) {
        # process inbound messages
        //print($m->NodeString("") . "\n");
    }
    $line = fgets_u(STDIN);
    if ($line != "") {
        if (strrchr($line, " ")) {
            $command = trim(strstr($line, ' ', TRUE));
        } else {
            $command = $line;
        }
        switch ($command) {
            case "/query":
                $dst = trim(strstr($line, ' ', FALSE));
                echo "[] Interactive conversation with $contact:\n";
                break;
            case "/lastseen":
                echo "[] Last seen $target: ";
                $w->sendGetRequestLastSeen($target);
                break;
            default:
                $w->sendMessage($target , $line);
                break;
        }
    }
}

class ProcessNode
{
    protected $wp = false;
    protected $target = false;

    public function __construct($wp, $target)
    {
        $this->wp = $wp;
        $this->target = $target;
    }

    public function process($node)
    {
        $text = $node->getChild('body');
 //       $text = $text->getData();
        $notify = $node->getAttribute("notify");
//	Simplexml_load_string($text) or die("Error: Cannot create object");
//print_r($xml);
//	echo "\n" + $node;
//	echo "\n" + $text;
//	echo ("AAAAAAA" + $text);
 //       echo "\n                    ***** " + $text + "          *******  \n";

    }
}
?>