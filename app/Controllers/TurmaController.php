<?php

class TurmaController
{
    public function professorIndex(): void
    {
        $professorId = $this->requireProfessor();
        $service = new TurmaService();
        $this->garantirToken('turma_professor_token');
        $turmas = $service->getTurmasDoProfessor($professorId);
        $mensagem = trim((string) ($_GET['mensagem'] ?? ''));
        $status = ($_GET['status'] ?? '') === 'ok' ? 'ok' : ($mensagem !== '' ? 'erro' : '');
        require APP_ROOT . '/resources/views/professor/turmas.php';
    }

    public function criarTurma(): void
    {
        $professorId = $this->requireProfessor();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(app_route('/professor/turmas'));
        }

        if (!$this->validarToken('turma_professor_token', (string) ($_POST['token'] ?? ''))) {
            $this->redirecionarComMensagem('/professor/turmas', false, 'A solicitação expirou. Atualize a página e tente novamente.');
        }

        try {
            $resultado = (new TurmaService())->criarTurma(
                $professorId,
                (string) ($_POST['nome'] ?? ''),
                (string) ($_POST['descricao'] ?? '')
            );
        } catch (Throwable $exception) {
            $resultado = ['sucesso' => false, 'mensagem' => 'Não foi possível criar a turma. Tente novamente.'];
        }

