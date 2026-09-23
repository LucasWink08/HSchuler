<?php

class VideoRepository
{
    private PDO $db;
    private static bool $schemaVerificado = false;

    public function __construct()
    {
        $this->db = Database::getConnection();
        if (!self::$schemaVerificado) {
            $this->ensureTurmaColumn();
            self::$schemaVerificado = true;
        }
    }

    public function findModuloByName(string $nome): ?array
    {
        $stmt = $this->db->prepare('SELECT id, nome FROM modulo WHERE nome = :nome LIMIT 1');
        $stmt->execute([':nome' => $nome]);

        $modulo = $stmt->fetch();
        return $modulo ?: null;
    }

    public function getVideoCountsByModulo(?int $turmaId = null): array
    {
        if ($turmaId === null) {
            $stmt = $this->db->query(
                'SELECT modulo_id, COUNT(*) AS total
                 FROM video
                 WHERE modulo_id IS NOT NULL AND turma_id IS NULL
                 GROUP BY modulo_id'
            );
        } else {
            $stmt = $this->db->prepare(
                'SELECT modulo_id, COUNT(*) AS total
                 FROM video
                 WHERE modulo_id IS NOT NULL AND turma_id = :turma_id
                 GROUP BY modulo_id'
            );
            $stmt->execute([':turma_id' => $turmaId]);
        }

        $counts = [];
        foreach ($stmt->fetchAll() as $row) {
            $counts[(int) $row['modulo_id']] = (int) $row['total'];
        }

        return $counts;
    }

    public function listByModulo(int $moduloId, ?int $turmaId = null): array
    {
        $filtroTurma = $turmaId === null ? 'video.turma_id IS NULL' : 'video.turma_id = :turma_id';
        $stmt = $this->db->prepare(
            'SELECT video.id, video.titulo, video.descricao, video.url_video, video.created_at,
                    professor.nome AS professor_nome
             FROM video
             INNER JOIN professor ON professor.id = video.professor_id
             WHERE video.modulo_id = :modulo_id AND ' . $filtroTurma . '
             ORDER BY video.created_at DESC, video.id DESC'
        );
        $parametros = [':modulo_id' => $moduloId];
        if ($turmaId !== null) {
            $parametros[':turma_id'] = $turmaId;
        }
        $stmt->execute($parametros);

        return $stmt->fetchAll() ?: [];
    }

    public function findById(int $videoId, ?int $turmaId = null): ?array
    {
        $filtroTurma = $turmaId === null ? 'video.turma_id IS NULL' : 'video.turma_id = :turma_id';
        $stmt = $this->db->prepare(
            'SELECT video.id, video.titulo, video.descricao, video.url_video, video.created_at, video.modulo_id, video.turma_id,
                    professor.nome AS professor_nome, modulo.nome AS modulo_nome
             FROM video
             INNER JOIN professor ON professor.id = video.professor_id
             LEFT JOIN modulo ON modulo.id = video.modulo_id
             WHERE video.id = :id AND ' . $filtroTurma . '
             LIMIT 1'
        );
        $parametros = [':id' => $videoId];
        if ($turmaId !== null) {
            $parametros[':turma_id'] = $turmaId;
        }
        $stmt->execute($parametros);

        $video = $stmt->fetch();
        return $video ?: null;
    }

    public function create(int $professorId, int $moduloId, int $turmaId, string $titulo, string $descricao, string $url): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO video (professor_id, turma_id, titulo, descricao, url_video, modulo_id)
             VALUES (:professor_id, :turma_id, :titulo, :descricao, :url_video, :modulo_id)'
        );

        return $stmt->execute([
            ':professor_id' => $professorId,
            ':turma_id' => $turmaId,
            ':titulo' => $titulo,
            ':descricao' => $descricao !== '' ? $descricao : null,
            ':url_video' => $url,
            ':modulo_id' => $moduloId,
        ]);
    }

    private function ensureTurmaColumn(): void
    {
        $coluna = $this->db->query("SHOW COLUMNS FROM video LIKE 'turma_id'")->fetch();
        if ($coluna === false) {
            $this->db->exec('ALTER TABLE video ADD COLUMN turma_id INT NULL AFTER professor_id');
        }
    }
}
