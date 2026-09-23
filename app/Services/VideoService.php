<?php

class VideoService
{
    private const AREAS = [
        'potenciacao' => ['titulo' => 'Potenciação', 'icone' => 'x²', 'nivel' => 'Iniciante', 'descricao' => 'Bases, expoentes e propriedades das potências.'],
        'fracoes-algebricas' => ['titulo' => 'Frações algébricas', 'icone' => 'x/y', 'nivel' => 'Intermediário', 'descricao' => 'Simplificação e operações com expressões algébricas.'],
        'produtos-notaveis' => ['titulo' => 'Produtos notáveis', 'icone' => '(a+b)²', 'nivel' => 'Intermediário', 'descricao' => 'Padrões, fórmulas e desenvolvimento de expressões.'],
        'fatoracao' => ['titulo' => 'Fatoração', 'icone' => '(x)', 'nivel' => 'Avançado', 'descricao' => 'Fator comum, agrupamento e casos notáveis.'],
        'equacoes' => ['titulo' => 'Equações', 'icone' => 'x = ?', 'nivel' => 'Iniciante', 'descricao' => 'Equações de primeiro e segundo grau passo a passo.'],
        'inequacoes' => ['titulo' => 'Inequações', 'icone' => 'x > 0', 'nivel' => 'Intermediário', 'descricao' => 'Comparações, intervalos e conjuntos solução.'],
    ];

    private VideoRepository $repository;

    public function __construct()
    {
        $this->repository = new VideoRepository();
    }

    public function getAreas(?int $turmaId = null): array
    {
        $counts = $this->repository->getVideoCountsByModulo($turmaId);
        $areas = [];

        foreach (self::AREAS as $slug => $area) {
            $modulo = $this->repository->findModuloByName($area['titulo']);
            $area['slug'] = $slug;
            $area['modulo_id'] = $modulo !== null ? (int) $modulo['id'] : null;
            $area['video_count'] = $area['modulo_id'] !== null ? ($counts[$area['modulo_id']] ?? 0) : 0;
            $areas[] = $area;
        }

        return $areas;
    }

    public function getArea(string $slug): ?array
    {
        $slug = strtolower(trim($slug));
        if (!isset(self::AREAS[$slug])) {
            return null;
        }

        $area = self::AREAS[$slug];
        $modulo = $this->repository->findModuloByName($area['titulo']);
        $area['slug'] = $slug;
        $area['modulo_id'] = $modulo !== null ? (int) $modulo['id'] : null;

        return $area;
    }

    public function getVideos(string $slug, ?int $turmaId = null): array
    {
        $area = $this->getArea($slug);
        if ($area === null || $area['modulo_id'] === null) {
            return [];
        }

        return $this->repository->listByModulo($area['modulo_id'], $turmaId);
    }

    public function getVideo(int $videoId, ?int $turmaId = null): ?array
    {
        return $videoId > 0 ? $this->repository->findById($videoId, $turmaId) : null;
    }

    public function createVideo(int $professorId, int $turmaId, string $slug, string $titulo, string $descricao, string $url): bool
    {
        $area = $this->getArea($slug);
        if ($area === null || $area['modulo_id'] === null) {
            return false;
        }

        return $this->repository->create($professorId, $area['modulo_id'], $turmaId, $titulo, $descricao, $url);
    }
}
