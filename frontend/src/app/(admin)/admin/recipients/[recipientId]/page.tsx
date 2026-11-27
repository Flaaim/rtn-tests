import { fetchRecipientAction } from "@/actions/recipient";
import { RecipientFull } from "@/interfaces/recipient.interface";
import AdminBreadcrumbs from "@/components/Admin/AdminBreadcrumbs";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import RecipientStatusBadge from "@/components/Admin/Domain/Recipient/RecipientStatusBadge";
import Link from "next/link";

interface RecipientOverviewPageProps {
  params: Promise<{ recipientId: string }>;
}

export default async function RecipientOverviewPage({ params }: RecipientOverviewPageProps) {
  const { recipientId } = await params;
  const result = await fetchRecipientAction(recipientId);
  if (!result.ok || !result.data) {
    return null;
  }
  const recipient: RecipientFull = result.data;
  const { product } = recipient;
  const items = [{ title: "Получатели", href: "/admin/recipients" }, { title: recipient.email }];

  const formatDateTime = (date?: string | Date | null): string => {
    if (!date) return "—";
    return new Date(date).toLocaleString("ru-RU", {
      day: "2-digit",
      month: "2-digit",
      year: "numeric",
      hour: "2-digit",
      minute: "2-digit",
    });
  };

  return (
    <div className="space-y-6">
      <AdminBreadcrumbs items={items} />
      <div className="flex flex-wrap items-center justify-between gap-4">
        <h1 className="text-3xl font-bold">Получатель {recipient.email}</h1>
        <p className="text-sm text-muted-foreground font-mono mt-1">ID: {recipient.recipientId}</p>
      </div>

      {recipient.failedReason && (
        <div className="rounded-lg border border-destructive/50 bg-destructive/10 p-4 text-destructive">
          <p className="text-sm font-semibold">Причина ошибки отправки</p>
          <p className="mt-1 text-sm font-mono break-words">{recipient.failedReason}</p>
        </div>
      )}

      <div className="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <Card>
          <CardHeader className="border-b pb-4">
            <CardTitle className="text-xl">Информация об отправлении</CardTitle>
          </CardHeader>
          <CardContent className="pt-6">
            <dl className="grid grid-cols-1 gap-4 sm:grid-cols-2">
              <div>
                <dt className="text-sm font-medium text-muted-foreground">Email</dt>
                <dd className="mt-1 font-mono text-sm break-all">{recipient.email}</dd>
              </div>
              <div>
                <dt className="text-sm font-medium text-muted-foreground">Статус</dt>
                <dd className="mt-1 font-mono text-sm">
                  <RecipientStatusBadge status={recipient.status} />
                </dd>
              </div>
              <div>
                <dt className="text-sm font-medium text-muted-foreground">Создано</dt>
                <dd className="mt-1 font-mono text-sm">{formatDateTime(recipient.createdAt)}</dd>
              </div>
              <div>
                <dt className="text-sm font-medium text-muted-foreground">Отправлено</dt>
                <dd className="mt-1 font-mono text-sm">{formatDateTime(recipient.sendAt)}</dd>
              </div>
            </dl>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="border-b pb-4">
            <CardTitle className="text-xl">Продукт</CardTitle>
          </CardHeader>
          <CardContent className="pt-6">
            <dl className="grid grid-cols-1 gap-4 sm:grid-cols-2">
              <div className="sm:col-span-2">
                <dt className="text-sm font-medium text-muted-foreground">Название</dt>
                <dd className="mt-1 text-sm font-medium">
                  <Link
                    href={`/admin/products/${product.productId}`}
                    className="hover:underline text-primary"
                  >
                    {product.name}
                  </Link>
                </dd>
              </div>
              <div>
                <dt className="text-sm font-medium text-muted-foreground">Шифр (Cipher)</dt>
                <dd className="mt-1 font-mono text-sm">{product.cipher}</dd>
              </div>
              <div>
                <dt className="text-sm font-medium text-muted-foreground">ID продукта</dt>
                <dd className="mt-1 font-mono text-sm break-all">{product.productId}</dd>
              </div>
            </dl>
          </CardContent>
        </Card>
      </div>
    </div>
  );
}
