<?php
include "config.inc.php";
$nome = isset($_POST['nome'])?$_POST['nome']:"";
$email = isset($_POST['email'])?$_POST['email']:"";
$id = isset($_POST['id'])?$_POST['id']:"";
if($nome != "" && $email != ""){
    $conexao = new PDO(dsn,usuario, senha);
    $sql = "UPDATE aluno
            SET nome = :nome, 
                email = :email
            WHERE id = :id";
    $comando = $conexao->prepare($sql);
    $comando->bindValue(':nome',$nome);
    $comando->bindValue(':email',$email);
    $comando->bindValue(':id',$id);
    if($comando->execute())
        echo "Dados atualizados com sucesso!";
    else
        echo "Erro ao atualizar os dados.";
}else
    echo "Os dados não podem estar em branco...";
?>