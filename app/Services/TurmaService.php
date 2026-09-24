<?php

class TurmaService
{
    private const CODIGO_ALFABETO = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    private const MAX_ANEXOS = 5;
    private const MAX_TAMANHO_ANEXO = 10 * 1024 * 1024;
    private const FUSO_HORARIO = 'America/Sao_Paulo';
    private static bool $schemaVerificado = false;

    private TurmaRepository $repository;

    public function __construct()
    {
        $this->repository = new TurmaRepository();
        if (!self::$schemaVerificado) {
            $this->repository->ensureSchema();
            self::$schemaVerificado = true;
        }
    }

    public function getTurmasDoProfessor(int $professorId): array
    {
        return $this->repository->listTeacherClasses($professorId);
    }

    public function criarTurma(int $professorId, string $nome, string $descricao): array
    {
        $nome = trim($nome);
        $descricao = trim($descricao);
        if ($nome === '' || $this->stringLength($nome) > 100 || $this->stringLength($descricao) > 4000) {
            return ['sucesso' => false, 'mensagem' => 'Informe um nome de turma com até 100 caracteres e uma descrição de até 4.000 caracteres.'];
        }

        for ($tentativa = 0; $tentativa < 8; $tentativa++) {
            $codigo = $this->gerarCodigo();
            if (!$this->repository->codeExists($codigo)) {
                $turmaId = $this->repository->createClass($professorId, $nome, $descricao, $codigo);
                return ['sucesso' => true, 'mensagem' => 'Turma criada. Compartilhe o código ' . $codigo . ' com seus alunos.', 'turma_id' => $turmaId];
            }
        }

        return ['sucesso' => false, 'mensagem' => 'Não foi possível gerar um código exclusivo. Tente novamente.'];
    }

    public function getTurmaDoProfessor(int $professorId, int $turmaId): ?array
    {
        return $this->repository->findTeacherClass($professorId, $turmaId);
    }

    public function getAlunosDaTurma(int $turmaId): array
    {
        return $this->repository->listStudents($turmaId);
    }

    public function getAtividadesDaTurma(int $turmaId): array
    {
        return $this->enriquecerAtividades($this->repository->listActivities($turmaId));
    }

    public function getAtividadesDaTurmaParaAluno(int $turmaId, int $alunoId): array
    {
        return $this->enriquecerAtividades($this->repository->listActivitiesForStudent($turmaId, $alunoId));
    }

    public function getEntregasDaTurmaParaProfessor(int $turmaId, int $professorId): array
    {
        return $this->repository->listSubmissionsForTeacher($turmaId, $professorId);
    }

    public function getAvisosDaTurma(int $turmaId): array
    {
        return $this->repository->listAnnouncements($turmaId);
    }

    public function criarAviso(int $professorId, int $turmaId, string $mensagem): array
    {
        if ($this->getTurmaDoProfessor($professorId, $turmaId) === null) {
            return ['sucesso' => false, 'mensagem' => 'Turma não encontrada.'];
        }

        $mensagem = trim($mensagem);
        if ($mensagem === '' || $this->stringLength($mensagem) > 5000) {
            return ['sucesso' => false, 'mensagem' => 'Escreva um aviso de até 5.000 caracteres.'];
        }

        $this->repository->createAnnouncement($turmaId, $professorId, $mensagem);
        return ['sucesso' => true, 'mensagem' => 'Aviso publicado para a turma.'];
    }

