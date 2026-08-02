import { Question } from "@/interfaces/task.interface";

export const generateExportableHtml = (questions: Question[]): string => {
  let html = `<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Ответы</title>
  <style>
    body { font-family: sans-serif; line-height: 1.5; color: #333; max-width: 900px; margin: 0 auto; padding: 20px; }
    .question-card { border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; margin-bottom: 24px; page-break-inside: avoid; }
    .question-title { margin-top: 0; font-size: 1.25rem; color: #111; }
    .question-text { font-size: 1rem; margin-bottom: 16px; }
    .question-img { max-height: 300px; max-width: 100%; border: 1px solid #e5e7eb; border-radius: 4px; margin-bottom: 16px; }
    .answers-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .answer-item { padding: 12px; border: 1px solid #e5e7eb; border-radius: 6px; }
    .answer-item.correct { border-color: #22c55e; background-color: #f0fdf4; }
    .answer-header { display: flex; justify-content: space-between; align-items: flex-start; }
    .answer-text { font-size: 0.9rem; }
    .correct-label { color: #16a34a; font-weight: bold; font-size: 0.8rem; margin-left: 8px; }
    .answer-img { max-height: 150px; max-width: 100%; margin-top: 8px; border-radius: 4px; }
  </style>
</head>
<body>
  <h1>Ответы на курсы олимпокс</h1>
  <div class="ticket-header">
        Ответы на курсы Олимпокс: <a href="https://olimpoks-help.ru" target="_blank">https://olimpoks-help.ru</a>
  </div>
  <hr style="margin-bottom: 24px; border: none; border-top: 1px solid #e5e7eb;" />
`;

  questions.forEach((q) => {
    html += `  <div class="question-card">\n`;
    html += `    <h2 class="question-title">Вопрос ${q.number}</h2>\n`;
    html += `    <p class="question-text">${q.text}</p>\n`;

    if (q.questionImg) {
      html += `    <img src="${q.questionImg}" alt="К вопросу ${q.number}" class="question-img" />\n`;
    }

    html += `    <div class="answers-grid">\n`;
    q.answers.forEach((a) => {
      const correctClass = a.isCorrect ? "answer-item correct" : "answer-item";
      html += `      <div class="${correctClass}">\n`;
      html += `        <div class="answer-header">\n`;
      html += `          <span class="answer-text">${a.text}</span>\n`;
      if (a.isCorrect) html += `          <span class="correct-label">✓</span>\n`;
      html += `        </div>\n`;
      if (a.answerImg) {
        html += `        <img src="${a.answerImg}" alt="К ответу" class="answer-img" />\n`;
      }
      html += `      </div>\n`;
    });
    html += `    </div>\n  </div>\n`;
  });

  html += `</body>\n</html>`;

  return html;
};
