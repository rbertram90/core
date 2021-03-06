<?php
namespace rbwebdesigns\core;

class AppSecurity {

public function __construct() {

}

/**
    Generate a secure key that will be stored as a session variable when submitting
    a form to prevent cross site scripting
**/
public function generateSecureKey() {
    $random = $this->changeBase(mt_rand(1000, 9999), 43); // Generate four random numbers and convert to different bases
    $random2 = $this->changeBase(mt_rand(1000, 9999), 61);
    $random3 = $this->changeBase(mt_rand(1000, 9999), 52);
    $random4 = $this->changeBase(mt_rand(1000, 9999), 37);
    $random5 = $this->changeBase(mt_rand(1000, 9999), mt_rand(16, 60)); // and two in random bases for good measure
    $random6 = $this->changeBase(mt_rand(1000, 9999), mt_rand(16, 60));
    $now = $this->changeBase(time() - mt_rand(1000, 9999), 17);
    $key = $random.$random5.$random2.$now.$random3.$random4.$random6; // Concatenate the four strings
    return base64_encode($key); // Base64 encode it
}

private function changeBase($number, $base=16) {
    $res = '';
    $start = floor($number / $base);
    $end = ($number % $base);
    $characters = array('0','1','2','3','4','5','6','7','8','9','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','=','/');
    if($start > $base - 1) $res = $this->changeBase($start, $base);
    else $res = $characters[$start];
    return $res.$characters[$end];
}

public function get_client_ip() {
    $ipaddress = '';
    if($_SERVER['HTTP_CLIENT_IP'])
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if($_SERVER['HTTP_X_FORWARDED_FOR'])
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if($_SERVER['HTTP_X_FORWARDED'])
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if($_SERVER['HTTP_FORWARDED_FOR'])
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if($_SERVER['HTTP_FORWARDED'])
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if($_SERVER['REMOTE_ADDR'])
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    
    return $ipaddress;
}
}
