import fetchSubscriptionsPaginated from "@/actions/subscriptions";
import AdminBreadcrumbs from "@/components/Admin/AdminBreadcrumbs";
import { AlertCircle } from "lucide-react";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import { SubscriptionAdminDTO } from "@/interfaces/subscription.interface";
import Link from "next/link";
import { format } from "date-fns";
import { ru } from "date-fns/locale";

interface AdminSubscriptionsPageProps {
  searchParams: Promise<{ page?: string; perPage?: string; q?: string }>;
}

export default async function AdminSubscriptionsPage({
  searchParams,
}: AdminSubscriptionsPageProps) {
  const currentPage = Number((await searchParams).page) || 1;
  const perPage = Number((await searchParams).perPage) || 25;
  const search = String((await searchParams).q || "");

  const result = await fetchSubscriptionsPaginated(currentPage, perPage, search);

  if (!result.ok || !result.data) {
    return (
      <div className="space-y-6">
        <AdminBreadcrumbs items={[{ title: "Подписки" }]} />
        <div className="flex items-center justify-between">
          <h1 className="text-3xl font-bold">Список подписок</h1>
        </div>
        <div className="mx-auto max-w-4xl p-4 md:p-8">
          <div className="flex min-h-[40vh] flex-col items-center justify-center space-y-4 text-center">
            <div className="flex size-16 items-center justify-center rounded-full bg-destructive/10 text-destructive">
              <AlertCircle className="size-8" />
            </div>
            <h2 className="text-xl font-semibold">Не удалось загрузить список подписок</h2>
            <p className="text-sm text-muted-foreground">
              {result.error ?? "Произошла непредвиденная ошибка"}
            </p>
          </div>
        </div>
      </div>
    );
  }

  const subscriptions: SubscriptionAdminDTO[] = result.data.items;

  const isHasSubscriptions = subscriptions && subscriptions.length > 0;

  const formatDate = (dateString: string | null) => {
    if (!dateString) return "—";
    return format(new Date(dateString), "dd.MM.yyyy, HH:mm", { locale: ru });
  };

  return (
    <div className="space-y-6">
      <AdminBreadcrumbs items={[{ title: "Подписки" }]} />
      <div className="flex items-center justify-between">
        <h1 className="text-3xl font-bold">Список подписок</h1>
      </div>
      <div className="rounded-md border bg-white">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Id</TableHead>
              <TableHead>Email</TableHead>
              <TableHead>План</TableHead>
              <TableHead>Статус</TableHead>
              <TableHead>Кол-во дней</TableHead>
              <TableHead>Начат</TableHead>
              <TableHead>Окончен</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {!isHasSubscriptions && (
              <TableRow>
                <TableCell colSpan={7} className="text-muted-foreground">
                  Подписки отсутствуют...
                </TableCell>
              </TableRow>
            )}
            {subscriptions.map((sub: SubscriptionAdminDTO) => (
              <TableRow key={sub.id}>
                <TableCell className="font-medium min-w-0 max-w-[300px] truncate">
                  <Link href={`/admin/subscriptions/${sub.id}`} className="hover:underline">
                    {sub.id}
                  </Link>
                </TableCell>
                <TableCell className="font-medium">{sub.email}</TableCell>
                <TableCell className="font-medium">{sub.plan}</TableCell>
                <TableCell className="font-medium">{sub.status}</TableCell>
                <TableCell className="font-medium">{sub.durationDays}</TableCell>
                <TableCell className="font-medium">{formatDate(sub.periodStart)}</TableCell>
                <TableCell className="font-medium">{formatDate(sub.periodEnd)}</TableCell>
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </div>
    </div>
  );
}
