-- Adiciona a referência da foto de perfil dos alunos já cadastrados.
-- Os arquivos são salvos em public/uploads/perfis; o banco armazena apenas o nome do arquivo.
ALTER TABLE aluno ADD COLUMN foto_perfil VARCHAR(255) NULL AFTER senha;
