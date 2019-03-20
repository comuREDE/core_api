<?php

require("init.php");

#echo "<pre>";

principal();

function principal(){
	echo "<h1>inicio - ".date('d/m/Y H:i:s')."</h1>";
	filtroPrimarioAgua();
	#filtroPrimarioLuz();
	#sleep(120);
	echo "<h1>passou pelo primario - ".date('d/m/Y H:i:s')."</h1>";

	filtroSecundario('A');
	#filtroSecundario('E');
	#sleep(60);
	echo "<h1>passou pelo secundario - ".date('d/m/Y H:i:s')."</h1>";

	#alertaSMS('A');
	#alertaSMS('E');
	#echo "<h1>recebeu os sms - ".date('d/m/Y H:i:s')."</h1>";
	#sleep(30);
	#echo "<h1>fim - ".date('d/m/Y H:i:s')."</h1>";

}


function filtroPrimarioAgua(){
	echo "<h3>".__FUNCTION__."</h3>";

	$q="SELECT id, dia_hora,
	DATE_FORMAT(dia_hora,'%Y/%m/%d %H:%i:%s') as data_hora,
	status,created_at, updated_at,
	estado,cep,sensor, day(dia_hora) as dia
	FROM sensores_agua 
	WHERE (status='' OR status IS NULL OR status <> 'T') 
	ORDER BY dia_hora ASC;";

	#echo $q;die;
	$res = (new BD())->query($q);

	#print_r($res);die;

	$loop=false;
	$regs=[];
	$count=count($res);
	for($i=0; $i<$count-2; $i++){
		$atual=$res[$i]['estado'];
		$proximo=$res[$i+1]['estado'];
		$proximo_prox=$res[$i+2]['estado'];
		if($atual==='D'){
			$loop=false;
		}
		$cond1 = ($atual==='L' && $proximo==='L' && $proximo_prox==='L');
		
/*        
		$atual_cep=$res[$i]['cep'];
        $proximo_cep=$res[$i+1]['cep'];
        $proximo_prox_cep=$res[$i+2]['cep'];

        $atual_sensor=$res[$i]['sensor'];
        $proximo_sensor=$res[$i+1]['sensor'];
        $proximo_prox_sensor=$res[$i+2]['sensor'];

        $cond2 = ($atual_cep == $proximo_cep) && ($atual_cep == $proximo_prox_cep);
        $cond3 = ($atual_sensor == $proximo_sensor) && ($atual_sensor == $proximo_prox_sensor);
*/

		#if($cond1 && $cond2 && $cond3){
		if($cond1){
			if(!$loop){
				$regs[]=$res[$i];
			}
			$loop=true;
		}
		#passou no registro
		$post=['id'=>$res[$i]['id'] , 'status'=>'T'];
		$res = (new Model())->setTable('sensores_agua')->upd($post);
		var_dump($res);
	}
	#print_r($regs);die;

	saveTriagem($regs,'A');
	echo "<h1>Triagem Agua ". date('d/m/Y H:m:i')."</h1>";
}



function filtroPrimarioLuz(){
	echo "<h3>".__FUNCTION__."</h3>";

	$sql="SELECT id, dia_hora,
	DATE_FORMAT(dia_hora,'%Y/%m/%d %H:%i:%s') as data_hora,

	status,created_at, updated_at,
	estado,cep,sensor, day(dia_hora) as dia

	FROM sensores_luz 
	WHERE (status='' OR status IS NULL OR status <> 'T') 
	ORDER BY dia_hora ASC;";
	$res = (new BD())->query($sql);

	$loop=false;
	$regs=[];
	$count=count($res);
	for($i=0; $i<$count-2; $i++){
		$atual=$res[$i]['estado'];
		$proximo=$res[$i+1]['estado'];
		$proximo_prox=$res[$i+2]['estado'];
		if($atual==='L'){
			$loop=false;
		}
		$cond1 = ($atual==='D' && $proximo==='D' && $proximo_prox==='D');
		if($cond1){
			if(!$loop){
				$regs[]=$res[$i];
			}
			$loop=true;
		}
		#passou no registro
		$post=['id'=>$res[$i]['id'] , 'status'=>'T'];
		$res = (new Model())->setTable('sensores_luz')->upd($post);
		var_dump($res);
	}
	#print_r($regs);

	saveTriagem($regs,'E');
	echo "<h1>Triagem Luz ". date('d/m/Y H:m:i')."</h1>";
}

function saveTriagem(array $regs,string $tipo){
	echo "<h3>".__FUNCTION__."</h3>";
	$total = count($regs);
	foreach ($regs as $k => $reg) {
		extract($reg);
		$sensores_id = $id;
		$info = compact('sensores_id','data_hora','cep','sensor','tipo');
				
		$res = (new Model())->setTable('triagem')->save($info);
		var_dump($res);
	}
	echo "<h3>$total registros ($tipo) upds em triagem</h3>";
}



