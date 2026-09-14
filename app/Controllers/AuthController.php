<?php

class AuthController
{
    public function loginForm(): void
    {
        require APP_ROOT . '/resources/views/auth/login.php';
    }

    public function loginSubmit(): void
    {
        $usuario = trim($_POST['usuario'] ?? '');
        $senha = $_POST['senha'] ?? '';

        if ($usuario === '' || $senha === '') {
            header('Location: ' . APP_URL . '/index.php?route=/login&auth_error=Usuário+ou+senha+inválidos');
            exit;
        }

        $service = new AuthService();
        $user = $service->login($usuario, $senha);

        if ($user === null) {
            header('Location: ' . APP_URL . '/index.php?route=/login&auth_error=Usuário+ou+senha+incorretos');
            exit;
        }

        $_SESSION['usuario'] = $user['usuario'];
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        header('Location: ' . APP_URL . '/index.php?route=/aluno/dashboard');
        exit;
    }

    public function registerAlunoSubmit(): void
    {
        $usuario = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $dataNascimento = trim($_POST['data_nascimento'] ?? '');
        $senha = $_POST['senha'] ?? '';
        $confirma = $_POST['confirma_senha'] ?? '';

        if ($usuario === '' || $email === '' || $senha === '' || $senha !== $confirma) {
            header('Location: ' . APP_URL . '/index.php?route=/cadastro/aluno&error=Dados+inválidos');
            exit;
        }

        $service = new AuthService();
        $success = $service->registerAluno($usuario, $email, $dataNascimento, $senha);

        if ($success) {
            header('Location: ' . APP_URL . '/index.php?route=/login&success=Cadastro+realizado');
            exit;
        }

        header('Location: ' . APP_URL . '/index.php?route=/cadastro/aluno&error=Email+ou+usuário+já+existem');
        exit;
    }

    public function registerProfessorSubmit(): void
    {
        $siape = trim($_POST['siape'] ?? '');
        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';
        $confirma = $_POST['confirma_senha'] ?? '';

        if ($siape === '' || $nome === '' || $email === '' || $senha === '' || $senha !== $confirma) {
            header('Location: ' . APP_URL . '/index.php?route=/cadastro/professor&error=Dados+inválidos');
            exit;
        }

        $service = new AuthService();
        $success = $service->registerProfessor($siape, $nome, $email, $senha);

        if ($success) {
            header('Location: ' . APP_URL . '/index.php?route=/login&success=Professor+cadastrado');
            exit;
        }

        header('Location: ' . APP_URL . '/index.php?route=/cadastro/professor&error=Dados+inválidos');
        exit;
    }

    public function logout(): void
    {
        session_destroy();
        header('Location: ' . APP_URL . '/index.php?route=/login');
        exit;
    }
}
