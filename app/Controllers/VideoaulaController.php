<?php

class VideoaulaController
{
    public function index(): void
    {
        $service = new VideoService();
        $slug = trim((string) ($_GET['area'] ?? ''));
        $areaSelecionada = $slug !== '' ? $service->getArea($slug) : null;

        if ($slug !== '' && $areaSelecionada === null) {
            header('Location: ' . app_route('/videoaulas'));
            exit;
        }

        $areas = $service->getAreas();
        $videos = $areaSelecionada === null ? [] : $service->getVideos($areaSelecionada['slug']);

        require APP_ROOT . '/resources/views/trilha/videoaulas.php';
    }
}
