"use client";

import React, { useState } from "react";
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from "@/components/ui/dialog";
import { Button } from "@/components/ui/button";
import { Download, FileText, Receipt } from "lucide-react";
import { Field, FieldError, FieldGroup, FieldLabel } from "@/components/ui/field";
import { Controller, useForm } from "react-hook-form";
import { Input } from "@/components/ui/input";
import { zodResolver } from "@hookform/resolvers/zod";
import { z } from "zod";
import { Card, CardContent } from "@/components/ui/card";
import { createPaymentProductAction } from "@/actions/product";
import { toast } from "sonner";

const schema = z.object({
  email: z.email("Пожалуйста, введите корректный email адрес."),
});

type PurchaseProductFormData = z.infer<typeof schema>;

interface PurchaseProductDialogProps {
  id: string;
  name: string;
  amount: number;
}

export default function PurchaseProductDialog({ id, name, amount }: PurchaseProductDialogProps) {
  const [open, setOpen] = useState<boolean>(false);
  const [loading, setIsLoading] = useState<boolean>(false);

  async function onSubmit(values: PurchaseProductFormData) {
    const result = await createPaymentProductAction({
      id: id,
      email: values.email,
    });

    if (result.ok && result.data) {
      window.location.href = result.data.confirmationUrl;
      return;
    }

    toast.error("Произошла ошибка при формировании платежа!");
    setIsLoading(false);
    setOpen(false);
  }
  const form = useForm({
    mode: "onSubmit",
    resolver: zodResolver(schema),
    defaultValues: {
      email: "",
    },
  });

  const submitButton = (
    <Button
      type="submit"
      form="purchase-product-form"
      disabled={form.formState.isSubmitting}
      className="w-full cursor-pointer py-2"
    >
      {loading ? "Загрузка..." : "Купить"}
    </Button>
  );

  return (
    <Dialog open={open} onOpenChange={setOpen}>
      <DialogTrigger render={<Button />}>
        <Download className="w-4 h-4" /> Купить файл с ответами
      </DialogTrigger>
      <DialogContent>
        <DialogHeader>
          <DialogTitle>Купить ответы на {name}</DialogTitle>
          <DialogDescription>
            Вы будете перенаправлены на сайт платежной системы. После оплаты файл ответами придет
            вам на электронную почту
          </DialogDescription>
        </DialogHeader>
        <Card className="bg-muted/50 border-dashed shadow-none">
          <CardContent className="p-4 grid gap-3 text-sm">
            <div className="flex items-center justify-between">
              <div className="flex items-center gap-2 text-muted-foreground">
                <Receipt className="w-4 h-4" />
                <span>Стоимость файла</span>
              </div>
              <span className="font-semibold text-base">{amount} ₽</span>
            </div>

            <div className="flex items-center justify-between">
              <div className="flex items-center gap-2 text-muted-foreground">
                <FileText className="w-4 h-4" />
                <span>Формат</span>
              </div>
              <span className="font-medium bg-background px-2 py-0.5 rounded border">PDF</span>
            </div>
          </CardContent>
        </Card>
        <form
          id="purchase-product-form"
          onSubmit={(e) => {
            void form.handleSubmit(onSubmit)(e);
          }}
          action=""
          method="POST"
          className="grid gap-4 py-4"
        >
          <FieldGroup>
            <Controller
              name="email"
              control={form.control}
              render={({ field, fieldState }) => (
                <Field data-invalid={fieldState.invalid}>
                  <FieldLabel htmlFor="email">Адрес электронной почты</FieldLabel>
                  <Input
                    {...field}
                    id="email"
                    value={field.value}
                    placeholder=""
                    aria-invalid={fieldState.invalid}
                  />
                  {fieldState.invalid && <FieldError errors={[fieldState.error]} />}
                </Field>
              )}
            ></Controller>
          </FieldGroup>
        </form>
        {form.formState.errors.root && (
          <div className="rounded-md bg-destructive/10 p-2 text-center text-sm font-medium text-destructive">
            {form.formState.errors.root.message}
          </div>
        )}
        <DialogFooter>{submitButton}</DialogFooter>
      </DialogContent>
    </Dialog>
  );
}
