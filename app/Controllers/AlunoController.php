<?php

class AlunoController
{
    private QuestaoService $questaoService;

    public function __construct()
    {
        $this->questaoService = new QuestaoService();
    }

    public function dashboard(): void
    {
        require APP_ROOT . '/resources/views/aluno/dashboard.php';
    }

    public function trilha(): void
    {
        require APP_ROOT . '/resources/views/aluno/trilha.php';
    }

    public function simulados(): void
    {
        $area = preg_replace('/[^a-z0-9-]/', '', strtolower($_GET['area'] ?? 'potenciacao')) ?: 'potenciacao';
        $questoes = $this->questaoService->getQuestoesSimulado();
        $resultado = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $respostas = $_POST['respostas'] ?? [];
            $acertos = 0;

            foreach ($questoes as $indice => $questao) {
                if ($this->questaoService->corrigir($questao, $respostas[$indice] ?? null)) {
                    $acertos++;
                }
            }

            $resultado = [
                'acertos' => $acertos,
                'total' => count($questoes),
                'percentual' => count($questoes) > 0 ? (int) round(($acertos / count($questoes)) * 100) : 0,
            ];
            $_SESSION['ultimo_simulado'] = $resultado;
        }

        require APP_ROOT . '/resources/views/aluno/simulados.php';
    }

    public function questoes(): void
    {
        $area = preg_replace('/[^a-z0-9-]/', '', strtolower($_GET['area'] ?? 'potenciacao')) ?: 'potenciacao';
        $questoes = $this->questaoService->getQuestoes($area);
        $resultado = null;
        $respostaEnviada = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $indice = filter_input(INPUT_POST, 'questao', FILTER_VALIDATE_INT);
            $respostaEnviada = filter_input(INPUT_POST, 'resposta', FILTER_VALIDATE_INT);

            if ($indice !== false && $indice !== null && isset($questoes[$indice])) {
                $questao = $questoes[$indice];
                $resultado = [
                    'correta' => $this->questaoService->corrigir($questao, $respostaEnviada),
                    'resposta_correta' => $questao['correta'],
                    'explicacao' => $questao['explicacao'],
                ];
            }
        }

        require APP_ROOT . '/resources/views/aluno/questoes.php';
    }

    public function ranking(): void
    {
        require APP_ROOT . '/resources/views/aluno/ranking.php';
    }
}
