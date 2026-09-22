<?php

class TrilhaService
{
    private const XP_POR_ACERTO = 5;

    public function getResumo(?int $alunoId, ?string $area = null): array
    {
        $resumo = [
            'nome' => null,
            'xp' => null,
            'nivel' => null,
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
            $xpTotal = $this->buscarXp($db, $alunoId);

            return [
                'nome' => $aluno['usuario'],
                'xp' => $xpTotal,
                'nivel' => $this->calcularNivel($xpTotal),
                'etapas_concluidas' => $area === null
                    ? $this->contarEtapasConcluidas($db, $alunoId)
                    : count(array_filter($this->getEstadosEtapas($alunoId, $area), static fn (string $estado): bool => $estado === 'complete')),
                'total_etapas' => $area === null ? $this->contarEtapas($db) : count($this->getEtapas($area)),
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

    public function getProgressaoNivel(int $xp): array
    {
        $faixas = [
            1 => ['inicio' => 0, 'fim' => 50],
            2 => ['inicio' => 50, 'fim' => 120],
            3 => ['inicio' => 120, 'fim' => 210],
            4 => ['inicio' => 210, 'fim' => 320],
            5 => ['inicio' => 320, 'fim' => 450],
        ];
        $xp = max(0, $xp);
        $nivel = 5;
        foreach ($faixas as $numero => $faixa) {
            if ($xp < $faixa['fim'] || $numero === 5) {
                $nivel = $numero;
                break;
            }
        }
        $faixa = $faixas[$nivel];
        $percentual = $nivel === 5 && $xp >= $faixa['fim']
            ? 100
            : (int) round((min($xp, $faixa['fim']) - $faixa['inicio']) / ($faixa['fim'] - $faixa['inicio']) * 100);

        return [
            'nivel' => $nivel,
            'inicio' => $faixa['inicio'],
            'fim' => $faixa['fim'],
            'percentual' => max(0, min(100, $percentual)),
            'xp_restante' => max(0, $faixa['fim'] - $xp),
        ];
    }

    public function getNomeEtapaDaArea(string $area, int $ordem): string
    {
        $etapas = $this->nomesEtapasDaArea($area);
        return $etapas[$ordem - 1] ?? 'Etapa ' . $ordem;
    }

    public function getEtapas(string $area): array
    {
        try {
            $db = Database::getConnection();
            $modulo = $this->buscarOuCriarModulo($db, $area);
            $stmt = $db->prepare(
                'SELECT id, nome, descricao, ordem_num, estado
                 FROM etapa_trilha
                 WHERE modulo_id = :modulo_id
                 ORDER BY ordem_num, id'
            );
            $stmt->execute([':modulo_id' => $modulo['id']]);

            return $stmt->fetchAll() ?: [];
        } catch (PDOException $exception) {
            return [];
        }
    }

    public function getEstadosEtapas(int $alunoId, string $area = 'potenciacao'): array
    {
        if ($alunoId <= 0) {
            return [];
        }

        try {
            $db = Database::getConnection();
            $modulo = $this->buscarOuCriarModulo($db, $area);
            $stmt = $db->prepare(
                'SELECT etapa.id, etapa.nome, etapa.ordem_num, etapa.estado AS estado_padrao,
                    (
                        SELECT progresso.estado
                        FROM progresso_aluno AS progresso
                        WHERE progresso.aluno_id = :aluno_id
                            AND progresso.etapa_id = etapa.id
                        ORDER BY progresso.ultima_atualizacao DESC, progresso.id DESC
                        LIMIT 1
                    ) AS estado_aluno
                 FROM etapa_trilha AS etapa
                 WHERE etapa.modulo_id = :modulo_id
                 ORDER BY etapa.ordem_num, etapa.id'
            );
            $stmt->execute([':aluno_id' => $alunoId, ':modulo_id' => $modulo['id']]);

            $estados = [];
            $anteriorConcluida = true;
            foreach ($stmt->fetchAll() as $etapa) {
                $estadoSalvo = $etapa['estado_aluno'] !== null
                    ? $this->normalizarEstado((string) $etapa['estado_aluno'])
                    : null;
                // Uma conclusão registrada só aparece como concluída quando todos os
                // pré-requisitos também estão concluídos. Isso mantém a sequência
                // coerente quando uma trilha recebe novas etapas intermediárias.
                $estado = $anteriorConcluida && $estadoSalvo === 'complete'
                    ? 'complete'
                    : ($anteriorConcluida ? ((int) $etapa['ordem_num'] % 5 === 0 ? 'checkpoint' : 'current') : 'locked');
                $estados[$this->normalizarIdentificador((string) $etapa['nome'])] = $estado;
                $anteriorConcluida = $estado === 'complete';
            }

            return $estados;
        } catch (PDOException $exception) {
            return [];
        }
    }

    public function concluirEtapa(int $alunoId, int $etapaId, array $respostas, array $questoes): ?array
    {
        if ($alunoId <= 0 || $etapaId <= 0 || count($questoes) !== 5 || count($respostas) !== 5) {
            return null;
        }

        try {
            $db = Database::getConnection();
            $db->beginTransaction();
            $stmtEtapa = $db->prepare(
                'SELECT etapa.id, etapa.nome, etapa.ordem_num, etapa.modulo_id
                 FROM etapa_trilha AS etapa
                 WHERE etapa.id = :etapa_id LIMIT 1'
            );
            $stmtEtapa->execute([':etapa_id' => $etapaId]);
            $etapa = $stmtEtapa->fetch();
            if ($etapa === false || !$this->etapaLiberada($db, $alunoId, (int) $etapa['modulo_id'], (int) $etapa['ordem_num'])) {
                $db->rollBack();
                return null;
            }

            $acertos = 0;
            $resultadosQuestoes = [];
            foreach ($questoes as $indice => $questao) {
                $resposta = isset($respostas[$indice]) ? (string) $respostas[$indice] : '';
                $indiceCorreto = (int) $questao['correta'];
                $indiceEscolhido = (int) $resposta;
                $acertou = $resposta !== '' && $indiceEscolhido === $indiceCorreto;

                if ($acertou) {
                    $acertos++;
                }

                $alternativas = $questao['alternativas'] ?? [];
                $resultadosQuestoes[] = [
                    'numero' => $indice + 1,
                    'enunciado' => (string) ($questao['enunciado'] ?? ''),
                    'acertou' => $acertou,
                    'indice_escolhido' => $indiceEscolhido,
                    'resposta_escolhida' => (string) ($alternativas[$indiceEscolhido] ?? ''),
                    'indice_correto' => $indiceCorreto,
                    'resposta_correta' => (string) ($alternativas[$indiceCorreto] ?? ''),
                    'explicacao' => (string) ($questao['explicacao'] ?? ''),
                ];
            }

            $stmtTentativa = $db->prepare(
                'INSERT INTO tentativa_etapa_trilha (aluno_id, etapa_id, acertos, erros, xp_recebido)
                 VALUES (:aluno_id, :etapa_id, :acertos, :erros, :xp_recebido)'
            );

            $stmtProgresso = $db->prepare(
                'SELECT id, estado FROM progresso_aluno
                 WHERE aluno_id = :aluno_id AND etapa_id = :etapa_id
                 ORDER BY id DESC LIMIT 1 FOR UPDATE'
            );
            $stmtProgresso->execute([':aluno_id' => $alunoId, ':etapa_id' => $etapaId]);
            $progresso = $stmtProgresso->fetch();
            $primeiraConclusao = $progresso === false || strtolower((string) $progresso['estado']) !== 'concluida';

            if ($progresso === false) {
                $stmt = $db->prepare(
                    "INSERT INTO progresso_aluno (aluno_id, etapa_id, estado, percentual)
                     VALUES (:aluno_id, :etapa_id, 'concluida', 100)"
                );
                $stmt->execute([':aluno_id' => $alunoId, ':etapa_id' => $etapaId]);
            } else {
                $stmt = $db->prepare(
                    "UPDATE progresso_aluno SET estado = 'concluida', percentual = 100,
                     ultima_atualizacao = CURRENT_TIMESTAMP WHERE id = :id"
                );
                $stmt->execute([':id' => $progresso['id']]);
            }

            $xpRecebido = $primeiraConclusao ? $acertos * self::XP_POR_ACERTO : 0;

            $stmtTentativa->execute([
                ':aluno_id' => $alunoId, ':etapa_id' => $etapaId,
                ':acertos' => $acertos, ':erros' => 5 - $acertos,
                ':xp_recebido' => $xpRecebido,
            ]);

            if ($xpRecebido > 0) {
                $stmt = $db->prepare(
                    "INSERT INTO xp_transacao (aluno_id, tipo, quantidade, descricao)
                     VALUES (:aluno_id, 'questoes_acertadas', :quantidade, :descricao)"
                );
                $stmt->execute([
                    ':aluno_id' => $alunoId,
                    ':quantidade' => $xpRecebido,
                    ':descricao' => $acertos . ' acerto(s) na etapa ' . $etapaId
                        . ' (' . self::XP_POR_ACERTO . ' XP por acerto).',
                ]);
            }

            $sequencia = $this->calcularSequencia($this->buscarDiasAtividade($db, $alunoId));
            $this->sincronizarStreak($db, $alunoId, $sequencia);
            $this->sincronizarPerfil($db, $alunoId);
            $this->sincronizarRanking($db, $alunoId, $sequencia);
            $db->commit();

            return [
                'acertos' => $acertos,
                'erros' => 5 - $acertos,
                'xp_recebido' => $xpRecebido,
                'xp_por_acerto' => self::XP_POR_ACERTO,
                'primeira_conclusao' => $primeiraConclusao,
                'questoes' => $resultadosQuestoes,
                'resumo' => $this->getResumo($alunoId),
            ];
        } catch (PDOException $exception) {
            if (isset($db) && $db->inTransaction()) {
                $db->rollBack();
            }
            return null;
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
                    ':descricao' => 'PontuaÃ§Ã£o registrada para a questÃ£o ' . $questaoId,
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
            $this->sincronizarPerfil($db, $alunoId);

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
            $this->sincronizarPerfil($db, $alunoId);
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
        $texto = strtr($texto, [
            'á' => 'a', 'à' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a',
            'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
            'í' => 'i', 'ì' => 'i', 'î' => 'i', 'ï' => 'i',
            'ó' => 'o', 'ò' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o',
            'ú' => 'u', 'ù' => 'u', 'û' => 'u', 'ü' => 'u', 'ç' => 'c',
            'Á' => 'A', 'À' => 'A', 'Â' => 'A', 'Ã' => 'A', 'É' => 'E',
            'Ê' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O',
            'Ú' => 'U', 'Ç' => 'C',
        ]);
        $texto = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $texto) ?: $texto;
        $texto = strtolower(trim($texto));
        $texto = preg_replace('/[^a-z0-9]+/', '-', $texto) ?: '';

        return trim($texto, '-');
    }

    private function buscarAluno(PDO $db, int $alunoId): ?array
    {
        $stmt = $db->prepare('SELECT id, usuario, xp_total, nivel FROM aluno WHERE id = :id LIMIT 1');
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
            'SELECT COALESCE(SUM(total), 0) AS total, COALESCE(SUM(acertos), 0) AS acertos
             FROM (
                SELECT COUNT(*) AS total, COALESCE(SUM(acertou = 1), 0) AS acertos
                FROM resposta_aluno WHERE aluno_id = :aluno_id_respostas
                UNION ALL
                SELECT COUNT(*) * 5 AS total, COALESCE(SUM(acertos), 0) AS acertos
                FROM tentativa_etapa_trilha WHERE aluno_id = :aluno_id_tentativas
             ) AS resultados'
        );
        $stmt->execute([
            ':aluno_id_respostas' => $alunoId,
            ':aluno_id_tentativas' => $alunoId,
        ]);
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
                UNION
                SELECT DATE(concluida_em) AS dia FROM tentativa_etapa_trilha WHERE aluno_id = :aluno_id_tentativa
             ) AS atividades
             ORDER BY dia ASC'
        );
        $stmt->execute([
            ':aluno_id_resposta' => $alunoId,
            ':aluno_id_simulado' => $alunoId,
            ':aluno_id_tentativa' => $alunoId,
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

        $hoje = new DateTimeImmutable('today');
        if ($anterior !== null && $anterior->format('Y-m-d') !== $hoje->format('Y-m-d') && $anterior->format('Y-m-d') !== $hoje->modify('-1 day')->format('Y-m-d')) { $atual = 0; }

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

        if ($data === null) {
            return;
        }

        $stmtStreak = $db->prepare(
            'SELECT id, streak_atual, maior_streak, ultima_data_atividade FROM streak WHERE aluno_id = :aluno_id ORDER BY id DESC LIMIT 1'
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
                ':streak_atual' => 1,
                ':maior_streak' => 1,
                ':ultima_data' => $data,
            ]);

            return;
        }

        $ultimaData = $streak['ultima_data_atividade'] ?: null;
        if ($ultimaData === $data) {
            return;
        }

        $streakAtual = 1;
        if ($ultimaData !== null) {
            $ultima = new DateTimeImmutable((string) $ultimaData);
            $nova = new DateTimeImmutable((string) $data);
            $intervalo = (int) $ultima->diff($nova)->format('%a');

            if ($intervalo === 1) {
                $streakAtual = (int) $streak['streak_atual'] + 1;
            }
        }

        $maiorStreak = max((int) $streak['maior_streak'], $streakAtual);

        $updateStreak = $db->prepare(
            'UPDATE streak
             SET streak_atual = :streak_atual,
                 maior_streak = :maior_streak,
                 ultima_data_atividade = :ultima_data
             WHERE id = :id'
        );
        $updateStreak->execute([
            ':streak_atual' => $streakAtual,
            ':maior_streak' => $maiorStreak,
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

    private function sincronizarPerfil(PDO $db, int $alunoId): void
    {
        $xpTotal = $this->buscarXp($db, $alunoId);
        $nivel = $this->calcularNivel($xpTotal);

        $stmt = $db->prepare(
            'UPDATE aluno
             SET xp_total = :xp_total,
                 nivel = :nivel
             WHERE id = :aluno_id'
        );
        $stmt->execute([
            ':xp_total' => $xpTotal,
            ':nivel' => $nivel,
            ':aluno_id' => $alunoId,
        ]);
    }

    private function calcularNivel(int $xp): int
    {
        return $this->getProgressaoNivel($xp)['nivel'];
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
    private function buscarOuCriarModulo(PDO $db, string $area): array
    {
        $area = $this->normalizarIdentificador($area) ?: 'potenciacao';
        $stmt = $db->query('SELECT id, nome FROM modulo ORDER BY id');
        foreach ($stmt->fetchAll() as $modulo) {
            if ($this->normalizarIdentificador((string) $modulo['nome']) === $area) {
                $this->sincronizarEtapasDaArea($db, (int) $modulo['id'], $area);
                return $modulo;
            }
        }
        $titulos = ['potenciacao' => 'Potenciação', 'fracoes-algebricas' => 'Frações algébricas', 'produtos-notaveis' => 'Produtos notáveis', 'fatoracao' => 'Fatoração', 'equacoes' => 'Equações', 'inequacoes' => 'Inequações'];
        $insert = $db->prepare('INSERT INTO modulo (nome, ordem_num) VALUES (:nome, 1)');
        $insert->execute([':nome' => $titulos[$area] ?? ucfirst(str_replace('-', ' ', $area))]);
        $id = (int) $db->lastInsertId();
        $this->sincronizarEtapasDaArea($db, $id, $area);
        return ['id' => $id, 'nome' => $titulos[$area] ?? $area];
    }

    /** Mantém as etapas já criadas e inclui os novos níveis sem perder progresso. */
    private function sincronizarEtapasDaArea(PDO $db, int $moduloId, string $area): void
    {
        $etapasPlanejadas = $this->nomesEtapasDaArea($area);
        $stmt = $db->prepare('SELECT id, nome, ordem_num FROM etapa_trilha WHERE modulo_id = :modulo_id');
        $stmt->execute([':modulo_id' => $moduloId]);

        $existentes = [];
        foreach ($stmt->fetchAll() as $etapa) {
            $existentes[$this->normalizarIdentificador((string) $etapa['nome'])] = [
                'id' => (int) $etapa['id'],
                'ordem' => (int) $etapa['ordem_num'],
            ];
        }

        $inserir = $db->prepare(
            'INSERT INTO etapa_trilha (modulo_id, nome, descricao, ordem_num, estado)
             VALUES (:modulo_id, :nome, :descricao, :ordem, :estado)'
        );
        $atualizar = $db->prepare(
            'UPDATE etapa_trilha
             SET ordem_num = :ordem
             WHERE id = :id'
        );

        foreach ($etapasPlanejadas as $indice => $nome) {
            $ordem = $indice + 1;
            $dados = [
                ':nome' => $nome,
                ':descricao' => 'Etapa de aprendizagem: ' . $nome,
                ':ordem' => $ordem,
                ':estado' => $ordem % 5 === 0 ? 'checkpoint' : 'bloqueada',
            ];
            $chave = $this->normalizarIdentificador($nome);

            if (isset($existentes[$chave])) {
                if ($existentes[$chave]['ordem'] !== $ordem) {
                    $atualizar->execute([':ordem' => $ordem, ':id' => $existentes[$chave]['id']]);
                }
                continue;
            }

            $inserir->execute($dados + [':modulo_id' => $moduloId]);
        }
    }

    private function etapaLiberada(PDO $db, int $alunoId, int $moduloId, int $ordem): bool
    {
        if ($ordem <= 1) return true;
        $stmt = $db->prepare('SELECT progresso.estado FROM progresso_aluno AS progresso INNER JOIN etapa_trilha AS etapa ON etapa.id = progresso.etapa_id WHERE progresso.aluno_id = :aluno_id AND etapa.modulo_id = :modulo_id AND etapa.ordem_num = :ordem ORDER BY progresso.ultima_atualizacao DESC, progresso.id DESC LIMIT 1');
        $stmt->execute([':aluno_id' => $alunoId, ':modulo_id' => $moduloId, ':ordem' => $ordem - 1]);
        $progresso = $stmt->fetch();
        return $progresso !== false && strtolower((string) $progresso['estado']) === 'concluida';
    }

    private function nomesEtapasDaArea(string $area): array
    {
        $etapas = [
            'potenciacao' => ['Introdução', 'Base e expoente', 'Potências de base 10', 'Expoente zero e um', 'Exercícios de potenciação', 'Revisão', 'Produto de potências', 'Quociente de potências', 'Potência de uma potência', 'Expoentes negativos', 'Notação científica', 'Propriedades combinadas', 'Desafio final'],
            'fracoes-algebricas' => ['Termos algébricos', 'Domínio', 'Fator comum', 'Exercícios com frações', 'Revisão', 'Multiplicação', 'Soma e subtração', 'Denominador comum', 'Frações complexas', 'Equações fracionárias', 'Simplificação avançada', 'Aplicações', 'Desafio final'],
            'fatoracao' => ['Fator comum', 'Agrupamento', 'Diferença de quadrados', 'Exercícios de fatoração', 'Revisão', 'Trinômios', 'Soma de cubos', 'Prática', 'Trinômio quadrado perfeito', 'Fatoração completa', 'Substituição', 'Aplicações e raízes', 'Desafio final'],
            'equacoes' => ['Princípio da igualdade', 'Termos semelhantes', 'Isolando a incógnita', 'Exercícios de equações', 'Revisão', 'Parênteses', 'Equações fracionárias', 'Proporções', 'Problemas', '2º grau', 'Fórmula de Bhaskara', 'Sistemas lineares', 'Desafio final'],
            'inequacoes' => ['Símbolos de comparação', 'Conjunto solução', 'Reta numérica', 'Exercícios de inequações', 'Revisão', 'Coeficientes negativos', 'Inequações compostas', 'Intervalos', 'Sistemas', 'Inequações fracionárias', 'Módulo', 'Prática', 'Desafio final'],
            'produtos-notaveis' => ['Padrões algébricos', 'Quadrado da soma', 'Quadrado da diferença', 'Exercícios de produtos notáveis', 'Revisão', 'Soma pela diferença', 'Aplicações', 'Fórmulas', 'Fatoração de quadrados perfeitos', 'Binômios com coeficientes', 'Expressões combinadas', 'Cálculo inteligente', 'Desafio final'],
        ];

        return $etapas[$area] ?? ['Introdução', 'Conceitos fundamentais', 'Prática guiada', 'Exercícios', 'Revisão', 'Aplicações iniciais', 'Aprofundamento', 'Prática intermediária', 'Consolidação', 'Aplicações avançadas', 'Estratégias de resolução', 'Prática final', 'Desafio final'];
    }
}




