<?php
@require 'config.dev.php';
require("pdo.php");
require("PHPMailer.php");
require("SMTP.php");

	echo "<pre>";

	$sql="SELECT no_evento, dia_hora,
	DATE_FORMAT(dia_hora,'%Y/%m/%d %H:%i:%s') as data_hora,

	estado,localizacao,sensor, day(dia_hora) as dia

	FROM sensores_luz ORDER BY dia_hora ASC;";
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
		if($atual==='D' && $proximo==='D' && $proximo_prox==='D'){
			if(!$loop){
				$regs[]=$res[$i];
			}
			$loop=true;
		}
	}

	echo "<hr>";
	
	$plots=[];
	$relats=[];
	$j=1;
	$count=count($regs);
	for($i=0; $i<$count; $i++){
		extract($regs[$i]); #no_evento dia_hora estado localizacao sensor
		echo $j,':',$no_evento,'-',$dia_hora,'-<b>','|',$dia,'|',$estado,'</b>-',$localizacao,'-',$sensor,'-',$data_hora,'<br>';
		if(!array_key_exists($dia, $plots)){
			$plots[$dia]=$regs[$i]['dia_hora'];
			$relats[$dia]=$regs[$i];
		}
		$j++;
	}

	print_r($plots);

	$charts=[];
	for($z=2;$z<31;$z++){
		$charts[$z]=array_key_exists($z, $plots)?$relats[$z]:null;
		#$charts[$z]=$plots[$z];
	}

	echo "<hr>";
	echo "<hr>";
	#print_r($relats);die;

	foreach ($relats as $key => $chart) {
		
		if(is_array($chart)){
			extract($chart);
			#$dia_hora = $data_hora;
			$cep = $localizacao;
			$tipo='E';
			salvaRelatorio($data_hora, $cep, $sensor, $tipo);
		} else {
			echo "Dia $key sem dados<br>";
		}
	}


	echo "<hr>";
	echo json_encode($charts,true);
	#echo "<hr>";die;



	$data1="2018/09/03";
	$data2="2018/09/23";
	$sql="SELECT DISTINCT cep FROM relatorios 
	WHERE data_hora BETWEEN '$data1' AND '$data2'
	AND tipo='E'
	;";
	$res = (new BD())->query($sql);	
	$ceps = array_column($res, 'cep');



	$ceps = implode(',',$ceps);

	$sql="SELECT * FROM cadastros WHERE cep IN ('$ceps');";
	$cadastros = (new BD())->query($sql);	
	#print_r($cadastros);die;
	foreach ($cadastros as $cadastro) {
		extract($cadastro);
		$msg="caiu agua";
		$ddd = substr($celular, 0,2);
		$celular = substr($celular, 3,9);
		$assunto = "assunto caiu agua";
		

		$data1="2018/09/03";
		$data2="2018/09/23";
		$tipo='A';
		$header = "header caiua energia<br>";
		$texto = montaRelatorioEmail($data1,$data2,$cep,$tipo);
		$footer = "footer caiu energia<br>";
		$msg = $header . $texto . $footer;
		#echo $msg;die;

		echo enviaEmail(GMAIL,"caiu ee nome",SENHA,$email,$nome,$assunto,$msg);
		enviaSMS($ddd,$celular,$msg);                                                                      
	}


	function montaRelatorioEmail($data1,$data2,$cep,$tipo='A'){

		$sql="SELECT DISTINCT data_hora FROM relatorios WHERE data_hora BETWEEN '$data1' AND '$data2';";
		$res = (new BD())->query($sql);
		$dias = array_column($res, "data_hora");
		$dias_sem = ['DOM','SEG','TER','QUA','QUI','SEX','SAB',];

		$msg = "";
		foreach ($dias as $dia) {
			$dia_num = date('w',strtotime($dia));
			$msg .=  $dias_sem[$dia_num] . " - " . $dia . "<br>"; 
		}
		return $msg;
	}


function salvaRelatorio($data_hora, $localizacao, $sensor, $tipo){
	#tipo - A agua - E energia.

	$info = [
				'data_hora'=>$data_hora,
				'cep'=>$localizacao,
				'sensor'=>$sensor,
				'tipo'=>$tipo,
			];              

	$res = (new Model())->setTable('relatorios')->save($info);

	var_dump($res);

}



##########################################################
function enviaSMS($ddd,$celular,$msg){
	$campos = array(
		'strUsuario' => urlencode('comurede_dev'),
		'strSenha' => urlencode('Comux01$'),
		'intDDD' => urlencode($ddd),
		'intCelular' => urlencode($celular),
		'memMensagem' => urlencode($msg),
		'sem_retorno' => urlencode('sim'),  //Não Altere este Campo
		'sms_marketing' => urlencode('sim')  //Não Altere este Campo
	);

	$urlSMS = 'http://www.phpsms.com.br/sms/envio_sms_rapido.asp';
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




function enviaEmail($emailDeOrigem,$nomeDeOrigem,$senha,$emailDeDestino,$nomeDeDestino,$assunto,$msg){
	$mail = new PHPMailer();
	// Define os dados do servidor e tipo de conexão
	//$mail->SMTPDebug  = 2;
	//$mail->SMTPAuth = true; // Usa autenticação SMTP? (opcional)
	//$mail->Username = 'seumail@dominio.net'; // Usuário do servidor SMTP
	//$mail->Password = 'senha'; // Senha do servidor SMTP

	// Config Gmail
	$mail->IsSMTP(); // Define que a mensagem será SMTP
	$mail->SMTPAuth   = true;                  // enable SMTP authentication
	$mail->SMTPSecure = "tls";                 // sets the prefix to the servier
	$mail->Host       = "smtp.gmail.com";      // sets GMAIL as the SMTP server
	$mail->Port       = 587;                   // set the SMTP port for the GMAIL server
	$mail->Username   = $emailDeOrigem;  		// GMAIL username
	$mail->Password   = $senha;            		// GMAIL password

	// Define o remetente
	$mail->SetFrom($emailDeOrigem, $nomeDeOrigem);
	$mail->AddReplyTo($emailDeOrigem, $nomeDeOrigem);

	// Define os destinatário(s)
	$mail->AddAddress($emailDeDestino, $nomeDeOrigem);
	//$mail->AddAddress('ciclano@site.net');
	//$mail->AddCC('ciclano@site.net', 'Ciclano'); // Copia
	//$mail->AddBCC('fulano@dominio.com.br', 'Fulano da Silva'); // Cópia Oculta

	// Define os dados técnicos da Mensagem
	$mail->ContentType = 'text/plain';
	#$mail->IsHTML(true);
	$mail->CharSet = 'UTF-8'; // Charset da mensagem (opcional)

	// Define a mensagem (Texto e Assunto)
	$mail->Subject  = $assunto; // Assunto da mensagem
	$mail->Body = $msg;
	$mail->AltBody = $msg; #texto PURO

	// Define os anexos (opcional)
	//$mail->AddAttachment("c:/temp/documento.pdf", "novo_nome.pdf");  // Insere um anexo
	$emailEnviado = $mail->Send();
	// Limpa os destinatários e os anexos
	$mail->ClearAllRecipients();
	#$mail->ClearAttachments();
  if (!$emailEnviado) {
      $m= "Informações do erro: <pre>" . print_r($mail->ErrorInfo) ."</pre>";
  		echo "Não foi possível enviar o e-mail",$m;
  		return false;
  }
	return true; #booleano
}
