<?php ;


class contextMenu
{
    public $id;
    public $menuItem;
    public $subMenu;
    public $callBackMethod;
    public $requestServer;
    public $postServiceMessage;
    public $shortCutToken;
   function __construct($id,$menuItem,$subMenu,$method,$requestServer = NULL,$postServiceMessage=NULL,$shortCutToken=NULL) {
       $this->id = $id;
       $this->menuItem = $menuItem;
       $this->subMenu = $subMenu;
       $this->callBackMethod = $method;
       $this->requestServer = $requestServer;
       $this->postServiceMessage = $postServiceMessage;
       $this->shortCutToken = $shortCutToken;
   }
   
   function toString()
   {
       return $this->id." ".$this->menuItem." ".$this->callBackMethod." ";
   }
   
 
        
}


class GenieConstants{
    public static  $RAILWAY_MENU_CONTEXT;
    public static  $WEATHER_MENU_CONTEXT;
    public static  $REGISTRATION_MENU_CONTEXT;
    public static  $PARENT_MENU_CONTEXT;
    public static  $CRICKET_MENU_CONTEXT;
    public static  $VAS_MENU_CONTEXT;
    public static  $MAIN_MENU_CONTEXT;
    public static  $INTRODUCTION;
    public static  $HELP;
    public static  $WELCOME_MESSAGE;
    public static  $SERVICE_UNAVAILABLE;
    public static  $INVALID_TRAIN_NUMBER;
    public static  $MATCH_UNAVAILABLE;
    public static  $INVALID_INFORMATION;
    public static  $INVALID_SERVICE;
    public static  $ERROR_MESSAGE;
    public static  $THANKYOU_MESSAGE;
    public static  $SERVICE_REQUEST_COMPLETE;
    public static  $userFields;
    public static  $genderQuestion;
    public static  $ageQuestion;
    public static  $cityQuestion;
    public static  $emailQuestion;
    public static  $fieldQuestions;
    public static  $genderRegex;
    public static  $ageRegex;
    public static  $cityRegex;
    public static  $emailRegex;
    public static  $regex;
    public static  $GENIE;
    
    public static  $GENDER_SUB_MENU_STRING;
    public static  $AGE_SUB_MENU_STRING;
    public static  $CITY_SUB_MENU_STRING;
    public static  $EMAIL_SUB_MENU_STRING;
    
    public static  $postMovieServiceMessage;
    public static  $postCricketServiceMessage;
    public static  $postRegistrationServiceMessage;
    public static  $anyThingElse;
    public static  $MAIN_MENU_STRING;
    public static  $THANKYOU_SERVICE_MESSAGE;
    
    public static  $SHOPPING_MENU_CONTEXT;
    public static  $postShoppingSearch;




    public static function searchElement($from, $searchElement,$property)
   { 
      foreach ($from as $key=>$value)
     {
         
         if($value->$property == $searchElement)
         {
             echo "\n\n\n\n\n". $key;
              echo "\n\n\n\n\n";
             return $key;
           
         }
     }
     
     
     return NULL;
  }
    
