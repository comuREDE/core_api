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

function procmsg_luz($topic,$msg){
	# $topic AGUA LUZ
	# $msg (L/D)__12345-Y
    echo "Msg Recebida: ".date("r")."\nTopic:{$topic}\n$msg\n";
    $data = date("Y-m-d H:i:s");
	$info = ['dia_hora'=>$data,'estado'=>$msg];              

	$res = (new Model())->setTable('sensores_luz')->save($info);

	var_dump($res);
}

function procmsg_agua($topic,$msg){
    echo "Msg Recebida: ".date("r")."\nTopic:{$topic}\n$msg\n";
    $data = date("Y-m-d H:i:s");
	$info = ['dia_hora'=>$data,'estado'=>$msg];              

	$res = (new Model())->setTable('sensores_agua')->save($info);

	var_dump($res);
}
