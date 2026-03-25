<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document title</title>
</head>
<body>
    <?php
     include 'conexao_inc.php';
    $sql = "SELECT * FROM atividade";
    $comando = $conexao->prepare($sql);

    $comando->execute();

    // Listagem dos registros
    $registros = $comando->fetchAll(PDO::FETCH_ASSOC);

    if($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['filtro']) && !empty($_GET['filtro'])) {
        $filtro = '%' . $_GET['filtro'] . '%';
        $sql = "SELECT * FROM atividade WHERE titulo LIKE :filtro OR descricao LIKE :filtro";
        $comando = $conexao->prepare($sql);
        $comando->bindParam(':filtro', $filtro, PDO::PARAM_STR);
        $comando->execute();
        $registros = $comando->fetchAll(PDO::FETCH_ASSOC);
    }
    ?>
     <form action="" method="get">
       <label for="filtro">Filtrar</label>
       <input type="text" id="filtro" name="filtro">
         <button type="submit">Aplicar</button>
     </form>
     
    <table border="1">
   
        <tr>
            <th>Titulo</th>
            <th>Descricao</th>
            <th>data</th>
            <th>peso</th>
            <th>Disciplina</th>
            <th>Ações</th>
        </tr>
        <?php foreach ($registros as $atividade): ?>
            <tr>
                <td><?php echo htmlspecialchars($atividade['titulo']); ?></td>
                <td><?php echo htmlspecialchars($atividade['descricao']); ?></td>
                <td><?php echo htmlspecialchars($atividade['data_realizacao']); ?></td>
                <td><?php echo htmlspecialchars($atividade['peso']); ?></td>
                <td><?php echo htmlspecialchars($atividade['disciplina']); ?></td>
                <td>
                    <a href="form_alterar.php?id=<?php echo urlencode($atividade['id']); ?>">Alterar</a>
                    <a href="excluir.php?id=<?php echo urlencode($atividade['id']); ?>">Excluir</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>