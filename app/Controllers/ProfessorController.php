<?php

class ProfessorController
{
    public function dashboard(): void
    {
        require APP_ROOT . '/resources/views/professor/dashboard.php';
    }

    public function videos(): void
    {
        require APP_ROOT . '/resources/views/professor/videos.php';
    }

    public function atividades(): void
    {
        require APP_ROOT . '/resources/views/professor/atividades.php';
    }

    public function geradorQuestoes(): void
    {
        require APP_ROOT . '/resources/views/professor/gerador_questoes.php';
    }
}