    public function criarAtividade(int $professorId, int $turmaId, string $titulo, string $descricao, string $periodoEntrega, ?array $arquivos): array
    {
        if ($this->getTurmaDoProfessor($professorId, $turmaId) === null) {
            return ['sucesso' => false, 'mensagem' => 'Turma não encontrada.'];
        }

        $titulo = trim($titulo);
        $descricao = trim($descricao);
        if ($titulo === '' || $this->stringLength($titulo) > 150 || $this->stringLength($descricao) > 5000) {
            return ['sucesso' => false, 'mensagem' => 'Informe um título de até 150 caracteres e uma descrição de até 5.000 caracteres.'];
        }

        $dataEntrega = $this->normalizarDataEntrega($periodoEntrega);
        if ($periodoEntrega !== '' && $dataEntrega === null) {
            return ['sucesso' => false, 'mensagem' => 'Informe uma data e hora de entrega válidas.'];
        }
        if ($dataEntrega !== null && $this->prazoEstaEncerrado($dataEntrega)) {
            return ['sucesso' => false, 'mensagem' => 'O prazo de entrega deve estar em uma data futura.'];
        }

        $anexos = $this->validarAnexos($arquivos);
        if (isset($anexos['erro'])) {
            return ['sucesso' => false, 'mensagem' => $anexos['erro']];
        }

        $atividadeId = $this->repository->createActivity($turmaId, $titulo, $descricao, $dataEntrega);
        $diretorio = APP_ROOT . '/storage/uploads/atividades';
        if (!is_dir($diretorio) && !mkdir($diretorio, 0755, true) && !is_dir($diretorio)) {
            $this->repository->deleteActivity($atividadeId);
            return ['sucesso' => false, 'mensagem' => 'Não foi possível preparar o armazenamento dos anexos.'];
        }

        $salvos = [];
        try {
            foreach ($anexos['itens'] as $anexo) {
                $nomeArquivo = bin2hex(random_bytes(16)) . '.' . $anexo['extensao'];
                $destino = $diretorio . DIRECTORY_SEPARATOR . $nomeArquivo;
                if (!move_uploaded_file($anexo['temporario'], $destino)) {
                    throw new RuntimeException('Não foi possível salvar um dos anexos.');
                }
                $salvos[] = $destino;
                $this->repository->createAttachment($atividadeId, $anexo['nome'], $nomeArquivo, $anexo['mime'], $anexo['tamanho']);
            }
        } catch (Throwable $exception) {
            foreach ($salvos as $arquivo) {
                if (is_file($arquivo)) {
                    @unlink($arquivo);
                }
            }
            $this->repository->deleteActivity($atividadeId);
            return ['sucesso' => false, 'mensagem' => 'Não foi possível cadastrar a atividade com os anexos. Tente novamente.'];
        }

        return ['sucesso' => true, 'mensagem' => 'Atividade publicada para a turma.'];
    }

    public function getTurmasDoAluno(int $alunoId): array
    {
        return $this->repository->listStudentClasses($alunoId);
    }

    public function getAtividadesPendentesProximasDoAluno(int $alunoId): array
    {
        $atividades = $this->enriquecerAtividades($this->repository->listPendingActivitiesForStudent($alunoId));
        $pendentes = array_filter($atividades, static function (array $atividade): bool {
            return ($atividade['entrega_id'] ?? null) === null
                && !empty($atividade['pode_entregar'])
                && in_array((string) ($atividade['prazo_status'] ?? ''), ['hoje', 'proximo'], true);
        });

        return array_values(array_slice($pendentes, 0, 4));
    }

    public function entrarNaTurma(int $alunoId, string $codigo): array
    {
        $codigo = strtoupper(preg_replace('/[^A-Z0-9]/', '', $codigo) ?? '');
        if ($codigo === '') {
            return ['sucesso' => false, 'mensagem' => 'Digite o código da turma.'];
        }

        $turma = $this->repository->findClassByCode($codigo);
        if ($turma === null) {
            return ['sucesso' => false, 'mensagem' => 'Não encontramos uma turma com esse código.'];
        }

        $entrouAgora = $this->repository->enrollStudent((int) $turma['id'], $alunoId);
        return [
            'sucesso' => true,
            'mensagem' => $entrouAgora ? 'Você entrou na turma ' . $turma['nome'] . '.' : 'Você já participa desta turma.',
            'turma_id' => (int) $turma['id'],
        ];
    }

    public function getTurmaDoAluno(int $alunoId, int $turmaId): ?array
    {
        return $this->repository->findStudentClass($alunoId, $turmaId);
    }

