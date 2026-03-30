<?php
session_start();
include 'conexao_inc.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $usuario = trim($_POST['usuario'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $data_nasc = trim($_POST['data_nascimento'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $confirma_senha = $_POST['confirma_senha'] ?? '';

    if ($senha == $confirma_senha) {

        // CORRIGIDO: era password_verify (que serve para VERIFICAR), deve ser password_hash (para CRIAR o hash)
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

        $conexao = new PDO(dsn, usuario, senha);

        $sql = "INSERT INTO ALUNO (usuario, email, data_nasc, senha) VALUES (:usuario, :email, :data_nasc, :senha_hash)";

        $comando = $conexao->prepare($sql);

        $comando->bindParam(':usuario', $usuario);
        $comando->bindParam(':email', $email);
        $comando->bindParam(':data_nasc', $data_nasc);
        $comando->bindParam(':senha_hash', $senha_hash);

        // CORRIGIDO: execute() estava sendo chamado duas vezes (uma vez sem verificação e outra no if)
        if ($comando->execute()) {
            header('Location: homepage.php?success=1');
        } else {
            header('Location: cadastro.php?error=1');
        }
        exit();

    } else {
        header('Location: cadastro.php?error=senhas_diferentes');
        exit();
    }
}
?>
