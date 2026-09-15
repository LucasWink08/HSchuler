-- Repara bancos criados antes do fluxo de conclusão de etapas.
-- Execute no mesmo banco configurado em config/database.php.

CREATE TABLE IF NOT EXISTS tentativa_etapa_trilha (
    id INT AUTO_INCREMENT PRIMARY KEY,
    aluno_id INT NOT NULL,
    etapa_id INT NOT NULL,
    acertos TINYINT UNSIGNED NOT NULL,
    erros TINYINT UNSIGNED NOT NULL,
    xp_recebido TINYINT UNSIGNED NOT NULL DEFAULT 0,
    concluida_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_tentativa_etapa_aluno FOREIGN KEY (aluno_id) REFERENCES aluno(id),
    CONSTRAINT fk_tentativa_etapa FOREIGN KEY (etapa_id) REFERENCES etapa_trilha(id),
    INDEX idx_tentativa_etapa_aluno (aluno_id, etapa_id)
);

ALTER TABLE aluno ADD COLUMN IF NOT EXISTS xp_total INT NOT NULL DEFAULT 0;
ALTER TABLE aluno ADD COLUMN IF NOT EXISTS nivel INT NOT NULL DEFAULT 1;
ALTER TABLE progresso_aluno ADD UNIQUE KEY IF NOT EXISTS uq_progresso_aluno_etapa (aluno_id, etapa_id);
ALTER TABLE streak ADD UNIQUE KEY IF NOT EXISTS uq_streak_aluno (aluno_id);
