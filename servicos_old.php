<?php 
require '_header.php'; 

$model = new Model();
#var_dump($model);

if($_POST){

	
	#echo "<pre>";print_r($_POST);

	$user = $model->setTable('users')->save($_POST);
	var_dump($user);
}
	


?>
<html>

	<head>
	<title>comuREDE - Serviços - Localização: Travessa Maurício de Lacerda</title>
		<meta charset="utf-8" name="viewport" content="width=device-width, initial-scale=1.0">
	</head>

	<body bgcolor="808080">
	
	<table align="center" border="1" cellpadding="1" cellspacing="1" style="width: 100%">
		<tr>
			<td style="text-align: center;"  bgcolor="white">
				<b> SERVIÇOS</b></td>
		</tr>
		<td bgcolor="C0C0C0"style="text-align: center; color:C0C0C0">
				xxx</td>
		<tr>
			<td style="text-align: center; background-color:00BFFF">
				<b>TA CAINDO ÁGUA</b></td>
		</tr>
	</table>
	<table align="center" cellpadding="1" cellspacing="1" style="width: 100%">
		
			<tr>
				<td bgcolor="C0C0C0" TD WIDTH=50% style="text-align: center;">Agora </td>
				<td bgcolor="C0C0C0"style="text-align: center;">
					Ultimos 30 dias</td>
			</tr>
			<tr>
				<td>
					<iframe width='220' height='220' frameborder='0'align='left' src='estado_agua_agora.php'></iframe> 
				</td>
			
			</tr>
		
	</table>

<table align="center" border="1" cellpadding="1" cellspacing="1" style="width: 100%">
		
		<tr>
		
		
		</tr>
		<td bgcolor="C0C0C0"style="text-align: center; color:C0C0C0">
				xxx</td>
		<tr>
			<td style="text-align: center; background-color:gold">
				<b>TÁ COM LUZ</b></td>
		</tr>
</table>
<table align="center" cellpadding="1" cellspacing="1" style="width: 100%">
	
		<tr>
			<td bgcolor="C0C0C0" TD WIDTH=50% style="text-align: center;">Agora </td>
			<td bgcolor="C0C0C0"style="text-align: center;">
				Ultimos 30 dias</td>
		</tr>
		<tr>
			<td>
				<iframe width='220' height='220' frameborder='0'align='left' src='estado_luz_agora.php'></iframe> 
			</td>
			<td>
    <iframe frameborder='0' align='center' src='imagens/grafico_30dias.jpg'></iframe>
			</td>
		
		
		</tr>
	
</table>

		 
	
	<table align="center" border="1" cellpadding="1" cellspacing="1" style="width: 100%">
<td style="text-align: center;"  bgcolor="white">
				CADASTRE-SE E RECEBA NOTIFICAÇÕES E RELATÓRIOS AUTOMATICAMENTE!</td>
		</table>	

<table>

    <form method="POST">
        <tr>
		<td><label>Nome:</label></td>
			<td><input type="text" name="nome"></td> 
        </tr>
        		
		<tr>
		<td><label>Celular:</label></td>
			<td><input type="text" name="celular" maxlength=11> </td>
        </tr>
		
		<tr>
		<td><label>Email:</label></td>
		<td><input type="text" name="email"> </td>
			</tr>
		<td><input type="submit" value="Enviar!"> <td>
    </form>
<!-- Fernanda, aqui que vc fará a parada para cadastrar no banco-->
     
	 </table>
	 
	<table align="center" border="1" cellpadding="1" cellspacing="1" style="width: 100%">
 <tr align='center'> <td bgcolor="C0C0C0">    SAIBA MAIS e ajude a manter ou expandir a rede de serviços e acesso a internet em <br>
<a HREF="http://www.facebook.com/comuREDE" TARGET="_blank"> facebook.com/comuREDE </a><b>CURTA E COMPARTILHE </b> Ela continuar depende de você :-).<br>
 </td> </tr> 
</table>

	
	</body>

</html>