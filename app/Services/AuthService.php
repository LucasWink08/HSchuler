<?php

class AuthService
{
    private UserRepository $repository;

    public function __construct()
    {
        $this->repository = new UserRepository();
    }

    public function login(string $usuario, string $senha): ?array
    {
        $user = $this->repository->findByUsuario($usuario);

        if (!$user || !password_verify($senha, $user['senha'] ?? '')) {
            return null;
        }

        return [
            'id' => (int) $user['id'],
            'usuario' => $user['usuario'],
            'foto_perfil' => $user['foto_perfil'] ?? null,
            'role' => 'aluno',
        ];
    }

    public function loginProfessor(string $siape, string $senha): ?array
    {
        $professor = $this->repository->findProfessorBySiape($siape);

        if (!$professor || !password_verify($senha, $professor['senha'] ?? '')) {
            return null;
        }

        return [
            'id' => (int) $professor['id'],
            'nome' => $professor['nome'],
            'role' => 'professor',
        ];
    }

    public function registerAluno(string $usuario, string $email, string $dataNascimento, string $senha): bool
    {
        if ($this->repository->findByUsuario($usuario) || $this->repository->findByEmail($email)) {
            return false;
        }

        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        return $this->repository->createAluno($usuario, $email, $dataNascimento, $senhaHash);
    }

    public function registerProfessor(string $siape, string $nome, string $email, string $senha): bool
    {
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        return $this->repository->createProfessor($siape, $nome, $email, $senhaHash);
    }

    public function getProfessorRegistrationError(string $siape, string $email): ?string
    {
        if ($this->repository->findProfessorBySiape($siape)) {
            return 'Este SIAPE já está cadastrado. Faça login para continuar.';
        }

        if ($this->repository->findProfessorByEmail($email)) {
            return 'Este e-mail já está cadastrado para outro professor.';
        }

        return null;
    }
}
