<?php

class AlunoController
{
    private QuestaoService $questaoService;
    private TrilhaService $trilhaService;

    public function __construct()
    {
        $this->questaoService = new QuestaoService();
        $this->trilhaService = new TrilhaService();
    }

    public function dashboard(): void
    {
        require APP_ROOT . '/resources/views/aluno/dashboard.php';
    }

    public function trilha(): void
    {
        $area = preg_replace('/[^a-z0-9-]/', '', strtolower($_GET['area'] ?? 'potenciacao')) ?: 'potenciacao';
        require APP_ROOT . '/resources/views/aluno/trilha.php';
    }

    public function simulados(): void
    {
        $area = preg_replace('/[^a-z0-9-]/', '', strtolower($_GET['area'] ?? 'potenciacao')) ?: 'potenciacao';
        $questoesDisponiveis = $this->questaoService->getQuestoesSimulado();

        if (($_GET['iniciar'] ?? '') !== '1') {
            require APP_ROOT . '/resources/views/aluno/pre_simulado.php';
            return;
        }

        $questoes = $questoesDisponiveis;
        $resultado = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $respostas = $_POST['respostas'] ?? [];
            $acertos = 0;
            $alunoId = $this->getAlunoId();

            foreach ($questoes as $indice => $questao) {
                $resposta = $respostas[$indice] ?? null;
                $correta = $this->questaoService->corrigir($questao, $resposta);

                if ($alunoId !== null && isset($questao['id']) && $resposta !== null) {
                    $registro = $this->trilhaService->registrarResposta($alunoId, (int) $questao['id'], (string) $resposta);
                    if ($registro !== null) {
                        $correta = $registro['correta'];
                    }
                }

                if ($correta) {
                    $acertos++;
                }
            }

            $resultado = [
                'acertos' => $acertos,
                'total' => count($questoes),
                'percentual' => count($questoes) > 0 ? (int) round(($acertos / count($questoes)) * 100) : 0,
            ];
            $_SESSION['ultimo_simulado'] = $resultado;

            if ($alunoId !== null) {
                $this->trilhaService->registrarSimulado($alunoId, $area, count($questoes), $resultado['percentual']);
            }
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
                $correta = $this->questaoService->corrigir($questao, $respostaEnviada);
                $alunoId = $this->getAlunoId();

                if ($alunoId !== null && isset($questao['id'])) {
                    $registro = $this->trilhaService->registrarResposta($alunoId, (int) $questao['id'], (string) $respostaEnviada);
                    if ($registro !== null) {
                        $correta = $registro['correta'];
                    }
                }

                $resultado = [
                    'correta' => $correta,
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

    private function getAlunoId(): ?int
    {
        $alunoId = filter_var($_SESSION['user_id'] ?? null, FILTER_VALIDATE_INT);

        return $alunoId !== false && $alunoId !== null && $alunoId > 0 ? $alunoId : null;
    }
}
