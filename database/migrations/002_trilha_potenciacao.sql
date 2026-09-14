-- Migração complementar da Trilha de Potenciação.
-- Execute uma única vez no banco configurado em config/database.php.
USE hschulerf;

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

-- Evita que duas requisições simultâneas premiem a mesma etapa duas vezes.
ALTER TABLE progresso_aluno ADD UNIQUE KEY uq_progresso_aluno_etapa (aluno_id, etapa_id);
ALTER TABLE streak ADD UNIQUE KEY uq_streak_aluno (aluno_id);
