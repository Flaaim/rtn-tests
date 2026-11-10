import { fetchRecipientsPagination } from "@/actions/recipient";
import AdminBreadcrumbs from "@/components/Admin/AdminBreadcrumbs";
import { AlertCircle } from "lucide-react";
import { ListRecipientsDTO, RecipientDTO } from "@/interfaces/recipient.interface";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import Link from "next/link";
import RecipientStatusBadge from "@/components/Admin/Domain/Recipient/RecipientStatusBadge";

interface AdminRecipientsPageProps {
  searchParams: Promise<{ page?: string; perPage?: string }>;
}

export default async function AdminRecipientsPage({ searchParams }: AdminRecipientsPageProps) {
  const currentPage = Number((await searchParams).page) || 1;
  const perPage = Number((await searchParams).perPage) || 15;
  const result = await fetchRecipientsPagination(currentPage, perPage);

  if (!result.ok || !result.data) {
    return (
      <div className="space-y-6">
        <AdminBreadcrumbs items={[{ title: "Получатели" }]} />
        <div className="flex items-center justify-between">
          <h1 className="text-3xl font-bold">Получатели</h1>
        </div>
        <div className="mx-auto max-w-4xl p-4 md:p-8">
          <div className="flex min-h-[40vh] flex-col items-center justify-center space-y-4 text-center">
            <div className="flex size-16 items-center justify-center rounded-full bg-destructive/10 text-destructive">
              <AlertCircle className="size-8" />
            </div>
            <h2 className="text-xl font-semibold">Не удалось загрузить получателей</h2>
            <p className="text-sm text-muted-foreground">
              {result.error ?? "Произошла непредвиденная ошибка"}
            </p>
          </div>
        </div>
      </div>
    );
  }

  const paginatedRecipients: ListRecipientsDTO = result.data;
  const recipients = paginatedRecipients.items;

  return (
    <div className="space-y-6">
      <AdminBreadcrumbs items={[{ title: "Получатели" }]} />
      <div className="flex items-center justify-between">
        <h1 className="text-3xl font-bold">Получатели</h1>
      </div>
      <div className="rounded-md border bg-white">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Id</TableHead>
              <TableHead>Email</TableHead>
              <TableHead>Статус</TableHead>
              <TableHead>Дата</TableHead>
              <TableHead>Ошибки</TableHead>
              <TableHead>Быстрые действия</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {recipients.map((recipient: RecipientDTO) => (
              <TableRow key={recipient.recipientId}>
                <TableCell className="font-medium">
                  <Link
                    href={`/admin/recipients/${recipient.recipientId}`}
                    className="hover:underline"
                  >
                    {recipient.recipientId}
                  </Link>
                </TableCell>
                <TableCell className="font-medium">{recipient.email}</TableCell>
                <TableCell className="font-medium">
                  <RecipientStatusBadge status={recipient.status} />
                </TableCell>
                <TableCell className="font-medium">
                  {new Date(recipient.createdAt).toLocaleDateString("ru-RU")}
                </TableCell>
                <TableCell className="font-medium">{recipient.failedReason}</TableCell>
                <TableCell className="font-medium"></TableCell>
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </div>
    </div>
  );
}
