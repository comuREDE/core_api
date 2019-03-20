<?php
require 'init.php';
#echo "<pre>";
#pegar o param cep
#$cep = $_GET['cep'];


$param = $_GET['param'];
switch ($param) {
	case 'estado_agua_agora':
		$sql="SELECT * FROM sensores_agua ORDER BY dia_hora DESC LIMIT 1;";
		$res = (new BD())->query($sql);
		extract($res[0]);
		echo json_encode($estado,true);
	break;
	
	case 'estado_agua_grafico':
		$d1="2019/03/15";
		#$d1=date('Y/m/d');

		$data1 = new DateTime($d1);
		$data_str_1 = $data1->format('Y/d/m');
		
		$data2 = $data1->modify('-7 day');
		$data_str_2 = $data2->format('Y/d/m');

		$cep="24130";
		$tipo='A';
		$res = montaJSONsemanal($data_str_1,$data_str_2,$cep,$tipo);
		echo json_encode($res,true);
	break;	


	case 'estado_luz_agora':
		$sql="SELECT * FROM sensores_luz ORDER BY dia_hora DESC LIMIT 1;";
		$res = (new BD())->query($sql);
		extract($res[0]);
		echo json_encode($estado,true);
	break;
	
	case 'estado_luz_grafico':
		$d1="2018/09/10";
		#$d1=date('Y/m/d');

		$data1 = new DateTime($d1);
		$data_str_1 = $data1->format('Y/m/d');
		
		$data2 = $data1->modify('-7 day');
		$data_str_2 = $data2->format('Y/m/d');

		$cep="24130";
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

	#print_r($res);
	#$data1_formatado_php = preg_replace('#(\d{4})/(\d{1,2})/(\d{1,2})#', '$3/$2/$1', $data1);
	#$data2_formatado_php = preg_replace('#(\d{4})/(\d{1,2})/(\d{1,2})#', '$3/$2/$1', $data2);

	$datas = array_column($res, 'data');
	#print_r($datas);

	$d1 = DateTime::createFromFormat('Y/d/m', $data1);
	$d2 = DateTime::createFromFormat('Y/d/m', $data2);
	#$d2 = DateTime($data2);

	$diff = $d2->diff($d1)->format("%a");	
	$dias_sem = ['D','S','T','Q','Q','S','S',];

	#echo $diff;
	$json=[];
	$data = DateTime::createFromFormat('Y/d/m', $data1);
	#$data = new DateTime($data1);
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
