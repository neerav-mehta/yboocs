<?php ;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of messaggingController
 *
 * @author neerav.mehta
 */

require_once 'Chat-API/src/whatsprot.class.php';
include 'dbManager.php';
include 'GenieConstants.php';
include 'Events/Dispatcher.php';
include 'Events/Event.php';
include 'Events/PubSub.php';
include 'ServiceController.php';
class MessaggingController {
    //put your code here
    ////////////////CONFIGURATION///////////////////////
////////////////////////////////////////////////////
//        private $username = "918222828292";
//        private $password = "gFQ6dv4Yf1IiiybNEcp56G+0KLI=";
//        private $nickname = "Dexter";
//        private $debug = true;
    
        private $username = "918222828292";
        private $password = "gFQ6dv4Yf1IiiybNEcp56G+0KLI=";
        private $nickname = "Dexter";
        private $debug = true;


        private $boturl = 'http://www.botlibre.com/rest/botlibre/form-chat?instance=1121773&user=neerav.mehta@hotmail.com&password=Jgd@2421&message=';

        private $target = "";//$_SERVER['argv'][1];
        
        public static $w;


        function __construct() {
       
        

        }
        
        
        public static function sendMessage($receiver,$message) {
            self::$w->sendMessage($receiver['phone'],$message);
            addMessage(GenieConstants::$GENIE,$receiver['phone'], $message);
        }


        public function configureWhatsAppService()
        {    
            self::$w= new WhatsProt($this->username, $this->nickname, $this->debug);
            self::$w = new WhatsProt($this->username, $this->nickname, $this->debug);
            echo "[] logging in as '$nickname' ($username)\n";
            self::$w->eventManager()->bind('onPresenceAvailable',array($this, 'onPresenceAvailable'));
            self::$w->eventManager()->bind("onPresenceUnavailable", array($this, "onPresenceUnavailable"));

            self::$w->connect(); // Nos conectamos a la red de WhatsApp
            self::$w->loginWithPassword($this->password); // Iniciamos sesion con nuestra contraseña
            echo "[*]Conectado a WhatsApp\n\n";
            self::$w->sendGetServerProperties(); // Obtenemos las propiedades del servidor
            self::$w->sendClientConfig(); // Enviamos nuestra configuración al servidor
            $sync = array($target);
            self::$w->sendSync($sync); // Sincronizamos el contacto
            self::$w->pollMessage(); // Volvemos a poner en cola mensajes
            self::$w->sendPresenceSubscription($target); // Nos suscribimos a la presencia del usuario

            $pn = new ProcessNode(self::$w, $target);
            self::$w->setNewMessageBind($pn);
            self::$w->eventManager()->bind("onGetMessage", array($this, "onMessage"));
        }
        
        
        public function startPollingMessage()
        {
                    while (1) {
                               $time = 0;
                                while (1) {
                                self::$w->pollMessage();
                                $time++;    
                                if($time >= 110){
                                    echo "PING \n";

                                    self::$w->sendPing();
                                    $time = 0;
                                 }   
                            }
                        }
        }

  
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
        
        
        function onMessage($mynumber, $from, $id, $type, $time, $name, $body)
        {
            echo $from;
            echo $id;
            echo $type;
            addMessage($from,  GenieConstants::$GENIE, $body);
            PubSub::publish('message_received',$mynumber,$from,$id,$type,$time,$name,$body);  
        }
        
        
        public function startsWith($haystack, $needle) {
            // search backwards starting from haystack length characters from the end
            return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
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
        $text = $text->getData();
        $notify = $node->getAttribute("notify");

        echo "\n- ".$notify.": ".$text."    ".date('H:i')."\n";

    }
}

?>
