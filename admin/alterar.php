<?php
include 'conexao_inc.php';

$titulo = isset($_POST['titulo']) ? $_POST['titulo'] : '';
$descricao = isset($_POST['descricao']) ? $_POST['descricao'] : '';
$data_realizacao = isset($_POST['data_realizacao']) ? $_POST['data_realizacao'] : '';
$peso = isset($_POST['peso']) ? $_POST['peso'] : '';
$disciplina = isset($_POST['disciplina']) ? $_POST['disciplina'] : '';
$id = isset($_POST['id']) ? $_POST['id'] : '';

if($id > 0){

    $sql = "UPDATE atividade 
            SET titulo = :titulo,
                descricao = :descricao,
                data_realizacao = :data_realizacao,
                peso = :peso,
                disciplina = :disciplina
            WHERE id = :id";

    $comando = $conexao->prepare($sql);

    $comando->bindValue(':titulo', $titulo);
    $comando->bindValue(':descricao', $descricao);
    $comando->bindValue(':data_realizacao', $data_realizacao);
    $comando->bindValue(':peso', $peso);
    $comando->bindValue(':disciplina', $disciplina);
    $comando->bindValue(':id', $id);

    if($comando->execute()){
        header("Location: listar.php");
    } else {
        echo "Erro ao atualizar registro";
    }

} else {
    header("Location: listar.php");
}
?>