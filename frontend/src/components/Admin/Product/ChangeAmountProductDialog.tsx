"use client";

import { z } from "zod";
import React, { useState } from "react";
import { useRouter } from "next/navigation";
import { toast } from "sonner";
import { Controller, useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { Button } from "@/components/ui/button";
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from "@/components/ui/dialog";
import { Pencil } from "lucide-react";
import { Field, FieldError, FieldGroup, FieldLabel } from "@/components/ui/field";
import { Input } from "@/components/ui/input";
import { changeAmountProductAction } from "@/actions/product";

const schema = z.object({
  amount: z.string().regex(/^\d+\.\d{2}$/, {
    message: "Сумма должна быть в формате 0.00 (две цифры после точки)",
  }),
});

type ChangeAmountProductFormData = z.infer<typeof schema>;

interface ChangeAmountProductDialogProps {
  id: string;
  amount: string;
}

export default function ChangeAmountProductDialog({ id, amount }: ChangeAmountProductDialogProps) {
  const [open, setOpen] = useState<boolean>(false);

  const router = useRouter();

  async function onSubmit(values: ChangeAmountProductFormData) {
    const result = await changeAmountProductAction({
      id: id,
      amount: values.amount,
    });

    if (!result.ok) {
      form.setError("root", { type: "server", message: result.error });
      return;
    }

    toast.success("Стоимость файла успешно обновлена!");
    form.reset(values);
    setOpen(false);
    router.refresh();
  }
  const form = useForm({
    mode: "onSubmit",
    resolver: zodResolver(schema),
    defaultValues: {
      amount: amount,
    },
  });

  const submitButton = (
    <Button
      type="submit"
      form="change-amount-product-form"
      disabled={form.formState.isSubmitting}
      className="w-full cursor-pointer py-2"
    >
      {form.formState.isSubmitting ? "Загрузка..." : "Изменить"}
    </Button>
  );

  return (
    <Dialog open={open} onOpenChange={setOpen}>
      <DialogTrigger render={<Button />}>
        <Pencil />
      </DialogTrigger>
      <DialogContent>
        <DialogHeader>
          <DialogTitle>Изменить стоимость товара</DialogTitle>
          <DialogDescription>Изменение стоимости PDF файла</DialogDescription>
        </DialogHeader>
        <form
          id="change-amount-product-form"
          onSubmit={(e) => {
            void form.handleSubmit(onSubmit)(e);
          }}
          action=""
          method="PUT"
          className="grid gap-4 py-4"
        >
          <FieldGroup>
            <Controller
              name="amount"
              control={form.control}
              render={({ field, fieldState }) => (
                <Field data-invalid={fieldState.invalid}>
                  <FieldLabel htmlFor="amount">Стоимость курса</FieldLabel>
                  <Input
                    {...field}
                    id="amount"
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
