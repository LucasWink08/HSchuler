<?php

class QuestaoService
{
    private array $questoes = [
        'potenciacao' => [
            [
                'enunciado' => 'Qual é o resultado de 2^3?',
                'alternativas' => ['4', '6', '8', '9'],
                'correta' => 2,
                'explicacao' => '2^3 significa 2 x 2 x 2, portanto o resultado é 8.',
            ],
            [
                'enunciado' => 'Qual é o resultado de x^2 * x^3?',
                'alternativas' => ['x^5', 'x^6', '2x^5', 'x'],
                'correta' => 0,
                'explicacao' => 'Na multiplicação de potências de mesma base, somamos os expoentes: x^(2+3) = x^5.',
            ],
        ],
        'fracoes-algebricas' => [
            [
                'enunciado' => 'Qual é a forma simplificada de 6x / 3?',
                'alternativas' => ['2x', '3x', '6x', 'x/2'],
                'correta' => 0,
                'explicacao' => 'Dividindo o coeficiente 6 por 3, obtemos 2x.',
            ],
            [
                'enunciado' => 'Para x diferente de zero, qual é o resultado de x/x?',
                'alternativas' => ['0', '1', 'x', 'x^2'],
                'correta' => 1,
                'explicacao' => 'Todo número diferente de zero dividido por ele mesmo é igual a 1.',
            ],
        ],
        'produtos-notaveis' => [
            [
                'enunciado' => 'Qual é o desenvolvimento de (x + 2)^2?',
                'alternativas' => ['x^2 + 2x + 4', 'x^2 + 4x + 4', 'x^2 + 4', '2x + 4'],
                'correta' => 1,
                'explicacao' => 'Aplicando (a+b)^2 = a^2 + 2ab + b^2, obtemos x^2 + 4x + 4.',
            ],
            [
                'enunciado' => 'Qual é o resultado de (x + 3)(x - 3)?',
                'alternativas' => ['x^2 + 9', 'x^2 - 6x + 9', 'x^2 - 9', 'x^2 + 6x + 9'],
                'correta' => 2,
                'explicacao' => 'Esse é o produto da soma pela diferença: x^2 - 3^2 = x^2 - 9.',
            ],
        ],
        'fatoracao' => [
            [
                'enunciado' => 'Qual é a fatoração de 3x + 6?',
                'alternativas' => ['3(x + 2)', '6(x + 3)', 'x(3 + 6)', '3(x + 6)'],
                'correta' => 0,
                'explicacao' => 'O fator comum é 3: 3x + 6 = 3(x + 2).',
            ],
        ],
        'equacoes' => [
            [
                'enunciado' => 'Qual é a solução de 2x + 4 = 10?',
                'alternativas' => ['2', '3', '4', '7'],
                'correta' => 1,
                'explicacao' => 'Subtraindo 4 e dividindo por 2: 2x = 6, então x = 3.',
            ],
        ],
        'inequacoes' => [
            [
                'enunciado' => 'Qual é a solução de x + 2 > 5?',
                'alternativas' => ['x > 3', 'x < 3', 'x > 7', 'x < 7'],
                'correta' => 0,
                'explicacao' => 'Subtraindo 2 dos dois lados, obtemos x > 3.',
            ],
        ],
    ];

    public function getAreas(): array
    {
        return [
            'potenciacao' => 'Potenciação',
            'fracoes-algebricas' => 'Frações Algébricas',
            'produtos-notaveis' => 'Produtos Notáveis',
            'fatoracao' => 'Fatoração',
            'equacoes' => 'Equações',
            'inequacoes' => 'Inequações',
        ];
    }

    public function getQuestoes(string $area): array
    {
        $questoesDoBanco = $this->buscarQuestoesDoBanco($area);

        if ($questoesDoBanco !== []) {
            return $questoesDoBanco;
        }

        return $this->questoes[$area] ?? $this->questoes['potenciacao'];
    }

