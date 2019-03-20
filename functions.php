<?php

function filtroPrimarioxxxxxxxxxxx(){

    $sql="SELECT id, dia_hora,
    DATE_FORMAT(dia_hora,'%Y/%m/%d %H:%i:%s') as data_hora,
    estado,cep,sensor, day(dia_hora) as dia

    FROM sensores_agua ORDER BY dia_hora ASC;";
    $res = (new BD())->query($sql);
    
    #print_r($res);
    if(!is_array($res)) echo "<h1>erro SQL</h1>";die;

    $loop=false;
    $regs=[];
    $count=count($res);


    for($i=0; $i<$count-2; $i++){
        $atual_estado=$res[$i]['estado'];
        $proximo_estado=$res[$i+1]['estado'];
        $proximo_prox_estado=$res[$i+2]['estado'];

        $atual_cep=$res[$i]['cep'];
        $proximo_cep=$res[$i+1]['cep'];
        $proximo_prox_cep=$res[$i+2]['cep'];

        $atual_sensor=$res[$i]['sensor'];
        $proximo_sensor=$res[$i+1]['sensor'];
        $proximo_prox_sensor=$res[$i+2]['sensor'];

        $cond1 = ($atual_estado==='L' && $proximo_estado==='L' && $proximo_prox_estado==='L');
        $cond2 = ($atual_cep == $proximo_cep) && ($atual_cep == $proximo_prox_cep);
        $cond3 = ($atual_sensor == $proximo_sensor) && ($atual_sensor == $proximo_prox_sensor);

        if($atual_estado==='D'){
            $loop=false;
        }
        if($atual_estado==='L' && $proximo_estado==='L' && $proximo_prox_estado==='L'){
            if(!$loop){
                $regs[]=$res[$i];
            }
            $loop=true;
        }
    }

    print_r($regs);
    return $regs;
}

function dataForm2BD($valor){
    if(preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $valor, $matches)){
      $dia = $matches[1];
      $mes = $matches[2];
      $ano = $matches[3];
      return $ano."-".$mes."-".$dia;
    } else {
      return false;
    }
}

function dataCompleta2BD($valor){
    if(preg_match('/^(\d{2})\/(\d{2})\/(\d{4}) (\d{2}):(\d{2}):(\d{2}))$/', $valor, $matches)){
      $dia = $matches[1];
      $mes = $matches[2];
      $ano = $matches[3];
      $h = $matches[4];
      $m = $matches[5];
      $s = $matches[6];
      return $ano."-".$mes."-".$dia." ".$h.":".$m.":".$s;

    } else {
      return false;
    }
}