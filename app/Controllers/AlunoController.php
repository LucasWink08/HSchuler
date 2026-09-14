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
        $this->requireAluno();
        require APP_ROOT . '/resources/views/aluno/dashboard.php';
    }

    public function trilha(): void
    {
        $this->requireAluno();
        $area = preg_replace('/[^a-z0-9-]/', '', strtolower($_GET['area'] ?? 'potenciacao')) ?: 'potenciacao';
        require APP_ROOT . '/resources/views/aluno/trilha.php';
    }

    public function simulados(): void
    {
        $this->requireAluno();
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
        $this->requireAluno();
        $area = preg_replace('/[^a-z0-9-]/', '', strtolower($_GET['area'] ?? 'potenciacao')) ?: 'potenciacao';
        $recorte = trim((string) ($_GET['etapa'] ?? ''));
        $recorte = preg_replace('/[^\p{L}\p{N}\s-]/u', '', $recorte) ?? '';
        $recorte = function_exists('mb_substr') ? mb_substr($recorte, 0, 80, 'UTF-8') : substr($recorte, 0, 80);
        $questoes = array_slice($this->questaoService->getQuestoes($area, $recorte), 0, 5);
        $resultado = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $respostas = $_POST['respostas'] ?? [];
            $acertos = 0;
            $alunoId = $this->getAlunoId();

            foreach ($questoes as $indice => $questao) {
                $resposta = isset($respostas[$indice]) ? (string) $respostas[$indice] : null;
                $correta = $this->questaoService->corrigir($questao, $resposta);

                if ($alunoId !== null && isset($questao['id']) && $resposta !== null) {
                    $registro = $this->trilhaService->registrarResposta($alunoId, (int) $questao['id'], $resposta);
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

            header('Location: ' . app_route('/aluno/trilha') . '&area=' . urlencode($area));
            exit;
        }

        require APP_ROOT . '/resources/views/aluno/questoes.php';
    }

    public function ranking(): void
    {
        $this->requireAluno();
        require APP_ROOT . '/resources/views/aluno/ranking.php';
    }

    private function requireAluno(): void
    {
        if ($this->getAlunoId() !== null) {
            return;
        }

        header('Location: ' . app_route('/') . '&access=login-required');
        exit;
    }

    private function getAlunoId(): ?int
    {
        if (($_SESSION['role'] ?? null) !== 'aluno') {
            return null;
        }

        $alunoId = filter_var($_SESSION['user_id'] ?? null, FILTER_VALIDATE_INT);

        return $alunoId !== false && $alunoId !== null && $alunoId > 0 ? $alunoId : null;
    }
}
