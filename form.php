<?php 

require 'init.php';

$name = $_POST['nome'];
$email = $_POST['email'];
$celular = $_POST['celular'];
$cep = $_POST['cep'];


$cadastro = $_POST; 
$id = $cadastro->save();

echo "cadastro incluido $id";
