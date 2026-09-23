<?php

class ProfessorRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findProfile(int $professorId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT id, nome, email, siape, created_at
             FROM professor
             WHERE id = :id
             LIMIT 1'
        );
        $stmt->execute([':id' => $professorId]);

        $perfil = $stmt->fetch();
        return $perfil ?: null;
    }

    /** @return array{videos: int, atividades: int, questoes: int} */
    public function getResumo(int $professorId): array
    {
        $stmt = $this->db->prepare(
            'SELECT
                (SELECT COUNT(*) FROM video WHERE professor_id = :id_video) AS videos,
                (SELECT COUNT(*) FROM atividade WHERE professor_id = :id_atividade) AS atividades,
                (SELECT COUNT(*) FROM questao WHERE professor_id = :id_questao) AS questoes'
        );
        $stmt->execute([
            ':id_video' => $professorId,
            ':id_atividade' => $professorId,
            ':id_questao' => $professorId,
        ]);

        $resumo = $stmt->fetch() ?: [];
        return [
            'videos' => (int) ($resumo['videos'] ?? 0),
            'atividades' => (int) ($resumo['atividades'] ?? 0),
            'questoes' => (int) ($resumo['questoes'] ?? 0),
        ];
    }

    public function listRecentVideos(int $professorId): array
    {
        $stmt = $this->db->prepare(
            'SELECT video.id, video.titulo, video.descricao, video.created_at, modulo.nome AS modulo_nome
             FROM video
             LEFT JOIN modulo ON modulo.id = video.modulo_id
             WHERE video.professor_id = :professor_id
             ORDER BY video.created_at DESC, video.id DESC
             LIMIT 5'
        );
        $stmt->execute([':professor_id' => $professorId]);

        return $stmt->fetchAll() ?: [];
    }

    public function listRecentActivities(int $professorId): array
    {
        $stmt = $this->db->prepare(
            'SELECT atividade.id, atividade.titulo, atividade.descricao, atividade.dificuldade, atividade.created_at,
                    modulo.nome AS modulo_nome, COUNT(questao.id) AS total_questoes
             FROM atividade
             LEFT JOIN modulo ON modulo.id = atividade.modulo_id
             LEFT JOIN questao ON questao.atividade_id = atividade.id
             WHERE atividade.professor_id = :professor_id
             GROUP BY atividade.id, atividade.titulo, atividade.descricao, atividade.dificuldade, atividade.created_at, modulo.nome
             ORDER BY atividade.created_at DESC, atividade.id DESC
             LIMIT 5'
        );
        $stmt->execute([':professor_id' => $professorId]);

        return $stmt->fetchAll() ?: [];
    }
}
