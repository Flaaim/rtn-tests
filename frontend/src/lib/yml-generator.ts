import { Question } from "@/interfaces/task.interface";
import { dump } from "js-yaml";

export const generateExportableYml = (questions: Question[]): string => {
  const data = {
    questions: questions.map((question) => ({
      name: `${question.number}. ${question.text}`,
      answers: question.answers.map((answer) => ({
        name: answer.text,
        right: answer.isCorrect ? 1 : 0,
      })),
    })),
  };

  return dump(data, {
    indent: 2,
    lineWidth: -1,
  });
};
