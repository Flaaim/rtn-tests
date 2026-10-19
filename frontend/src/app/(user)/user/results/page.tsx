import UserBreadcrumbs from "@/components/User/UserBreadcrumbs";
import { fetchUserAttemptsPaginationAction } from "@/actions/profile";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import { UserAttemptDTO } from "@/interfaces/user.interface";
import Link from "next/link";
import { format } from "date-fns";
import { ru } from "date-fns/locale";
import { CheckCircle2, XCircle, Clock } from "lucide-react";
import Pagination from "@/components/Pagination/Pagination";

// Хелпер для форматирования даты
const formatDate = (dateString: string | null) => {
  if (!dateString) return "—";
  return format(new Date(dateString), "dd.MM.yyyy, HH:mm", { locale: ru });
};

// Хелпер для визуального оформления статуса
const getStatusBadge = (status: string) => {
  switch (status) {
    case "passed":
      return (
        <span className="inline-flex items-center gap-1.5 rounded-md bg-green-500/10 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-500/20">
          <CheckCircle2 className="h-3.5 w-3.5" />
          Сдан
        </span>
      );
    case "failed":
      return (
        <span className="inline-flex items-center gap-1.5 rounded-md bg-red-500/10 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-500/20">
          <XCircle className="h-3.5 w-3.5" />
          Не сдан
        </span>
      );
    case "in_progress":
      return (
        <span className="inline-flex items-center gap-1.5 rounded-md bg-blue-500/10 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-500/20">
          <Clock className="h-3.5 w-3.5" />В процессе
        </span>
      );
    default:
      return (
        <span className="inline-flex items-center rounded-md bg-muted px-2 py-1 text-xs font-medium text-muted-foreground ring-1 ring-inset ring-border">
          {status}
        </span>
      );
  }
};

interface UserResultsPageProps {
  searchParams: Promise<{ page?: string; perPage?: string }>;
}

export default async function UserResultsPage({ searchParams }: UserResultsPageProps) {
  const currentPage = Number((await searchParams).page) || 1;
  const perPage = Number((await searchParams).perPage) || 15;

  const result = await fetchUserAttemptsPaginationAction(currentPage, perPage);

  if (!result || !result.data) {
    return (
      <div className="space-y-6">
        <UserBreadcrumbs items={[{ title: "Результаты" }]} />
        <div className="flex items-center justify-between">
          <h1 className="text-3xl font-bold tracking-tight">Результаты тестов</h1>
        </div>
        <div className="flex h-64 w-full items-center justify-center rounded-lg border border-dashed bg-muted/30 text-muted-foreground">
          Результаты тестов не найдены.
        </div>
      </div>
    );
  }
  const userAttempts: UserAttemptDTO[] = result.data.items;

  return (
    <div className="space-y-6">
      <UserBreadcrumbs items={[{ title: "Результаты" }]} />
      <div className="flex items-center justify-between">
        <h1 className="text-3xl font-bold tracking-tight">Результаты тестов</h1>
      </div>

      <div className="rounded-md border">
        <Table>
          <TableHeader className="bg-muted/50">
            <TableRow>
              <TableHead className="w-[300px]">Название теста</TableHead>
              <TableHead>Шифр</TableHead>
              <TableHead>Статус</TableHead>
              <TableHead className="text-center">Билет</TableHead>
              <TableHead className="text-center">Правильно</TableHead>
              <TableHead className="text-center">Ошибки (Допустимо)</TableHead>
              <TableHead>Начат</TableHead>
              <TableHead>Окончен</TableHead>
            </TableRow>
          </TableHeader>
          {/* ❗️ TableBody вынесен из TableHeader */}
          <TableBody>
            {userAttempts.map((attempt: UserAttemptDTO) => (
              <TableRow key={attempt.id} className="hover:bg-muted/30 transition-colors">
                <TableCell className="font-medium">
                  <Link
                    href={`/attempts/${attempt.id}/result`}
                    className="text-foreground hover:text-primary transition-colors"
                  >
                    {attempt.name}
                  </Link>
                </TableCell>
                <TableCell className="text-muted-foreground">{attempt.cipher}</TableCell>
                <TableCell>{getStatusBadge(attempt.status)}</TableCell>
                <TableCell className="text-center font-mono">
                  {attempt.ticketNumber !== 0 ? attempt.ticketNumber : `Работа над ошибками`}
                </TableCell>
                <TableCell className="text-center font-semibold text-green-600">
                  {attempt.score > 0 ? attempt.score : "—"}
                </TableCell>
                <TableCell className="text-center">
                  <span
                    className={
                      attempt.mistakes > attempt.allowedMistakes ? "text-red-600 font-semibold" : ""
                    }
                  >
                    {attempt.mistakes}
                  </span>
                  {" / "}
                  <span className="text-muted-foreground">{attempt.allowedMistakes}</span>
                </TableCell>
                <TableCell className="text-sm text-muted-foreground">
                  {formatDate(attempt.startedAt)}
                </TableCell>
                <TableCell className="text-sm text-muted-foreground">
                  {formatDate(attempt.finishedAt)}
                </TableCell>
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </div>
      <div className="space-y-6">
        <Pagination
          currentPage={currentPage}
          totalPages={result.data.totalPages}
          baseUrl="/user/results"
        />
      </div>
    </div>
  );
}
