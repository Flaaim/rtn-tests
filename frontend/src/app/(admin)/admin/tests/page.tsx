import AdminBreadcrumbs from "@/components/Admin/AdminBreadcrumbs";
import { fetchTestsPaginatedAction } from "@/actions/test";
import { AlertCircle } from "lucide-react";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import Link from "next/link";
import Pagination from "@/components/Pagination/Pagination";
import { TestItem } from "@/interfaces/test.interface";
import AddTestDialog from "@/components/Admin/Test/AddTestDialog";
import TestStatusControl from "@/components/Admin/Test/Status/TestStatusControl";
import RemoveTestDialog from "@/components/Admin/Test/RemoveTestDialog";

interface AdminTestsPageProps {
  searchParams: Promise<{ page?: string; perPage?: string; q?: string }>;
}

export default async function AdminTestsPage({ searchParams }: AdminTestsPageProps) {
  const currentPage = Number((await searchParams).page) || 1;
  const perPage = Number((await searchParams).perPage) || 15;
  const search = String((await searchParams).q || "");

  const result = await fetchTestsPaginatedAction(currentPage, perPage, search);

  if (!result.ok || !result.data) {
    return (
      <div className="space-y-6">
        <AdminBreadcrumbs items={[{ title: "Тесты" }]} />
        <div className="flex items-center justify-between">
          <h1 className="text-3xl font-bold">Тесты</h1>
        </div>
        <div className="mx-auto max-w-4xl p-4 md:p-8">
          <div className="flex min-h-[40vh] flex-col items-center justify-center space-y-4 text-center">
            <div className="flex size-16 items-center justify-center rounded-full bg-destructive/10 text-destructive">
              <AlertCircle className="size-8" />
            </div>
            <h2 className="text-xl font-semibold">Не удалось загрузить тесты</h2>
            <p className="text-sm text-muted-foreground">
              {result.error ?? "Произошла непредвиденная ошибка"}
            </p>
          </div>
        </div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      <AdminBreadcrumbs items={[{ title: "Тесты" }]} />
      <div className="flex items-center justify-between">
        <h1 className="text-3xl font-bold">Тесты</h1>
        <AddTestDialog />
      </div>
      <div className="rounded-md border bg-white">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Название</TableHead>
              <TableHead>Шифр</TableHead>
              <TableHead>Создан</TableHead>
              <TableHead>Статус</TableHead>
              <TableHead>Активировать</TableHead>
              <TableHead>Удалить</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {result.data.items.length === 0 ? (
              <TableRow>
                <TableCell colSpan={5} className="text-muted-foreground">
                  Тесты отсутствуют...
                </TableCell>
              </TableRow>
            ) : (
              result.data.items.map((test: TestItem) => (
                <TableRow key={test.testId}>
                  <TableCell className="font-medium">
                    <Link href={`/admin/tests/${test.testId}`} className="hover:underline">
                      {test.name}
                    </Link>
                  </TableCell>
                  <TableCell className="font-medium">{test.cipher}</TableCell>
                  <TableCell className="font-medium">
                    {new Date(test.createdAt).toLocaleDateString("ru-RU")}
                  </TableCell>
                  <TestStatusControl id={test.testId} initialStatus={test.status} />
                  <TableCell>
                    <RemoveTestDialog id={test.testId} />
                  </TableCell>
                </TableRow>
              ))
            )}
          </TableBody>
        </Table>
        <Pagination
          currentPage={currentPage}
          totalPages={result.data.totalCount}
          baseUrl="/admin/tests"
        />
      </div>
    </div>
  );
}
