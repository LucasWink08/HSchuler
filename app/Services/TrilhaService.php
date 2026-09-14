<?php

class TrilhaService
{
    public function getResumo(?int $alunoId): array
    {
        $resumo = [
            'nome' => null,
            'xp' => null,
            'etapas_concluidas' => null,
            'total_etapas' => null,
            'simulados_realizados' => null,
            'percentual_acertos' => null,
            'streak_atual' => null,
            'maior_streak' => null,
            'dias_atividade' => [],
        ];

        if ($alunoId === null || $alunoId <= 0) {
            return $resumo;
        }

        try {
            $db = Database::getConnection();
            $aluno = $this->buscarAluno($db, $alunoId);

            if ($aluno === null) {
                return $resumo;
            }

            $diasAtividade = $this->buscarDiasAtividade($db, $alunoId);
            $sequencia = $this->calcularSequencia($diasAtividade);
            $respostas = $this->buscarResumoRespostas($db, $alunoId);

            return [
                'nome' => $aluno['usuario'],
                'xp' => $this->buscarXp($db, $alunoId),
                'etapas_concluidas' => $this->contarEtapasConcluidas($db, $alunoId),
                'total_etapas' => $this->contarEtapas($db),
                'simulados_realizados' => $this->contarSimulados($db, $alunoId),
                'percentual_acertos' => $respostas['total'] > 0
                    ? (int) round(($respostas['acertos'] / $respostas['total']) * 100)
                    : null,
                'streak_atual' => $sequencia['atual'],
                'maior_streak' => $sequencia['maior'],
                'dias_atividade' => $diasAtividade,
            ];
        } catch (PDOException $exception) {
            return $resumo;
        }
    }

    public function getProgressoPorArea(?int $alunoId): array
    {
        if ($alunoId === null || $alunoId <= 0) {
            return [];
        }

        try {
            $db = Database::getConnection();
            $stmt = $db->prepare(
                'SELECT assunto.nome,
                    COUNT(DISTINCT questao.id) AS total_questoes,
                    COUNT(DISTINCT CASE WHEN resposta.acertou = 1 THEN questao.id END) AS questoes_concluidas
                 FROM assunto
                 LEFT JOIN questao ON questao.assunto_id = assunto.id
                 LEFT JOIN resposta_aluno AS resposta
                    ON resposta.questao_id = questao.id AND resposta.aluno_id = :aluno_id
                 GROUP BY assunto.id, assunto.nome'
            );
            $stmt->execute([':aluno_id' => $alunoId]);

            $progresso = [];
            foreach ($stmt->fetchAll() as $area) {
                $total = (int) $area['total_questoes'];
                $concluidas = (int) $area['questoes_concluidas'];
                $progresso[$this->normalizarIdentificador((string) $area['nome'])] = [
                    'concluidas' => $concluidas,
                    'total' => $total,
                    'percentual' => $total > 0 ? (int) round(($concluidas / $total) * 100) : 0,
                ];
            }

            return $progresso;
        } catch (PDOException $exception) {
            return [];
        }
    }

    public function getEstadosEtapas(int $alunoId): array
    {
        if ($alunoId <= 0) {
            return [];
        }

        try {
            $db = Database::getConnection();
            $stmt = $db->prepare(
                'SELECT etapa.id, etapa.nome, etapa.estado AS estado_padrao,
                    (
                        SELECT progresso.estado
                        FROM progresso_aluno AS progresso
                        WHERE progresso.aluno_id = :aluno_id
                            AND progresso.etapa_id = etapa.id
                        ORDER BY progresso.ultima_atualizacao DESC, progresso.id DESC
                        LIMIT 1
                    ) AS estado_aluno
                FROM etapa_trilha AS etapa'
            );
            $stmt->execute([':aluno_id' => $alunoId]);

            $estados = [];
            foreach ($stmt->fetchAll() as $etapa) {
                $estado = $etapa['estado_aluno'] ?? $etapa['estado_padrao'];
                $estados[$this->normalizarIdentificador((string) $etapa['nome'])] = $this->normalizarEstado((string) $estado);
            }

            return $estados;
        } catch (PDOException $exception) {
            return [];
        }
    }

