-- Vincula cada videoaula nova a uma turma específica.
ALTER TABLE video ADD COLUMN IF NOT EXISTS turma_id INT NULL AFTER professor_id;
CREATE INDEX IF NOT EXISTS idx_video_turma ON video(turma_id);
