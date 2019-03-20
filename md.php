<?php 
require 'init.php';

if($_POST){
  #var_dump($_POST);
  extract($_POST);
  if(!preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $data1, $matches)){
    echo "<h3>erro em data1</h3>";
  }
  
  if(!preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $data2, $matches)){
    echo "<h3>erro em data2</h3>";
  }

  if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
    echo "<h3>email invalido</h3>";
  } 
  

  if(montaEnviaEmail($email,$data1,$data2,$cep,$tipo)){
    echo "<h1>email enviado OKKKKK</h1>";
  } else {
    echo "<h1>email com erro</h1>";
  }
}

$data1 = new DateTime(date('Y/m/d'));
$data_str_1 = $data1->format('d/m/Y');

$data2 = $data1->modify('-7 day');
$data_str_2 = $data2->format('d/m/Y');


function getCepByEmail(string $email){
  $res = (new Model())->setTable('cadastros')->getOneBy('email',$email);
  echo $res['cep'];
}


function montaEnviaEmail(string $email,string $data1,string $data2, string $cep,string $tipo='A'){
  #$data1="2018/09/03";
  #$data2="2018/09/23";
  #$tipo='A';
  $header = "header caiua a<br>";
  $texto = montaRelatorioEmail($data1,$data2,$cep,$tipo);
  $footer = "footer caiu a<br>";
  $msg = $header . $texto . $footer;
  $assunto = "email caiu agua";
  $nome = "usuario kaiu ag";
  return enviaEmail(GMAIL,"caiu agua nome",SENHA,$email,$nome,$assunto,$msg);

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
  $mail->Username   = $emailDeOrigem;     // GMAIL username
  $mail->Password   = $senha;               // GMAIL password

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

################# imagens das torneiras


?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <title>Material Design Bootstrap</title>

  <link href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" rel="stylesheet">
  <link href="assets/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/mdb.min.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
</head>

<body class="#7b1fa2 purple darken-2">


<div class="container ">
  
  <br>
  <!-- Card deck -->
  <div class="card-deck">

    <div class="card mb-4 #7b1fa2 purple darken-2">
        <h4 class="card-title text-default ">Ta caindo agua</h4>
        <div class="card-body #7b1fa2 purple darken-2 text-center">
          <img id="img_agua">
        </div>
        <div class="card-body #e1bee7 purple lighten-5">
          <canvas id="lineChart"></canvas>
        </div>
    </div>

    <div class="card mb-4">

      <h4 class="card-title">Ta caindo ee</h4>
        <div class="card-body #7b1fa2 purple darken-2 text-center">
          <img id="img_luz">
        </div>
        <div class="card-body #e1bee7 purple lighten-5">
          <canvas id="lineChart2"></canvas>
        </div>

    </div>

  </div>
  <!-- Card deck -->
  <hr>
  <div class="row">
    <div class="col-md-12">
        <form method="POST">
          <input type="hidden" name="cep" value="24130">

          <div class="row">

            <!--Grid column-->
            <div class="col-md-6 mb-4">

              <div class="md-form">
                <input type="text" id="data1" name="data1" value="<?=$data_str_1?>" class="form-control">
              </div>

            </div>

            <div class="col-md-6 mb-4">

              <div class="md-form">
                <input type="text" id="data2" name="data2" value="<?=$data_str_2?>" class="form-control">
              </div>

            </div>
            <!--Grid column-->

          </div>


          <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" class="custom-control-input" value="A" name="tipo">
            <label class="custom-control-label" for="defaultInline1">Agua</label>
          </div>
          <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" class="custom-control-input" value="E" name="tipo">
            <label class="custom-control-label" for="defaultInline2">Luz</label>
          </div>


          <div class="md-form input-group mb-3">
            <input type="text" name="email" class="form-control" placeholder="email" aria-label="email" aria-describedby="email">
            <div class="input-group-append">
              <button class="btn btn-md btn-secondary m-0 px-3 waves-effect waves-light">Enviar</button>
            </div>
          </div>



        </form>
      
    </div>
  </div>

</div>



  <script type="text/javascript" src="assets/js/jquery-3.3.1.min.js"></script>
  <script type="text/javascript" src="assets/js/popper.min.js"></script>
  <script type="text/javascript" src="assets/js/bootstrap.min.js"></script>
  <script type="text/javascript" src="assets/js/mdb.js"></script>

<script>


$(function () {


    /////////////////////////////// IMG - AGUA ////////////////////////////////
    $.ajax({
        url:'servicos.php?param=estado_agua_agora',
        dataType:'JSON',
        type:'GET',
        success: function(data){
          alert(data);
          if(data==='D'){
            $('#img_agua').attr("src", 'assets/img/Icone-torneira-2.svg');
          } else {
            $('#img_agua').attr("src", 'assets/img/Icone-torneira.svg');
          } 

        }//success
    
    });

    /////////////////////////////// IMG - LUZ ////////////////////////////////
    $.ajax({
        url:'servicos.php?param=estado_luz_agora',
        dataType:'JSON',
        type:'GET',
        success: function(data){
          alert(data);
          if(data==='D'){
            $('#img_luz').attr("src", 'assets/img/Icone-Lampada-2.svg');
          } else {
            $('#img_luz').attr("src", 'assets/img/Icone-Lampada.svg');
          } 

        }//success
    
    });

    /////////////////////////////// AGUA ////////////////////////////////
    $.ajax({
        url:'servicos.php?param=estado_agua_grafico',
        dataType:'JSON',
        type:'GET',
        success: function(data){

            var dias=[];
            var caidas=[];

            for(i=0; i< data.length; i++){
                for(x in data[i]){
                    if(x === 'dia'){
                        dias.push(data[i]['dia']);
                    }
                    if(x === 'caiu'){
                        caidas.push(data[i]['caiu']);
                    }
                }
            }


            var ctxL = document.getElementById("lineChart").getContext('2d');
            var myLineChart = new Chart(ctxL, {
              type: 'line',
              data: {
                labels: dias,
                datasets: [{
                    label: "Caiu Agua",
                    data: caidas,
                    backgroundColor: [
                      'rgba(105, 0, 132, .2)',
                    ],
                    borderColor: [
                      'rgba(200, 99, 132, .7)',
                    ],
                    lineTension: 0,
                    borderWidth: 3
                  }

                ]
              },
              options: {
                responsive: true
              }
            });

        }//success
    
    });


    /////////////////////////////// LUZ ////////////////////////////////
    $.ajax({
        url:'servicos.php?param=estado_luz_grafico',
        dataType:'JSON',
        type:'GET',
        success: function(data){

            var dias=[];
            var caidas=[];

            for(i=0; i< data.length; i++){
                for(x in data[i]){
                    if(x === 'dia'){
                        dias.push(data[i]['dia']);
                    }
                    if(x === 'caiu'){
                        caidas.push(data[i]['caiu']);
                    }
                }
            }


            var ctxL = document.getElementById("lineChart2").getContext('2d');
            var myLineChart = new Chart(ctxL, {
              type: 'line',
              data: {
                labels: dias,
                datasets: [{
                    label: "Caiu Luz",
                    data: caidas,
                    backgroundColor: [
                      'rgba(105, 0, 132, .2)',
                    ],
                    borderColor: [
                      'rgba(200, 99, 132, .7)',
                    ],
                    lineTension: 0,
                    borderWidth: 3
                  }

                ]
              },
              options: {
                responsive: true
              }
            });

        }//success
    
    });



});






</script>

</body>

</html>