    public function registrarResposta(int $alunoId, int $questaoId, string $respostaEscolhida): ?array
    {
        if ($alunoId <= 0 || $questaoId <= 0) {
            return null;
        }

        try {
            $db = Database::getConnection();
            $db->beginTransaction();

            $stmtQuestao = $db->prepare(
                'SELECT questao.id, questao.resposta_correta, questao.pontuacao, questao.atividade_id,
                        atividade.etapa_id
                 FROM questao
                 LEFT JOIN atividade ON atividade.id = questao.atividade_id
                 WHERE questao.id = :questao_id
                 LIMIT 1'
            );
            $stmtQuestao->execute([':questao_id' => $questaoId]);
            $questao = $stmtQuestao->fetch();

            if ($questao === false) {
                $db->rollBack();
                return null;
            }

            $correta = $this->respostaCorreta((string) $questao['resposta_correta'], $respostaEscolhida);
            $stmtResposta = $db->prepare(
                'SELECT id, acertou, tentativas
                 FROM resposta_aluno
                 WHERE aluno_id = :aluno_id AND questao_id = :questao_id
                 ORDER BY data_resposta DESC, id DESC
                 LIMIT 1
                 FOR UPDATE'
            );
            $stmtResposta->execute([
                ':aluno_id' => $alunoId,
                ':questao_id' => $questaoId,
            ]);
            $respostaAnterior = $stmtResposta->fetch() ?: null;
            $jaEstavaCorreta = $respostaAnterior !== null && (int) $respostaAnterior['acertou'] === 1;

            if ($respostaAnterior === null) {
                $insertResposta = $db->prepare(
                    'INSERT INTO resposta_aluno (aluno_id, questao_id, resposta_escolhida, acertou, tentativas)
                     VALUES (:aluno_id, :questao_id, :resposta, :acertou, 1)'
                );
                $insertResposta->execute([
                    ':aluno_id' => $alunoId,
                    ':questao_id' => $questaoId,
                    ':resposta' => $respostaEscolhida,
                    ':acertou' => $correta ? 1 : 0,
                ]);
            } else {
                $updateResposta = $db->prepare(
                    'UPDATE resposta_aluno
                     SET resposta_escolhida = :resposta,
                         acertou = CASE WHEN acertou = 1 OR :acertou = 1 THEN 1 ELSE 0 END,
                         tentativas = tentativas + 1,
                         data_resposta = CURRENT_TIMESTAMP
                     WHERE id = :id'
                );
                $updateResposta->execute([
                    ':resposta' => $respostaEscolhida,
                    ':acertou' => $correta ? 1 : 0,
                    ':id' => $respostaAnterior['id'],
                ]);
            }

            if ($correta && !$jaEstavaCorreta && (int) $questao['pontuacao'] !== 0) {
                $insertXp = $db->prepare(
                    'INSERT INTO xp_transacao (aluno_id, tipo, quantidade, descricao)
                     VALUES (:aluno_id, :tipo, :quantidade, :descricao)'
                );
                $insertXp->execute([
                    ':aluno_id' => $alunoId,
                    ':tipo' => 'questao_concluida',
                    ':quantidade' => (int) $questao['pontuacao'],
                    ':descricao' => 'Pontuação registrada para a questão ' . $questaoId,
                ]);
            }

            if ($correta && !empty($questao['atividade_id']) && !empty($questao['etapa_id'])) {
                $this->concluirEtapaSeAtividadeConcluida(
                    $db,
                    $alunoId,
                    (int) $questao['atividade_id'],
                    (int) $questao['etapa_id']
                );
            }

            $diasAtividade = $this->buscarDiasAtividade($db, $alunoId);
            $sequencia = $this->calcularSequencia($diasAtividade);
            $this->sincronizarStreak($db, $alunoId, $sequencia);
            $this->sincronizarRanking($db, $alunoId, $sequencia);

            $db->commit();

            return [
                'correta' => $correta,
                'resumo' => $this->getResumo($alunoId),
            ];
        } catch (PDOException $exception) {
            if (isset($db) && $db->inTransaction()) {
                $db->rollBack();
            }

            return null;
        }
    }

