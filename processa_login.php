<?php
$usuario = isset($_POST['usuario'])?$_POST['usuario']:"";
$senha = isset($_POST['senha'])?$_POST['senha']:"";
include_once "conexao_inc.php";
$conexao = new PDO(dsn,usuario,senha);
$sql = "SELECT id,nome 
          FROM aluno 
         WHERE usuario = :usuario
           AND senha = :password_hash(senha)";
$comando = $conexao->prepare($sql);
$comando->bindValue(":usuario",$usuario);
$comando->bindValue(":senha",password_hash($senha));
$comando->execute();
$registro = $comando->fetch();
if ($registro){
    session_start();
    $_SESSION['id'] = $registro['id'];
    $_SESSION['nome'] = $registro['nome'];
    header("location: homepage.php");
}else{
    header("location: login.html?auth_error=Usuário ou senha incorretos!");
}


