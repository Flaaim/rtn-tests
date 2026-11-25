"use client";

import { z } from "zod";
import { createPaymentProductAction } from "@/actions/product";
import { toast } from "sonner";
import { Controller, useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { Button } from "@/components/ui/button";
import React, { useId, useState } from "react";
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from "@/components/ui/card";
import { Download, FileText, Loader2, Receipt } from "lucide-react";
import { Field, FieldError, FieldGroup, FieldLabel } from "@/components/ui/field";
import { Input } from "@/components/ui/input";
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from "@/components/ui/dialog";

const schema = z.object({
  email: z.email("Пожалуйста, введите корректный email адрес."),
});

type PurchaseProductFormData = z.infer<typeof schema>;

interface PurchaseProductProps {
  id: string;
  name: string;
  amount: string | number;
  variant?: "card" | "dialog";
}

export default function PurchaseProduct({
  id,
  name,
  amount,
  variant = "card",
}: PurchaseProductProps) {
  const [open, setOpen] = useState(false);
  const uid = useId();
  const formId = `purchase-product-form-${uid}`;
  const emailId = `email-${uid}`;

  const form = useForm<PurchaseProductFormData>({
    mode: "onSubmit",
    resolver: zodResolver(schema),
    defaultValues: {
      email: "",
    },
  });

  const { isSubmitting } = form.formState;

  async function onSubmit(values: PurchaseProductFormData) {
    try {
      const result = await createPaymentProductAction({
        id,
        email: values.email,
      });

      if (result.ok && result.data) {
        window.location.assign(result.data.confirmationUrl);
        return;
      }

      toast.error("Произошла ошибка при формировании платежа!");
    } catch {
      toast.error("Не удалось соединиться с сервером.");
    }
  }
  const productSummary = (
    <div className="grid gap-3 rounded-lg border border-dashed bg-muted/50 p-4 text-sm">
      <div className="flex items-center justify-between">
        <div className="flex items-center gap-2 text-muted-foreground">
          <Receipt className="h-4 w-4" />
          <span>Стоимость файла</span>
        </div>
        <span className="text-base font-semibold">{amount} ₽</span>
      </div>

      <div className="flex items-center justify-between">
        <div className="flex items-center gap-2 text-muted-foreground">
          <FileText className="h-4 w-4" />
          <span>Формат</span>
        </div>
        <span className="rounded border bg-background px-2 py-0.5 text-xs font-medium">PDF</span>
      </div>
    </div>
  );

  const emailForm = (
    <form id={formId} onSubmit={form.handleSubmit(onSubmit)} className="grid gap-3" noValidate>
      <FieldGroup>
        <Controller
          name="email"
          control={form.control}
          render={({ field, fieldState }) => (
            <Field data-invalid={fieldState.invalid}>
              <FieldLabel htmlFor={emailId}>Адрес электронной почты</FieldLabel>
              <Input
                {...field}
                id={emailId}
                type="email"
                placeholder="example@mail.ru"
                autoComplete="email"
                disabled={isSubmitting}
                aria-invalid={fieldState.invalid}
                className="bg-background"
              />
              {fieldState.invalid && <FieldError errors={[fieldState.error]} />}
            </Field>
          )}
        />
      </FieldGroup>

      {form.formState.errors.root && (
        <div className="rounded-md bg-destructive/10 p-2 text-center text-sm font-medium text-destructive">
          {form.formState.errors.root.message}
        </div>
      )}
    </form>
  );

  const submitButton = (
    <Button type="submit" form={formId} disabled={isSubmitting} className="w-full cursor-pointer">
      {isSubmitting ? (
        <>
          <Loader2 className="mr-2 h-4 w-4 animate-spin" />
          Переход к оплате...
        </>
      ) : (
        `Купить за ${amount} ₽`
      )}
    </Button>
  );

  if (variant === "dialog") {
    return (
      <Dialog open={open} onOpenChange={setOpen}>
        <DialogTrigger render={<Button />}>
          <Download className="h-4 w-4" /> Купить файл с ответами
        </DialogTrigger>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Купить ответы на {name}</DialogTitle>
            <DialogDescription>
              Вы будете перенаправлены на сайт платежной системы. После оплаты файл с ответами
              придет вам на электронную почту.
            </DialogDescription>
          </DialogHeader>

          {productSummary}
          <div className="py-2">{emailForm}</div>

          <DialogFooter>{submitButton}</DialogFooter>
        </DialogContent>
      </Dialog>
    );
  }

  return (
    <Card className="flex h-full flex-col border-dashed bg-muted/40 shadow-none transition-colors hover:border-primary/40">
      <CardHeader className="pb-3">
        <CardTitle className="text-lg leading-snug">{name}</CardTitle>
      </CardHeader>

      <CardContent className="flex flex-1 flex-col gap-4">
        {productSummary}
        <div className="mt-auto">{emailForm}</div>
      </CardContent>

      <CardFooter className="pt-2">{submitButton}</CardFooter>
    </Card>
  );
}
