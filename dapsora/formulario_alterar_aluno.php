<?php
include "config.inc.php";
$id = isset($_GET['id'])?$_GET['id']:0;
$aluno = array();
if ($id > 0){
    // abrir conexão com o banco
    $conexao = new PDO(dsn, usuario, senha);
    // montar consulta
    $sql = "SELECT * 
              FROM aluno
             WHERE id = :id";
    // prepara consulta
    $comando = $conexao->prepare($sql);
    // enviar parâmetros da consulta
    $comando->bindValue(':id',$id);
    // executar executar consulta
    $comando->execute();
    // listar os registros do banco
    $aluno = $comando->fetch();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Alunos</title>
</head>
<body>
    <form action="atualizar.php" method="post">
        <label for="id">Id:</label>
        <input type="text" readonly name="id" id="id" value="<?=isset($aluno)?$aluno['id']:0?>" >
        <label for="nome">Nome:</label>
        <input type="text" name="nome" id="" value="<?=isset($aluno)?$aluno['nome']:''?>">
        <label for="email">Email:</label>
        <input type="text" name="email" id="" value="<?=isset($aluno)?$aluno['email']:''?>">
        <input type="submit" value="Salvar">
    </form>
</body>
</html>