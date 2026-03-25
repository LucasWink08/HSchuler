<?php
include 'conexao_inc.php';
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo=isset($_POST['titulo']) ? $_POST['titulo'] : '';
    $descricao=isset($_POST['descricao']) ? $_POST['descricao'] : '';
    $data_realizacao=isset($_POST['data_realizacao']) ? $_POST['data_realizacao'] : null;
    $peso=isset($_POST['peso']) ? $_POST['peso'] : '';
    $disciplina=isset($_POST['disciplina']) ? $_POST['disciplina'] : '';
}
 
$sql='INSERT INTO ATIVIDADE (titulo, descricao, data_realizacao, peso, disciplina) VALUES (:titulo, :descricao, :data_realizacao, :peso, :disciplina)';

$comando=$conexao->prepare($sql);

$comando->bindParam(':titulo', $titulo);
$comando->bindParam(':descricao', $descricao);
$comando->bindParam(':data_realizacao',$data_realizacao);
$comando->bindParam(':peso', $peso);
$comando->bindParam(':disciplina', $disciplina);
$comando->execute();
header('Location: listar.php');
?>