    public function registrarSimulado(int $alunoId, string $area, int $quantidade, int $percentual): bool
    {
        if ($alunoId <= 0 || $quantidade <= 0) {
            return false;
        }

        try {
            $db = Database::getConnection();
            $db->beginTransaction();
            $assuntoId = $this->buscarAssuntoId($db, $area);
            $stmt = $db->prepare(
                'INSERT INTO simulado (aluno_id, assunto_id, quantidade, inicio, fim, percentual)
                 VALUES (:aluno_id, :assunto_id, :quantidade, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, :percentual)'
            );
            $registrado = $stmt->execute([
                ':aluno_id' => $alunoId,
                ':assunto_id' => $assuntoId,
                ':quantidade' => $quantidade,
                ':percentual' => $percentual,
            ]);

            if (!$registrado) {
                $db->rollBack();
                return false;
            }

            $sequencia = $this->calcularSequencia($this->buscarDiasAtividade($db, $alunoId));
            $this->sincronizarStreak($db, $alunoId, $sequencia);
            $this->sincronizarRanking($db, $alunoId, $sequencia);
            $db->commit();

            return true;
        } catch (PDOException $exception) {
            if (isset($db) && $db->inTransaction()) {
                $db->rollBack();
            }

            return false;
        }
    }

    public function normalizarIdentificador(string $texto): string
    {
        $texto = html_entity_decode($texto, ENT_QUOTES, 'UTF-8');
        $texto = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $texto) ?: $texto;
        $texto = strtolower(trim($texto));
        $texto = preg_replace('/[^a-z0-9]+/', '-', $texto) ?: '';

