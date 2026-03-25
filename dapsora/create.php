<?php
include "config.inc.php";

// pegar informações do formulário
$nome = isset($_POST['nome'])?$_POST['nome']:'';
$email = isset($_POST['email'])?$_POST['email']:'';
$senha = isset($_POST['senha'])?$_POST['senha']:'';
if ($nome != ''){
    // CRUD de Aluno
    // Conectar com o banco de dados
    $conexao = new PDO(dsn, usuario, senha);

    // Create
    // montar sql
    $sql = 'INSERT INTO aluno (nome, email, senha) 
                values(:nome, :email, :senha)';
    // preparar comando para executar no banco de dados
    $comando = $conexao->prepare($sql);
    // informar parametros
    $comando->bindValue(':nome',$nome);
    $comando->bindValue(':email',$email);
    $comando->bindValue(':senha',md5($senha));
    // executar um comando
    if ($comando->execute())
        echo 'Dados inseridos com sucesso!';
    else
        echo 'Erro ao inserir dados no banco.';
}
 else
    echo 'Informe um nome válido';