    static function init()
      {
        self::$RAILWAY_MENU_CONTEXT = [
            '1' => new contextMenu('1','PNR',NULL,'askForPnrStatus','getPnrStatus',NULL,"#pnr"),
            '2' => new contextMenu('2','LIVE STATUS',NULL,'askForLiveRunningStatus','getLiveRunningStatus',NULL,'#livestatus'),
            '3' => new contextMenu('3','SEAT INFORMATION',NULL,'askForSeatInformation','getSeatAvailability',NULL,'#seatInfo')
        ];
        
        self::$GENIE = "GENIE";
        
        self::$WEATHER_MENU_CONTEXT = [
            '1' => 'PNR',
            '2' => 'LIVE_STATUS',
            '3' => 'SEAT_INFORMATION',
        ];
        
        self::$GENDER_SUB_MENU_STRING = 'gender';
        self::$AGE_SUB_MENU_STRING = 'age';
        self::$CITY_SUB_MENU_STRING = 'location';
        self::$EMAIL_SUB_MENU_STRING = 'email';

        
        self::$postRegistrationServiceMessage = "Thank you for the information.";
        
        self::$anyThingElse = "Is there anything else I can assist you with?
                                1 for Yes
                                2 for No";

        self::$THANKYOU_SERVICE_MESSAGE = "Thank you for using our services.";

        
        self::$REGISTRATION_MENU_CONTEXT = [
            '1' => new contextMenu('1', self::$GENDER_SUB_MENU_STRING,NULL,'processGenderInformation'),
            '2' => new contextMenu('2', self::$AGE_SUB_MENU_STRING,NULL,'processAgeInformation'),
            '3' => new contextMenu('3', self::$CITY_SUB_MENU_STRING,NULL,'processCityInformation'),
            '4' => new contextMenu('4', self::$EMAIL_SUB_MENU_STRING,NULL,'processEmailInformation',self::$postRegistrationServiceMessage),
        ];
        
        self::$SERVICE_REQUEST_COMPLETE = "SERVICE_REQUEST_COMPLETE";

        self::$PARENT_MENU_CONTEXT  = [];
        
        self::$SHOPPING_MENU_CONTEXT = [
            '1' => new contextMenu('1','SEARCH PRODUCT',NULL,'getProductInformation','getProductInformation',self::$postShoppingSearch,'#shopping'),
            '2' => new contextMenu('2','LATEST OFFERS',NULL,'getLatestOffers','getLatestOffers','#offers')
       ];
                
        self::$CRICKET_MENU_CONTEXT = [
            '1' => new contextMenu('1','LIVE SCORE',NULL,'askForLiveScore','getLiveScore'),
            '2' => new contextMenu('2','RESET SERVICE MENU',NULL,'resetServiceMenu','resetServiceMenu')
       ];
        
        
        self::$postMovieServiceMessage = 'You can also send "#movie <movie-name>" to get movie information in single message';
        self::$postCricketServiceMessage = 'You can send "#cricket <team1,team2>" to get latest match score information';
        
        self::$postShoppingSearch = 'You can also send #shopping <product-name> to get updated product information.';
        self::$VAS_MENU_CONTEXT = [
            '1' => new contextMenu('1','MOVIE INFORMATION',NULL,'askForMovieInformation','getMovieReview',self::$postMovieServiceMessage,'#movie'),
            '2' => new contextMenu('2','CRICKET INFORMATION',NULL,'askForMatchInformation','getLiveScore',self::$postCricketServiceMessage,'#cricket')
       ];
        

        

        self::$MAIN_MENU_CONTEXT = [
           '0' => new contextMenu('0','Parent',self::$PARENT_MENU_CONTEXT,NULL),
           '1' => new contextMenu('1','Weather',self::$WEATHER_MENU_CONTEXT,NULL),
//           '3' => 'STOCK',
           '5' => new contextMenu('5','Railway',self::$RAILWAY_MENU_CONTEXT,null),
           '6' => new contextMenu('6','Shopping',self::$SHOPPING_MENU_CONTEXT,null),
           '2' => new contextMenu('2','VAS',self::$VAS_MENU_CONTEXT,null),
           '8' => new contextMenu('8','UserManagement',self::$REGISTRATION_MENU_CONTEXT,null)
       ];

        self::$INTRODUCTION = "I am Genie, your whatsapp based personal assistant.\n
            How can I assist you today?
            1 for Weather Information \n
            2 for Value Added Services \n
            3 for Stock Quotes \n
            5 for Railway Enquiry \n
            6 for Shopping Assistance \n
            7 for Phone Recharge";

        
        self::$MAIN_MENU_STRING = "What can I do for you?
            1 for Weather Information \n
            2 for Value Added Services \n
            3 for Stock Quotes \n
            5 for Railway Enquiry \n
            6 for Shopping Assistance \n
            7 for Phone Recharge";

        self::$HELP = "Send #help to connect to our service agent";

        self::$WELCOME_MESSAGE = ", It seems we are meeting for the first time.\n".
               "To assist you better I will ask you a few questions. To start with,";

        self::$SERVICE_UNAVAILABLE = "Service temporarily not available, please try after some time";

        self::$INVALID_TRAIN_NUMBER = "Invalid train number or journey date";

        self::$MATCH_UNAVAILABLE = "Sorry, this match information is not available.";

        self::$INVALID_INFORMATION = "Invalid information, please check again";

        self::$INVALID_SERVICE = "There is no such service, Please enter a valid service name or #help for assistance";

        self::$ERROR_MESSAGE = "Sorry, I could not understand, Please try again";

        self::$THANKYOU_MESSAGE = "Thank you for the information.";

        self::$userFields = array('gender','age','location','email');
        self::$genderQuestion = "May I know your gender please?\n1 for Male\n2 for Female";
        self::$ageQuestion = 'Tell me which age group do you belong to:
           1 for <18 yrs
           2 for 18-25 yrs
           3 for 26-39 yrs
           4 for 40-60 yrs
           5 for >60 yrs';
        self::$cityQuestion = 'Alright, May I know which city you belong to?';
        self::$emailQuestion = 'One last question! Tell me your email address.';
        self::$fieldQuestions = array(self::$genderQuestion,  self::$ageQuestion,  self::$cityQuestion,  self::$emailQuestion);




         self::$genderRegex = '/[1-2]/';
         self::$ageRegex = '/[1-5]/';
         self::$cityRegex = '/^[a-zA-Z ]*$/';
         self::$emailRegex = "/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/";
         self::$regex = array(self::$genderRegex,  self::$ageRegex,  self::$cityRegex,  self::$emailRegex);
      }
}

GenieConstants::init();

?>
