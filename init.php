<?php 

/*set_error_handler(function($code, $message, $file, $line){
    if (0 == error_reporting()){ 
        return; 
    } 
    throw new ErrorException($message, 0, $code, $file, $line); 

}); 
set_exception_handler(function($e){
	var_dump($e->getMessage());
	echo $e->getMessage();
});*/ 		

setlocale(LC_ALL,'pt_BR');

$_GET = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);
$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);            
$_COOKIE  = filter_input_array(INPUT_COOKIE, FILTER_SANITIZE_STRING);            
$_SERVER = filter_input_array(INPUT_SERVER, FILTER_SANITIZE_STRING);            


/*@require 'config/config.prod.php';
error_reporting(E_ALL);
ini_set('display_startup_errors',0);
ini_set('display_errors',0);
*/
@require 'config/config.dev.php';
error_reporting(E_ALL);
ini_set('display_startup_errors',TRUE);
ini_set('display_errors',TRUE);


require "core/pdo.php";
require "core/phpMQTT.php";
require "core/PHPMailer.php";
require "core/SMTP.php";

#if(!session_id()){
#	session_start();
#}

