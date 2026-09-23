-- Sistema de turmas, matrículas, atividades e anexos.
CREATE TABLE IF NOT EXISTS turma (
    id INT AUTO_INCREMENT PRIMARY KEY,
    professor_id INT NOT NULL,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT NULL,
    codigo VARCHAR(12) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (professor_id) REFERENCES professor(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS turma_aluno (
    turma_id INT NOT NULL,
    aluno_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (turma_id, aluno_id),
    FOREIGN KEY (turma_id) REFERENCES turma(id) ON DELETE CASCADE,
    FOREIGN KEY (aluno_id) REFERENCES aluno(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS atividade_turma (
    id INT AUTO_INCREMENT PRIMARY KEY,
    turma_id INT NOT NULL,
    titulo VARCHAR(150) NOT NULL,
    descricao TEXT NULL,
    periodo_entrega DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (turma_id) REFERENCES turma(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS anexo_atividade_turma (
    id INT AUTO_INCREMENT PRIMARY KEY,
    atividade_turma_id INT NOT NULL,
    nome_original VARCHAR(255) NOT NULL,
    nome_arquivo VARCHAR(255) NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    tamanho INT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (atividade_turma_id) REFERENCES atividade_turma(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS entrega_atividade_turma (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
