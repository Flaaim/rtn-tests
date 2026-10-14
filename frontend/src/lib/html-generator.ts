import { Question } from "@/interfaces/task.interface";

export const generateExportableHtml = (questions: Question[]): string => {
  // Компактный CSS: убраны лишние классы и отступы для экономии веса
  const styles = `
    body { font-family: sans-serif; font-size: 14px; line-height: 1.4; max-width: 900px; margin: 0 auto; padding: 20px; color: #111; }
    .q { margin-bottom: 20px; page-break-inside: avoid; }
    .c { color: #16a34a; font-weight: bold; }
    img { max-height: 200px; max-width: 100%; margin-top: 8px; border-radius: 4px; display: block; }
    ul { margin: 8px 0 0; padding-left: 20px; }
    li { margin-bottom: 6px; }
    a { color: #2563eb; text-decoration: none; }
  `.replace(/\s{2,}/g, ' '); // Небольшая минификация стилей

  const header = `<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Ответы</title>
  <style>${styles}</style>
</head>
<body>
  <p>Подготовка к аттестации в Ростехнадзоре: <a href="https://rtn-tests.ru" target="_blank">https://rtn-tests.ru</a></br>
  Ответы на вопросы тестирования: <a href="https://rtn-help.ru" target="_blank">https://rtn-help.ru</a></p>
  <hr style="margin-bottom:20px; border:none; border-top:1px solid #e5e7eb;">
`;

  const body = questions.map((q) => {
    let qHtml = `  <div class="q">\n`;

    // Нумерация добавлена непосредственно в текст вопроса, используется тег <b>
    qHtml += `    <b>${q.number}. ${q.text}</b>\n`;

    if (q.questionImg) {
      qHtml += `    <img src="${q.questionImg}" alt="К вопросу ${q.number}" />\n`;
    }
    qHtml += `    <ul>\n`;
    q.answers.forEach((a) => {
      const liClass = a.isCorrect ? ' class="c"' : '';
      const checkMark = a.isCorrect ? ' ✓' : '';

      qHtml += `      <li${liClass}>${a.text}${checkMark}`;
      if (a.answerImg) {
        qHtml += `<br><img src="${a.answerImg}" alt="К ответу" />`;
      }
      qHtml += `</li>\n`;
    });

    qHtml += `    </ul>\n  </div>`;
    return qHtml;
  }).join('\n');

  return header + body + `\n</body>\n</html>`;
};
