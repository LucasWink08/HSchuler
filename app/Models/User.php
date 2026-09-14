<?php

class User
{
    public int $id;
    public string $usuario;
    public string $email;
    public ?string $senha;
    public ?string $dataNascimento;
    public string $tipo = 'aluno';

    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }
}