    public function enviarAtividade(int $alunoId, int $atividadeId, ?array $arquivo): array
    {
        $atividade = $this->repository->findActivityForStudent($alunoId, $atividadeId);
        if ($atividade === null) {
            return ['sucesso' => false, 'mensagem' => 'Atividade não encontrada ou indisponível para sua conta.'];
        }
        if ($this->prazoEstaEncerrado($atividade['periodo_entrega'] ?? null)) {
            return ['sucesso' => false, 'mensagem' => 'O prazo desta atividade já foi encerrado. Não é mais possível enviar ou substituir a entrega.'];
        }

        $anexo = $this->validarArquivoEntrega($arquivo);
        if (isset($anexo['erro'])) {
            return ['sucesso' => false, 'mensagem' => $anexo['erro']];
        }

        $item = $anexo['item'];
        $diretorio = APP_ROOT . '/storage/uploads/entregas';
        if (!is_dir($diretorio) && !mkdir($diretorio, 0755, true) && !is_dir($diretorio)) {
            return ['sucesso' => false, 'mensagem' => 'Não foi possível preparar o armazenamento da entrega.'];
        }

        try {
            $nomeArquivo = bin2hex(random_bytes(16)) . '.' . $item['extensao'];
        } catch (Throwable $exception) {
            return ['sucesso' => false, 'mensagem' => 'Não foi possível preparar sua entrega. Tente novamente.'];
        }

        $destino = $diretorio . DIRECTORY_SEPARATOR . $nomeArquivo;
        if (!move_uploaded_file($item['temporario'], $destino)) {
            return ['sucesso' => false, 'mensagem' => 'Não foi possível salvar o arquivo enviado. Tente novamente.'];
        }

        try {
            $arquivoAnterior = $this->repository->saveSubmission(
                $atividadeId,
                $alunoId,
                $item['nome'],
                $nomeArquivo,
                $item['mime'],
                $item['tamanho']
            );
        } catch (Throwable $exception) {
            @unlink($destino);
            return ['sucesso' => false, 'mensagem' => 'Não foi possível registrar sua entrega. Tente novamente.'];
        }

        if ($arquivoAnterior !== '') {
            $caminhoAnterior = $diretorio . DIRECTORY_SEPARATOR . basename($arquivoAnterior);
            if (is_file($caminhoAnterior)) {
                @unlink($caminhoAnterior);
            }
        }

        return ['sucesso' => true, 'mensagem' => $arquivoAnterior === '' ? 'Atividade enviada com sucesso.' : 'Entrega atualizada com sucesso.'];
    }

    public function atribuirNota(int $professorId, int $entregaId, string $notaInformada): array
    {
        $notaInformada = str_replace(',', '.', trim($notaInformada));
        if ($notaInformada === '' || !is_numeric($notaInformada)) {
            return ['sucesso' => false, 'mensagem' => 'Informe uma nota válida entre 0 e 10.'];
        }

        $nota = (float) $notaInformada;
        if ($nota < 0 || $nota > 10) {
            return ['sucesso' => false, 'mensagem' => 'A nota deve estar entre 0 e 10.'];
        }
        if ($this->repository->findSubmissionForTeacher($entregaId, $professorId) === null) {
            return ['sucesso' => false, 'mensagem' => 'Entrega não encontrada ou indisponível para sua conta.'];
        }

        $this->repository->setSubmissionGrade($entregaId, round($nota, 2));
        return ['sucesso' => true, 'mensagem' => 'Nota salva com sucesso.'];
    }

