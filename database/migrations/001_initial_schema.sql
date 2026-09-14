create schema hschuler
use hschuler
-- 1. ALUNO (Já existente / Ajustar se necessário)
CREATE TABLE ALUNO (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP,
    ultimo_login DATETIME,
    status_conta TINYINT(1) DEFAULT 1,
    pontos INT DEFAULT 0,
    streak_dias INT DEFAULT 0
);

-- 2. PROFESSOR (Já existente / Ajustar se necessário)
CREATE TABLE professor (
    id_professor INT AUTO_INCREMENT PRIMARY KEY,
    siape VARCHAR(20) NOT NULL UNIQUE,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 3. VÍDEOS
CREATE TABLE video (
    id_video INT AUTO_INCREMENT PRIMARY KEY,
    id_professor INT NOT NULL,
    titulo VARCHAR(150) NOT NULL,
    descricao TEXT,
    url_video VARCHAR(255) NOT NULL,
    data_publicacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_professor) REFERENCES professor(id_professor) ON DELETE CASCADE
);

-- 4. QUESTÕES PRÉ-DEFINIDAS (Múltipla escolha A, B, C, D)
CREATE TABLE questao (
    id_questao INT AUTO_INCREMENT PRIMARY KEY,
    id_video INT NULL,
    enunciado TEXT NOT NULL,
    opcao_a VARCHAR(255) NOT NULL,
    opcao_b VARCHAR(255) NOT NULL,
    opcao_c VARCHAR(255) NOT NULL,
    opcao_d VARCHAR(255) NOT NULL,
    letra_correta CHAR(1) NOT NULL,
    pontos INT DEFAULT 10,
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_video) REFERENCES video(id_video) ON DELETE SET NULL
);

-- 5. RESPOSTAS DOS ALUNOS
CREATE TABLE resposta_aluno (
    id_resposta INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_questao INT NOT NULL,
    opcao_escolhida CHAR(1) NOT NULL,
    acertou TINYINT(1) NOT NULL,
    data_resposta DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES ALUNO(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_questao) REFERENCES questao(id_questao) ON DELETE CASCADE
);

select * from aluno

