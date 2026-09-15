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

    public function getQuestoes(string $area, ?string $etapa = null): array
    {
        $questoesDoBanco = $this->buscarQuestoesDoBanco($area, $etapa);
        $questoes = $questoesDoBanco !== []
            ? $questoesDoBanco
            : ($this->questoes[$area] ?? $this->questoes['potenciacao']);

        if (count($questoes) < 5) {
            $questoes = array_merge($questoes, $this->getQuestoesComplementares($area));
        }

        $visiveis = [];
        $usadas = [];
        foreach ($questoes as $questao) {
            $chave = $questao['id'] ?? hash('sha256', json_encode($questao['enunciado'] ?? '') . '|' . json_encode($questao['alternativas'] ?? []));
            if (isset($usadas[$chave])) {
                continue;
            }

            $usadas[$chave] = true;
            $visiveis[] = $questao;
            if (count($visiveis) >= 5) {
                break;
            }
        }

        while (count($visiveis) < 5 && $questoes !== []) {
            $qualquer = $questoes[count($visiveis) % count($questoes)];
            $chave = $qualquer['id'] ?? hash('sha256', json_encode($qualquer['enunciado'] ?? '') . '|' . json_encode($qualquer['alternativas'] ?? []));
            if (!isset($usadas[$chave])) {
                $usadas[$chave] = true;
                $visiveis[] = $qualquer;
            }
        }

        return array_slice($visiveis, 0, 5);
    }

    /** Retorna sempre cinco questões alinhadas ao nó selecionado da trilha. */
    public function getQuestoesDaEtapa(string $area, string $etapa): array
    {
        if ($area !== 'potenciacao') {
            $quizzes = $this->getQuizzesEspecificosDaTrilha($area);
            $chave = str_replace('-', '', $this->normalizarArea($etapa));
            if (isset($quizzes[$chave])) {
                return $this->formatarQuizDaTrilha($quizzes[$chave]);
            }

            return array_slice($this->getQuestoes($area, $etapa), 0, 5);
        }

        // Também remove hífens para suportar nomes antigos gravados com acentuação
        // codificada incorretamente, sem deixar de identificar o conteúdo da etapa.
        $chave = str_replace('-', '', $this->normalizarArea($etapa));
        $quizzes = [
            'introducao' => [
                ['O que representa 2^3?', ['2 + 3', '2 × 2 × 2', '3 × 3', '2 × 3'], 1],
                ['Qual é o resultado de 3^2?', ['6', '9', '5', '8'], 1],
                ['Em 5^4, qual é a base?', ['4', '5', '20', '9'], 1],
                ['Qual expressão é uma potência?', ['4 + 4', '4 × 4', '4^2', '4 ÷ 2'], 2],
                ['Qual é o resultado de 1^8?', ['0', '1', '8', 'Indefinido'], 1],
            ],
            'baseeexpoente' => [
                ['No número 7^3, qual é o expoente?', ['7', '3', '10', '21'], 1],
                ['No número 4^5, qual é a base?', ['4', '5', '9', '20'], 0],
                ['Como se lê 6^2?', ['Seis vezes dois', 'Dois elevado a seis', 'Seis ao quadrado', 'Seis dividido por dois'], 2],
                ['Qual potência representa 8 × 8 × 8?', ['3^8', '8^3', '8^2', '24^1'], 1],
                ['Qual é o valor de 10^1?', ['1', '0', '10', '100'], 2],
            ],
            'potenciasdebase10' => [
                ['Qual é o resultado de 10^2?', ['20', '100', '1.000', '12'], 1],
                ['Qual é o resultado de 10^3?', ['30', '300', '1.000', '10.000'], 2],
                ['Quantos zeros tem 10^5?', ['3', '4', '5', '10'], 2],
                ['Qual potência de 10 vale 100.000?', ['10^3', '10^4', '10^5', '10^6'], 2],
                ['Qual é o resultado de 10^0?', ['0', '1', '10', 'Não existe'], 1],
            ],
            'exerciciosdepotenciacao' => [
                ['Qual é o resultado de 2^4?', ['6', '8', '16', '24'], 2],
                ['Qual é o resultado de 5^2?', ['10', '25', '7', '15'], 1],
                ['Qual é o resultado de 3^3?', ['9', '18', '27', '6'], 2],
                ['Qual é o resultado de 4^2?', ['8', '16', '6', '12'], 1],
                ['Qual é o resultado de 2^5?', ['10', '16', '25', '32'], 3],
            ],
            'revisao' => [
                ['Qual é o resultado de 9^0?', ['0', '1', '9', 'Não existe'], 1],
                ['Qual potência é igual a 49?', ['7^2', '7^3', '49^2', '2^7'], 0],
                ['Qual é o resultado de 6^2?', ['12', '18', '36', '64'], 2],
                ['Qual escrita equivale a 3 × 3 × 3 × 3?', ['3^3', '4^3', '3^4', '12^1'], 2],
                ['Em 2^6, o expoente indica que o 2 aparece quantas vezes?', ['2', '6', '8', '12'], 1],
            ],
            'produtodepotencias' => [
                ['Qual é o resultado de x^2 × x^3?', ['x^5', 'x^6', '2x^5', 'x'], 0],
                ['Qual é o resultado de 2^3 × 2^2?', ['2^5', '4^5', '2^6', '2^1'], 0],
                ['Na multiplicação de potências de mesma base, devemos:', ['Subtrair expoentes', 'Multiplicar bases', 'Somar expoentes', 'Dividir expoentes'], 2],
                ['Qual é o resultado de a^4 × a?', ['a^4', 'a^5', '2a^4', 'a^8'], 1],
                ['Qual é o resultado de 10^2 × 10^3?', ['10^5', '10^6', '100^5', '10^1'], 0],
            ],
            'quocientedepotencias' => [
                ['Qual é o resultado de x^5 ÷ x^2?', ['x^10', 'x^3', 'x^2', '1'], 1],
                ['Na divisão de potências de mesma base, devemos:', ['Somar expoentes', 'Subtrair expoentes', 'Multiplicar expoentes', 'Manter o maior'], 1],
                ['Qual é o resultado de 2^6 ÷ 2^2?', ['2^3', '2^4', '2^8', '4^4'], 1],
                ['Qual é o resultado de a^7 ÷ a?', ['a^6', 'a^7', 'a^8', '1'], 0],
                ['Qual é o resultado de 10^5 ÷ 10^3?', ['10^8', '10^2', '10^15', '1'], 1],
            ],
            'expoentesnegativos' => [
                ['Qual é o valor de 2^-1?', ['-2', '1/2', '2', '0'], 1],
                ['Qual é a forma de 10^-2?', ['100', '-100', '1/100', '1/10'], 2],
                ['Um expoente negativo indica:', ['Uma base negativa', 'O inverso da potência', 'Que o resultado é zero', 'Uma soma'], 1],
                ['Qual é o valor de 5^-1?', ['-5', '5', '1/5', '0'], 2],
                ['Qual é o valor de x^-3?', ['-x^3', '1/x^3', 'x/3', '3/x'], 1],
            ],
            'desafiofinal' => [
                ['Qual é o resultado de 2^3 × 2^4?', ['2^7', '4^7', '2^12', '2^1'], 0],
                ['Qual é o resultado de 3^5 ÷ 3^2?', ['3^7', '3^3', '1', '3^10'], 1],
                ['Qual é o valor de (2^3)^2?', ['2^5', '2^6', '4^5', '2^1'], 1],
                ['Qual é o resultado de 10^3 ÷ 10?', ['10^4', '10^3', '10^2', '10'], 2],
                ['Qual é o resultado de 4^0 + 2^3?', ['8', '9', '12', '1'], 1],
            ],
        ];

        $selecionadas = $quizzes[$chave] ?? $quizzes['introducao'];
        return $this->formatarQuizDaTrilha($selecionadas);
    }

    private function formatarQuizDaTrilha(array $questoes): array
    {
        return array_map(static fn (array $q): array => [
            'enunciado' => $q[0], 'alternativas' => $q[1], 'correta' => $q[2], 'explicacao' => '',
        ], $questoes);
    }

    private function getQuizzesEspecificosDaTrilha(string $area): array
    {
        return match ($area) {
            'fracoes-algebricas' => $this->getQuizzesFracoesAlgebricas(),
            'produtos-notaveis' => $this->getQuizzesProdutosNotaveis(),
            'equacoes' => $this->getQuizzesEquacoes(),
            'inequacoes' => $this->getQuizzesInequacoes(),
            'fatoracao' => $this->getQuizzesFatoracao(),
            default => [],
        };
    }

    private function getQuizzesProdutosNotaveis(): array
    {
        return [
            'padroesalgebricos' => [
                ['Qual expressão representa o quadrado de uma soma?', ['(a + b)²', '(a + b)(a - b)', 'a² - b²', 'a² + b²'], 0],
                ['Em (x + 4)², quais são os dois termos do binômio?', ['x e 4', 'x² e 4²', 'x + 4 e 2', 'x e x + 4'], 0],
                ['Qual produto é uma soma pela diferença?', ['(a + b)(a - b)', '(a + b)²', '(a - b)²', '(a + b)(a + b)'], 0],
                ['Qual é o termo do meio em (a + b)²?', ['2ab', 'a²b²', 'a + b', 'a² + b²'], 0],
                ['Qual identidade gera a² - b²?', ['(a + b)(a - b)', '(a + b)²', '(a - b)²', 'a(a - b)'], 0],
            ],
            'quadradodasoma' => [
                ['Qual é o desenvolvimento de (x + 3)²?', ['x² + 6x + 9', 'x² + 3x + 9', 'x² + 9', 'x² - 6x + 9'], 0],
                ['Qual é o desenvolvimento de (a + b)²?', ['a² + 2ab + b²', 'a² - 2ab + b²', 'a² + b²', '2a + 2b'], 0],
                ['Qual é o resultado de (y + 5)²?', ['y² + 10y + 25', 'y² + 5y + 25', 'y² + 25', 'y² - 10y + 25'], 0],
                ['No quadrado da soma, o termo do meio é:', ['O dobro do produto dos termos', 'A soma dos quadrados', 'A diferença dos termos', 'O produto dos quadrados'], 0],
                ['Qual expressão é igual a x² + 8x + 16?', ['(x + 4)²', '(x - 4)²', '(x + 8)²', '(x + 2)²'], 0],
            ],
            'quadradodadiferenca' => [
                ['Qual é o desenvolvimento de (x - 3)²?', ['x² - 6x + 9', 'x² - 9', 'x² + 6x + 9', 'x² - 3x + 9'], 0],
                ['Qual é o desenvolvimento de (a - b)²?', ['a² - 2ab + b²', 'a² + 2ab + b²', 'a² - b²', 'a² + b²'], 0],
                ['Qual é o resultado de (y - 4)²?', ['y² - 8y + 16', 'y² - 16', 'y² + 8y + 16', 'y² - 4y + 16'], 0],
                ['Qual expressão é igual a x² - 10x + 25?', ['(x - 5)²', '(x + 5)²', '(x - 10)²', '(x - 25)²'], 0],
                ['No quadrado da diferença, o termo do meio é:', ['Negativo e igual a 2ab', 'Positivo e igual a 2ab', 'Sempre zero', 'Igual a a²b²'], 0],
            ],
            'exerciciosdeprodutosnotaveis' => [
                ['Qual é o resultado de (2x + 1)²?', ['4x² + 4x + 1', '4x² + 1', '2x² + 4x + 1', '4x² - 4x + 1'], 0],
                ['Qual é o resultado de (3a - 2)²?', ['9a² - 12a + 4', '9a² - 4', '9a² + 12a + 4', '3a² - 12a + 4'], 0],
                ['Qual é o resultado de (x + 7)(x - 7)?', ['x² - 49', 'x² + 49', 'x² - 14x + 49', 'x² + 14x + 49'], 0],
                ['Qual é o resultado de (m + n)²?', ['m² + 2mn + n²', 'm² - 2mn + n²', 'm² - n²', '2m + 2n'], 0],
                ['Qual expressão fatorada representa x² - 16?', ['(x + 4)(x - 4)', '(x - 4)²', '(x + 16)(x - 1)', 'x(x - 16)'], 0],
            ],
            'revisao' => [
                ['Qual é o resultado de (x + 2)²?', ['x² + 4x + 4', 'x² + 2x + 4', 'x² + 4', 'x² - 4x + 4'], 0],
                ['Qual é o resultado de (x - 6)(x + 6)?', ['x² - 36', 'x² + 36', 'x² - 12x + 36', 'x² + 12x + 36'], 0],
                ['Qual expressão corresponde a a² + 14a + 49?', ['(a + 7)²', '(a - 7)²', '(a + 14)²', '(a + 49)²'], 0],
                ['Qual é o termo independente de (x - 9)²?', ['81', '-81', '18', '-18'], 0],
                ['Qual identidade simplifica (p + q)(p - q)?', ['p² - q²', 'p² + 2pq + q²', 'p² - 2pq + q²', 'p + q'], 0],
            ],
            'somapeladiferenca' => [
                ['Qual é o resultado de (a + b)(a - b)?', ['a² - b²', 'a² + b²', 'a² - 2ab + b²', 'a² + 2ab + b²'], 0],
                ['Qual é o resultado de (x + 5)(x - 5)?', ['x² - 25', 'x² + 25', 'x² - 10x + 25', 'x² + 10x + 25'], 0],
                ['Qual é o resultado de (3y + 2)(3y - 2)?', ['9y² - 4', '9y² + 4', '9y² - 12y + 4', '6y² - 4'], 0],
                ['Qual é o resultado de (m + 1)(m - 1)?', ['m² - 1', 'm² + 1', 'm² - 2m + 1', 'm² + 2m + 1'], 0],
                ['Qual produto fatorado corresponde a 4x² - 9?', ['(2x + 3)(2x - 3)', '(2x - 3)²', '(4x + 3)(x - 3)', '(2x + 9)(2x - 1)'], 0],
            ],
            'aplicacoes' => [
                ['A área de um quadrado de lado x + 2 é:', ['x² + 4x + 4', 'x² + 2x + 4', 'x² + 4', '2x + 4'], 0],
                ['Um retângulo tem lados x + 4 e x - 4. Sua área é:', ['x² - 16', 'x² + 16', 'x² - 8x + 16', 'x² + 8x + 16'], 0],
                ['Qual é o resultado de 102² usando produto notável?', ['10.404', '10.204', '10.000', '10.040'], 0],
                ['Qual é o resultado de 98 × 102?', ['9.996', '10.000', '9.800', '10.004'], 0],
                ['Qual expressão calcula a diferença entre os quadrados de x e 3?', ['(x + 3)(x - 3)', '(x - 3)²', '(x + 3)²', 'x(x - 3)'], 0],
            ],
            'formulas' => [
                ['Qual é a fórmula correta para (a + b)²?', ['a² + 2ab + b²', 'a² - 2ab + b²', 'a² - b²', 'a² + b²'], 0],
                ['Qual é a fórmula correta para (a - b)²?', ['a² - 2ab + b²', 'a² + 2ab + b²', 'a² - b²', 'a² + b²'], 0],
                ['Qual fórmula corresponde a a² - b²?', ['(a + b)(a - b)', '(a - b)²', '(a + b)²', 'a(a - b)'], 0],
                ['Em (a + b)², qual termo é sempre positivo?', ['b²', '-2ab', '-b²', 'a - b'], 0],
                ['Em (a - b)², qual termo do meio aparece?', ['-2ab', '+2ab', 'ab²', '-a²b²'], 0],
            ],
            'desafiofinal' => [
                ['Qual é o resultado de (x + 3)² - (x - 3)²?', ['12x', '6x', '18', '12x²'], 0],
                ['Qual é o resultado de (2x + 3)(2x - 3)?', ['4x² - 9', '4x² + 9', '4x² - 12x + 9', '2x² - 9'], 0],
                ['Qual expressão é equivalente a x² - 25?', ['(x + 5)(x - 5)', '(x - 5)²', '(x + 25)(x - 1)', 'x(x - 25)'], 0],
                ['Qual é o resultado de (a - 2)² + 4a?', ['a² + 4', 'a² - 4a + 4', 'a² + 4a + 4', 'a² - 4'], 0],
                ['Qual é o resultado de (x + 1)² - 2x?', ['x² + 1', 'x² + 2x + 1', 'x² - 1', '2x² + 1'], 0],
            ],
        ];
    }

    private function getQuizzesFracoesAlgebricas(): array
    {
        return [
            'termosalgebricos' => [
                ['Em 3x/5, qual é o numerador?', ['3x', '5', 'x/5', '3'], 0],
                ['Em a/(b + 1), qual é o denominador?', ['a', 'b', 'b + 1', 'a + b'], 2],
                ['Qual expressão é uma fração algébrica?', ['x + 4', '5/x', '2x', 'x²'], 1],
                ['Em (x + 2)/(x - 3), qual parte não pode ser zero?', ['x + 2', 'x - 3', 'x', '2'], 1],
                ['Qual é o coeficiente de x em 7x/y?', ['7', 'x', 'y', '7y'], 0],
            ],
            'dominio' => [
                ['Qual valor deve ser excluído do domínio de 1/x?', ['0', '1', '-1', 'Nenhum'], 0],
                ['Qual valor de x não pertence ao domínio de 3/(x - 4)?', ['0', '3', '4', '-4'], 2],
                ['Para qual valor x/(x + 2) não está definida?', ['2', '-2', '0', '1'], 1],
                ['O domínio de uma fração algébrica exclui valores que:', ['Zeram o numerador', 'Zeram o denominador', 'Tornam x positivo', 'Tornam x inteiro'], 1],
                ['Qual valor deve ser excluído de 5/(2x)?', ['0', '2', '5', '-2'], 0],
            ],
            'fatorcomum' => [
                ['Qual é a forma fatorada de 6x + 12?', ['6(x + 2)', '3(2x + 12)', '6x(1 + 2)', '12(x + 1)'], 0],
                ['Qual fator comum pode ser retirado de 4x² + 8x?', ['2x', '4x', '8x', '4'], 1],
                ['Simplifique 6x/(3x), com x ≠ 0.', ['2', '3', '2x', 'x/2'], 0],
                ['Simplifique 10a²/(5a), com a ≠ 0.', ['2a', '2a²', '5a', 'a/2'], 0],
                ['Qual fração equivale a 3(x + 1)/3?', ['x + 1', '3x + 1', 'x + 3', '3(x + 1)'], 0],
            ],
            'exercicioscomfracoes' => [
                ['Simplifique 8x/(4).', ['2x', '4x', '8x', 'x/2'], 0],
                ['Simplifique 12y/(3y), y ≠ 0.', ['4', '4y', '9y', '1/4'], 0],
                ['Qual é a forma simplificada de (x² - 4)/(x - 2)?', ['x - 2', 'x + 2', 'x² + 2', '4'], 1],
                ['Simplifique (3x + 6)/3.', ['x + 2', '3x + 2', 'x + 6', '3x'], 0],
                ['Qual é o resultado de (5a)/(a), a ≠ 0?', ['5', 'a', '5a', '1'], 0],
            ],
            'revisao' => [
                ['Qual valor é proibido em 2/(x + 5)?', ['5', '-5', '0', '2'], 1],
                ['Simplifique 9x/(3x), x ≠ 0.', ['3', '3x', '6x', 'x/3'], 0],
                ['Qual fatoração é correta para x² - 9?', ['(x - 3)(x + 3)', '(x - 9)(x + 1)', 'x(x - 9)', '(x - 3)²'], 0],
                ['A expressão 4/(x - 1) está definida para x =:', ['1', '0', 'Somente 1', 'Nenhum número'], 1],
                ['Qual é o denominador de (2x - 1)/(3x)?', ['2x - 1', '3x', 'x', '2x'], 1],
            ],
            'multiplicacao' => [
                ['Qual é o resultado de (2/x) × (x/3), x ≠ 0?', ['2/3', '2x/3', 'x/6', '6'], 0],
                ['Ao multiplicar frações algébricas, devemos:', ['Somar numeradores', 'Multiplicar numeradores e denominadores', 'Subtrair denominadores', 'Inverter a primeira fração'], 1],
                ['Simplifique (3x/4) × (8/x), x ≠ 0.', ['6', '24', '6x', '2x'], 0],
                ['Qual é o resultado de (a/5) × (10/a), a ≠ 0?', ['2', '50', '2a', 'a/2'], 0],
                ['Qual é o resultado de (x/2) × (3/x), x ≠ 0?', ['3/2', '3x/2', 'x/6', '6/x'], 0],
            ],
            'somaesubtracao' => [
                ['Qual é o resultado de 2/x + 3/x?', ['5/x', '5/(2x)', '6/x', 'x/5'], 0],
                ['Qual é o resultado de 7/a - 2/a?', ['5/a', '9/a', '5', 'a/5'], 0],
                ['Qual denominador comum serve para 1/x e 1/2?', ['x', '2', '2x', 'x + 2'], 2],
                ['Qual é o resultado de x/3 + 2/3?', ['(x + 2)/3', 'x + 2/3', '(2x)/3', 'x/5'], 0],
                ['Qual é o resultado de 5/(x + 1) - 2/(x + 1)?', ['3/(x + 1)', '7/(x + 1)', '3/x', '3'], 0],
            ],
            'fracoescomplexas' => [
                ['Simplifique (1/x) ÷ (2/x), x ≠ 0.', ['1/2', '2', 'x/2', '2/x'], 0],
                ['Dividir por uma fração é o mesmo que:', ['Somar a fração', 'Multiplicar pelo inverso', 'Subtrair o inverso', 'Dobrar o denominador'], 1],
                ['Qual é o resultado de (3/a) ÷ (6/a), a ≠ 0?', ['1/2', '2', '18/a', 'a/2'], 0],
                ['Simplifique (x/4) ÷ (x/8), x ≠ 0.', ['2', '1/2', 'x/32', '32/x'], 0],
                ['O inverso de 5/x é:', ['x/5', '5x', '1/(5x)', 'x - 5'], 0],
            ],
            'desafiofinal' => [
                ['Simplifique (x² - 4)/(x - 2), x ≠ 2.', ['x - 2', 'x + 2', 'x² + 2', '4'], 1],
                ['Qual é o resultado de 1/x + 1/x?', ['2/x', '1/(2x)', 'x/2', '2x'], 0],
                ['Simplifique (2x/3) × (9/(4x)), x ≠ 0.', ['3/2', '6', '3x/2', '9/(6x)'], 0],
                ['Qual valor deve ser excluído de (x + 1)/(x² - 1)?', ['0', '1 e -1', 'Somente 1', 'Somente -1'], 1],
                ['Qual é o resultado de (4/a) ÷ (2/a), a ≠ 0?', ['2', '8/a', '2a', 'a/2'], 0],
            ],
        ];
    }

    private function getQuizzesEquacoes(): array
    {
        return [
            'principiodaigualdade' => [
                ['Em uma equação, o que fazemos em um lado deve ser feito no outro?', ['Nada', 'A mesma operação', 'Somente uma soma', 'Somente uma divisão'], 1],
                ['Em x + 4 = 9, qual operação isola x?', ['Somar 4', 'Subtrair 4', 'Multiplicar por 4', 'Dividir por 4'], 1],
                ['Qual é a solução de x - 3 = 8?', ['5', '11', '-5', '-11'], 1],
                ['Qual é a solução de x + 7 = 7?', ['7', '14', '0', '-7'], 2],
                ['Se 2x = 14, qual operação deve ser usada para isolar x?', ['Somar 2', 'Subtrair 2', 'Multiplicar por 2', 'Dividir por 2'], 3],
            ],
            'termossemelhantes' => [
                ['Quais termos são semelhantes?', ['3x e 3y', '2x e 5x', 'x e x²', '4 e 4x'], 1],
                ['Qual é o resultado de 3x + 5x?', ['8', '8x', '15x', '2x'], 1],
                ['Simplifique 7a - 2a.', ['5', '5a', '9a', '-5a'], 1],
                ['Qual é o resultado de 4x + 3 - x?', ['3x + 3', '4x + 2', '3x', '7x'], 0],
                ['Qual é o resultado de 2y + 6y - y?', ['7', '7y', '8y', '6y'], 1],
            ],
            'isolandoaincognita' => [
                ['Qual é a solução de 3x = 18?', ['3', '6', '9', '21'], 1],
                ['Qual é a solução de 5x + 2 = 17?', ['2', '3', '5', '15'], 1],
                ['Qual é a solução de 4x - 8 = 12?', ['1', '4', '5', '8'], 2],
                ['Qual é a solução de x/3 = 5?', ['2', '8', '15', '5/3'], 2],
                ['Qual é a solução de 2x + 6 = 0?', ['3', '-3', '6', '-6'], 1],
            ],
            'exerciciosdeequacoes' => [
                ['Qual é a solução de 7x = 49?', ['6', '7', '8', '42'], 1],
                ['Qual é a solução de x - 9 = 4?', ['-5', '5', '13', '-13'], 2],
                ['Qual é a solução de 6x + 1 = 19?', ['2', '3', '4', '5'], 1],
                ['Qual é a solução de 3(x + 2) = 15?', ['3', '5', '7', '9'], 0],
                ['Qual é a solução de 10 - 2x = 4?', ['2', '3', '-2', '-3'], 1],
            ],
            'revisao' => [
                ['Qual é a solução de 2x - 4 = 10?', ['3', '5', '7', '14'], 2],
                ['Qual é a solução de 8x = 64?', ['6', '7', '8', '9'], 2],
                ['Qual é a solução de x + 12 = 20?', ['8', '12', '20', '32'], 0],
                ['Qual é a solução de 5x = 0?', ['0', '5', '1', 'Não existe'], 0],
                ['Qual é a solução de 9 + x = 3?', ['6', '-6', '12', '-12'], 1],
            ],
            'parenteses' => [
                ['Qual é a solução de 2(x + 3) = 14?', ['4', '7', '10', '1'], 0],
                ['Qual é a solução de 3(x - 1) = 12?', ['3', '4', '5', '6'], 2],
                ['Qual expressão resulta de 4(x + 2)?', ['4x + 2', '4x + 6', '4x + 8', 'x + 8'], 2],
                ['Qual é a solução de 5(x + 1) = 20?', ['3', '4', '5', '15'], 0],
                ['Qual é a solução de 2(x - 4) = 6?', ['1', '3', '7', '11'], 2],
            ],
            'problemas' => [
                ['Um número somado a 8 é 15. Qual é o número?', ['5', '7', '8', '23'], 1],
                ['O dobro de um número é 18. Qual é esse número?', ['7', '8', '9', '16'], 2],
                ['Um número menos 6 é igual a 10. Qual é o número?', ['4', '6', '16', '-16'], 2],
                ['Três vezes um número mais 2 é 14. Qual é o número?', ['2', '4', '6', '12'], 1],
                ['A metade de um número é 11. Qual é o número?', ['5,5', '11', '13', '22'], 3],
            ],
            '2ograu' => [
                ['Quais são as raízes de x² - 9 = 0?', ['x = 3', 'x = -3', 'x = 3 ou x = -3', 'x = 9'], 2],
                ['Quais são as raízes de x² - 4x = 0?', ['0 e 4', '2 e 2', '-4 e 0', '1 e 4'], 0],
                ['Qual é a solução positiva de x² = 25?', ['-5', '5', '25', '0'], 1],
                ['Qual é a fatoração de x² - 16?', ['(x - 4)(x + 4)', '(x - 16)(x + 1)', 'x(x - 16)', '(x - 4)²'], 0],
                ['Quais são as raízes de x² + x - 6 = 0?', ['2 e 3', '-2 e 3', '2 e -3', '-2 e -3'], 2],
            ],
            'desafiofinal' => [
                ['Qual é a solução de 2(x + 1) + 3 = 13?', ['3', '4', '5', '6'], 1],
                ['Qual é a solução de 4x - 3 = 2x + 7?', ['2', '5', '7', '10'], 1],
                ['Quais são as raízes de x² - 5x = 0?', ['0 e 5', '1 e 5', '-5 e 0', '5 e 5'], 0],
                ['Qual é a solução de (x/2) + 4 = 10?', ['3', '6', '12', '14'], 2],
                ['Qual é a solução de 3(x - 2) = 2x + 4?', ['4', '6', '8', '10'], 3],
            ],
        ];
    }

    private function getQuizzesInequacoes(): array
    {
        return [
            'simbolosdecomparacao' => [
                ['Qual símbolo significa “maior que”?', ['<', '>', '≤', '≠'], 1],
                ['Qual símbolo significa “menor ou igual a”?', ['≥', '≤', '<', '>'], 1],
                ['Qual sentença representa “x é maior que 4”?', ['x < 4', 'x > 4', 'x ≤ 4', 'x = 4'], 1],
                ['Qual símbolo significa “maior ou igual a”?', ['≥', '≤', '=', '≠'], 0],
                ['Qual é a leitura de x ≠ 2?', ['x é igual a 2', 'x é menor que 2', 'x é diferente de 2', 'x é maior que 2'], 2],
            ],
            'conjuntosolucao' => [
                ['Qual número satisfaz x > 5?', ['4', '5', '6', '-1'], 2],
                ['Qual número satisfaz x ≤ 3?', ['4', '5', '3', '10'], 2],
                ['Qual conjunto representa x > 2?', ['{0, 1, 2}', '{3, 4, 5, ...}', '{2}', '{..., 0, 1}'], 1],
                ['Qual número NÃO satisfaz x < 7?', ['2', '6', '7', '-3'], 2],
                ['Se x ≥ 0, qual número pertence à solução?', ['-2', '-1', '0', '-5'], 2],
            ],
            'retanumerica' => [
                ['Para x > 3, o ponto 3 na reta é:', ['Fechado', 'Aberto', 'Inexistente', 'Sempre azul'], 1],
                ['Para x ≥ 3, o ponto 3 na reta é:', ['Aberto', 'Fechado', 'Excluído', 'Negativo'], 1],
                ['A solução de x < 4 é representada para qual lado?', ['Direita de 4', 'Esquerda de 4', 'Somente no 4', 'Em nenhum lado'], 1],
                ['A solução de x > -2 é representada para qual lado?', ['Esquerda de -2', 'Direita de -2', 'Somente no -2', 'Não existe'], 1],
                ['Em x ≤ 1, o número 1 pertence à solução?', ['Sim', 'Não', 'Somente se x for 0', 'Nunca'], 0],
            ],
            'exerciciosdeinequacoes' => [
                ['Qual é a solução de x + 2 > 5?', ['x > 3', 'x < 3', 'x > 7', 'x < 7'], 0],
                ['Qual é a solução de 2x > 8?', ['x > 4', 'x < 4', 'x > 6', 'x < 6'], 0],
                ['Qual é a solução de x - 4 ≤ 3?', ['x ≤ -1', 'x ≤ 7', 'x ≥ 7', 'x < 3'], 1],
                ['Qual é a solução de 3x ≥ 12?', ['x ≥ 4', 'x ≤ 4', 'x ≥ 9', 'x > 12'], 0],
                ['Qual é a solução de x/2 < 5?', ['x < 2,5', 'x < 7', 'x < 10', 'x > 10'], 2],
            ],
            'revisao' => [
                ['Qual é a solução de x + 7 < 10?', ['x < 3', 'x > 3', 'x < 17', 'x > 17'], 0],
                ['Qual é a solução de 5x ≤ 20?', ['x ≤ 4', 'x ≥ 4', 'x ≤ 15', 'x > 25'], 0],
                ['Qual número satisfaz x ≥ -1?', ['-2', '-3', '-1', '-5'], 2],
                ['Qual é a solução de 4x - 1 > 7?', ['x > 1', 'x > 2', 'x < 2', 'x < 8'], 1],
                ['Em x < 0, o zero está incluído?', ['Sim', 'Não', 'Somente às vezes', 'Apenas se x for inteiro'], 1],
            ],
            'intervalos' => [
                ['Qual intervalo representa x > 2?', ['(2, +∞)', '[2, +∞)', '(-∞, 2)', '(-∞, 2]'], 0],
                ['Qual intervalo representa x ≤ 4?', ['(4, +∞)', '[4, +∞)', '(-∞, 4]', '(-∞, 4)'], 2],
                ['Qual intervalo representa -1 < x < 3?', ['[-1, 3]', '(-1, 3)', '(-∞, 3)', '[-1, +∞)'], 1],
                ['O colchete [ indica que o extremo é:', ['Excluído', 'Incluído', 'Negativo', 'Infinito'], 1],
                ['O parêntese ( indica que o extremo é:', ['Incluído', 'Excluído', 'Inteiro', 'Positivo'], 1],
            ],
            'sistemas' => [
                ['Qual é a interseção de x > 2 e x < 6?', ['x < 2', '2 < x < 6', 'x > 6', 'Todos os reais'], 1],
                ['Qual é a solução de x ≥ 1 e x ≤ 4?', ['[1, 4]', '(1, 4)', '(-∞, 1]', '[4, +∞)'], 0],
                ['Qual é a interseção de x < 3 e x < 5?', ['x < 3', 'x < 5', 'x > 3', '3 < x < 5'], 0],
                ['Qual é a interseção de x > 4 e x > 1?', ['x > 1', 'x > 4', '1 < x < 4', 'Nenhuma'], 1],
                ['Qual é a solução de x ≥ 0 e x < 0?', ['x = 0', 'Todos os reais', 'Nenhum número real', 'x > 0'], 2],
            ],
            'pratica' => [
                ['Qual é a solução de -2x < 8?', ['x < -4', 'x > -4', 'x < 4', 'x > 4'], 1],
                ['Ao multiplicar uma inequação por número negativo, devemos:', ['Manter o sinal', 'Inverter o sinal', 'Somar 1', 'Zerar x'], 1],
                ['Qual é a solução de -3x ≥ 9?', ['x ≥ -3', 'x ≤ -3', 'x ≥ 3', 'x ≤ 3'], 1],
                ['Qual é a solução de 2 - x > 5?', ['x > -3', 'x < -3', 'x > 3', 'x < 3'], 1],
                ['Qual é a solução de -x ≤ 2?', ['x ≥ -2', 'x ≤ -2', 'x ≥ 2', 'x ≤ 2'], 0],
            ],
            'desafiofinal' => [
                ['Qual é a solução de 2(x - 1) ≤ 6?', ['x ≤ 2', 'x ≤ 4', 'x ≥ 4', 'x < 6'], 1],
                ['Qual é a solução de 3x + 2 > 11?', ['x > 3', 'x < 3', 'x > 13', 'x < 13'], 0],
                ['Qual é a solução de -x + 4 ≥ 7?', ['x ≥ -3', 'x ≤ -3', 'x ≥ 3', 'x ≤ 3'], 1],
                ['Qual intervalo representa x ≥ -2?', ['(-∞, -2)', '(-∞, -2]', '[-2, +∞)', '(-2, +∞)'], 2],
                ['Qual é a interseção de x > 0 e x ≤ 5?', ['[0, 5]', '(0, 5]', '(0, 5)', '[0, 5)'], 1],
            ],
        ];
    }

    private function getQuizzesFatoracao(): array
    {
        return [
            'fatorcomum' => [
                ['Qual é a fatoração de 3x + 6?', ['3(x + 2)', '6(x + 3)', 'x(3 + 6)', '3(x + 6)'], 0],
                ['Qual fator comum pode ser colocado em evidência em 5x + 10?', ['5', '10', 'x', '15'], 0],
                ['Qual é a fatoração de 4x² + 8x?', ['4x(x + 2)', '4(x² + 8x)', '8x(x + 1)', '4x²(1 + 2)'], 0],
                ['Qual é a fatoração de 7a - 14?', ['7(a - 2)', '14(a - 1)', '7a(a - 2)', 'a(7 - 14)'], 0],
                ['Qual fator comum há em 6y² - 9y?', ['3y', '6y', '9y', 'y²'], 0],
            ],
            'agrupamento' => [
                ['Qual é a fatoração de ax + ay + bx + by?', ['(a + b)(x + y)', '(a + x)(b + y)', 'ab(x + y)', '(a + b)xy'], 0],
                ['Qual é a fatoração de x² + 3x + 2x + 6?', ['(x + 2)(x + 3)', '(x + 6)(x + 1)', 'x(x + 5) + 6', '(x + 3)²'], 0],
                ['Qual é o primeiro passo na fatoração por agrupamento?', ['Agrupar termos com fator comum', 'Somar todos os termos', 'Dividir por zero', 'Elevar tudo ao quadrado'], 0],
                ['Qual é a fatoração de 2x + 2y + ax + ay?', ['(2 + a)(x + y)', '(2a)(x + y)', '(x + a)(y + 2)', '2(x + y) + a'], 0],
                ['Agrupando x² + 2x + 3x + 6, qual fator comum aparece?', ['x + 3', 'x + 2', 'x²', '6'], 1],
            ],
            'diferencadequadrados' => [
                ['Qual é a fatoração de x² - 9?', ['(x - 3)(x + 3)', '(x - 9)(x + 1)', '(x - 3)²', 'x(x - 9)'], 0],
                ['Qual é a fatoração de a² - 16?', ['(a - 4)(a + 4)', '(a - 16)(a + 1)', '(a - 4)²', 'a(a - 16)'], 0],
                ['Qual fórmula representa diferença de quadrados?', ['a² - b² = (a - b)(a + b)', 'a² + b² = (a + b)²', 'a² - b² = (a - b)²', 'a² + b² = (a - b)(a + b)'], 0],
                ['Qual é a fatoração de 25x² - 1?', ['(5x - 1)(5x + 1)', '(25x - 1)(x + 1)', '(5x - 1)²', 'x(25x - 1)'], 0],
                ['Qual é a fatoração de 4y² - 49?', ['(2y - 7)(2y + 7)', '(4y - 7)(y + 7)', '(2y - 7)²', '(4y - 49)'], 0],
            ],
            'exerciciosdefatoracao' => [
                ['Qual é a fatoração de x² + 5x?', ['x(x + 5)', '(x + 5)²', 'x(x - 5)', '5(x + 1)'], 0],
                ['Qual é a fatoração de 2x² - 8x?', ['2x(x - 4)', '2(x² - 4)', '8x(x - 2)', '2x(x + 4)'], 0],
                ['Qual é a fatoração de y² - 25?', ['(y - 5)(y + 5)', '(y - 25)(y + 1)', '(y - 5)²', 'y(y - 25)'], 0],
                ['Qual é a forma expandida de 3(x + 4)?', ['3x + 4', '3x + 7', '3x + 12', 'x + 12'], 2],
                ['Qual é a fatoração de 6a + 18?', ['6(a + 3)', '18(a + 1)', '3(2a + 18)', '6a(a + 3)'], 0],
            ],
            'revisao' => [
                ['Qual é a fatoração de 8x + 12?', ['4(2x + 3)', '8(x + 12)', '2(4x + 12)', '4x(2 + 3)'], 0],
                ['Qual é a fatoração de z² - 36?', ['(z - 6)(z + 6)', '(z - 36)(z + 1)', '(z - 6)²', 'z(z - 36)'], 0],
                ['Qual é a fatoração de x² + 7x?', ['x(x + 7)', '(x + 7)²', '7(x + 1)', 'x(x - 7)'], 0],
                ['Qual fator comum há em 9a² + 3a?', ['3a', '9a', '3', 'a²'], 0],
                ['Qual é a fatoração de x² + 4x + 4?', ['(x + 2)²', '(x + 4)²', '(x - 2)²', 'x(x + 4)'], 0],
            ],
            'trinomios' => [
                ['Qual é a fatoração de x² + 5x + 6?', ['(x + 2)(x + 3)', '(x + 1)(x + 6)', '(x - 2)(x - 3)', '(x + 5)(x + 1)'], 0],
                ['Qual é a fatoração de x² - 5x + 6?', ['(x - 2)(x - 3)', '(x + 2)(x + 3)', '(x - 1)(x - 6)', '(x + 1)(x - 6)'], 0],
                ['Qual é a fatoração de x² + 7x + 12?', ['(x + 3)(x + 4)', '(x + 2)(x + 6)', '(x - 3)(x - 4)', '(x + 1)(x + 12)'], 0],
                ['Qual é a fatoração de x² - x - 6?', ['(x - 3)(x + 2)', '(x + 3)(x - 2)', '(x - 1)(x + 6)', '(x + 6)(x - 1)'], 0],
                ['Para fatorar x² + bx + c, buscamos dois números cujo produto seja:', ['b', 'c', 'b + c', 'b²'], 1],
            ],
            'somadecubos' => [
                ['Qual é a fatoração de a³ + b³?', ['(a + b)(a² - ab + b²)', '(a + b)³', '(a - b)(a² + ab + b²)', 'a(a² + b²)'], 0],
                ['Qual é a fatoração de x³ + 8?', ['(x + 2)(x² - 2x + 4)', '(x + 2)³', '(x - 2)(x² + 2x + 4)', 'x(x² + 8)'], 0],
                ['Qual é a fatoração de x³ - 8?', ['(x - 2)(x² + 2x + 4)', '(x - 2)³', '(x + 2)(x² - 2x + 4)', 'x(x² - 8)'], 0],
                ['Qual é o cubo de 2?', ['4', '6', '8', '16'], 2],
                ['Na soma de cubos, o segundo fator começa com:', ['a² + ab + b²', 'a² - ab + b²', 'a² - b²', 'a + b'], 1],
            ],
            'pratica' => [
                ['Qual é a fatoração de 3x² + 12x?', ['3x(x + 4)', '3(x² + 4)', '12x(x + 3)', '3x(x - 4)'], 0],
                ['Qual é a fatoração de 16 - x²?', ['(4 - x)(4 + x)', '(16 - x)(1 + x)', '(4 - x)²', 'x(16 - x)'], 0],
                ['Qual é a fatoração de x² + 9x + 20?', ['(x + 4)(x + 5)', '(x + 2)(x + 10)', '(x - 4)(x - 5)', '(x + 1)(x + 20)'], 0],
                ['Qual é a fatoração de 4a³ + 12a²?', ['4a²(a + 3)', '4a(a² + 3)', '12a²(a + 1)', '4a²(a - 3)'], 0],
                ['Qual é a fatoração de 49m² - n²?', ['(7m - n)(7m + n)', '(49m - n)(m + n)', '(7m - n)²', '(49m - n²)'], 0],
            ],
            'desafiofinal' => [
                ['Qual é a fatoração de x² - 9x + 20?', ['(x - 4)(x - 5)', '(x + 4)(x + 5)', '(x - 2)(x - 10)', '(x - 1)(x - 20)'], 0],
                ['Qual é a fatoração de 2x² + 10x?', ['2x(x + 5)', '2(x² + 5)', '10x(x + 2)', '2x(x - 5)'], 0],
                ['Qual é a fatoração de 9a² - 25?', ['(3a - 5)(3a + 5)', '(9a - 5)(a + 5)', '(3a - 5)²', 'a(9a - 25)'], 0],
                ['Qual é a fatoração de x³ - 27?', ['(x - 3)(x² + 3x + 9)', '(x - 3)³', '(x + 3)(x² - 3x + 9)', 'x(x² - 27)'], 0],
                ['Qual é a fatoração de x² + x - 12?', ['(x + 4)(x - 3)', '(x - 4)(x + 3)', '(x + 6)(x - 2)', '(x - 1)(x + 12)'], 0],
            ],
        ];
    }

    private function getQuestoesComplementares(string $area): array
    {
        $questoes = [
            'potenciacao' => [
                ['enunciado' => 'Qual Ã© o resultado de 5^0?', 'alternativas' => ['0', '1', '5', 'NÃ£o existe'], 'correta' => 1, 'explicacao' => 'Toda potÃªncia de base diferente de zero elevada a 0 Ã© igual a 1.'],
                ['enunciado' => 'Qual Ã© o resultado de 10^2?', 'alternativas' => ['20', '100', '1.000', '12'], 'correta' => 1, 'explicacao' => '10^2 Ã© 10 multiplicado por 10, portanto Ã© igual a 100.'],
                ['enunciado' => 'Qual expressÃ£o Ã© equivalente a 3^4?', 'alternativas' => ['3 + 4', '3 x 4', '3 x 3 x 3 x 3', '4 x 4 x 4'], 'correta' => 2, 'explicacao' => 'O expoente indica quantas vezes a base Ã© multiplicada por ela mesma.'],
            ],
            'fracoes-algebricas' => [
                ['enunciado' => 'Qual Ã© a forma simplificada de 8x / 4?', 'alternativas' => ['2x', '4x', '8x', 'x/2'], 'correta' => 0, 'explicacao' => 'Dividindo 8 por 4, obtemos 2x.'],
                ['enunciado' => 'Qual valor de x deve ser excluÃ­do de 1/x?', 'alternativas' => ['0', '1', '-1', 'Nenhum'], 'correta' => 0, 'explicacao' => 'O denominador nÃ£o pode ser igual a zero.'],
                ['enunciado' => 'Qual Ã© o resultado de (2x)/x, com x diferente de zero?', 'alternativas' => ['0', '1', '2', '2x'], 'correta' => 2, 'explicacao' => 'O fator x Ã© simplificado, restando 2.'],
            ],
            'produtos-notaveis' => [
                ['enunciado' => 'Qual Ã© o desenvolvimento de (x - 2)^2?', 'alternativas' => ['x^2 - 4x + 4', 'x^2 - 4', 'x^2 + 4x + 4', 'x^2 - 2x + 4'], 'correta' => 0, 'explicacao' => 'Aplicamos (a-b)^2 = a^2 - 2ab + b^2.'],
                ['enunciado' => 'Qual Ã© o resultado de (a + b)(a - b)?', 'alternativas' => ['a^2 + b^2', 'a^2 - b^2', 'a^2 - 2ab + b^2', 'a + b'], 'correta' => 1, 'explicacao' => 'Ã‰ o produto da soma pela diferenÃ§a.'],
                ['enunciado' => 'Qual termo aparece duas vezes em (x + y)^2?', 'alternativas' => ['x^2', 'y^2', '2xy', 'x + y'], 'correta' => 2, 'explicacao' => 'O termo do meio Ã© 2xy.'],
            ],
            'fatoracao' => [
                ['enunciado' => 'Qual Ã© a fatoraÃ§Ã£o de x^2 - 16?', 'alternativas' => ['(x - 4)(x + 4)', '(x - 16)(x + 1)', 'x(x - 16)', '(x - 4)^2'], 'correta' => 0, 'explicacao' => 'Ã‰ uma diferenÃ§a de quadrados: x^2 - 4^2.'],
                ['enunciado' => 'Qual Ã© o fator comum de 4x + 8?', 'alternativas' => ['2', '4', '8', 'x'], 'correta' => 1, 'explicacao' => '4 Ã© divisor comum de 4x e 8.'],
                ['enunciado' => 'Qual Ã© a fatoraÃ§Ã£o de x^2 + 5x?', 'alternativas' => ['x(x + 5)', '(x + 5)^2', 'x(x + 1)', '(x - 5)(x + 5)'], 'correta' => 0, 'explicacao' => 'Colocamos x em evidÃªncia.'],
                ['enunciado' => 'Qual expressÃ£o resulta de 2(x + 3)?', 'alternativas' => ['2x + 3', '2x + 5', '2x + 6', 'x + 6'], 'correta' => 2, 'explicacao' => 'A distributiva multiplica os dois termos por 2.'],
            ],
            'equacoes' => [
                ['enunciado' => 'Qual Ã© a soluÃ§Ã£o de x - 7 = 5?', 'alternativas' => ['2', '12', '-12', '35'], 'correta' => 1, 'explicacao' => 'Somamos 7 aos dois lados da equaÃ§Ã£o.'],
                ['enunciado' => 'Qual Ã© a soluÃ§Ã£o de 3x = 18?', 'alternativas' => ['3', '6', '9', '21'], 'correta' => 1, 'explicacao' => 'Dividimos os dois lados por 3.'],
                ['enunciado' => 'Qual Ã© o valor de x em 5x - 5 = 20?', 'alternativas' => ['3', '4', '5', '6'], 'correta' => 2, 'explicacao' => 'Somando 5, temos 5x = 25; depois dividimos por 5.'],
                ['enunciado' => 'Qual Ã© a soluÃ§Ã£o de x/2 = 4?', 'alternativas' => ['2', '4', '6', '8'], 'correta' => 3, 'explicacao' => 'Multiplicamos ambos os lados por 2.'],
            ],
            'inequacoes' => [
                ['enunciado' => 'Qual Ã© a soluÃ§Ã£o de 2x > 8?', 'alternativas' => ['x > 4', 'x < 4', 'x > 6', 'x < 6'], 'correta' => 0, 'explicacao' => 'Dividimos ambos os lados positivos por 2.'],
                ['enunciado' => 'Qual nÃºmero satisfaz x < 3?', 'alternativas' => ['5', '3', '2', '4'], 'correta' => 2, 'explicacao' => '2 Ã© menor que 3.'],
                ['enunciado' => 'Qual Ã© a soluÃ§Ã£o de x - 1 >= 4?', 'alternativas' => ['x >= 3', 'x >= 5', 'x <= 5', 'x > 4'], 'correta' => 1, 'explicacao' => 'Somamos 1 aos dois lados.'],
                ['enunciado' => 'Qual sÃ­mbolo representa "menor ou igual"?', 'alternativas' => ['>', '<', '>=', '<='], 'correta' => 3, 'explicacao' => 'O sÃ­mbolo <= significa menor ou igual.'],
            ],
        ];

        return $questoes[$area] ?? $questoes['potenciacao'];
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

    private function buscarQuestoesDoBanco(?string $area = null, ?string $etapa = null): array
    {
        try {
            $db = Database::getConnection();
            $stmt = $db->query(
                'SELECT questao.id, questao.enunciado, questao.alternativa_a, questao.alternativa_b,
                        questao.alternativa_c, questao.alternativa_d, questao.resposta_correta,
                        questao.explicacao, questao.pontuacao, questao.atividade_id,
                        assunto.nome AS assunto,
                        etapa_trilha.nome AS etapa
                 FROM questao
                 LEFT JOIN assunto ON assunto.id = questao.assunto_id
                 LEFT JOIN atividade ON atividade.id = questao.atividade_id
                 LEFT JOIN etapa_trilha ON etapa_trilha.id = atividade.etapa_id
                 ORDER BY questao.id'
            );
            $questoes = [];

            foreach ($stmt->fetchAll() as $questao) {
                if ($area !== null && $this->normalizarArea((string) $questao['assunto']) !== $area) {
                    continue;
                }

                if ($etapa !== null && trim($etapa) !== '' && $this->normalizarArea((string) $questao['etapa']) !== $this->normalizarArea((string) $etapa)) {
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
        $area = strtr($area, [
            'á' => 'a', 'à' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a',
            'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
            'í' => 'i', 'ì' => 'i', 'î' => 'i', 'ï' => 'i',
            'ó' => 'o', 'ò' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o',
            'ú' => 'u', 'ù' => 'u', 'û' => 'u', 'ü' => 'u', 'ç' => 'c',
            'Á' => 'A', 'À' => 'A', 'Â' => 'A', 'Ã' => 'A', 'É' => 'E',
            'Ê' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O',
            'Ú' => 'U', 'Ç' => 'C',
        ]);
        $area = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $area) ?: $area;
        $area = strtolower(trim($area));
        $area = preg_replace('/[^a-z0-9]+/', '-', $area) ?: '';

        return trim($area, '-');
    }
}