        return trim($texto, '-');
    }

    private function buscarAluno(PDO $db, int $alunoId): ?array
    {
        $stmt = $db->prepare('SELECT id, usuario FROM aluno WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $alunoId]);
        $aluno = $stmt->fetch();

        return $aluno ?: null;
    }

    private function buscarAssuntoId(PDO $db, string $area): ?int
    {
        $stmt = $db->query('SELECT id, nome FROM assunto');

        foreach ($stmt->fetchAll() as $assunto) {
            if ($this->normalizarIdentificador((string) $assunto['nome']) === $area) {
                return (int) $assunto['id'];
            }
        }

        return null;
    }

    private function buscarXp(PDO $db, int $alunoId): int
    {
        $stmt = $db->prepare('SELECT COALESCE(SUM(quantidade), 0) AS total FROM xp_transacao WHERE aluno_id = :aluno_id');
        $stmt->execute([':aluno_id' => $alunoId]);

        return (int) ($stmt->fetch()['total'] ?? 0);
    }

    private function contarEtapas(PDO $db): int
    {
        return (int) $db->query('SELECT COUNT(*) FROM etapa_trilha')->fetchColumn();
    }

    private function contarEtapasConcluidas(PDO $db, int $alunoId): int
    {
        $stmt = $db->prepare(
            'SELECT etapa_id, estado
             FROM progresso_aluno
             WHERE aluno_id = :aluno_id
             ORDER BY ultima_atualizacao DESC, id DESC'
        );
        $stmt->execute([':aluno_id' => $alunoId]);

        $etapas = [];
        foreach ($stmt->fetchAll() as $progresso) {
            $etapaId = (int) $progresso['etapa_id'];
            if (!array_key_exists($etapaId, $etapas)) {
                $etapas[$etapaId] = $progresso['estado'];
            }
        }

        return count(array_filter($etapas, static function (string $estado): bool {
            return strtolower($estado) === 'concluida';
        }));
    }

    private function contarSimulados(PDO $db, int $alunoId): int
    {
        $stmt = $db->prepare('SELECT COUNT(*) FROM simulado WHERE aluno_id = :aluno_id');
        $stmt->execute([':aluno_id' => $alunoId]);

        return (int) $stmt->fetchColumn();
    }

    private function buscarResumoRespostas(PDO $db, int $alunoId): array
    {
        $stmt = $db->prepare(
            'SELECT COUNT(*) AS total, COALESCE(SUM(acertou = 1), 0) AS acertos
             FROM resposta_aluno
             WHERE aluno_id = :aluno_id'
        );
        $stmt->execute([':aluno_id' => $alunoId]);
        $resultado = $stmt->fetch() ?: [];

        return [
            'total' => (int) ($resultado['total'] ?? 0),
            'acertos' => (int) ($resultado['acertos'] ?? 0),
        ];
    }

    private function buscarDiasAtividade(PDO $db, int $alunoId): array
    {
        $stmt = $db->prepare(
            'SELECT DISTINCT dia
             FROM (
                SELECT DATE(data_resposta) AS dia FROM resposta_aluno WHERE aluno_id = :aluno_id_resposta
                UNION
                SELECT DATE(created_at) AS dia FROM simulado WHERE aluno_id = :aluno_id_simulado
             ) AS atividades
             ORDER BY dia ASC'
        );
        $stmt->execute([
            ':aluno_id_resposta' => $alunoId,
            ':aluno_id_simulado' => $alunoId,
        ]);

        return array_values(array_filter(array_column($stmt->fetchAll(), 'dia')));
    }

    private function calcularSequencia(array $diasAtividade): array
    {
        if ($diasAtividade === []) {
            return ['atual' => 0, 'maior' => 0];
        }

        $atual = 0;
        $maior = 0;
        $sequencia = 0;
        $anterior = null;

        foreach ($diasAtividade as $dia) {
            $data = new DateTimeImmutable($dia);
            if ($anterior !== null && $data->format('Y-m-d') === $anterior->modify('+1 day')->format('Y-m-d')) {
                $sequencia++;
            } else {
                $sequencia = 1;
            }

            $maior = max($maior, $sequencia);
            $atual = $sequencia;
            $anterior = $data;
        }

        return ['atual' => $atual, 'maior' => $maior];
    }

    private function respostaCorreta(string $respostaCorreta, string $respostaEscolhida): bool
    {
        return $this->normalizarResposta($respostaCorreta) === $this->normalizarResposta($respostaEscolhida);
    }

    private function normalizarResposta(string $resposta): string
    {
        $resposta = strtolower(trim($resposta));
        $mapa = ['a' => '0', 'b' => '1', 'c' => '2', 'd' => '3'];

        return $mapa[$resposta] ?? $resposta;
    }

    private function concluirEtapaSeAtividadeConcluida(PDO $db, int $alunoId, int $atividadeId, int $etapaId): void
    {
        $stmtQuestoes = $db->prepare('SELECT COUNT(*) FROM questao WHERE atividade_id = :atividade_id');
        $stmtQuestoes->execute([':atividade_id' => $atividadeId]);
        $totalQuestoes = (int) $stmtQuestoes->fetchColumn();

        if ($totalQuestoes === 0) {
            return;
        }

        $stmtAcertos = $db->prepare(
            'SELECT COUNT(DISTINCT resposta.questao_id)
             FROM resposta_aluno AS resposta
             INNER JOIN questao ON questao.id = resposta.questao_id
             WHERE resposta.aluno_id = :aluno_id
                AND questao.atividade_id = :atividade_id
                AND resposta.acertou = 1'
        );
        $stmtAcertos->execute([
            ':aluno_id' => $alunoId,
            ':atividade_id' => $atividadeId,
        ]);

        if ((int) $stmtAcertos->fetchColumn() !== $totalQuestoes) {
            return;
        }

        $stmtProgresso = $db->prepare(
            'SELECT id FROM progresso_aluno
             WHERE aluno_id = :aluno_id AND etapa_id = :etapa_id
             ORDER BY ultima_atualizacao DESC, id DESC
             LIMIT 1'
        );
        $stmtProgresso->execute([
            ':aluno_id' => $alunoId,
            ':etapa_id' => $etapaId,
        ]);
        $progresso = $stmtProgresso->fetch();

        if ($progresso === false) {
            $insertProgresso = $db->prepare(
                "INSERT INTO progresso_aluno (aluno_id, etapa_id, estado, percentual)
                 VALUES (:aluno_id, :etapa_id, 'concluida', 100)"
            );
            $insertProgresso->execute([
                ':aluno_id' => $alunoId,
                ':etapa_id' => $etapaId,
            ]);

            return;
        }

        $updateProgresso = $db->prepare(
            "UPDATE progresso_aluno
             SET estado = 'concluida', percentual = 100, ultima_atualizacao = CURRENT_TIMESTAMP
             WHERE id = :id"
        );
        $updateProgresso->execute([':id' => $progresso['id']]);
    }

    private function sincronizarStreak(PDO $db, int $alunoId, array $sequencia): void
    {
        $diasAtividade = $this->buscarDiasAtividade($db, $alunoId);
        $data = $diasAtividade === [] ? null : end($diasAtividade);

        $stmtStreak = $db->prepare(
            'SELECT id FROM streak WHERE aluno_id = :aluno_id ORDER BY id DESC LIMIT 1'
        );
        $stmtStreak->execute([':aluno_id' => $alunoId]);
        $streak = $stmtStreak->fetch();

        if ($streak === false) {
            $insertStreak = $db->prepare(
                'INSERT INTO streak (aluno_id, streak_atual, maior_streak, ultima_data_atividade)
                 VALUES (:aluno_id, :streak_atual, :maior_streak, :ultima_data)'
            );
            $insertStreak->execute([
                ':aluno_id' => $alunoId,
                ':streak_atual' => $sequencia['atual'],
                ':maior_streak' => $sequencia['maior'],
                ':ultima_data' => $data,
            ]);

            return;
        }

        $updateStreak = $db->prepare(
            'UPDATE streak
             SET streak_atual = :streak_atual,
                 maior_streak = :maior_streak,
                 ultima_data_atividade = :ultima_data
             WHERE id = :id'
        );
        $updateStreak->execute([
            ':streak_atual' => $sequencia['atual'],
            ':maior_streak' => $sequencia['maior'],
            ':ultima_data' => $data,
            ':id' => $streak['id'],
        ]);
    }

    private function sincronizarRanking(PDO $db, int $alunoId, array $sequencia): void
    {
        $respostas = $this->buscarResumoRespostas($db, $alunoId);
        $percentual = $respostas['total'] > 0
            ? round(($respostas['acertos'] / $respostas['total']) * 100, 2)
            : 0;
        $stmtRanking = $db->prepare('SELECT id FROM ranking WHERE aluno_id = :aluno_id ORDER BY id DESC LIMIT 1');
        $stmtRanking->execute([':aluno_id' => $alunoId]);
        $ranking = $stmtRanking->fetch();
        $dados = [
            ':xp_total' => $this->buscarXp($db, $alunoId),
            ':atividades_concluidas' => $this->contarEtapasConcluidas($db, $alunoId),
            ':percentual_acertos' => $percentual,
            ':maior_streak' => $sequencia['maior'],
        ];

        if ($ranking === false) {
            $insertRanking = $db->prepare(
                'INSERT INTO ranking (aluno_id, xp_total, atividades_concluidas, percentual_acertos, maior_streak)
                 VALUES (:aluno_id, :xp_total, :atividades_concluidas, :percentual_acertos, :maior_streak)'
            );
            $dados[':aluno_id'] = $alunoId;
            $insertRanking->execute($dados);

            return;
        }

        $updateRanking = $db->prepare(
            'UPDATE ranking
             SET xp_total = :xp_total,
                 atividades_concluidas = :atividades_concluidas,
                 percentual_acertos = :percentual_acertos,
                 maior_streak = :maior_streak
             WHERE id = :id'
        );
        $dados[':id'] = $ranking['id'];
        $updateRanking->execute($dados);
    }

    private function normalizarEstado(string $estado): string
    {
        $estado = strtolower(trim($estado));
        $mapa = [
            'concluida' => 'complete',
            'concluido' => 'complete',
            'em_andamento' => 'current',
            'atual' => 'current',
            'disponivel' => 'available',
            'bloqueada' => 'locked',
            'bloqueado' => 'locked',
            'checkpoint' => 'checkpoint',
            'desafio' => 'checkpoint',
        ];

        return $mapa[$estado] ?? 'locked';
    }
}
