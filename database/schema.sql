CREATE DATABASE IF NOT EXISTS hschulerf;
USE hschulerf;

CREATE TABLE IF NOT EXISTS aluno (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(150) NOT NULL UNIQUE,
    data_nasc DATE NULL,
    senha VARCHAR(255) NOT NULL,
    xp_total INT NOT NULL DEFAULT 0,
    nivel INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS professor (
    id INT AUTO_INCREMENT PRIMARY KEY,
    siape VARCHAR(50) NOT NULL UNIQUE,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS assunto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS modulo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    ordem_num INT NOT NULL DEFAULT 1
);

CREATE TABLE IF NOT EXISTS etapa_trilha (
    id INT AUTO_INCREMENT PRIMARY KEY,
    modulo_id INT NOT NULL,
    nome VARCHAR(150) NOT NULL,
    descricao TEXT,
    ordem_num INT NOT NULL DEFAULT 1,
    estado VARCHAR(20) NOT NULL DEFAULT 'bloqueada',
    FOREIGN KEY (modulo_id) REFERENCES modulo(id)
);

CREATE TABLE IF NOT EXISTS video (
    id INT AUTO_INCREMENT PRIMARY KEY,
    professor_id INT NOT NULL,
    titulo VARCHAR(150) NOT NULL,
    descricao TEXT,
    url_video VARCHAR(255) NOT NULL,
    assunto_id INT NULL,
    modulo_id INT NULL,
    etapa_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (professor_id) REFERENCES professor(id),
    FOREIGN KEY (assunto_id) REFERENCES assunto(id),
    FOREIGN KEY (modulo_id) REFERENCES modulo(id),
    FOREIGN KEY (etapa_id) REFERENCES etapa_trilha(id)
);

CREATE TABLE IF NOT EXISTS atividade (
    id INT AUTO_INCREMENT PRIMARY KEY,
    professor_id INT NOT NULL,
    titulo VARCHAR(150) NOT NULL,
    descricao TEXT,
    assunto_id INT NULL,
    modulo_id INT NULL,
    etapa_id INT NULL,
    dificuldade VARCHAR(20) NOT NULL DEFAULT 'medio',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (professor_id) REFERENCES professor(id),
    FOREIGN KEY (assunto_id) REFERENCES assunto(id),
    FOREIGN KEY (modulo_id) REFERENCES modulo(id),
    FOREIGN KEY (etapa_id) REFERENCES etapa_trilha(id)
);

CREATE TABLE IF NOT EXISTS questao (
    id INT AUTO_INCREMENT PRIMARY KEY,
    atividade_id INT NULL,
    professor_id INT NOT NULL,
    enunciado TEXT NOT NULL,
    tipo VARCHAR(20) NOT NULL DEFAULT 'multipla_escolha',
    dificuldade VARCHAR(20) NOT NULL DEFAULT 'medio',
    assunto_id INT NULL,
    alternativa_a VARCHAR(255) NULL,
    alternativa_b VARCHAR(255) NULL,
    alternativa_c VARCHAR(255) NULL,
    alternativa_d VARCHAR(255) NULL,
    resposta_correta VARCHAR(10) NULL,
    explicacao TEXT NULL,
    pontuacao INT NOT NULL DEFAULT 10,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (atividade_id) REFERENCES atividade(id),
    FOREIGN KEY (professor_id) REFERENCES professor(id),
    FOREIGN KEY (assunto_id) REFERENCES assunto(id)
);

CREATE TABLE IF NOT EXISTS resposta_aluno (
    id INT AUTO_INCREMENT PRIMARY KEY,
    aluno_id INT NOT NULL,
    questao_id INT NOT NULL,
    resposta_escolhida VARCHAR(10) NULL,
    acertou TINYINT(1) NOT NULL DEFAULT 0,
    tempo_gasto INT DEFAULT 0,
    tentativas INT DEFAULT 1,
    data_resposta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (aluno_id) REFERENCES aluno(id),
    FOREIGN KEY (questao_id) REFERENCES questao(id)
);

CREATE TABLE IF NOT EXISTS simulado (
    id INT AUTO_INCREMENT PRIMARY KEY,
    aluno_id INT NOT NULL,
    assunto_id INT NULL,
    dificuldade VARCHAR(20) DEFAULT 'medio',
    quantidade INT NOT NULL DEFAULT 5,
    inicio TIMESTAMP NULL,
    fim TIMESTAMP NULL,
    nota DECIMAL(5,2) DEFAULT 0,
    percentual DECIMAL(5,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (aluno_id) REFERENCES aluno(id),
    FOREIGN KEY (assunto_id) REFERENCES assunto(id)
);

CREATE TABLE IF NOT EXISTS progresso_aluno (
    id INT AUTO_INCREMENT PRIMARY KEY,
    aluno_id INT NOT NULL,
    etapa_id INT NOT NULL,
    estado VARCHAR(20) NOT NULL DEFAULT 'disponivel',
    percentual INT NOT NULL DEFAULT 0,
    ultima_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (aluno_id) REFERENCES aluno(id),
    FOREIGN KEY (etapa_id) REFERENCES etapa_trilha(id)
);

CREATE TABLE IF NOT EXISTS xp_transacao (
    id INT AUTO_INCREMENT PRIMARY KEY,
    aluno_id INT NOT NULL,
    tipo VARCHAR(50) NOT NULL,
    quantidade INT NOT NULL DEFAULT 0,
    descricao TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (aluno_id) REFERENCES aluno(id)
);

CREATE TABLE IF NOT EXISTS streak (
    id INT AUTO_INCREMENT PRIMARY KEY,
    aluno_id INT NOT NULL,
    streak_atual INT NOT NULL DEFAULT 0,
    maior_streak INT NOT NULL DEFAULT 0,
    ultima_data_atividade DATE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (aluno_id) REFERENCES aluno(id)
);

CREATE TABLE IF NOT EXISTS ranking (
    id INT AUTO_INCREMENT PRIMARY KEY,
    aluno_id INT NOT NULL,
    xp_total INT NOT NULL DEFAULT 0,
    atividades_concluidas INT NOT NULL DEFAULT 0,
    percentual_acertos DECIMAL(5,2) DEFAULT 0,
    maior_streak INT NOT NULL DEFAULT 0,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (aluno_id) REFERENCES aluno(id)
);

CREATE INDEX idx_aluno_usuario ON aluno(usuario);
CREATE INDEX idx_aluno_email ON aluno(email);
CREATE INDEX idx_questao_assunto ON questao(assunto_id);
CREATE INDEX idx_atividade_assunto ON atividade(assunto_id);
CREATE INDEX idx_progresso_aluno ON progresso_aluno(aluno_id);
CREATE INDEX idx_ranking_xp ON ranking(xp_total DESC);
CREATE INDEX idx_streak_aluno ON streak(aluno_id);
