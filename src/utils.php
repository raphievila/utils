<?php
Namespace Utils;
use xTags\xTags;

//Author: Rafael Vila
/*
 *Version: 1.0.2
 *Last Modified: September 9th, 2017 14:43
 *License:
	Utils is a object swiss tool to help programming php with quick tools
    that allows you to automize basic curl connections, echoing arrays or
    objects ready for html output. Requires raphievila/xtags object.
	Copyright (C) 2016  Rafael Vila - Revolution Visual Arts

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

class Utils {
    protected static $docroot;
    protected static $http_message = array(
        // Informational 1xx
        100 => 'Continue',
        101 => 'Switching Protocols',

        // Success 2xx
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',

        // Redirection 3xx
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',  // 1.1
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        // 306 is deprecated but reserved
        307 => 'Temporary Redirect',

        // Client Error 4xx
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',

        // Server Error 5xx
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        509 => 'Bandwidth Limit Exceeded'
    );

    static private $company = "Your Company Name";
    static private $phone = "555-555-5555";
    static private $email = "anyone@example.com";
    static private $url = "http://example.com";
    
    static private $approvedDomains = array(
        "localhost",
        "127\.0\.0",
    );
    
    public function __construct(){        
        self::$docroot = filter_input(INPUT_SERVER, "DOCUMENT_ROOT", FILTER_SANITIZE_STRING);
    }
    
    //TO PROTECT PHISHING TO AUTOMATICALLY GENERATED LIST OF SUBDOMAINS
    //SET UP AVAILABLE DOMAINS self::$approvedDomains
    protected function STOPNONAUTHORIZEDDOMAINS() {
        $x = new xTags();
        $hh = filter_input(INPUT_SERVER, 'HTTP_HOST', FILTER_SANITIZE_STRING);
        $meta = $x->head( $x->title("NON AUTHORIZED SEARCH CONTAINER")
                . $x->meta("","charset:UTF-8",1)
                //create a css authorized.css file to style
                . $x->link("","href:/css/unauthorized.css,rel:stylesheet,media:all,type:text/css"));
        $title = $x->h1($x->span("","class:glyphicon glyphicon-info-sign bottomalign") . " Unauthorized URL", "class:text-warning centertext");
        $content = $x->p("The URL you are trying to access is not registered under our authorized list of URLs.")
                . $x->p("If you are trying to access " . self::$company . ". online portal, please go to "
                    . $x->a("our official website.", array("href" => self::$url)))
                . $x->p("For more information call " . self::$phone . ", or contact us by email at "
                    . $x->a(self::$email, array("href" => "mailto:" . self::$email)));
        $body = $x->body($x->div("","class:midalign midset") . $x->div("$title$content", "class:midalign tag text-justify"), "class:centertext");
        $html = "<!DOCTYPE html>" . $x->html($meta . $body);
        
        if(!preg_match('/^($http)?(s)?(:\/\/)?(www\.)?(' . join('|',self::$approvedDomains) . ')(\.)?.*?$/i',$hh)) {
            die($html);
        }
    }
    
    //STOP DISPLAY OF WEB IF REQUESTER USING UNATHORIZED URL STRUCTURE
    public function check_url() {
        self::STOPNONAUTHORIZEDDOMAINS();
    }
    
    //WEB CHECKS FROM URL RETURN FALSE OR TRUE
    public function check_domain() {
        $hh = filter_input(INPUT_SERVER, 'HTTP_HOST', FILTER_SANITIZE_STRING);
        return !preg_match('/^($http)?(s)?(:\/\/)?(www\.)?(' . join('|',self::$approvedDomains) . ')(\.)?.*?$/i',$hh);
    }
    
    //RETURN NUMBERS WITH SPECIFIED NUMBER OF LEADING ZEROS AS STRING
    private static function LEADZEROS($num,$spaces = 2){
        $residue = (strlen($num) < $spaces)? $spaces - strlen((string)$num) : 0;
        $pre = '';
        for($i = 0; $i < $residue; $i++){
            $pre .= '0';
        }
        return $pre.$num;
    }
    
    public function lead_zeros($num, $spaces = 2) {
        return self::LEADZEROS($num, $spaces);
    }
    
    //TO CONFIRM THAT AN ARRAY OR OBJECT IS NOT EMPTY
    private static function checking_keys_values($obj) {
        $result = '';
        foreach($obj as $k => $v){
            $stringValue = (is_array($v) || is_object($v))? join('',(array) $v) : $v;
            if(!is_null($stringValue) && !empty($stringValue)){ $result .= $k; }
        }
        $trimed = trim($result);
        $checkEmpty = (empty($trimed));
        if($checkEmpty){ return false; } else { return true; }
    }
    
    public function confirm_methods($arr){
        $rev = ($this->isMap($arr))? (array) count($arr) : 0;
        if($rev > 0){
            return self::checking_keys_values($arr);
        }
        return false;
    }
    
    //RETURN BROWSER INFORMATION IN AN EASY TO READ OBJECT
    private function split_enchancement($string) {
        $r = new stdClass();
        $r->full = $string;
        if (strpos($r->full, '/') > 0) {
            $r->vendor = substr($r->full, 0, strpos($r->full, '/'));
            $r->version = substr($r->full, strpos($r->full, '/') + 1, strlen($r->full));
        }
        return $r;
    }
    
    public function ua_strip($ua = FALSE) {
        if (!$ua) { $ua = getenv("HTTP_USER_AGENT"); }
        $matches = array();
        $browser = FALSE;
        preg_match_all('/^(.*?)\s(\(.*?\))\s(.*?)\s(\(.*?\)\s)?(.*?)$/', $ua, $matches);
        
        //$this->echo_array($matches);
        
        if(count($matches) > 0){
            
            if(is_array($matches[0]) && count($matches[0]) > 0){
                $browser = new stdClass();
                if(isset($matches[0][0])){ $browser->full = $this->split_enchancement($matches[0][0]); }
                if(isset($matches[1][0])){ $browser->compatibility = $this->split_enchancement($matches[1][0]); }

                //User System
                if(isset($matches[2]) && isset($matches[2][0])){
                    $browser->system = new stdClass();
                    $browser->system->full = trim(str_replace(array("(",")"), "", $matches[2][0]));
                    $splitSystem = explode('; ', $browser->system->full);
                    $browser->system->details = $splitSystem;
                }

                //Platform
                if(isset($matches[3]) && isset($matches[3][0])){
                    $browser->platform = new stdClass();
                    $browser->platform->used = $this->split_enchancement($matches[3][0]);
                }

                if(isset($matches[4]) && is_array($matches[4]) && count($matches[4]) > 0){
                    $browser->platform->details = trim(str_replace(array("(",")"), "", $matches[4][0]));
                }
                
                if(isset($matches[5]) && isset($matches[5][0])){
                    $enhancements = explode(' ',trim($matches[5][0]));
                    $browser->enhancements = new stdClass();
                    if(count($enhancements) > 0){
                        $totalEnhance = count($enhancements);
                        $thisBrowser = $enhancements[$totalEnhance - 1];
                        $browser->current = $this->split_enchancement($thisBrowser);

                        unset($enhancements[$totalEnhance - 1]);
                        if (count($enhancements) > 0){
                            $i = 1;
                            foreach($enhancements as $enchanced) {
                                $browser->enhancements->{"enhance_$i"} = $this->split_enchancement($enchanced);
                                $i++;
                            }
                        }
                    }
                }
            }
        }
        
        return $browser;
    }
    
    //CHECK A STRING FOR COMMON BASIC XSS EXPLOITS
    public function getXSSFilter(){
        $pattern = '/((\%3C)|<)((\%2F)|\/)*[a-z0-9\%]+((\%3E)|>) | ((\%3C)|<)((\%69)|i|(\%49))((\%6D)|m|(\%4D))((\%67)|g|(\%47))[^\n]+((\%3E)|>) | ((\%3C)|<)[^\n]+((\%3E)|>)/ix';
        return $pattern;
    }
    
    public function clean_XSS($string){
        if(is_numeric($string) || is_bool($string)){
            return $string;
        } else {
            $filter = htmlspecialchars(preg_replace($this->getXSSFilter(),'',$string));
            return $filter;
        }
    }
    
    //WRAPS AND ARRAY OR OBJECT INSIDE A <PRE> HTML TAG - FOR TESTING DURING DEVELOPING
    public function echo_array($arr){
        echo '<pre style="white-space:pre-wrap">'; print_r($arr); echo '</pre>';
    }
    
    //DISPLAYS STRING INSIDE A TEXTAREA - FOR TESTING DURING DEVELOPMENT
    public function echo_text($text) {
        echo '<textarea>' . $text . '</textarea>';
    }

    //echo current code main url - for example http://example.com | https://example.com:446
    //use full for a transportable website
    protected static function get_url(){
        $https = filter_input(INPUT_SERVER,'HTTPS'); $port = filter_input(INPUT_SERVER,'SERVER_PORT'); $name = filter_input(INPUT_SERVER,'SERVER_NAME');
        $url = 'http';
        $url .= (isset($https) && $https == "on")? 's': '';
        $url .= '://';
        $url .= (isset($port) && $port != "80")? $name.":".$port : $name;
        return $url;
    }
    
    public function site_url() {
        return self::get_url();
    }

    //convert Exception into a formatted html message the stop script execution
    //use as catch(Exception $e) { throw_error($e); }
    public function throw_error($e,$cNum = false) {
        $name = filter_input(INPUT_SERVER,'SERVER_NAME');
        $root = filter_input(INPUT_SERVER,'DOCUMENT_ROOT');
        if($cNum == 'RAW'){
            echo "<pre>";
            print_r($e); 
            echo "</pre>";
            exit;
        } else {
            echo "<div class=\"generror\" style=\"text-align:left; display:inline-block;\">";
            $eNum = ($cNum)? $cNum : $e->getCode();
            echo "<h2>Error No: ".$eNum. "</h2><span style=\"color:red; font-size:20;\">". nl2br($e->getMessage()) . "</span>";
            if(substr_count($e->getFile(),'\\') > 0){
                    $file = explode('\\',$e->getFile());
            } elseif(substr_count($e->getFile(),'/') > 0) {
                    $file = explode('/',$e->getFile());
            } else {
                    $file = $e->getFile();
            }
            $fileLine = $e->getLine();
            if(is_array($file)){
                    $fcount = count($file);
                    $lastcount = $fcount - 1;
                    $actualfile = $file[$lastcount];
                    //echo '<pre>'; print_r($file); echo '</pre>';
            } else {
                    $actualfile = $file;
            }
            $root = (substr_count($name,'localhost') > 0)? str_replace('/','\\',$root) : '/var/www/html';
            $droot = $root;
            $details =  str_replace( $droot, '', nl2br($e->getTraceAsString()));
            echo "<div>Thrown by: $actualfile on line #$fileLine <br /><span style=\"font-family:monospace;\">$details</span></div>";
            echo "</div>";
            exit;
        }
    }

    //calculate given date to specify time lenght either substracting or adding and return
    //the calculated range on the specified format
    public function getTimeRange($actual,$length,$lengthType='week',$set='sub',$format="Y-m-d"){
        $date = new DateTime($actual);
        $amount = $length;

        switch($lengthType){
            case 'day':
                $style = "D";
                break;
            case 'month':
                $style = "M";
                break;
            case 'year':
                $style = "Y";
                break;
            default:
                $amount = $length*7;
                $style = "D";
        }

        if($set == 'sub'){
            $date->sub(new DateInterval("P".$amount.$style));
        } else {
            $date->add(new DateInterval("P".$amount.$style));
        }

        return $date->format($format);
    }
    
    //string and value analising function, basic cleaning,
    //file naming formating and more.
    protected static function CLEANHTML($v) {
        //Clean some special html characters for better DB storage        
        $htmlSymbols = array('&reg;','&copy;','&dollar;','&trade;','&deg;','&bull;','&amp;');
        $plainText = array('®','©','$','™','°','•','&');
        return str_replace($htmlSymbols, $plainText, $v); 
    }
    
    public function clean_html($v){
        return self::CLEANHTML($v);
    }
    
    public function reverse_html($v){
        //Clean some special html characters for better DB storage        
        $htmlSymbols = array('&reg;','&copy;','&dollar;','&trade;','&deg;','&bull;','&amp;');
        $plainText = array('®','©','$','™','°','•','&');
        return str_replace($plainText, $htmlSymbols, $v);        
    }
    
    //check if value given is not a string
    protected static function SPECIALITEM($val) {
        if(is_array($val) || is_bool($val) || is_object($val) || is_numeric($val) || is_null($val) || is_resource($val)){
            return true;
        }
        return false;
    }
    
    public function isSpecial($val){
        return self::SPECIALITEM($val);
    }
    
    //verify if value given is either array or object
    public function isMap($val){
        return is_array($val) || is_object($val);
    }
    
    //basic string cleaning to protect from basic SQL Injections (use as extra layer, not the only solution).
    protected static function processInput($val,$decode=TRUE){
        $value = (!self::SPECIALITEM($val))? self::CLEANHTML($val) : $val;
        return ($decode && !is_array($value) && !is_object($value))? utf8_decode($value) : $value;
    }
    
    public function processInputData($val, $decode=TRUE) {
        return self::processInput($val, $decode);
    }
    
    //convert slugs into user friendly titles
    public function slugsToString($text){
        $string = $this->processInputData($text);
        return ucwords(str_replace(array('--','-','_','|'),' ', $string));
    }
    
    //converts string into url friendly slugs
    public static function stringToSlug($text) {
        $removingShortWords = preg_replace('/(\s|^)(\w{0,2})(\s|$)/',' ',$text);
        $convertingToEntities = htmlentities($removingShortWords, ENT_QUOTES);
        $removingLocaleCharacters = preg_replace('/(\&)(\w{1})(acute|grave|circ|cedil|uml|tilde|slash|elig)(;)/','$2',$convertingToEntities);
        $removingOtherEntities = preg_replace('/(\&)(#\d{3}|\w{2,8})(;)/', ' ', $removingLocaleCharacters);
        $removingNonAlphaNumerics = preg_replace("/[^A-Za-z0-9 ]/", '', $removingOtherEntities);
        $cleaningSpaces = preg_replace('/\s\s/', '', $removingNonAlphaNumerics);
        return strtolower(str_replace(' ', '-', $cleaningSpaces));
    }
    
    //convert weird part numbers into url or file name save strings
    public static function urlFriendlyPartNumber($pn){
        $string = explode('.',preg_replace('/\/|\"|\'|\s/','_',$pn));
        $name = $string[0];
        $ext = (isset($string[1]))? $string[1] : '';
        $return = strtoupper($name);
        $return .= (strlen($ext) > 0)? '.'.$ext : '';
        return $return;
    }
    
    //set of functions to set curl request
    /*
     * $url = url string
     * $args = array of arguments as follow:
     * 
     * to show headers set key showHeaders to TRUE
     * ======================================================
     * to set post set key asPost to TRUE
     * send arguments as array or string with key post
     * ======================================================
     * key nossetion > default FALSE
     * by default the curl includes session ID
     * originally I wrote the code to request data from
     * local directory with file_get_contents but didn't
     * return data as current session, this fixed the issue.
     * IMPORTANT!!!
     * TO AVOID THIS set key nosession to TRUE
     * ======================================================
     * to change content type set with key contentType
     * default text/html
     * ======================================================
     * to set credentials set key user and pass
     * ======================================================
     * to set timeout <default 30> change with key timeout
     * ======================================================
     * set ssl if you want to skip verify peer and status
     * ======================================================
     * to send using https set key https to TRUE
     * ======================================================
     * IF REQUEST IS UNSUCCESSFUL WILL THROW EXCEPTION
     */
    protected static function session_integrity($url,$args=FALSE){
        $showHeaders = FALSE;
        $asPost = FALSE;
        $user = NULL; $pass = NULL; $post = NULL;
        $contentType = "text/html";
        $timeout = 30;
        $nosession = FALSE;
        $session = 'PHPSESSID=' . session_id() . '; path=/';
        session_write_close();
        
        if(is_array($args)){
            foreach($args as $k=>$v){
                if($k !== "session"){
                    ${$k} = self::processInput($v);
                }
            }
        }
        
        $https = filter_input(INPUT_SERVER,'HTTPS');
        $ssl = (isset($https) && $https == "on")? TRUE: FALSE;
        if($asPost) {
            $contentType = "application/x-www-form-urlencoded";
        }
        
        $header = array(
            "Content-type: $contentType; charset=UTF-8;",
            "Content-transfer-encoding: text"
        );
        
        if(isset($post) && is_array($post)){
            $posts = array();
            foreach($post as $var=>$val){
                $posts[] = "$var=$val";
            }
            $post_string = (count($posts) > 0)? implode("&",$posts) : "";
            $header[] = "Content-length: ".strlen($post_string);
        }
        
        $header[] = "Connection: close";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
        if($ssl) { curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); }
        if($ssl) { curl_setopt($ch, CURLOPT_SSL_VERIFYSTATUS, 0); }
        if($showHeaders) { curl_setopt($ch, CURLOPT_HEADER, TRUE); }
        if($asPost) { curl_setopt($ch, CURLOPT_POST, TRUE); }
        curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
        curl_setopt($ch, CURLOPT_COOKIESESSION, TRUE);
        if(!$nosession) { curl_setopt($ch, CURLOPT_COOKIE, $session); }
        curl_setopt($ch, CURLOPT_FAILONERROR, FALSE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        if(isset($user)){ curl_setopt($ch, CURLOPT_USERNAME, $user); }
        if(isset($pass)){ curl_setopt($ch, CURLOPT_USERPWD, $pass); }
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $response = curl_exec($ch);
        if($response === FALSE){
            throw new \Exception("cURL Error #" . curl_errno($ch) . ": " . curl_error($ch), curl_errno($ch));
        }
        curl_close($ch);
        
        return $response;
    }
    
    public function curl_session_integrity($url, $args = FALSE) {
        return self::session_integrity($url, $args);
    }
    
    //to calculate height ration from given width
    public function image_size_by_ratio($x,$y,$max){
        $width  = (round($max*($x/$y)) > $max)? $max : round($max*($x/$y));
        $height = (round($max*($y/$x)) > $max)? $max : round($max*($y/$x));        
        return (object) array('width'=>$width,'height'=>$height);
    }

    //return fraction equivalent of given decimal (float number)
    public function decToFraction($float) {
        // 1/2, 1/4, 1/8, 1/16, 1/3 ,2/3, 3/4, 3/8, 5/8, 7/8, 3/16, 5/16, 7/16,
        // 9/16, 11/16, 13/16, 15/16
        $whole = floor($float);
        $decimal = $float - $whole;
        $leastCommonDenom = 16; //16 * 3;
        $denominators = array (2, 3, 4, 8, 16); //, 24, 48
        $roundedDecimal = round ( $decimal * $leastCommonDenom ) / $leastCommonDenom;
        $denom = 1;
        if ($roundedDecimal == 0){ return $whole; }
        if ($roundedDecimal == 1){ return $whole + 1; }
        foreach ( $denominators as $d ) {
            if ($roundedDecimal * $d == floor ( $roundedDecimal * $d )) {
                $denom = $d;
                break;
            }
        }
        return ($whole == 0 ? '' : $whole . " ") . ($roundedDecimal * $denom) . "/" . $denom;
    }
    
    //generate an automatic password base on specific length > default 8
    public function password_generator($length=8){
        $len = abs($length);
        $allowed = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','!','@','#','$','%','^','&','*','(',')','-','=','_','+',1,2,3,4,5,6,7,8,9,0);
        $casefunc = array('upper','lower');
        $lastNum = count($allowed) - 1;
        $r = '';
        for($i=0;$i<$len;$i++){
            $case = rand(0,1);
            $use = $casefunc[$case];
            $char = rand(0,$lastNum);
            $r .= ($use === 'upper')? strtoupper($allowed[$char]) : strtolower($allowed[$char]);
        }
        return $r;
    }
    
    //user name auto generator
    //used to create user list from a excel table with name and last names
    //be sending both values, it generate a user name based on this value.
    //$string = name
    //$string2 (OPTIONAL) = last name
    public function user_generator($string, $string2 = FALSE){
        $replacing = array('!','@','#','$','%','^','&','*','(',')','-','_',',',';','\'','"','{','}','[',']','.');
        $removeSymbols = str_replace($replacing,'',$string);
        $words = explode(' ',strtolower($removeSymbols)); $user = array();
        for($i=0;$i<count($words);$i++){
            if($string2){
                $user[] = substr($words[$i], 0,1);
            } else {
                $user[] = ($i > 0)? ucfirst($words[$i]) : $words[$i];
            }
        }
        $nuser = join('',$user);
        if($string2){
            $br = explode(' ',$string2);
            $nuser .= str_replace($replacing,'',ucfirst(strtolower($br[0])));
            if(isset($br[1])){ $nuser .= str_replace($replacing,'',ucfirst(strtolower($br[1]))); }
        }
        return substr($nuser,0,19);
    }
    
    //Check if current time is morning, afternoon or evening
    //based on user timezone
    public function timeofday() {
        $actualtime = (int) date("H");
        if($actualtime >= 18) { return 'evening'; }
        if($actualtime >= 12) { return 'afternoon'; }
        return 'morning';
    }
    
    //return an user friendly description of given HTTP status
    //for example 200 will return OK
    //            500 will return Internal Server Error
    //Used to automatically set message on throw new Exception method
    //example: throw new Exception($ut->http_response(500), 500);
    public static function http_response($n) {
        $list = self::$http_message;
        if(isset($list[$n])) {
            return $list[$n];
        } else {
            return "Unknown Error";
        }
    }

    //basic email structure validation
    public static function validateEmail($email) {
        return preg_match("/^([\w\d\._]{4,250})(@)([\w\d\._]{4,250})(\.\w{2,6})$/",$email);
    }
}