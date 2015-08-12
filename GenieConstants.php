<?php
$INTRODUCTION = "I am Genie, your whatsapp based personal assistant.\n
                        How can I assist you today?
                        1 for Weather Information \n
                        2 for Movie Reviews \n
                        3 for Stock Quotes \n
                        4 for Live Cricket Scores \n
                        5 for Railway Enquiry \n
                        6 for Shopping Assistance
                        7 for Phone Recharge";


$HELP = "Send #help to connect to our service agent";

$WELCOME_MESSAGE = ", It seems we are meeting for the first time.\n".
        "To assist you better I will ask you a few questions. To start with,";

$SERVICE_UNAVAILABLE = "Service temporarily not available, please try after some time";

$INVALID_TRAIN_NUMBER = "Invalid train number or journey date";

$MATCH_UNAVAILABLE = "Sorry, this match information is not available.";

$INVALID_INFORMATION = "Invalid information, please check again";

$INVALID_SERVICE = "There is no such service, Please enter a valid service name or #help for assistance";

$ERROR_MESSAGE = "Sorry, I could not understand, Please try again";

$THANKYOU_MESSAGE = "Thank you for the information.";

$userFields = array('gender','age','location','email');
$genderQuestion = "Alright, May I know your gender please?\n1 for Male\n2 for Female";
$ageQuestion = 'Tell me which age group do you belong to:
    1 for <18 yrs
    2 for 18-25 yrs
    3 for 26-39 yrs
    4 for 40-60 yrs
    5 for >60 yrs';
$cityQuestion = 'May I know which city you belong to?';
$emailQuestion = 'One last question! Tell me your email address.';
$fieldQuestions = array($genderQuestion,$ageQuestion,$cityQuestion,$emailQuestion);




$genderRegex = '/[1-2]/';
$ageRegex = '/[1-5]/';
$locationRegex = '/^[a-zA-Z ]*$/';
$emailRegex = "/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/";
$regex = array($genderRegex,$ageRegex,$locationRegex,$emailRegex);

?>
