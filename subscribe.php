<?php
require 'init.php';

$mqtt = new phpMQTT("10.62.63.50", 1883, "phpMQTT"); //Change client name to something unique
if(!$mqtt->connect()){
    echo "Erro ao conectar MQTT";die;
}
$topics['AGUA'] = array("qos"=>0, "function"=>"procmsg_agua");
$topics['LUZ'] = array("qos"=>0, "function"=>"procmsg_luz");

$mqtt->subscribe($topics,0);

while($mqtt->proc()){

	#if($mqtt->delay > 10 ){
	#	sms();
	#}

}
$mqtt->close();


function processaTopico($topic){
	# $topic AGUA LUZ
	#1 $msg (L/D)__12345-Y
	#2 $msg (L/D)__12345789-XXXX
	if(preg_match("/^(L|D)__(\d{8})-(\d{1,2})$/",$topic,$matches)){
		$estado = $matches[1];
		$cep = $matches[2];
		$sensor = (int) $matches[3];
		return compact("estado","cep","sensor");
	} 
	return null;
}

function procmsg_luz($topic,$msg){

    echo "Msg Recebida: ".date("r")."\nTopic:{$topic}\n$msg\n";
    $data = date("Y-m-d H:i:s");
	$dados = processaTopico($msg);#"estado","cep","sensor"
	extract($dados);#$estado $cep $sensor
	$info = ['dia_hora'=>$data,'estado'=>$estado,'cep'=>$cep,'sensor'=>$sensor,];              

	$res = (new Model())->setTable('sensores_luz')->save($info);

	var_dump($res);
}

function procmsg_agua($topic,$msg){
    echo "Msg Recebida: ".date("r")."\nTopic:{$topic}\n$msg\n";
    $data = date("Y-m-d H:i:s");
	$dados = processaTopico($msg);#"estado","cep","sensor"
	extract($dados);#$estado $cep $sensor
	$info = ['dia_hora'=>$data,'estado'=>$estado,'cep'=>$cep,'sensor'=>$sensor,];              

	$res = (new Model())->setTable('sensores_agua')->save($info);

	var_dump($res);
}
