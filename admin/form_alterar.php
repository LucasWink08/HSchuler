<?php
include 'conexao_inc.php';

$atividade = null;

// Verifica se o ID foi fornecido via GET
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Busca os dados da atividade no banco de dados
    $sql = "SELECT * FROM atividade WHERE id = :id";
    $comando = $conexao->prepare($sql);
    $comando->bindParam(':id', $id, PDO::PARAM_INT);
    $comando->execute();
    
    $atividade = $comando->fetch(PDO::FETCH_ASSOC);
    
    // Se não encontrar a atividade, redireciona para listar
    if (!$atividade) {
        header('Location: listar.php');
        exit();
    }
} else {
    // Se não há ID, redireciona para listar
    header('Location: listar.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulário de Atividade</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
        }
        form {
            background-color: #f4f4f4;
            padding: 20px;
            border-radius: 5px;
        }
        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }
        input, textarea, select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 3px;
            box-sizing: border-box;
        }
        button {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h1>Formulário de Atividade</h1>
    <form method="POST" action="alterar.php">
        <input type="hidden" id="id" name="id" value="<?php echo htmlspecialchars($atividade['id']); ?>">

        <label for="titulo">Título:</label>
        <input type="text" id="titulo" name="titulo" value="<?php echo htmlspecialchars($atividade['titulo']); ?>" required>

        <label for="descricao">Descrição:</label>
        <textarea id="descricao" name="descricao" rows="4" required><?php echo htmlspecialchars($atividade['descricao']); ?></textarea>

        <label for="data_realizacao">Data de Realização:</label>
        <input type="date" id="data_realizacao" name="data_realizacao" value="<?php echo htmlspecialchars($atividade['data_realizacao']); ?>" required>

        <label for="peso">Peso:</label>
        <input type="number" id="peso" name="peso" step="0.1" value="<?php echo htmlspecialchars($atividade['peso']); ?>" required>

        <label for="disciplina">Disciplina:</label>
        <select id="disciplina" name="disciplina" required>
            <option value="">Selecione uma disciplina</option>
            <option value="Matemática" <?php echo ($atividade['disciplina'] === 'Matemática') ? 'selected' : ''; ?>>Matemática</option>
            <option value="Português" <?php echo ($atividade['disciplina'] === 'Português') ? 'selected' : ''; ?>>Português</option>
            <option value="Inglês" <?php echo ($atividade['disciplina'] === 'Inglês') ? 'selected' : ''; ?>>Inglês</option>
            <option value="História" <?php echo ($atividade['disciplina'] === 'História') ? 'selected' : ''; ?>>História</option>
        </select>

        <button type="submit">Atualizar</button>
    </form>
</body>
</html>