        $this->redirecionarComMensagem('/professor/turmas', (bool) $resultado['sucesso'], (string) $resultado['mensagem']);
    }

    public function professorTurma(): void
    {
        $professorId = $this->requireProfessor();
        $turmaId = $this->getTurmaId();
        $service = new TurmaService();
        $turma = $turmaId === null ? null : $service->getTurmaDoProfessor($professorId, $turmaId);
        if ($turma === null) {
            $this->notFound('Turma não encontrada.');
        }

        $this->garantirToken('atividade_turma_token');
        $this->garantirToken('nota_entrega_token');
        $this->garantirToken('aviso_turma_token');
        $aba = $this->getAbaAtiva();
        $atividades = $service->getAtividadesDaTurma((int) $turma['id']);
        $avisos = $service->getAvisosDaTurma((int) $turma['id']);
        $alunos = $service->getAlunosDaTurma((int) $turma['id']);
        $entregas = $service->getEntregasDaTurmaParaProfessor((int) $turma['id'], $professorId);
        $mensagem = trim((string) ($_GET['mensagem'] ?? ''));
        $status = ($_GET['status'] ?? '') === 'ok' ? 'ok' : ($mensagem !== '' ? 'erro' : '');
        require APP_ROOT . '/resources/views/professor/turma.php';
    }

    public function criarAtividade(): void
    {
        $professorId = $this->requireProfessor();
        $turmaId = $this->getTurmaId();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $turmaId === null) {
            $this->redirect(app_route('/professor/turmas'));
        }

        $destino = '/professor/turma&id=' . $turmaId . '&aba=atividades';
        if (!$this->validarToken('atividade_turma_token', (string) ($_POST['token'] ?? ''))) {
            $this->redirecionarComMensagem($destino, false, 'A solicitação expirou. Atualize a página e tente novamente.');
        }

        try {
            $resultado = (new TurmaService())->criarAtividade(
                $professorId,
                $turmaId,
                (string) ($_POST['titulo'] ?? ''),
                (string) ($_POST['descricao'] ?? ''),
                (string) ($_POST['periodo_entrega'] ?? ''),
                $_FILES['anexos'] ?? null
            );
        } catch (Throwable $exception) {
            $resultado = ['sucesso' => false, 'mensagem' => 'Não foi possível publicar a atividade. Tente novamente.'];
        }

        $this->redirecionarComMensagem($destino, (bool) $resultado['sucesso'], (string) $resultado['mensagem']);
    }

    public function criarAviso(): void
    {
        $professorId = $this->requireProfessor();
        $turmaId = $this->getTurmaId();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $turmaId === null) {
            $this->redirect(app_route('/professor/turmas'));
        }

        $destino = '/professor/turma&id=' . $turmaId . '&aba=avisos';
        if (!$this->validarToken('aviso_turma_token', (string) ($_POST['token'] ?? ''))) {
            $this->redirecionarComMensagem($destino, false, 'A solicitação expirou. Atualize a página e tente novamente.');
        }

        try {
            $resultado = (new TurmaService())->criarAviso(
                $professorId,
                $turmaId,
                (string) ($_POST['mensagem'] ?? '')
            );
        } catch (Throwable $exception) {
            $resultado = ['sucesso' => false, 'mensagem' => 'Não foi possível publicar o aviso. Tente novamente.'];
        }

        $this->redirecionarComMensagem($destino, (bool) $resultado['sucesso'], (string) $resultado['mensagem']);
    }

    public function alunoIndex(): void
    {
        $alunoId = $this->requireAluno();
        $service = new TurmaService();
        $this->garantirToken('turma_aluno_token');
        $turmas = $service->getTurmasDoAluno($alunoId);
        $atividadesProximas = $service->getAtividadesPendentesProximasDoAluno($alunoId);
        $mensagem = trim((string) ($_GET['mensagem'] ?? ''));
        $status = ($_GET['status'] ?? '') === 'ok' ? 'ok' : ($mensagem !== '' ? 'erro' : '');
        require APP_ROOT . '/resources/views/aluno/turma.php';
    }

    public function entrarNaTurma(): void
    {
        $alunoId = $this->requireAluno();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(app_route('/aluno/turma'));
        }

        if (!$this->validarToken('turma_aluno_token', (string) ($_POST['token'] ?? ''))) {
            $this->redirecionarComMensagem('/aluno/turma', false, 'A solicitação expirou. Atualize a página e tente novamente.');
        }

        try {
            $resultado = (new TurmaService())->entrarNaTurma($alunoId, (string) ($_POST['codigo'] ?? ''));
        } catch (Throwable $exception) {
            $resultado = ['sucesso' => false, 'mensagem' => 'Não foi possível entrar na turma. Tente novamente.'];
        }

        if (($resultado['sucesso'] ?? false) && isset($resultado['turma_id'])) {
            $url = app_route('/aluno/turma/detalhe') . '&id=' . (int) $resultado['turma_id'] . '&status=ok&mensagem=' . rawurlencode((string) $resultado['mensagem']);
            $this->redirect($url);
        }

        $this->redirecionarComMensagem('/aluno/turma', false, (string) $resultado['mensagem']);
    }

    public function alunoTurma(): void
    {
        $alunoId = $this->requireAluno();
        $turmaId = $this->getTurmaId();
        $service = new TurmaService();
        $turma = $turmaId === null ? null : $service->getTurmaDoAluno($alunoId, $turmaId);
        if ($turma === null) {
            $this->notFound('Turma não encontrada ou indisponível para sua conta.');
        }

        $this->garantirToken('entrega_turma_token');
        $aba = $this->getAbaAtiva();
        $atividades = $service->getAtividadesDaTurmaParaAluno((int) $turma['id'], $alunoId);
        $avisos = $service->getAvisosDaTurma((int) $turma['id']);
        $mensagem = trim((string) ($_GET['mensagem'] ?? ''));
        $status = ($_GET['status'] ?? '') === 'ok' ? 'ok' : ($mensagem !== '' ? 'erro' : '');
        require APP_ROOT . '/resources/views/aluno/turma_detalhe.php';
    }

    public function enviarAtividade(): void
    {
        $alunoId = $this->requireAluno();
        $turmaId = $this->getTurmaId();
        $atividadeId = filter_var($_POST['atividade_id'] ?? null, FILTER_VALIDATE_INT);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $turmaId === null || $atividadeId === false || $atividadeId === null || $atividadeId <= 0) {
            $this->redirect(app_route('/aluno/turma'));
        }

        $destino = '/aluno/turma/detalhe&id=' . $turmaId . '&aba=atividades';
        if (!$this->validarToken('entrega_turma_token', (string) ($_POST['token'] ?? ''))) {
            $this->redirecionarComMensagem($destino, false, 'A solicitação expirou. Atualize a página e tente novamente.');
        }

        try {
            $resultado = (new TurmaService())->enviarAtividade($alunoId, (int) $atividadeId, $_FILES['entrega'] ?? null);
        } catch (Throwable $exception) {
            $resultado = ['sucesso' => false, 'mensagem' => 'Não foi possível enviar a atividade. Tente novamente.'];
        }

        $this->redirecionarComMensagem($destino, (bool) $resultado['sucesso'], (string) $resultado['mensagem']);
    }

    public function atribuirNota(): void
    {
        $professorId = $this->requireProfessor();
        $turmaId = $this->getTurmaId();
        $entregaId = filter_var($_POST['entrega_id'] ?? null, FILTER_VALIDATE_INT);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $turmaId === null || $entregaId === false || $entregaId === null || $entregaId <= 0) {
            $this->redirect(app_route('/professor/turmas'));
        }

        $destino = '/professor/turma&id=' . $turmaId . '&aba=atividades';
        if (!$this->validarToken('nota_entrega_token', (string) ($_POST['token'] ?? ''))) {
            $this->redirecionarComMensagem($destino, false, 'A solicitação expirou. Atualize a página e tente novamente.');
        }

        try {
            $resultado = (new TurmaService())->atribuirNota($professorId, (int) $entregaId, (string) ($_POST['nota'] ?? ''));
        } catch (Throwable $exception) {
            $resultado = ['sucesso' => false, 'mensagem' => 'Não foi possível salvar a nota. Tente novamente.'];
        }

        $this->redirecionarComMensagem($destino, (bool) $resultado['sucesso'], (string) $resultado['mensagem']);
    }

    public function baixarAnexo(): void
    {
        $anexoId = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
        if ($anexoId === false || $anexoId === null || $anexoId <= 0) {
            $this->notFound('Anexo não encontrado.');
        }

        $repository = new TurmaRepository();
        $role = $_SESSION['role'] ?? null;
        $usuarioId = filter_var($_SESSION['user_id'] ?? null, FILTER_VALIDATE_INT);
        $anexo = null;
        if ($usuarioId !== false && $usuarioId !== null && $usuarioId > 0 && $role === 'professor') {
            $anexo = $repository->findAttachmentForTeacher((int) $anexoId, (int) $usuarioId);
        } elseif ($usuarioId !== false && $usuarioId !== null && $usuarioId > 0 && $role === 'aluno') {
            $anexo = $repository->findAttachmentForStudent((int) $anexoId, (int) $usuarioId);
        }

        if ($anexo === null) {
            $this->notFound('Anexo não encontrado ou indisponível para sua conta.');
        }

        $arquivo = APP_ROOT . '/storage/uploads/atividades/' . basename((string) $anexo['nome_arquivo']);
        if (!is_file($arquivo)) {
            $this->notFound('O arquivo deste anexo não está mais disponível.');
        }

        $nome = basename((string) $anexo['nome_original']);
        header('Content-Type: ' . (string) $anexo['mime_type']);
        header('Content-Length: ' . (string) filesize($arquivo));
        header("Content-Disposition: attachment; filename*=UTF-8''" . rawurlencode($nome));
        header('X-Content-Type-Options: nosniff');
        readfile($arquivo);
        exit;
    }

    public function baixarEntrega(): void
    {
        $entregaId = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
        if ($entregaId === false || $entregaId === null || $entregaId <= 0) {
            $this->notFound('Entrega não encontrada.');
        }

        $repository = new TurmaRepository();
        $role = $_SESSION['role'] ?? null;
        $usuarioId = filter_var($_SESSION['user_id'] ?? null, FILTER_VALIDATE_INT);
        $entrega = null;
        if ($usuarioId !== false && $usuarioId !== null && $usuarioId > 0 && $role === 'professor') {
            $entrega = $repository->findSubmissionForTeacher((int) $entregaId, (int) $usuarioId);
        } elseif ($usuarioId !== false && $usuarioId !== null && $usuarioId > 0 && $role === 'aluno') {
            $entrega = $repository->findSubmissionForStudent((int) $entregaId, (int) $usuarioId);
        }

        if ($entrega === null) {
            $this->notFound('Entrega não encontrada ou indisponível para sua conta.');
        }

        $arquivo = APP_ROOT . '/storage/uploads/entregas/' . basename((string) $entrega['nome_arquivo']);
        if (!is_file($arquivo)) {
            $this->notFound('O arquivo desta entrega não está mais disponível.');
        }

        $nome = basename((string) $entrega['nome_original']);
        header('Content-Type: ' . (string) $entrega['mime_type']);
        header('Content-Length: ' . (string) filesize($arquivo));
        header("Content-Disposition: attachment; filename*=UTF-8''" . rawurlencode($nome));
        header('X-Content-Type-Options: nosniff');
        readfile($arquivo);
        exit;
    }

    private function requireProfessor(): int
    {
        $id = filter_var($_SESSION['user_id'] ?? null, FILTER_VALIDATE_INT);
        if (($_SESSION['role'] ?? null) === 'professor' && $id !== false && $id !== null && $id > 0) {
            return (int) $id;
        }

        $this->redirect(app_route('/') . '&access=login-required');
    }

    private function requireAluno(): int
    {
        $id = filter_var($_SESSION['user_id'] ?? null, FILTER_VALIDATE_INT);
        if (($_SESSION['role'] ?? null) === 'aluno' && $id !== false && $id !== null && $id > 0) {
            return (int) $id;
        }

        $this->redirect(app_route('/') . '&access=login-required');
    }

    private function getTurmaId(): ?int
    {
        $turmaId = filter_var($_GET['id'] ?? $_POST['turma_id'] ?? null, FILTER_VALIDATE_INT);
        return $turmaId !== false && $turmaId !== null && $turmaId > 0 ? (int) $turmaId : null;
    }

    private function getAbaAtiva(): string
    {
        $aba = strtolower(trim((string) ($_GET['aba'] ?? 'avisos')));
        return in_array($aba, ['avisos', 'atividades', 'videoaulas'], true) ? $aba : 'avisos';
    }

    private function garantirToken(string $chave): void
    {
        if (empty($_SESSION[$chave])) {
            $_SESSION[$chave] = bin2hex(random_bytes(24));
        }
    }

    private function validarToken(string $chave, string $token): bool
    {
        return isset($_SESSION[$chave]) && $token !== '' && hash_equals((string) $_SESSION[$chave], $token);
    }

    private function redirecionarComMensagem(string $rota, bool $sucesso, string $mensagem): void
    {
        [$caminho, $query] = array_pad(explode('&', $rota, 2), 2, '');
        $url = app_route($caminho);
        if ($query !== '') {
            $url .= '&' . $query;
        }
        $this->redirect($url . '&status=' . ($sucesso ? 'ok' : 'erro') . '&mensagem=' . rawurlencode($mensagem));
    }

    private function redirect(string $url): never
    {
        header('Location: ' . $url);
        exit;
    }

    private function notFound(string $mensagem): never
    {
        http_response_code(404);
        echo $mensagem;
        exit;
    }
}
