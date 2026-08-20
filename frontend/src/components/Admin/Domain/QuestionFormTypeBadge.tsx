import { Badge } from "@/components/ui/badge";

const QUESTION_FORM: Record<string, string> = {
  single_choice: "Одиночный выбор",
  multiple_choice: "Множественный выбор",
  matching: "Сопоставить выбор",
  sequence: "Установить последовательность",
};
interface QuestionFormTypeBadgeProps {
  type: string;
}
export default function QuestionFormTypeBadge({ type }: QuestionFormTypeBadgeProps) {
  return <Badge variant="outline">{QUESTION_FORM[type]}</Badge>;
}
