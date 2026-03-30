<?php
// CORRIGIDO: session_start() deve vir antes de qualquer uso de $_SESSION
session_start();

$usuario = isset($_POST['usuario']) ? $_POST['usuario'] : "";
$senha = isset($_POST['senha']) ? $_POST['senha'] : "";

include_once "conexao_inc.php";

// CORRIGIDO: a senha NÃO deve ser comparada diretamente no SQL quando se usa password_hash,
// pois o hash é diferente a cada vez. Busca apenas pelo usuário e verifica o hash no PHP.
$sql = "SELECT id, usuario, senha
        FROM aluno 
        WHERE usuario = :usuario";

$conexao = new PDO(dsn, usuario, senha);
$comando = $conexao->prepare($sql);
$comando->bindParam(":usuario", $usuario);
$comando->execute();
$linha = $comando->fetch(PDO::FETCH_ASSOC);

// CORRIGIDO: password_verify compara a senha digitada com o hash salvo no banco
if ($linha && password_verify($senha, $linha['senha'])) {
    $_SESSION['id'] = $linha['id'];
    $_SESSION['nome'] = $linha['usuario']; // CORRIGIDO: coluna 'nome' não existe na query, usar 'usuario'
    header("Location: homepage.php");
    exit();
} else {
    header("Location: login.php?auth_error=Usuário ou senha incorretos!");
    exit();
}
?>
