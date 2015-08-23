<?php
include_once '../ServiceController.php';


PubSub::subscribe('Shopping', function(){
    $moduleName = 'Shopping';
    $params = func_get_args();
    $callBackMethod = $params[0];
    array_shift($params);
    
    echo $callBackMethod;
    call_user_func_array(array(__NAMESPACE__ .'\Shopping',$callBackMethod),$params);
});

class Shopping
{
    public static $flipkartSearchURL = 'https://affiliate-api.flipkart.net/affiliate/search/json?query=';
    public static $askForProductInformation = "So May I know what are you planning to shop today?";
    public static $ShoppingWelcomeMessage = "Welcome to shopping assistance service
                                             I have tieups with Snapdeal, Flipkart, Amazon, Paytm etc and I can provide you best prices, current offers etc";
    public static $HELP = "Anytime you can press help to go to the main menu";
    
    public static $fKAffiliateId = 'neeravmeh';
    public static $fKAffiliateToken = '3de426c2b5a346329ae76537bcc8f5db';
    
    public static function getURLContent($url,$affiliateId,$affiliateToken)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Fk-Affiliate-Id: '.$affiliateId,
        'Fk-Affiliate-Token: '.$affiliateToken
        ));
       
        curl_setopt($ch, CURLOPT_REFERER, "http://www.askgenie.co.in");
        curl_setopt($ch, CURLOPT_USERAGENT, "MozillaXYZ/1.0");
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    public static function initializeService($requester)
    {
        $welcomeMessage = self::$ShoppingWelcomeMessage;
        MessaggingController::sendMessage($requester, $welcomeMessage);
        
        $help = self::$HELP;
        MessaggingController::sendMessage($requester, $help); 

        $menu = self::$askForProductInformation;
        MessaggingController::sendMessage($requester, $menu);
        updateMessageContext(GenieConstants::searchElement(GenieConstants::$MAIN_MENU_CONTEXT,  GenieConstants::$SHOPPING_MENU_CONTEXT, 'subMenu'),'1', $requester['phone']);
    }
    
    public static function getProductInformation($requester,$body)
    {
        $body = str_replace(' ', '', $body);
        $url = self::$flipkartSearchURL.$body.'&resultCount=1';
        $searchJson = self::getURLContent($url,self::$fKAffiliateId, self::$fKAffiliateToken);
        echo $searchJson;
        $json = json_decode($searchJson, true);

                 $productObj = $json['productInfoList'][0]['productBaseInfo']['productAttributes'];
                 echo $json['productInfoList'][0]['productBaseInfo']['productAttributes']['title'];
                 $newLine = '\n';
                 $fkMRP = "MRP: ".$productObj['maximumRetailPrice']['amount'];
                 $fkSellngPrice = "Selling Price: ".$productObj['sellingPrice']['amount'];
                 $fkProductLink = $productObj['productUrl'];
                 $fkTittle = $productObj['title'];
                 $fkPriceProduct =  "{$fkTittle}\n{$fkMRP}\n{$fkSellngPrice}\n{$fkProductLink}";         
                 MessaggingController::sendMessage($requester, $fkPriceProduct);  
                 PubSub::publish(GenieConstants::$SERVICE_REQUEST_COMPLETE,$requester);     
    }
    
}
?>
