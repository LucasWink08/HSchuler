<?php

class TurmaRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function ensureSchema(): void
    {
        $this->db->exec(
            'CREATE TABLE IF NOT EXISTS turma (
                id INT AUTO_INCREMENT PRIMARY KEY,
                professor_id INT NOT NULL,
                nome VARCHAR(100) NOT NULL,
                descricao TEXT NULL,
                codigo VARCHAR(12) NOT NULL UNIQUE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (professor_id) REFERENCES professor(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        );
        $this->db->exec(
            'CREATE TABLE IF NOT EXISTS turma_aluno (
                turma_id INT NOT NULL,
                aluno_id INT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (turma_id, aluno_id),
                FOREIGN KEY (turma_id) REFERENCES turma(id) ON DELETE CASCADE,
                FOREIGN KEY (aluno_id) REFERENCES aluno(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        );
        $this->db->exec(
            'CREATE TABLE IF NOT EXISTS atividade_turma (
                id INT AUTO_INCREMENT PRIMARY KEY,
                turma_id INT NOT NULL,
                titulo VARCHAR(150) NOT NULL,
                descricao TEXT NULL,
                periodo_entrega DATETIME NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (turma_id) REFERENCES turma(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        );
        $this->db->exec(
            'CREATE TABLE IF NOT EXISTS anexo_atividade_turma (
                id INT AUTO_INCREMENT PRIMARY KEY,
                atividade_turma_id INT NOT NULL,
                nome_original VARCHAR(255) NOT NULL,
                nome_arquivo VARCHAR(255) NOT NULL,
                mime_type VARCHAR(100) NOT NULL,
                tamanho INT UNSIGNED NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (atividade_turma_id) REFERENCES atividade_turma(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        );
        $this->db->exec(
            'CREATE TABLE IF NOT EXISTS entrega_atividade_turma (
                id INT AUTO_INCREMENT PRIMARY KEY,
                atividade_turma_id INT NOT NULL,
                aluno_id INT NOT NULL,
                nome_original VARCHAR(255) NOT NULL,
                nome_arquivo VARCHAR(255) NOT NULL,
                mime_type VARCHAR(100) NOT NULL,
                tamanho INT UNSIGNED NOT NULL,
                nota DECIMAL(4,2) NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY uq_entrega_atividade_aluno (atividade_turma_id, aluno_id),
                FOREIGN KEY (atividade_turma_id) REFERENCES atividade_turma(id) ON DELETE CASCADE,
                FOREIGN KEY (aluno_id) REFERENCES aluno(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        );
    }

    public function codeExists(string $codigo): bool
    {
        $stmt = $this->db->prepare('SELECT 1 FROM turma WHERE codigo = :codigo LIMIT 1');
        $stmt->execute([':codigo' => $codigo]);

        return $stmt->fetchColumn() !== false;
    }

    public function createClass(int $professorId, string $nome, string $descricao, string $codigo): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO turma (professor_id, nome, descricao, codigo)
             VALUES (:professor_id, :nome, :descricao, :codigo)'
        );
        $stmt->execute([
            ':professor_id' => $professorId,
            ':nome' => $nome,
            ':descricao' => $descricao !== '' ? $descricao : null,
            ':codigo' => $codigo,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function listTeacherClasses(int $professorId): array
    {
        $stmt = $this->db->prepare(
            'SELECT turma.id, turma.nome, turma.descricao, turma.codigo, turma.created_at,
                    COUNT(DISTINCT turma_aluno.aluno_id) AS total_alunos,
                    COUNT(DISTINCT atividade_turma.id) AS total_atividades
             FROM turma
             LEFT JOIN turma_aluno ON turma_aluno.turma_id = turma.id
             LEFT JOIN atividade_turma ON atividade_turma.turma_id = turma.id
             WHERE turma.professor_id = :professor_id
             GROUP BY turma.id, turma.nome, turma.descricao, turma.codigo, turma.created_at
             ORDER BY turma.created_at DESC, turma.id DESC'
        );
        $stmt->execute([':professor_id' => $professorId]);

        return $stmt->fetchAll() ?: [];
    }

    public function findTeacherClass(int $professorId, int $turmaId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT turma.id, turma.nome, turma.descricao, turma.codigo, turma.created_at,
                    professor.nome AS professor_nome, professor.email AS professor_email,
                    COUNT(DISTINCT turma_aluno.aluno_id) AS total_alunos
             FROM turma
             INNER JOIN professor ON professor.id = turma.professor_id
             LEFT JOIN turma_aluno ON turma_aluno.turma_id = turma.id
             WHERE turma.id = :turma_id AND turma.professor_id = :professor_id
             GROUP BY turma.id, turma.nome, turma.descricao, turma.codigo, turma.created_at, professor.nome, professor.email
             LIMIT 1'
        );
        $stmt->execute([':turma_id' => $turmaId, ':professor_id' => $professorId]);

        $turma = $stmt->fetch();
        return $turma ?: null;
    }

    public function findClassByCode(string $codigo): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT turma.id, turma.nome, turma.descricao, turma.codigo, turma.professor_id,
                    professor.nome AS professor_nome
             FROM turma
             INNER JOIN professor ON professor.id = turma.professor_id
             WHERE turma.codigo = :codigo
             LIMIT 1'
        );
        $stmt->execute([':codigo' => $codigo]);

        $turma = $stmt->fetch();
        return $turma ?: null;
    }

    public function enrollStudent(int $turmaId, int $alunoId): bool
    {
        $stmt = $this->db->prepare('INSERT IGNORE INTO turma_aluno (turma_id, aluno_id) VALUES (:turma_id, :aluno_id)');
        $stmt->execute([':turma_id' => $turmaId, ':aluno_id' => $alunoId]);

        return $stmt->rowCount() > 0;
    }

    public function listStudentClasses(int $alunoId): array
    {
        $stmt = $this->db->prepare(
            'SELECT turma.id, turma.nome, turma.descricao, turma.codigo, turma.created_at,
                    professor.nome AS professor_nome,
                    COUNT(DISTINCT atividade_turma.id) AS total_atividades
             FROM turma_aluno
             INNER JOIN turma ON turma.id = turma_aluno.turma_id
             INNER JOIN professor ON professor.id = turma.professor_id
             LEFT JOIN atividade_turma ON atividade_turma.turma_id = turma.id
             WHERE turma_aluno.aluno_id = :aluno_id
             GROUP BY turma.id, turma.nome, turma.descricao, turma.codigo, turma.created_at, professor.nome
             ORDER BY turma.created_at DESC, turma.id DESC'
        );
        $stmt->execute([':aluno_id' => $alunoId]);

        return $stmt->fetchAll() ?: [];
    }

    public function findStudentClass(int $alunoId, int $turmaId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT turma.id, turma.nome, turma.descricao, turma.codigo, turma.created_at,
                    professor.nome AS professor_nome, professor.email AS professor_email
             FROM turma_aluno
             INNER JOIN turma ON turma.id = turma_aluno.turma_id
             INNER JOIN professor ON professor.id = turma.professor_id
             WHERE turma_aluno.aluno_id = :aluno_id AND turma.id = :turma_id
             LIMIT 1'
        );
        $stmt->execute([':aluno_id' => $alunoId, ':turma_id' => $turmaId]);

        $turma = $stmt->fetch();
        return $turma ?: null;
    }

    public function findActivityForStudent(int $alunoId, int $atividadeId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT atividade_turma.id, atividade_turma.turma_id
             FROM atividade_turma
             INNER JOIN turma_aluno ON turma_aluno.turma_id = atividade_turma.turma_id
             WHERE atividade_turma.id = :atividade_id AND turma_aluno.aluno_id = :aluno_id
             LIMIT 1'
        );
        $stmt->execute([':atividade_id' => $atividadeId, ':aluno_id' => $alunoId]);

        $atividade = $stmt->fetch();
        return $atividade ?: null;
    }

    public function listStudents(int $turmaId): array
    {
        $stmt = $this->db->prepare(
            'SELECT aluno.id, aluno.usuario, aluno.email, turma_aluno.created_at
             FROM turma_aluno
             INNER JOIN aluno ON aluno.id = turma_aluno.aluno_id
             WHERE turma_aluno.turma_id = :turma_id
             ORDER BY aluno.usuario ASC'
        );
        $stmt->execute([':turma_id' => $turmaId]);

        return $stmt->fetchAll() ?: [];
    }

    public function createActivity(int $turmaId, string $titulo, string $descricao, ?string $periodoEntrega): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO atividade_turma (turma_id, titulo, descricao, periodo_entrega)
             VALUES (:turma_id, :titulo, :descricao, :periodo_entrega)'
        );
        $stmt->execute([
            ':turma_id' => $turmaId,
            ':titulo' => $titulo,
            ':descricao' => $descricao !== '' ? $descricao : null,
            ':periodo_entrega' => $periodoEntrega,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function createAttachment(int $atividadeId, string $nomeOriginal, string $nomeArquivo, string $mime, int $tamanho): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO anexo_atividade_turma (atividade_turma_id, nome_original, nome_arquivo, mime_type, tamanho)
             VALUES (:atividade_id, :nome_original, :nome_arquivo, :mime, :tamanho)'
        );
        $stmt->execute([
            ':atividade_id' => $atividadeId,
            ':nome_original' => $nomeOriginal,
            ':nome_arquivo' => $nomeArquivo,
            ':mime' => $mime,
            ':tamanho' => $tamanho,
        ]);
    }

    public function deleteActivity(int $atividadeId): void
    {
        $stmt = $this->db->prepare('DELETE FROM atividade_turma WHERE id = :id');
        $stmt->execute([':id' => $atividadeId]);
    }

    public function saveSubmission(int $atividadeId, int $alunoId, string $nomeOriginal, string $nomeArquivo, string $mime, int $tamanho): string
    {
        $stmtAnterior = $this->db->prepare(
            'SELECT nome_arquivo FROM entrega_atividade_turma
             WHERE atividade_turma_id = :atividade_id AND aluno_id = :aluno_id
             LIMIT 1'
        );
        $stmtAnterior->execute([':atividade_id' => $atividadeId, ':aluno_id' => $alunoId]);
        $nomeAnterior = (string) ($stmtAnterior->fetchColumn() ?: '');

        $stmt = $this->db->prepare(
            'INSERT INTO entrega_atividade_turma (atividade_turma_id, aluno_id, nome_original, nome_arquivo, mime_type, tamanho)
             VALUES (:atividade_id, :aluno_id, :nome_original, :nome_arquivo, :mime_type, :tamanho)
             ON DUPLICATE KEY UPDATE nome_original = VALUES(nome_original), nome_arquivo = VALUES(nome_arquivo),
                 mime_type = VALUES(mime_type), tamanho = VALUES(tamanho), nota = NULL, updated_at = CURRENT_TIMESTAMP'
        );
        $stmt->execute([
            ':atividade_id' => $atividadeId,
            ':aluno_id' => $alunoId,
            ':nome_original' => $nomeOriginal,
            ':nome_arquivo' => $nomeArquivo,
            ':mime_type' => $mime,
            ':tamanho' => $tamanho,
        ]);

        return $nomeAnterior;
    }

    public function findAttachmentForTeacher(int $anexoId, int $professorId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT anexo_atividade_turma.id, anexo_atividade_turma.nome_original, anexo_atividade_turma.nome_arquivo,
                    anexo_atividade_turma.mime_type, anexo_atividade_turma.tamanho
             FROM anexo_atividade_turma
             INNER JOIN atividade_turma ON atividade_turma.id = anexo_atividade_turma.atividade_turma_id
             INNER JOIN turma ON turma.id = atividade_turma.turma_id
             WHERE anexo_atividade_turma.id = :anexo_id AND turma.professor_id = :professor_id
             LIMIT 1'
        );
        $stmt->execute([':anexo_id' => $anexoId, ':professor_id' => $professorId]);

        $anexo = $stmt->fetch();
        return $anexo ?: null;
    }

    public function findAttachmentForStudent(int $anexoId, int $alunoId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT anexo_atividade_turma.id, anexo_atividade_turma.nome_original, anexo_atividade_turma.nome_arquivo,
                    anexo_atividade_turma.mime_type, anexo_atividade_turma.tamanho
             FROM anexo_atividade_turma
             INNER JOIN atividade_turma ON atividade_turma.id = anexo_atividade_turma.atividade_turma_id
             INNER JOIN turma_aluno ON turma_aluno.turma_id = atividade_turma.turma_id
             WHERE anexo_atividade_turma.id = :anexo_id AND turma_aluno.aluno_id = :aluno_id
             LIMIT 1'
        );
        $stmt->execute([':anexo_id' => $anexoId, ':aluno_id' => $alunoId]);

        $anexo = $stmt->fetch();
        return $anexo ?: null;
    }

    public function findSubmissionForTeacher(int $entregaId, int $professorId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT entrega_atividade_turma.id, entrega_atividade_turma.nome_original, entrega_atividade_turma.nome_arquivo,
                    entrega_atividade_turma.mime_type, entrega_atividade_turma.tamanho, entrega_atividade_turma.nota
             FROM entrega_atividade_turma
             INNER JOIN atividade_turma ON atividade_turma.id = entrega_atividade_turma.atividade_turma_id
             INNER JOIN turma ON turma.id = atividade_turma.turma_id
             WHERE entrega_atividade_turma.id = :entrega_id AND turma.professor_id = :professor_id
             LIMIT 1'
        );
        $stmt->execute([':entrega_id' => $entregaId, ':professor_id' => $professorId]);

        $entrega = $stmt->fetch();
        return $entrega ?: null;
    }

    public function findSubmissionForStudent(int $entregaId, int $alunoId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT id, nome_original, nome_arquivo, mime_type, tamanho, nota
             FROM entrega_atividade_turma
             WHERE id = :entrega_id AND aluno_id = :aluno_id
             LIMIT 1'
        );
        $stmt->execute([':entrega_id' => $entregaId, ':aluno_id' => $alunoId]);

        $entrega = $stmt->fetch();
        return $entrega ?: null;
    }

    public function setSubmissionGrade(int $entregaId, float $nota): void
    {
        $stmt = $this->db->prepare('UPDATE entrega_atividade_turma SET nota = :nota WHERE id = :id');
        $stmt->execute([':nota' => $nota, ':id' => $entregaId]);
    }

    public function listActivities(int $turmaId): array
    {
        $stmt = $this->db->prepare(
            'SELECT id, titulo, descricao, periodo_entrega, created_at
             FROM atividade_turma
             WHERE turma_id = :turma_id
             ORDER BY CASE WHEN periodo_entrega IS NULL THEN 1 ELSE 0 END, periodo_entrega ASC, id DESC'
        );
        $stmt->execute([':turma_id' => $turmaId]);
        $atividades = $stmt->fetchAll() ?: [];

        foreach ($atividades as &$atividade) {
            $atividade['anexos'] = $this->listAttachments((int) $atividade['id']);
        }
        unset($atividade);

        return $atividades;
    }

    public function listActivitiesForStudent(int $turmaId, int $alunoId): array
    {
        $stmt = $this->db->prepare(
            'SELECT atividade_turma.id, atividade_turma.titulo, atividade_turma.descricao, atividade_turma.periodo_entrega,
                    atividade_turma.created_at, entrega_atividade_turma.id AS entrega_id,
                    entrega_atividade_turma.nome_original AS entrega_nome_original,
                    entrega_atividade_turma.created_at AS entrega_enviada_em, entrega_atividade_turma.nota AS entrega_nota
             FROM atividade_turma
             LEFT JOIN entrega_atividade_turma ON entrega_atividade_turma.atividade_turma_id = atividade_turma.id
                 AND entrega_atividade_turma.aluno_id = :aluno_id
             WHERE atividade_turma.turma_id = :turma_id
             ORDER BY CASE WHEN atividade_turma.periodo_entrega IS NULL THEN 1 ELSE 0 END, atividade_turma.periodo_entrega ASC, atividade_turma.id DESC'
        );
        $stmt->execute([':turma_id' => $turmaId, ':aluno_id' => $alunoId]);
        $atividades = $stmt->fetchAll() ?: [];

        foreach ($atividades as &$atividade) {
            $atividade['anexos'] = $this->listAttachments((int) $atividade['id']);
        }
        unset($atividade);

        return $atividades;
    }

    public function listSubmissionsForTeacher(int $turmaId, int $professorId): array
    {
        $stmt = $this->db->prepare(
            'SELECT entrega_atividade_turma.id, entrega_atividade_turma.nome_original, entrega_atividade_turma.nota,
                    entrega_atividade_turma.created_at, atividade_turma.id AS atividade_id, atividade_turma.titulo AS atividade_titulo,
                    aluno.usuario AS aluno_nome, aluno.email AS aluno_email
             FROM entrega_atividade_turma
             INNER JOIN atividade_turma ON atividade_turma.id = entrega_atividade_turma.atividade_turma_id
             INNER JOIN turma ON turma.id = atividade_turma.turma_id
             INNER JOIN aluno ON aluno.id = entrega_atividade_turma.aluno_id
             WHERE turma.id = :turma_id AND turma.professor_id = :professor_id
             ORDER BY entrega_atividade_turma.created_at DESC, entrega_atividade_turma.id DESC'
        );
        $stmt->execute([':turma_id' => $turmaId, ':professor_id' => $professorId]);

        return $stmt->fetchAll() ?: [];
    }

    private function listAttachments(int $atividadeId): array
    {
        $stmt = $this->db->prepare(
            'SELECT id, nome_original, nome_arquivo, mime_type, tamanho, created_at
             FROM anexo_atividade_turma
             WHERE atividade_turma_id = :atividade_id
             ORDER BY id ASC'
        );
        $stmt->execute([':atividade_id' => $atividadeId]);

        return $stmt->fetchAll() ?: [];
    }
}
