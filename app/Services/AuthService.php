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
            'role' => 'aluno',
        ];
    }

    public function registerAluno(string $usuario, string $email, string $dataNascimento, string $senha): bool
    {
        if ($this->repository->findByEmail($email)) {
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
}