    /** @return array{itens: array<int, array<string, mixed>>}|array{erro: string} */
    private function validarAnexos(?array $arquivos): array
    {
        if ($arquivos === null || !isset($arquivos['name']) || !is_array($arquivos['name'])) {
            return ['itens' => []];
        }

        $itens = [];
        $tiposAceitos = [
            'application/pdf' => 'pdf',
            'text/plain' => 'txt',
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'application/vnd.ms-excel' => 'xls',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
            'application/vnd.ms-powerpoint' => 'ppt',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
        ];
        $finfo = new finfo(FILEINFO_MIME_TYPE);

        foreach ($arquivos['name'] as $indice => $nome) {
            $erro = (int) ($arquivos['error'][$indice] ?? UPLOAD_ERR_NO_FILE);
            if ($erro === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            if ($erro !== UPLOAD_ERR_OK) {
                return ['erro' => 'Um dos anexos não pôde ser enviado.'];
            }
            if (count($itens) >= self::MAX_ANEXOS) {
                return ['erro' => 'Envie no máximo ' . self::MAX_ANEXOS . ' anexos por atividade.'];
            }

            $temporario = (string) ($arquivos['tmp_name'][$indice] ?? '');
            $tamanho = (int) ($arquivos['size'][$indice] ?? 0);
            if ($tamanho <= 0 || $tamanho > self::MAX_TAMANHO_ANEXO || !is_uploaded_file($temporario)) {
                return ['erro' => 'Cada anexo deve ser válido e ter no máximo 10 MB.'];
            }

            $nomeSeguro = trim((string) basename((string) $nome));
            $extensaoOriginal = strtolower(pathinfo($nomeSeguro, PATHINFO_EXTENSION));
            $mime = (string) $finfo->file($temporario);
            if ($mime === 'application/zip' && in_array($extensaoOriginal, ['docx', 'xlsx', 'pptx'], true)) {
                $mime = match ($extensaoOriginal) {
                    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    default => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                };
            }
            if (!isset($tiposAceitos[$mime])) {
                return ['erro' => 'Use anexos PDF, TXT, JPG, PNG, DOC, DOCX, XLS, XLSX, PPT ou PPTX.'];
            }

            $nomeSeguro = $nomeSeguro !== '' ? $nomeSeguro : 'anexo.' . $tiposAceitos[$mime];
            $itens[] = [
                'nome' => function_exists('mb_substr') ? mb_substr($nomeSeguro, 0, 255, 'UTF-8') : substr($nomeSeguro, 0, 255),
                'temporario' => $temporario,
                'tamanho' => $tamanho,
                'mime' => $mime,
                'extensao' => $tiposAceitos[$mime],
            ];
        }

        return ['itens' => $itens];
    }

    /** @return array{item: array<string, mixed>}|array{erro: string} */
    private function validarArquivoEntrega(?array $arquivo): array
    {
        if ($arquivo === null || !isset($arquivo['name'])) {
            return ['erro' => 'Selecione o arquivo que deseja enviar.'];
        }

        $erro = (int) ($arquivo['error'] ?? UPLOAD_ERR_NO_FILE);
        if ($erro === UPLOAD_ERR_NO_FILE) {
            return ['erro' => 'Selecione o arquivo que deseja enviar.'];
        }
        if ($erro !== UPLOAD_ERR_OK) {
            return ['erro' => 'O arquivo não pôde ser enviado. Tente novamente.'];
        }

        $temporario = (string) ($arquivo['tmp_name'] ?? '');
        $tamanho = (int) ($arquivo['size'] ?? 0);
        if ($tamanho <= 0 || $tamanho > self::MAX_TAMANHO_ANEXO || !is_uploaded_file($temporario)) {
            return ['erro' => 'O arquivo deve ser válido e ter no máximo 10 MB.'];
        }

        $tiposAceitos = [
            'application/pdf' => 'pdf',
            'text/plain' => 'txt',
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'application/vnd.ms-excel' => 'xls',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
            'application/vnd.ms-powerpoint' => 'ppt',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
        ];
        $nomeSeguro = trim((string) basename((string) $arquivo['name']));
        $extensaoOriginal = strtolower(pathinfo($nomeSeguro, PATHINFO_EXTENSION));
        $mime = (string) (new finfo(FILEINFO_MIME_TYPE))->file($temporario);
        if ($mime === 'application/zip' && in_array($extensaoOriginal, ['docx', 'xlsx', 'pptx'], true)) {
            $mime = match ($extensaoOriginal) {
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                default => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            };
        }
        if (!isset($tiposAceitos[$mime])) {
            return ['erro' => 'Use arquivos PDF, TXT, JPG, PNG, DOC, DOCX, XLS, XLSX, PPT ou PPTX.'];
        }

        return ['item' => [
            'nome' => $nomeSeguro !== '' ? (function_exists('mb_substr') ? mb_substr($nomeSeguro, 0, 255, 'UTF-8') : substr($nomeSeguro, 0, 255)) : 'entrega.' . $tiposAceitos[$mime],
            'temporario' => $temporario,
            'tamanho' => $tamanho,
            'mime' => $mime,
            'extensao' => $tiposAceitos[$mime],
        ]];
    }

    private function normalizarDataEntrega(string $valor): ?string
    {
        if ($valor === '') {
            return null;
        }

        $data = DateTimeImmutable::createFromFormat('!Y-m-d\\TH:i', $valor, $this->fusoHorario());
        $erros = DateTimeImmutable::getLastErrors();
        if ($data === false || ($erros !== false && ($erros['warning_count'] > 0 || $erros['error_count'] > 0))) {
            return null;
        }

        return $data->format('Y-m-d H:i:s');
    }

    private function enriquecerAtividades(array $atividades): array
    {
        foreach ($atividades as &$atividade) {
            $prazo = $this->informacoesDoPrazo($atividade['periodo_entrega'] ?? null);
            $atividade = array_merge($atividade, $prazo);
            $atividade['entrega_apos_prazo'] = false;

            if (!empty($atividade['entrega_enviada_em']) && $prazo['prazo_data'] instanceof DateTimeImmutable) {
                $entrega = $this->dataDoBanco((string) $atividade['entrega_enviada_em']);
                $atividade['entrega_apos_prazo'] = $entrega instanceof DateTimeImmutable && $entrega > $prazo['prazo_data'];
            }
        }
        unset($atividade);

        return $atividades;
    }

    /** @return array{prazo_status: string, prazo_texto: string, prazo_data: ?DateTimeImmutable, pode_entregar: bool} */
    private function informacoesDoPrazo(?string $periodoEntrega): array
    {
        $data = $this->dataDoBanco($periodoEntrega);
        if (!$data instanceof DateTimeImmutable) {
            return [
                'prazo_status' => 'sem-prazo',
                'prazo_texto' => 'Sem prazo definido',
                'prazo_data' => null,
                'pode_entregar' => true,
            ];
        }

        $agora = $this->agora();
        $formatada = $data->format('d/m/Y \\à\\s H:i');
        if ($agora > $data) {
            return [
                'prazo_status' => 'encerrado',
                'prazo_texto' => 'Prazo encerrado em ' . $formatada,
                'prazo_data' => $data,
                'pode_entregar' => false,
            ];
        }

        $segundos = $data->getTimestamp() - $agora->getTimestamp();
        if ($data->format('Y-m-d') === $agora->format('Y-m-d')) {
            $texto = 'Entrega hoje às ' . $data->format('H:i');
            $status = 'hoje';
        } elseif ($segundos <= 72 * 60 * 60) {
            $texto = 'Prazo próximo: ' . $formatada;
            $status = 'proximo';
        } else {
            $texto = 'Entrega até ' . $formatada;
            $status = 'aberto';
        }

        return [
            'prazo_status' => $status,
            'prazo_texto' => $texto,
            'prazo_data' => $data,
            'pode_entregar' => true,
        ];
    }

    private function prazoEstaEncerrado(?string $periodoEntrega): bool
    {
        $data = $this->dataDoBanco($periodoEntrega);
        return $data instanceof DateTimeImmutable && $this->agora() > $data;
    }

    private function dataDoBanco(?string $valor): ?DateTimeImmutable
    {
        if ($valor === null || $valor === '') {
            return null;
        }

        $data = DateTimeImmutable::createFromFormat('!Y-m-d H:i:s', $valor, $this->fusoHorario());
        $erros = DateTimeImmutable::getLastErrors();
        if ($data === false || ($erros !== false && ($erros['warning_count'] > 0 || $erros['error_count'] > 0))) {
            return null;
        }

        return $data;
    }

    private function agora(): DateTimeImmutable
    {
        return new DateTimeImmutable('now', $this->fusoHorario());
    }

    private function fusoHorario(): DateTimeZone
    {
        return new DateTimeZone(self::FUSO_HORARIO);
    }

    private function gerarCodigo(): string
    {
        $codigo = '';
        $ultimoIndice = strlen(self::CODIGO_ALFABETO) - 1;
        for ($indice = 0; $indice < 8; $indice++) {
            $codigo .= self::CODIGO_ALFABETO[random_int(0, $ultimoIndice)];
        }

        return $codigo;
    }

    private function stringLength(string $valor): int
    {
        return function_exists('mb_strlen') ? mb_strlen($valor, 'UTF-8') : strlen($valor);
    }
}
