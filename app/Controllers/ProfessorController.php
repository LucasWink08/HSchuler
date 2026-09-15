<?php

class ProfessorController
{
    public function dashboard(): void
    {
        $this->requireProfessor();
        header('Location: ' . app_route('/'));
        exit;
    }

    public function videos(): void
    {
        $this->requireProfessor();
        $service = new VideoService();
        $areas = $service->getAreas();
        $success = trim((string) ($_GET['success'] ?? ''));
        $error = trim((string) ($_GET['error'] ?? ''));
        require APP_ROOT . '/resources/views/professor/videos.php';
    }

    public function videoForm(): void
    {
        $this->requireProfessor();
        $service = new VideoService();
        $area = $service->getArea((string) ($_GET['area'] ?? ''));

        if ($area === null || $area['modulo_id'] === null) {
            header('Location: ' . app_route('/professor/videos') . '&error=' . rawurlencode('Conteúdo indisponível para publicação.'));
            exit;
        }

        $_SESSION['video_form_token'] = bin2hex(random_bytes(32));
        require APP_ROOT . '/resources/views/professor/cadastro_video.php';
    }

    public function saveVideo(): void
    {
        $this->requireProfessor();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . app_route('/professor/videos'));
            exit;
        }

        $slug = trim((string) ($_POST['area'] ?? ''));
        $token = (string) ($_POST['token'] ?? '');
        $sessionToken = (string) ($_SESSION['video_form_token'] ?? '');

        if ($sessionToken === '' || !hash_equals($sessionToken, $token)) {
            header('Location: ' . app_route('/professor/videos') . '&error=' . rawurlencode('Não foi possível validar o formulário. Tente novamente.'));
            exit;
        }

        $titulo = trim((string) ($_POST['titulo'] ?? ''));
        $descricao = trim((string) ($_POST['descricao'] ?? ''));
        $url = trim((string) ($_POST['url_video'] ?? ''));
        $urlParts = filter_var($url, FILTER_VALIDATE_URL) ? parse_url($url) : null;
        $urlValida = is_array($urlParts) && in_array(strtolower((string) ($urlParts['scheme'] ?? '')), ['http', 'https'], true);
        $tituloValido = $titulo !== '' && $this->stringLength($titulo) <= 150;
        $descricaoValida = $this->stringLength($descricao) <= 2000;

        if (!$tituloValido || !$descricaoValida || !$urlValida) {
            header('Location: ' . app_route('/professor/video/cadastro') . '&area=' . rawurlencode($slug) . '&error=' . rawurlencode('Preencha um título e uma URL válida. A descrição pode ter até 2.000 caracteres.'));
            exit;
        }

        $service = new VideoService();
        $area = $service->getArea($slug);

        if ($area === null || $area['modulo_id'] === null) {
            header('Location: ' . app_route('/professor/videos') . '&error=' . rawurlencode('Conteúdo indisponível para publicação.'));
            exit;
        }

        try {
            $saved = $service->createVideo((int) $_SESSION['user_id'], $slug, $titulo, $descricao, $url);
        } catch (PDOException $exception) {
            $saved = false;
        }

        if (!$saved) {
            header('Location: ' . app_route('/professor/video/cadastro') . '&area=' . rawurlencode($slug) . '&error=' . rawurlencode('Não foi possível publicar a videoaula. Tente novamente.'));
            exit;
        }

        unset($_SESSION['video_form_token']);
        header('Location: ' . app_route('/professor/videos') . '&success=' . rawurlencode('Videoaula publicada com sucesso.'));
        exit;
    }

    public function atividades(): void
    {
        $this->requireProfessor();
        require APP_ROOT . '/resources/views/professor/atividades.php';
    }

    public function geradorQuestoes(): void
    {
        $this->requireProfessor();
        require APP_ROOT . '/resources/views/professor/gerador_questoes.php';
    }

    private function requireProfessor(): void
    {
        $professorId = filter_var($_SESSION['user_id'] ?? null, FILTER_VALIDATE_INT);
        if (($_SESSION['role'] ?? null) === 'professor' && $professorId !== false && $professorId !== null && $professorId > 0) {
            return;
        }

        header('Location: ' . app_route('/') . '&access=login-required');
        exit;
    }

    private function stringLength(string $value): int
    {
        return function_exists('mb_strlen') ? mb_strlen($value, 'UTF-8') : strlen($value);
    }
}
