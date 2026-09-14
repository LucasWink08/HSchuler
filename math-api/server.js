const express = require('express');
const cors = require('cors');
const { evaluate } = require('mathjs');

const app = express();
const PORT = process.env.PORT || 3001;

app.use(cors());
app.use(express.json());

app.get('/health', (req, res) => {
  res.json({ success: true, status: 'ok' });
});

app.post('/api/math/evaluate', (req, res) => {
  try {
    const { expression, scope = {} } = req.body || {};

    if (!expression || typeof expression !== 'string') {
      return res.status(400).json({ success: false, error: 'Expression is required.' });
    }

    const result = evaluate(expression, scope);

    return res.json({
      success: true,
      expression,
      result,
    });
  } catch (error) {
    return res.status(400).json({
      success: false,
      error: 'Expressão matemática inválida.',
      details: error.message,
    });
  }
});

app.listen(PORT, () => {
  console.log(`Math API running on port ${PORT}`);
});
