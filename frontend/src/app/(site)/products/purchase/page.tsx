import { AlertCircle, ReceiptText, RefreshCcw } from "lucide-react";
import { z } from "zod";
import { fetchProductPaymentResult } from "@/actions/product";
import {
  Card,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from "@/components/ui/card";
import { ProductPaymentResult } from "@/interfaces/product.interface";
import ProductPaymentStatusBadge from "@/components/Product/ProductPaymentStatusBadge";
import Link from "next/link";
import { Button } from "@/components/ui/button";

interface PageProps {
  searchParams: Promise<{ paymentId: string }>;
}

const schema = z.string().uuid("Неверный формат идентификатора платежа");

export default async function PurchaseProductPage({ searchParams }: PageProps) {
  const { paymentId } = await searchParams;
  const parsed = schema.safeParse(paymentId);

  if (!paymentId || !parsed.success) {
    return (
      <div className="flex min-h-[50vh] flex-col items-center justify-center space-y-4 text-center">
        <div className="flex size-16 items-center justify-center rounded-full bg-destructive/10 text-destructive">
          <AlertCircle className="size-8" />
        </div>
        <h2 className="text-xl font-semibold">Некорректная ссылка</h2>
        <p className="text-sm text-muted-foreground">
          {parsed.error?.issues[0]?.message ?? "Отсутствует или неверен идентификатор платежа"}
        </p>
      </div>
    );
  }

  const result = await fetchProductPaymentResult(paymentId);

  if (!result || !result.data) {
    return (
      <div className="flex min-h-[50vh] flex-col items-center justify-center space-y-4 text-center">
        <div className="flex size-16 items-center justify-center rounded-full bg-muted text-muted-foreground">
          <ReceiptText className="size-8" />
        </div>
        <h2 className="text-xl font-semibold">Платеж не найден</h2>
        <p className="text-sm text-muted-foreground">
          Мы не смогли найти информацию по этому платежу. Возможно, он еще обрабатывается.
        </p>
      </div>
    );
  }

  const paymentResult: ProductPaymentResult = result.data;
  const isPending = paymentResult.status === "pending";

  const formattedDate = (date: Date | string) => {
    return new Date(date).toLocaleString("ru-RU", {
      day: "2-digit",
      month: "2-digit",
      year: "numeric",
    });
  };

  return (
    <div className="mx-auto max-w-2xl space-y-6 p-4 md:p-8">
      <h1 className="text-3xl font-bold tracking-tight">Статус заказа</h1>

      <Card>
        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-4">
          <div className="space-y-1">
            <CardTitle className="text-xl">Детали платежа</CardTitle>
            <CardDescription>ID: {paymentId}</CardDescription>
          </div>
          <ProductPaymentStatusBadge status={paymentResult.status} />
        </CardHeader>

        <CardContent className="space-y-4">
          <div className="grid grid-cols-2 gap-4 rounded-lg bg-muted/50 p-4 text-sm">
            <div className="space-y-1">
              <span className="text-muted-foreground">Сумма к оплате</span>
              <p className="font-medium">
                {paymentResult.amount} {paymentResult.currency}
              </p>
            </div>
            <div className="space-y-1">
              <span className="text-muted-foreground">Email получателя</span>
              <p className="font-medium">{paymentResult.email}</p>
            </div>
            <div className="space-y-1">
              <span className="text-muted-foreground">Дата</span>
              <p className="font-medium">{formattedDate(paymentResult.createdAt)}</p>
            </div>
          </div>

          {isPending && (
            <div className="flex items-center gap-3 rounded-lg border border-blue-200 bg-blue-50 p-4 text-sm text-blue-800 dark:border-blue-900 dark:bg-blue-950/50 dark:text-blue-300">
              <RefreshCcw className="size-5 animate-spin" />
              <p>
                Мы ожидаем подтверждения от платежной системы. Пожалуйста, обновите страницу через
                минуту.
              </p>
            </div>
          )}
        </CardContent>

        <CardFooter className="flex flex-col items-start space-y-4 border-t px-6 py-4 text-sm text-muted-foreground">
          <p>
            Если вы по ошибке указали неверный адрес электронной почты или товар не пришел, вы
            можете написать{" "}
            <Link
              href="https://t.me/flaaim"
              target="_blank"
              className="font-medium text-primary underline-offset-4 hover:underline"
            >
              мне в Telegram
            </Link>
            .
          </p>

          {isPending && (
            <Button variant="outline" className="w-full">
              <Link href={`?paymentId=${paymentId}`}>Обновить статус</Link>
            </Button>
          )}
        </CardFooter>
      </Card>
    </div>
  );
}
