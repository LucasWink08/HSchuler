<?php

class UserRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findByUsuario(string $usuario): ?array
    {
        $sql = 'SELECT id, usuario, senha FROM aluno WHERE usuario = :usuario LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':usuario' => $usuario]);

        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function findProfessorBySiape(string $siape): ?array
    {
        $sql = 'SELECT id, siape, nome, senha FROM professor WHERE siape = :siape LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':siape' => $siape]);

        $professor = $stmt->fetch();
        return $professor ?: null;
    }

    public function findProfessorByEmail(string $email): ?array
    {
        $sql = 'SELECT id, email FROM professor WHERE email = :email LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);

        $professor = $stmt->fetch();
        return $professor ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $sql = 'SELECT id, email FROM aluno WHERE email = :email LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);

        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function createAluno(string $usuario, string $email, string $dataNascimento, string $senhaHash): bool
    {
        $sql = 'INSERT INTO aluno (usuario, email, data_nasc, senha) VALUES (:usuario, :email, :data_nasc, :senha)';
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':usuario' => $usuario,
            ':email' => $email,
            ':data_nasc' => $dataNascimento,
            ':senha' => $senhaHash,
        ]);
    }

    public function createProfessor(string $siape, string $nome, string $email, string $senhaHash): bool
    {
        $sql = 'INSERT INTO professor (siape, nome, email, senha) VALUES (:siape, :nome, :email, :senha)';
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':siape' => $siape,
            ':nome' => $nome,
            ':email' => $email,
            ':senha' => $senhaHash,
        ]);
    }
}
