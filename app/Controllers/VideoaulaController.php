<?php

class VideoaulaController
{
    public function index(): void
    {
        $service = new VideoService();
        $turmaSelecionada = $this->getTurmaDoAlunoSelecionada();
        $turmaId = (int) $turmaSelecionada['id'];
        $slug = trim((string) ($_GET['area'] ?? ''));
        $areaSelecionada = $slug !== '' ? $service->getArea($slug) : null;

        if ($slug !== '' && $areaSelecionada === null) {
            header('Location: ' . $this->videoaulasRoute($turmaId));
            exit;
        }

        $areas = $service->getAreas($turmaId);
        $videos = $areaSelecionada === null ? [] : $service->getVideos($areaSelecionada['slug'], $turmaId);

        require APP_ROOT . '/resources/views/trilha/videoaulas.php';
    }

    public function assistir(): void
    {
        $turmaSelecionada = $this->getTurmaDoAlunoSelecionada();
        $turmaId = (int) $turmaSelecionada['id'];
        $videoId = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
        if ($videoId === false || $videoId === null || $videoId <= 0) {
            header('Location: ' . $this->videoaulasRoute($turmaId));
            exit;
        }

        $service = new VideoService();
        $video = $service->getVideo((int) $videoId, $turmaId);
        if ($video === null) {
            http_response_code(404);
            echo 'Videoaula não encontrada.';
            return;
        }

        $areaSolicitada = trim((string) ($_GET['area'] ?? ''));
        $areaSelecionada = $areaSolicitada !== '' ? $service->getArea($areaSolicitada) : null;
        $player = $this->montarPlayer((string) $video['url_video']);

        require APP_ROOT . '/resources/views/trilha/assistir_video.php';
    }

    private function getTurmaDoAlunoSelecionada(): array
    {
        $turmaId = filter_var($_GET['turma_id'] ?? null, FILTER_VALIDATE_INT);
        if ($turmaId === false || $turmaId === null) {
            $this->redirecionarParaTurmas();
        }
        if ($turmaId <= 0) {
            $this->notFound('Turma não encontrada.');
        }

        $usuarioId = filter_var($_SESSION['user_id'] ?? null, FILTER_VALIDATE_INT);
        $role = $_SESSION['role'] ?? null;
        if ($usuarioId === false || $usuarioId === null || $usuarioId <= 0) {
            header('Location: ' . app_route('/') . '&access=login-required');
            exit;
        }

        if ($role !== 'aluno') {
            header('Location: ' . app_route('/professor/videos'));
            exit;
        }

        $turmaService = new TurmaService();
        $turma = $turmaService->getTurmaDoAluno((int) $usuarioId, (int) $turmaId);
        if ($turma === null) {
            $this->notFound('Turma não encontrada ou indisponível para sua conta.');
        }

        return $turma;
    }

    private function videoaulasRoute(int $turmaId): string
    {
        return app_route('/videoaulas') . '&turma_id=' . $turmaId;
    }

    private function redirecionarParaTurmas(): never
    {
        header('Location: ' . app_route('/aluno/turma'));
        exit;
    }

    private function notFound(string $mensagem): never
    {
        http_response_code(404);
        echo $mensagem;
        exit;
    }

    /** @return array{tipo: string, url: string, provedor: string} */
    private function montarPlayer(string $url): array
    {
        $partes = parse_url($url);
        $host = strtolower((string) ($partes['host'] ?? ''));
        $host = preg_replace('/^www\\./', '', $host) ?? $host;
        $path = (string) ($partes['path'] ?? '');
        $query = [];
        parse_str((string) ($partes['query'] ?? ''), $query);

        if (in_array($host, ['youtube.com', 'm.youtube.com', 'youtu.be'], true)) {
            $videoId = $host === 'youtu.be'
                ? trim((string) strtok(ltrim($path, '/'), '/'))
                : (string) ($query['v'] ?? '');

            if ($videoId === '' && preg_match('#/(?:embed|shorts)/([A-Za-z0-9_-]+)#', $path, $matches)) {
                $videoId = $matches[1];
            }

            if (preg_match('/^[A-Za-z0-9_-]{6,}$/', $videoId)) {
                return [
                    'tipo' => 'embed',
                    'url' => 'https://www.youtube-nocookie.com/embed/' . rawurlencode($videoId) . '?rel=0&modestbranding=1',
                    'provedor' => 'YouTube',
                ];
            }
        }

        if (in_array($host, ['vimeo.com', 'player.vimeo.com'], true) && preg_match('#/(?:video/)?([0-9]+)#', $path, $matches)) {
            return [
                'tipo' => 'embed',
                'url' => 'https://player.vimeo.com/video/' . $matches[1] . '?dnt=1',
                'provedor' => 'Vimeo',
            ];
        }

        if ($host === 'drive.google.com' && preg_match('#/file/d/([A-Za-z0-9_-]+)#', $path, $matches)) {
            return [
                'tipo' => 'embed',
                'url' => 'https://drive.google.com/file/d/' . rawurlencode($matches[1]) . '/preview',
                'provedor' => 'Google Drive',
            ];
        }

        $extensao = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (in_array($extensao, ['mp4', 'webm', 'ogg'], true)) {
            return ['tipo' => 'arquivo', 'url' => $url, 'provedor' => 'Vídeo'];
        }

        return ['tipo' => 'externo', 'url' => $url, 'provedor' => 'Site externo'];
    }
}
