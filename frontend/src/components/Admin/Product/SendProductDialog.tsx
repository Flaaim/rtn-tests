"use client";

import { z } from "zod";
import React, { useState } from "react";
import { useRouter } from "next/navigation";
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
import { Mail } from "lucide-react";
import { Field, FieldError, FieldGroup, FieldLabel } from "@/components/ui/field";
import { Controller, useForm } from "react-hook-form";
import { Input } from "@/components/ui/input";
import { sendProductAction } from "@/actions/product";
import { toast } from "sonner";
import { zodResolver } from "@hookform/resolvers/zod";

const schema = z.object({
  email: z.email("Пожалуйста, введите корректный email адрес."),
});

type SendProductFormData = z.infer<typeof schema>;

interface SendProductDialogsProps {
  id: string;
}

export default function SendProductDialog({ id }: SendProductDialogsProps) {
  const [open, setOpen] = useState<boolean>(false);

  const router = useRouter();
  async function onSubmit(values: SendProductFormData) {
    const result = await sendProductAction({
      id: id,
      email: values.email,
    });

    if (!result.ok) {
      form.setError("root", { type: "server", message: result.error });
      return;
    }

    toast.success("Файл отправлен!");
    form.reset(values);
    setOpen(false);
    router.refresh();
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
      form="send-product-form"
      disabled={form.formState.isSubmitting}
      className="w-full cursor-pointer py-2"
    >
      {form.formState.isSubmitting ? "Загрузка..." : "Отправить"}
    </Button>
  );

  return (
    <Dialog open={open} onOpenChange={setOpen}>
      <DialogTrigger render={<Button />}>
        <Mail /> Отправить
      </DialogTrigger>
      <DialogContent>
        <DialogHeader>
          <DialogTitle>Отправить файл</DialogTitle>
          <DialogDescription>Отправить PDF файл на почту</DialogDescription>
        </DialogHeader>
        <form
          id="send-product-form"
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
