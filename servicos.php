<?php
require 'init.php';
#echo "<pre>";
#pegar o param cep

#$cep = $_GET['cep'];
$cep="24130400";

$param = $_GET['param'];
switch ($param) {
	case 'estado_agua_agora':
		#$d1="2019/03/15";
		$d1=date('Y/m/d');
		$data = new DateTime($d1);
		$dia = $data->format('d');
		$mes = $data->format('m');
		$ano = $data->format('Y');

		$q="SELECT DISTINCT 
		estado,cep,sensor,
		concat(day(dia_hora),'/',month(dia_hora),'/',year(dia_hora))
		FROM sensores_agua WHERE 
		cep='$cep' 
		AND day(dia_hora)='$dia' 
		AND month(dia_hora)='$mes' 
		AND year(dia_hora)='$ano'
		ORDER BY sensor,dia_hora DESC
		;";
		#ORDER BY dia_hora DESC LIMIT 1
		#echo $q;die;
		$res = (new BD())->query($q);
		#var_dump($res);die;
		if(is_array($res)){
			$status = "D";
			$sensores=[];
			foreach ($res as $reg) {
				extract($reg);# sensor estado
				if(!isset($sensores[$sensor])){
					$sensores[$sensor]=$estado;
				}
			}
			foreach ($sensores as $sensor) {
				if($sensor == "L"){
					$status = "L";
				}
			}
			echo json_encode($status,true);
		} else {
			echo json_encode('Erro',true);
		}
	break;
	
	case 'estado_agua_grafico':
		#$d1="2019/03/15";
		$d1=date('Y/m/d');

		$data1 = new DateTime($d1);
		$data_str_1 = $data1->format('Y/d/m');
		
		$data2 = $data1->modify('-7 day');
		$data_str_2 = $data2->format('Y/d/m');

		$tipo='A';
		$res = montaJSONsemanal($data_str_1,$data_str_2,$cep,$tipo);
		echo json_encode($res,true);
	break;	


	case 'estado_luz_agora':
		#$d1="2019/03/15";
		$d1=date('Y/m/d');
		$data = new DateTime($d1);
		$dia = $data->format('d');
		$mes = $data->format('m');
		$ano = $data->format('Y');

		$q="SELECT DISTINCT 
		estado,cep,sensor,
		concat(day(dia_hora),'/',month(dia_hora),'/',year(dia_hora))
		FROM sensores_luz WHERE 
		cep='$cep' 
		AND day(dia_hora)='$dia' 
		AND month(dia_hora)='$mes' 
		AND year(dia_hora)='$ano'
		ORDER BY sensor,dia_hora DESC
		;";
		#ORDER BY dia_hora DESC LIMIT 1
		#echo $q;die;
		$res = (new BD())->query($q);
		#var_dump($res);die;
		if(is_array($res)){
			$status = "D";
			$sensores=[];
			foreach ($res as $reg) {
				extract($reg);# sensor estado
				if(!isset($sensores[$sensor])){
					$sensores[$sensor]=$estado;
				}
			}
			foreach ($sensores as $sensor) {
				if($sensor == "L"){
					$status = "L";
				}
			}
			echo json_encode($status,true);
		} else {
			echo json_encode('Erro',true);
		}

	break;
	
	case 'estado_luz_grafico':
		#$d1="2019/03/15";
		$d1=date('Y/m/d');

		$data1 = new DateTime($d1);
		$data_str_1 = $data1->format('Y/d/m');
		
		$data2 = $data1->modify('-7 day');
		$data_str_2 = $data2->format('Y/d/m');

		$tipo='E';
		$res = montaJSONsemanal($data_str_1,$data_str_2,$cep,$tipo);
		echo json_encode($res,true);

	break;	

	default:
		echo "Informe um parametro";
	break;
}


# BETWEEN '2019/18/03' AND '2019/11/03'
function montaJSONsemanal($data1,$data2,$cep,$tipo){
	#echo "<pre>";
	$q="SELECT 
	DATE_FORMAT(data_hora,'%Y/%d/%m') as `data`
	
	FROM relatorios 
	WHERE (data_hora BETWEEN '$data1' AND '$data2') 
	AND cep='$cep'
	AND tipo='$tipo'
	ORDER BY data_hora ASC;";

	#echo $q; die;

	$res = (new BD())->query($q);

	$datas = array_column($res, 'data');
	#print_r($datas);

	$d1 = DateTime::createFromFormat('Y/d/m', $data1);
	$d2 = DateTime::createFromFormat('Y/d/m', $data2);

	$diff = $d2->diff($d1)->format("%a");	
	$dias_sem = ['D','S','T','Q','Q','S','S',];

	#echo $diff;
	$json=[];
	$data = DateTime::createFromFormat('Y/d/m', $data1);

	#echo '<hr>',$data1,'<hr>',$data2,'<hr>';
	
	$data->modify('-1 day');

	for($i=0;$i<=$diff;$i++){

		$data->modify('+1 day');
		$data_str = $data->format('Y/d/m');
		$data_str2 = $data->format('d/m');

		#print_r($data_str);
		#print_r($datas);
		#echo "<hr>";
		$json[]=
				['data'=>$data_str,
				 'dia'=> $dias_sem[date('w',strtotime($data_str))] ." ". $data_str2,
				 'caiu'=>in_array($data_str,$datas)?1:0
				 #'caiu'=>in_array($data_str,$datas)?0:1
				]; 	
	}

	#print_r($json);
	return $json;
}
