"use client";

import React, { useState } from "react";
import { z } from "zod";
import { Controller, useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { Button } from "@/components/ui/button";
import { KeyRound } from "lucide-react";
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from "@/components/ui/dialog";
import { Field, FieldError, FieldGroup, FieldLabel } from "@/components/ui/field";
import { Input } from "@/components/ui/input";
import { forceChangeUserPassword } from "@/actions/auth";
import { toast } from "sonner";
import { useRouter } from "next/navigation";

const schema = z.object({
  password: z
    .string()
    .min(8, "Пароль должен содержать минимум 8 символов.")
    .max(18, "Пароль должен содержать максимум 18 символов."),
});

interface ForceChangeUserPasswordProps {
  userId: string;
}

type ForceChangePasswordPayload = z.infer<typeof schema>;

export default function ForceChangeUserPassword({ userId }: ForceChangeUserPasswordProps) {
  const [open, setOpen] = useState(false);

  const router = useRouter();

  const form = useForm({
    mode: "onSubmit",
    resolver: zodResolver(schema),
    defaultValues: {
      password: "",
    },
  });

  async function onSubmit(values: ForceChangePasswordPayload) {
    const result = await forceChangeUserPassword({
      userId: userId,
      password: values.password,
    });

    if (!result.ok) {
      form.setError("root", { type: "server", message: result.error });
      return;
    }

    toast.success("Пароль успешно изменен!");
    form.reset();
    setOpen(false);
    router.refresh();
  }
  const submitButton = (
    <Button
      type="submit"
      form="change-password-form"
      disabled={form.formState.isSubmitting}
      className="w-full cursor-pointer py-2"
    >
      {form.formState.isSubmitting ? "Загрузка..." : "Изменить"}
    </Button>
  );

  return (
    <Dialog open={open} onOpenChange={setOpen}>
      <DialogTrigger render={<Button />}>
        <KeyRound className="mr-2 h-4 w-4" /> Изменить
      </DialogTrigger>
      <DialogContent className="sm:max-w-2xl">
        <DialogHeader>
          <DialogTitle>Смена пароля</DialogTitle>
          <DialogDescription>
            Задайте новый пароль для этого пользователя. Предыдущий пароль перестанет работать
            немедленно.
          </DialogDescription>
        </DialogHeader>
        <form
          id="change-password-form"
          onSubmit={(e) => {
            void form.handleSubmit(onSubmit)(e);
          }}
          action=""
          method="POST"
          className="grid gap-4 py-4"
        >
          <FieldGroup>
            <Controller
              name="password"
              control={form.control}
              render={({ field, fieldState }) => (
                <Field data-invalid={fieldState.invalid}>
                  <FieldLabel htmlFor="password">Новый пароль</FieldLabel>
                  <Input
                    {...field}
                    id="password"
                    type="password"
                    value={field.value}
                    placeholder="Укажите новый пароль"
                    aria-invalid={fieldState.invalid}
                    autoComplete="password"
                  />
                  {fieldState.invalid && <FieldError errors={[fieldState.error]} />}
                </Field>
              )}
            />
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
