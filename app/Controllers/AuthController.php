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

        header('Location: ' . app_route('/'));
        exit;
    }

    public function professorLoginForm(): void
    {
        require APP_ROOT . '/resources/views/auth/login_professor.php';
    }

    public function professorLoginSubmit(): void
    {
        $siape = trim($_POST['siape'] ?? '');
        $senha = $_POST['senha'] ?? '';

        if ($siape === '' || $senha === '') {
            header('Location: ' . app_route('/login/professor') . '&auth_error=SIAPE+ou+senha+inv%C3%A1lidos');
            exit;
        }

        $service = new AuthService();
        $professor = $service->loginProfessor($siape, $senha);

        if ($professor === null) {
            header('Location: ' . app_route('/login/professor') . '&auth_error=SIAPE+ou+senha+incorretos');
            exit;
        }

        session_regenerate_id(true);
        $_SESSION['usuario'] = $professor['nome'];
        $_SESSION['user_id'] = $professor['id'];
        $_SESSION['role'] = $professor['role'];

        header('Location: ' . app_route('/'));
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
        try {
            $success = $service->registerAluno($usuario, $email, $dataNascimento, $senha);
        } catch (PDOException $exception) {
            $message = $exception->getCode() === '23000'
                ? 'E-mail ou usuário já está cadastrado.'
                : 'Não foi possível criar a conta. Tente novamente.';

            header('Location: ' . APP_URL . '/index.php?route=/cadastro/aluno&error=' . rawurlencode($message));
            exit;
        }

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
        $registrationError = $service->getProfessorRegistrationError($siape, $email);

        if ($registrationError !== null) {
            header('Location: ' . app_route('/cadastro/professor') . '&error=' . rawurlencode($registrationError));
            exit;
        }

        try {
            $success = $service->registerProfessor($siape, $nome, $email, $senha);
        } catch (PDOException $exception) {
            $message = $exception->getCode() === '23000'
                ? 'Este SIAPE ou e-mail já está cadastrado.'
                : 'Não foi possível criar a conta. Tente novamente.';

            header('Location: ' . app_route('/cadastro/professor') . '&error=' . rawurlencode($message));
            exit;
        }

        if ($success) {
            header('Location: ' . APP_URL . '/index.php?route=/login&success=Professor+cadastrado');
            exit;
        }

        header('Location: ' . app_route('/cadastro/professor') . '&error=' . rawurlencode('Não foi possível criar a conta. Tente novamente.'));
        exit;
    }

    public function logout(): void
    {
        session_destroy();
        header('Location: ' . APP_URL . '/index.php?route=/login');
        exit;
    }
}
