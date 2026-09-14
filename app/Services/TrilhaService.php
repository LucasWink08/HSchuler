<?php

class TrilhaService
{
    public function getModulos(): array
    {
        return [
            [
                'titulo' => 'Módulo 1 — Fundamentos',
                'etapas' => [
                    ['nome' => 'Números e operações', 'estado' => 'concluida'],
                    ['nome' => 'Expressões algébricas', 'estado' => 'concluida'],
                    ['nome' => 'Monômios', 'estado' => 'em_andamento'],
                    ['nome' => 'Polinômios', 'estado' => 'disponivel'],
                ],
            ],
            [
                'titulo' => 'Módulo 2 — Produtos Notáveis',
                'etapas' => [
                    ['nome' => 'Quadrado da soma', 'estado' => 'disponivel'],
                    ['nome' => 'Quadrado da diferença', 'estado' => 'bloqueada'],
                    ['nome' => 'Produto da soma pela diferença', 'estado' => 'bloqueada'],
                ],
            ],
            [
                'titulo' => 'Módulo 3 — Fatoração',
                'etapas' => [
                    ['nome' => 'Fator comum', 'estado' => 'bloqueada'],
                    ['nome' => 'Agrupamento', 'estado' => 'bloqueada'],
                    ['nome' => 'Diferença de quadrados', 'estado' => 'bloqueada'],
                ],
            ],
            [
                'titulo' => 'Módulo 4 — Equações',
                'etapas' => [
                    ['nome' => 'Equação do 1º grau', 'estado' => 'bloqueada'],
                    ['nome' => 'Equação do 2º grau', 'estado' => 'bloqueada'],
                    ['nome' => 'Sistemas de equações', 'estado' => 'bloqueada'],
                ],
            ],
            [
                'titulo' => 'Módulo 5 — Funções',
                'etapas' => [
                    ['nome' => 'Função afim', 'estado' => 'bloqueada'],
                    ['nome' => 'Função quadrática', 'estado' => 'bloqueada'],
                    ['nome' => 'Interpretação de gráficos', 'estado' => 'bloqueada'],
                ],
            ],
        ];
    }

    public function getResumo(): array
    {
        return [
            'nome' => $_SESSION['usuario'] ?? 'Aluno',
            'nivel' => 3,
            'xp' => 420,
            'atividades_concluidas' => 12,
            'simulados_realizados' => 4,
            'percentual_acertos' => 78,
            'streak_atual' => 6,
            'maior_streak' => 9,
            'posicao_ranking' => 7,
        ];
    }
}
