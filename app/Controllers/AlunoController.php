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
        $resumo = $this->trilhaService->getResumo($this->getAlunoId());
        require APP_ROOT . '/resources/views/aluno/dashboard.php';
    }

    public function trilha(): void
    {
        $this->requireAluno();
        $area = $this->getAreaDaRequisicao();
        require APP_ROOT . '/resources/views/aluno/trilha.php';
    }

    public function etapa(): void
    {
        $this->requireAluno();
        $area = $this->getAreaDaRequisicao();
        $etapaId = filter_var($_GET['etapa_id'] ?? null, FILTER_VALIDATE_INT);
        $etapaNome = trim((string) ($_GET['etapa'] ?? ''));
        $alunoId = $this->getAlunoId();
        $etapas = $this->trilhaService->getEtapas($area);
        $etapa = null;
        foreach ($etapas as $item) {
            $idCorresponde = $etapaId !== false && $etapaId !== null && (int) $item['id'] === (int) $etapaId;
            $nomeCorresponde = $etapaNome !== ''
                && $this->trilhaService->normalizarIdentificador((string) $item['nome'])
                    === $this->trilhaService->normalizarIdentificador($etapaNome);
            if ($idCorresponde || $nomeCorresponde) {
                $etapa = $item;
                break;
            }
        }
        if ($etapa === null || $alunoId === null) { http_response_code(404); echo 'Etapa não encontrada.'; return; }
        $estados = $this->trilhaService->getEstadosEtapas($alunoId, $area);
        $estado = $estados[$this->trilhaService->normalizarIdentificador((string) $etapa['nome'])] ?? 'locked';
        if ($estado === 'locked') { http_response_code(403); echo 'Conclua a etapa anterior para desbloquear esta atividade.'; return; }
        $etapa['nome'] = $this->trilhaService->getNomeEtapaDaArea($area, (int) $etapa['ordem_num']);
        $questoes = $this->questaoService->getQuestoesDaEtapa($area, (string) $etapa['nome']);
        $resultado = null;
        $erroFormulario = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $respostas = $_POST['respostas'] ?? [];
            $validas = is_array($respostas) && count($respostas) === 5;
            foreach ((array) $respostas as $resposta) $validas = $validas && is_scalar($resposta) && in_array((string) $resposta, ['0','1','2','3'], true);
            if (!$validas || count($questoes) !== 5) {
                $erroFormulario = 'Responda às cinco questões antes de finalizar.';
            } else {
                $resultado = $this->trilhaService->concluirEtapa($alunoId, (int) $etapa['id'], $respostas, $questoes);
                if ($resultado === null) $erroFormulario = 'Não foi possível concluir esta etapa. Atualize a trilha e tente novamente.';
            }
        }
        $resumo = $this->trilhaService->getResumo($alunoId, $area);
        $areaLabel = $this->questaoService->getAreaLabel($area);
        require APP_ROOT . '/resources/views/aluno/etapa.php';
    }
    public function simulados(): void
    {
        $this->requireAluno();
        $area = $this->getAreaDaRequisicao();
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
        $area = $this->getAreaDaRequisicao();
        $alunoId = $this->getAlunoId();
        $etapas = $this->trilhaService->getEtapas($area);
        $estados = $alunoId === null ? [] : $this->trilhaService->getEstadosEtapas($alunoId, $area);
        $etapaSolicitada = trim((string) ($_GET['etapa'] ?? ''));
        $etapaSelecionada = null;

        foreach ($etapas as $etapa) {
            $identificador = $this->trilhaService->normalizarIdentificador((string) $etapa['nome']);
            if ($etapaSolicitada !== '' && $identificador === $this->trilhaService->normalizarIdentificador($etapaSolicitada)
                && ($estados[$identificador] ?? 'locked') !== 'locked') {
                $etapaSelecionada = $etapa;
                break;
            }
        }

        if ($etapaSelecionada === null) {
            foreach ($etapas as $etapa) {
                $identificador = $this->trilhaService->normalizarIdentificador((string) $etapa['nome']);
                if (in_array($estados[$identificador] ?? 'locked', ['current', 'checkpoint'], true)) {
                    $etapaSelecionada = $etapa;
                    break;
                }
            }
        }

        if ($etapaSelecionada === null) {
            foreach (array_reverse($etapas) as $etapa) {
                $identificador = $this->trilhaService->normalizarIdentificador((string) $etapa['nome']);
                if (($estados[$identificador] ?? 'locked') === 'complete') {
                    $etapaSelecionada = $etapa;
                    break;
                }
            }
        }

        if ($etapaSelecionada === null) {
            header('Location: ' . app_route('/aluno/trilha') . '&area=' . urlencode($area));
            exit;
        }

        $url = app_route('/aluno/etapa')
            . '&area=' . urlencode($area)
            . '&etapa_id=' . (int) $etapaSelecionada['id']
            . '&etapa=' . urlencode((string) $etapaSelecionada['nome']);
        header('Location: ' . $url);
        exit;
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

    private function getAreaDaRequisicao(): string
    {
        $area = preg_replace('/[^a-z0-9-]/', '', strtolower((string) ($_GET['area'] ?? 'potenciacao'))) ?: 'potenciacao';

        return array_key_exists($area, $this->questaoService->getAreas()) ? $area : 'potenciacao';
    }
}


