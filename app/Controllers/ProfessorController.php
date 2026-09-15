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
        require APP_ROOT . '/resources/views/professor/videos.php';
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
}
