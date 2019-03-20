<?php 

@require 'config/config.dev.php';
error_reporting(E_ALL);
ini_set('display_startup_errors',TRUE);
ini_set('display_errors',TRUE);

require "core/pdo.php";

if($_POST){
	$name = $_POST['nome'];
	$email = $_POST['email'];
	$celular = $_POST['celular'];
	$cep = $_POST['cep'];


	$cadastro = $_POST; 
	$id = $cadastro->save();

	echo "cadastro incluido $id";
	
} else {
	echo "form nao recebido";

}
