<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listar Usuários - HSchuler</title>
    <link rel="stylesheet" href="estilo_listar.css">
</head>
<body>
<?php

include 'conexao_inc.php';
$conexao = new PDO(dsn,usuario,senha);
$sql = "SELECT id, usuario, email, data_nasc FROM usuario ORDER BY id DESC";
$comando = $conexao->prepare($sql);
$comando->execute();
$usuarios = $comando->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['filtro']) && !empty($_GET['filtro'])) {
    $filtro = '%' . $_GET['filtro'] . '%';
    $sql = "SELECT id, usuario, email, data_nasc FROM usuarios WHERE usuario LIKE :filtro OR email LIKE :filtro";
    $comando = $conexao->prepare($sql);
    $comando->bindParam(':filtro', $filtro);
    $comando->execute();
    $usuarios = $comando->fetchAll(PDO::FETCH_ASSOC);
}
?>
<nav>
  <ul>
    <li><a href="homepage.php">Home</a></li>
    <li><a href="listar.php">Listar</a></li>
  </ul>
</nav>
<div class="container">
  <h1>Usuários Cadastrados</h1>
  
  <form class="search-form" action="" method="get">
    <input type="text" name="filtro" id="filtro" placeholder="Filtrar por nome ou email..." value="<?php echo htmlspecialchars($_GET['filtro'] ?? ''); ?>">
    <button type="submit">Filtrar</button>
  </form>

  <?php if (empty($usuarios)): ?>
    <div class="no-results">
      Nenhum usuário encontrado.
    </div>
  <?php else: ?>
    <table>
      <tr>
        <th>ID</th>
        <th>Usuário</th>
        <th>Email</th>
        <th>Data Nasc.</th>
        <th>Ações</th>
      </tr>
      <?php foreach ($usuarios as $user): ?>
        <tr>
          <td><?php echo htmlspecialchars($user['id']); ?></td>
          <td><?php echo htmlspecialchars($user['usuario']); ?></td>
          <td><?php echo htmlspecialchars($user['email']); ?></td>
          <td><?php echo date('d/m/Y', strtotime($user['data_nasc'])); ?></td>
          <td class="actions">
            <a href="#" class="edit">Editar</a>
            <a href="#" class="delete">Excluir</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>
  <?php endif; ?>
</div>
</body>
</html>