function filtroSecundario(string $tipo){
	echo "<h3>".__FUNCTION__."</h3>";

	$sql="SELECT id, 
	DATE_FORMAT(data_hora,'%Y/%m/%d %H:%i:%s') as data_hora,
	DATE_FORMAT(data_hora,'%d/%m/%Y') as diames,
	status,sensores_id,
	cep,sensor
	FROM triagem 
	WHERE (status='' OR status IS NULL OR status <> 'R') 
	AND tipo='$tipo'
	ORDER BY data_hora ASC;";
	$res = (new BD())->query($sql);

	$relats=[];
	$diames = array_column($res, "diames");
	$diames2 = array_unique($diames); #preserva a chave
	$count=count($res);
	for($j=0; $j<$count; $j++){
		if(array_key_exists($j, $diames2)){
			$relats[]=$res[$j];
		}
		$post=['id'=>$res[$j]['id'] , 'status'=>'R'];
		(new Model())->setTable('triagem')->upd($post);
	}

	saveRelatorios($relats,$tipo);
	echo "<h2>Relatorio ". date('d/m/Y H:m:i')."</h2>";
}

function saveRelatorios(array $relats, string $tipo){
	echo "<h3>".__FUNCTION__."</h3>";
	$total = count($relats);
	foreach ($relats as $k => $relat) {
		extract($relat);
		$triagem_id = $id;
		$info = compact('triagem_id','data_hora','cep','sensor','tipo');
				
		$res = (new Model())->setTable('relatorios')->save($info);
		var_dump($res);
	}
	echo "<h3>$total registros upds em relatorios</h3>";
}


function alertaSMS($tipo){
	echo "<h3>".__FUNCTION__."</h3>";
	$sql="SELECT id, 
	DATE_FORMAT(data_hora,'%Y/%m/%d %H:%i:%s') as data_hora,
	cep
	FROM relatorios 
	WHERE (status='' OR status IS NULL OR status <> 'SMS') 
	ORDER BY data_hora ASC;";
	$res = (new BD())->query($sql);

	#print_r($res);
	$count=count($res);
	$ceps1=array_column($res, 'cep');
	$ceps = array_unique($ceps1);
	#print_r($ceps);

	$ceps = implode("','",$ceps);
	$sql="SELECT DISTINCT * FROM cadastros WHERE cep IN ('$ceps');";
	#echo $sql;
	$cadastros = (new BD())->query($sql);	
	#print_r($cadastros);
	$total = count($cadastros);	
	foreach ($cadastros as $cadastro) {
		extract($cadastro);
		$msg="caiu $tipo";
		$ddd = substr($celular, 0,2);
		$celular = substr($celular, 2,9);
		$assunto = "assunto caiu $tipo";

		$header = "header caiua $tipo<br>";
		$texto = "vai la ver agora q --- $tipo";
		$footer = "footer $tipo<br>";
		$msg = $header . $texto . $footer;
		#echo "enviaSMS - $id - $nome $celular<br>";
		if((int) date('H') >= 8 and (int) date('H') < 19){
			enviaSMS($ddd,$celular,$msg,'rapido');
		} else {
			enviaSMS($ddd,$celular,$msg,'fila');
		}
	}

	for($j=0; $j<$count; $j++){
		$post=['id'=>$res[$j]['id'] , 'status'=>'SMS'];
		$res = (new Model())->setTable('relatorios')->upd($post);
		var_dump($res);
	}
	echo "<h1>SMS - $total tentativas SMS ". date('d/m/Y H:m:i')."</h1>";
}


function enviaSMS($ddd,$celular,$msg,$tipo='rapido'){
	#echo "<hr>",$ddd,"<hr>",$celular,"<hr>",$msg,"<br>";
	$campos = [
		'strUsuario' => urlencode('comurede_dev'),
		'strSenha' => urlencode('Comux01$'),
		'intDDD' => urlencode($ddd),
		'intCelular' => urlencode($celular),
		'memMensagem' => urlencode($msg),
		'sem_retorno' => urlencode('sim'),  //Não Altere este Campo
		'sms_marketing' => urlencode('sim')  //Não Altere este Campo
	];

	$urlSMS = 'http://www.phpsms.com.br/sms/';
	$urlSMS .= ($tipo=="rapido")?'envio_sms_rapido.asp' : 'envio_sms_long.asp';
	#$string_campos = http_build_query($campos);
		
	#echo $tipo,'<hr>',$urlSMS,'<hr>';
	$string_campos = '';
	foreach($campos as $name => $valor) :
		$string_campos .= $name.'='.$valor.'&';
	endforeach;
	$string_campos = rtrim($string_campos,'&');
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $urlSMS);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_POST,count($campos));
	curl_setopt($ch, CURLOPT_POSTFIELDS,$string_campos);
	$result = curl_exec($ch);
	echo($result); //Retorna ENVIADO se Enviado com Sucesso!
	curl_close($ch);
	
}



