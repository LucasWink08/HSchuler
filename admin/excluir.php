<?php
include 'conexao_inc.php';
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Monta consulta SQL para exclusão
    $sql = "DELETE FROM atividade WHERE id = :id";
    $comando = $conexao->prepare($sql);
    $comando->bindParam(':id', $id, PDO::PARAM_INT);

    // Executa consulta
    if ($comando->execute()) {
        header('Location: listar.php');
        exit();
    } else {
        echo "Erro ao excluir registro.";
    }
} else {
    echo "ID do registro não fornecido.";
}




?>