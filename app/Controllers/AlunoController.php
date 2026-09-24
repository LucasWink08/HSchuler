<?php

class AlunoController
{
    private QuestaoService $questaoService;
    private TrilhaService $trilhaService;
    private static bool $estruturaFotoPerfilVerificada = false;

    public function __construct()
    {
        $this->questaoService = new QuestaoService();
        $this->trilhaService = new TrilhaService();
    }

    public function dashboard(): void
    {
        $this->requireAluno();
        $alunoId = $this->getAlunoId();
        if ($alunoId !== null) {
            $this->garantirCampoFotoPerfil();
        }
        if (empty($_SESSION['foto_perfil_token'])) {
            $_SESSION['foto_perfil_token'] = bin2hex(random_bytes(24));
        }
        $fotoToken = (string) $_SESSION['foto_perfil_token'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $alunoId !== null) {
            $tokenEnviado = (string) ($_POST['foto_perfil_token'] ?? '');
            $resultadoFoto = hash_equals($fotoToken, $tokenEnviado)
                ? $this->salvarFotoPerfil($alunoId, $_FILES['foto_perfil'] ?? null)
                : ['sucesso' => false, 'mensagem' => 'A solicitação expirou. Atualize a página e tente novamente.'];
            $url = app_route('/aluno/dashboard')
                . '&foto_status=' . ($resultadoFoto['sucesso'] ? 'ok' : 'erro')
                . '&foto_mensagem=' . rawurlencode($resultadoFoto['mensagem']);
            header('Location: ' . $url);
            exit;
        }

        $perfil = $alunoId === null ? null : $this->buscarPerfil($alunoId);
        $_SESSION['foto_perfil'] = $perfil['foto_perfil'] ?? null;
        $resumo = $this->trilhaService->getResumo($alunoId);
        $fotoMensagem = trim((string) ($_GET['foto_mensagem'] ?? ''));
        $fotoStatus = ($_GET['foto_status'] ?? '') === 'ok' ? 'ok' : ($fotoMensagem !== '' ? 'erro' : '');
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
        $participantes = $this->trilhaService->getRankingGeral();
        require APP_ROOT . '/resources/views/ranking/ranking.php';
    }

    private function buscarPerfil(int $alunoId): ?array
    {
        try {
            $stmt = Database::getConnection()->prepare(
                'SELECT usuario, email, foto_perfil FROM aluno WHERE id = :id LIMIT 1'
            );
            $stmt->execute([':id' => $alunoId]);
            $perfil = $stmt->fetch();

            return $perfil ?: null;
        } catch (PDOException $exception) {
            try {
                $stmt = Database::getConnection()->prepare(
                    'SELECT usuario, email FROM aluno WHERE id = :id LIMIT 1'
                );
                $stmt->execute([':id' => $alunoId]);
                $perfil = $stmt->fetch();
                if ($perfil !== false) {
                    $perfil['foto_perfil'] = null;
                }

                return $perfil ?: null;
            } catch (PDOException $fallbackException) {
                return null;
            }
        }
    }

    /** Aplica a alteração de estrutura uma única vez para bancos já existentes. */
    private function garantirCampoFotoPerfil(): bool
    {
        if (self::$estruturaFotoPerfilVerificada) {
            return true;
        }

        try {
            $db = Database::getConnection();
            $colunaExiste = $db->query("SHOW COLUMNS FROM aluno LIKE 'foto_perfil'")->fetch() !== false;

            if (!$colunaExiste) {
                $db->exec('ALTER TABLE aluno ADD COLUMN foto_perfil VARCHAR(255) NULL AFTER senha');
                $colunaExiste = $db->query("SHOW COLUMNS FROM aluno LIKE 'foto_perfil'")->fetch() !== false;
            }

            if (!$colunaExiste) {
                return false;
            }

            self::$estruturaFotoPerfilVerificada = true;

            return true;
        } catch (PDOException $exception) {
            return false;
        }
    }

    /** @param array<string, mixed>|null $arquivo */
    private function salvarFotoPerfil(int $alunoId, ?array $arquivo): array
    {
        if ($arquivo === null || ($arquivo['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return ['sucesso' => false, 'mensagem' => 'Selecione uma imagem para enviar.'];
        }

        if (($arquivo['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            return ['sucesso' => false, 'mensagem' => 'Não foi possível enviar a imagem. Tente novamente.'];
        }

        $tamanho = (int) ($arquivo['size'] ?? 0);
        $temporario = (string) ($arquivo['tmp_name'] ?? '');
        if ($tamanho <= 0 || $tamanho > 3 * 1024 * 1024 || !is_uploaded_file($temporario)) {
            return ['sucesso' => false, 'mensagem' => 'Use uma imagem válida de até 3 MB.'];
        }

        $imagem = @getimagesize($temporario);
        $tiposAceitos = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ];
        $mime = is_array($imagem) ? (string) ($imagem['mime'] ?? '') : '';
        $largura = is_array($imagem) ? (int) ($imagem[0] ?? 0) : 0;
        $altura = is_array($imagem) ? (int) ($imagem[1] ?? 0) : 0;
        if (!isset($tiposAceitos[$mime]) || $largura < 32 || $altura < 32 || $largura > 6000 || $altura > 6000) {
            return ['sucesso' => false, 'mensagem' => 'Envie uma imagem JPG, PNG ou WEBP entre 32 px e 6000 px.'];
        }

        $diretorio = APP_ROOT . '/public/uploads/perfis';
        if (!is_dir($diretorio) && !mkdir($diretorio, 0755, true) && !is_dir($diretorio)) {
            return ['sucesso' => false, 'mensagem' => 'Não foi possível preparar o armazenamento da foto.'];
        }

        try {
            $nomeArquivo = bin2hex(random_bytes(16)) . '.' . $tiposAceitos[$mime];
        } catch (Throwable $exception) {
            return ['sucesso' => false, 'mensagem' => 'Não foi possível preparar a foto. Tente novamente.'];
        }

        $destino = $diretorio . DIRECTORY_SEPARATOR . $nomeArquivo;
        if (!move_uploaded_file($temporario, $destino)) {
            return ['sucesso' => false, 'mensagem' => 'Não foi possível salvar a foto enviada.'];
        }

        try {
            $db = Database::getConnection();
            $stmtAnterior = $db->prepare('SELECT foto_perfil FROM aluno WHERE id = :id LIMIT 1');
            $stmtAnterior->execute([':id' => $alunoId]);
            $fotoAnterior = (string) ($stmtAnterior->fetchColumn() ?: '');

            $stmt = $db->prepare('UPDATE aluno SET foto_perfil = :foto_perfil WHERE id = :id');
            $stmt->execute([':foto_perfil' => $nomeArquivo, ':id' => $alunoId]);
            $_SESSION['foto_perfil'] = $nomeArquivo;

            $arquivoAnterior = $diretorio . DIRECTORY_SEPARATOR . basename($fotoAnterior);
            if ($fotoAnterior !== '' && is_file($arquivoAnterior)) {
                @unlink($arquivoAnterior);
            }

            return ['sucesso' => true, 'mensagem' => 'Foto de perfil atualizada com sucesso.'];
        } catch (PDOException $exception) {
            if (is_file($destino)) {
                @unlink($destino);
            }

            return ['sucesso' => false, 'mensagem' => 'Não foi possível registrar a foto. Atualize o banco de dados e tente novamente.'];
        }
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


