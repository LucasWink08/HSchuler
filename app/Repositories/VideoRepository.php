<?php

class VideoRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findModuloByName(string $nome): ?array
    {
        $stmt = $this->db->prepare('SELECT id, nome FROM modulo WHERE nome = :nome LIMIT 1');
        $stmt->execute([':nome' => $nome]);

        $modulo = $stmt->fetch();
        return $modulo ?: null;
    }

    public function getVideoCountsByModulo(): array
    {
        $stmt = $this->db->query(
            'SELECT modulo_id, COUNT(*) AS total
             FROM video
             WHERE modulo_id IS NOT NULL
             GROUP BY modulo_id'
        );

        $counts = [];
        foreach ($stmt->fetchAll() as $row) {
            $counts[(int) $row['modulo_id']] = (int) $row['total'];
        }

        return $counts;
    }

    public function listByModulo(int $moduloId): array
    {
        $stmt = $this->db->prepare(
            'SELECT video.id, video.titulo, video.descricao, video.url_video, video.created_at,
                    professor.nome AS professor_nome
             FROM video
             INNER JOIN professor ON professor.id = video.professor_id
             WHERE video.modulo_id = :modulo_id
             ORDER BY video.created_at DESC, video.id DESC'
        );
        $stmt->execute([':modulo_id' => $moduloId]);

        return $stmt->fetchAll() ?: [];
    }

    public function create(int $professorId, int $moduloId, string $titulo, string $descricao, string $url): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO video (professor_id, titulo, descricao, url_video, modulo_id)
             VALUES (:professor_id, :titulo, :descricao, :url_video, :modulo_id)'
        );

        return $stmt->execute([
            ':professor_id' => $professorId,
            ':titulo' => $titulo,
            ':descricao' => $descricao !== '' ? $descricao : null,
            ':url_video' => $url,
            ':modulo_id' => $moduloId,
        ]);
    }
}