    public function getQuestoesSimulado(): array
    {
        $questoesDoBanco = $this->buscarQuestoesDoBanco();

        if ($questoesDoBanco !== []) {
            return $questoesDoBanco;
        }

        $questoes = [];

        foreach ($this->questoes as $areaQuestoes) {
            foreach ($areaQuestoes as $questao) {
                $questoes[] = $questao;
            }
        }

        $questoes[] = [
            'enunciado' => 'Qual é o valor de 3^2 + 4?',
            'alternativas' => ['9', '11', '13', '16'],
            'correta' => 1,
            'explicacao' => '3^2 vale 9; somando 4, obtemos 13.',
        ];
        $questoes[] = [
            'enunciado' => 'Qual é o resultado de 5x - 2x?',
            'alternativas' => ['2x', '3x', '5x', '7x'],
            'correta' => 1,
            'explicacao' => 'Subtraindo os coeficientes, 5 - 2 = 3.',
        ];
        $questoes[] = [
            'enunciado' => 'Qual é a raiz de x^2 - 25 = 0?',
            'alternativas' => ['x = 5', 'x = -5', 'x = 5 ou x = -5', 'x = 25'],
            'correta' => 2,
            'explicacao' => 'As raízes de x^2 = 25 são 5 e -5.',
        ];
        $questoes[] = [
            'enunciado' => 'Qual expressão é equivalente a 2(x + 3)?',
            'alternativas' => ['2x + 3', '2x + 5', '2x + 6', 'x + 6'],
            'correta' => 2,
            'explicacao' => 'Aplicando a distributiva, 2(x + 3) = 2x + 6.',
        ];
        $questoes[] = [
            'enunciado' => 'Se x = 4, qual é o valor de x^2 - 3x?',
            'alternativas' => ['2', '4', '8', '16'],
            'correta' => 1,
            'explicacao' => '16 - 12 = 4.',
        ];
        $questoes[] = [
            'enunciado' => 'Qual é a solução de 3x = 21?',
            'alternativas' => ['3', '6', '7', '9'],
            'correta' => 2,
            'explicacao' => 'Dividindo 21 por 3, encontramos x = 7.',
        ];
        $questoes[] = [
            'enunciado' => 'Qual é a fatoração de x^2 - 9?',
            'alternativas' => ['(x - 9)(x + 1)', '(x - 3)(x + 3)', 'x(x - 9)', '(x - 3)^2'],
            'correta' => 1,
            'explicacao' => 'É uma diferença de quadrados: x^2 - 3^2.',
        ];
        $questoes[] = [
            'enunciado' => 'Qual é o valor de (2 + 3)^2?',
            'alternativas' => ['10', '13', '20', '25'],
            'correta' => 3,
            'explicacao' => 'Primeiro somamos 2 + 3 = 5; depois 5^2 = 25.',
        ];
        $questoes[] = [
            'enunciado' => 'Qual intervalo representa x < 4?',
            'alternativas' => ['(-infinito, 4)', '(4, infinito)', '[4, infinito)', '(-infinito, 4]'],
            'correta' => 0,
            'explicacao' => 'Como 4 não está incluído, usamos parênteses em 4.',
        ];
        $questoes[] = [
            'enunciado' => 'Qual é o coeficiente de x em 7x + 2?',
            'alternativas' => ['2', '5', '7', '9'],
            'correta' => 2,
            'explicacao' => 'O número que multiplica x é 7.',
        ];
        $questoes[] = [
            'enunciado' => 'Qual é o resultado de x^4 / x^2, com x diferente de zero?',
            'alternativas' => ['x^2', 'x^6', '2x', 'x^8'],
            'correta' => 0,
            'explicacao' => 'Na divisão de potências de mesma base, subtraímos os expoentes.',
        ];

        return array_slice($questoes, 0, 20);
    }

    public function getAreaLabel(string $area): string
    {
        return $this->getAreas()[$area] ?? $this->getAreas()['potenciacao'];
    }

    public function corrigir(array $questao, ?string $resposta): bool
    {
        return $resposta !== null && (int) $resposta === (int) $questao['correta'];
    }

    private function buscarQuestoesDoBanco(?string $area = null): array
    {
        try {
            $db = Database::getConnection();
            $stmt = $db->query(
                'SELECT questao.id, questao.enunciado, questao.alternativa_a, questao.alternativa_b,
                        questao.alternativa_c, questao.alternativa_d, questao.resposta_correta,
                        questao.explicacao, questao.pontuacao, questao.atividade_id,
                        assunto.nome AS assunto
                 FROM questao
                 LEFT JOIN assunto ON assunto.id = questao.assunto_id
                 ORDER BY questao.id'
            );
            $questoes = [];

            foreach ($stmt->fetchAll() as $questao) {
                if ($area !== null && $this->normalizarArea((string) $questao['assunto']) !== $area) {
                    continue;
                }

                $alternativas = [
                    $questao['alternativa_a'],
                    $questao['alternativa_b'],
                    $questao['alternativa_c'],
                    $questao['alternativa_d'],
                ];

                if (in_array(null, $alternativas, true) || in_array('', $alternativas, true)) {
                    continue;
                }

                $indiceCorreto = $this->indiceResposta((string) $questao['resposta_correta']);
                if ($indiceCorreto === null) {
                    continue;
                }

                $questoes[] = [
                    'id' => (int) $questao['id'],
                    'enunciado' => $questao['enunciado'],
                    'alternativas' => $alternativas,
                    'correta' => $indiceCorreto,
                    'explicacao' => $questao['explicacao'] ?? '',
                    'pontuacao' => (int) $questao['pontuacao'],
                    'atividade_id' => $questao['atividade_id'] === null ? null : (int) $questao['atividade_id'],
                ];
            }

            return $questoes;
        } catch (PDOException $exception) {
            return [];
        }
    }

    private function indiceResposta(string $resposta): ?int
    {
        $resposta = strtolower(trim($resposta));
        $mapa = ['a' => 0, 'b' => 1, 'c' => 2, 'd' => 3];

        if (array_key_exists($resposta, $mapa)) {
            return $mapa[$resposta];
        }

        return ctype_digit($resposta) && (int) $resposta >= 0 && (int) $resposta <= 3
            ? (int) $resposta
            : null;
    }

    private function normalizarArea(string $area): string
    {
        $area = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $area) ?: $area;
        $area = strtolower(trim($area));
        $area = preg_replace('/[^a-z0-9]+/', '-', $area) ?: '';

        return trim($area, '-');
    }
}
