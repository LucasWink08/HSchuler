-- Corrige duplicações antigas de módulos/etapas de Potenciação e preserva o progresso.
-- Este projeto usa o módulo de Potenciação de menor id como canônico (normalmente id = 1).
-- Faça backup do banco antes de executar em produção.
USE hschulerf;
START TRANSACTION;

SET @modulo_potenciacao := (
    SELECT MIN(id) FROM modulo WHERE nome = 'Potenciação'
);

CREATE TEMPORARY TABLE etapas_potenciacao_canonicas AS
SELECT id, ordem_num
FROM etapa_trilha
WHERE modulo_id = @modulo_potenciacao;

-- Caso já exista progresso na etapa canônica, preserva-o e remove somente a cópia.
DELETE origem
FROM progresso_aluno AS origem
INNER JOIN etapa_trilha AS etapa_origem ON etapa_origem.id = origem.etapa_id
INNER JOIN etapas_potenciacao_canonicas AS etapa_destino
    ON etapa_destino.ordem_num = etapa_origem.ordem_num
INNER JOIN progresso_aluno AS destino
    ON destino.aluno_id = origem.aluno_id AND destino.etapa_id = etapa_destino.id
WHERE etapa_origem.modulo_id <> @modulo_potenciacao
  AND etapa_origem.modulo_id IN (SELECT id FROM modulo WHERE nome = 'Potenciação');

UPDATE progresso_aluno AS progresso
INNER JOIN etapa_trilha AS etapa_origem ON etapa_origem.id = progresso.etapa_id
INNER JOIN etapas_potenciacao_canonicas AS etapa_destino
    ON etapa_destino.ordem_num = etapa_origem.ordem_num
SET progresso.etapa_id = etapa_destino.id
WHERE etapa_origem.modulo_id <> @modulo_potenciacao
  AND etapa_origem.modulo_id IN (SELECT id FROM modulo WHERE nome = 'Potenciação');

UPDATE tentativa_etapa_trilha AS tentativa
INNER JOIN etapa_trilha AS etapa_origem ON etapa_origem.id = tentativa.etapa_id
INNER JOIN etapas_potenciacao_canonicas AS etapa_destino
    ON etapa_destino.ordem_num = etapa_origem.ordem_num
SET tentativa.etapa_id = etapa_destino.id
WHERE etapa_origem.modulo_id <> @modulo_potenciacao
  AND etapa_origem.modulo_id IN (SELECT id FROM modulo WHERE nome = 'Potenciação');

UPDATE atividade AS atividade
INNER JOIN etapa_trilha AS etapa_origem ON etapa_origem.id = atividade.etapa_id
INNER JOIN etapas_potenciacao_canonicas AS etapa_destino
    ON etapa_destino.ordem_num = etapa_origem.ordem_num
SET atividade.etapa_id = etapa_destino.id
WHERE etapa_origem.modulo_id <> @modulo_potenciacao
  AND etapa_origem.modulo_id IN (SELECT id FROM modulo WHERE nome = 'Potenciação');

UPDATE video AS video
INNER JOIN etapa_trilha AS etapa_origem ON etapa_origem.id = video.etapa_id
INNER JOIN etapas_potenciacao_canonicas AS etapa_destino
    ON etapa_destino.ordem_num = etapa_origem.ordem_num
SET video.etapa_id = etapa_destino.id
WHERE etapa_origem.modulo_id <> @modulo_potenciacao
  AND etapa_origem.modulo_id IN (SELECT id FROM modulo WHERE nome = 'Potenciação');

DELETE etapa
FROM etapa_trilha AS etapa
INNER JOIN modulo AS modulo ON modulo.id = etapa.modulo_id
WHERE modulo.id <> @modulo_potenciacao AND modulo.nome = 'Potenciação';

DELETE FROM modulo
WHERE id <> @modulo_potenciacao AND nome = 'Potenciação';

COMMIT;
