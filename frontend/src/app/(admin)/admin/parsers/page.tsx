import AdminBreadcrumbs from "@/components/Admin/AdminBreadcrumbs";
import AddParserDialog from "@/components/Admin/Parser/AddParserDialog";
import { fetchParsersAction } from "@/actions/parser";
import { AlertCircle } from "lucide-react";
import {
  Table,
  TableBody,
  TableHead,
  TableHeader,
  TableRow,
  TableCell,
} from "@/components/ui/table";
import { ParserShort } from "@/interfaces/parser.interface";
import Link from "next/link";

export default async function AdminParsersPage() {
  const result = await fetchParsersAction();

  if (!result.ok || !result.data) {
    return (
      <div className="space-y-6">
        <AdminBreadcrumbs items={[{ title: "Парсеры" }]} />
        <div className="flex items-center justify-between">
          <h1 className="text-3xl font-bold">Парсеры</h1>
          <AddParserDialog />
        </div>
        <div className="mx-auto max-w-4xl p-4 md:p-8">
          <div className="flex min-h-[40vh] flex-col items-center justify-center space-y-4 text-center">
            <div className="flex size-16 items-center justify-center rounded-full bg-destructive/10 text-destructive">
              <AlertCircle className="size-8" />
            </div>
            <h2 className="text-xl font-semibold">Не удалось загрузить парсеры</h2>
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
      <AdminBreadcrumbs items={[{ title: "Парсеры" }]} />
      <div className="flex items-center justify-between">
        <h1 className="text-3xl font-bold">Парсеры</h1>
        <AddParserDialog />
      </div>
      <div className="rounded-md border bg-white">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Номер</TableHead>
              <TableHead>Хост</TableHead>
              <TableHead>Действия</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {result.data.map((parser: ParserShort) => (
              <TableRow key={parser.id}>
                <TableCell className="font-medium">
                  <Link href={`/admin/parsers/${parser.id}`} className="hover:underline">
                    {parser.id}
                  </Link>
                </TableCell>
                <TableCell>{parser.host}</TableCell>
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </div>
    </div>
  );
}
