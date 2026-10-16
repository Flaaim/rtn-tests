"use client";

import { z } from "zod";
import { useState } from "react";
import { Controller, useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { JoinAction } from "@/actions/auth";
import { MailCheck } from "lucide-react";
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";
import { Field, FieldError, FieldGroup, FieldLabel } from "@/components/ui/field";

const schema = z
  .object({
    email: z.email("Пожалуйста, введите корректный email адрес."),
    password: z.string().min(8, "Минимум 8 символов.").max(18, "Максимум 18 символов."),
    confirm_password: z.string(),
  })
  .superRefine(({ confirm_password, password }, ctx) => {
    if (confirm_password !== password) {
      ctx.addIssue({
        code: "custom",
        message: "Пароли не совпадают",
        path: ["confirm_password"],
      });
    }
  });

type FormData = z.infer<typeof schema>;

export default function JoinByEmail() {
  const [isSuccess, setIsSuccess] = useState<boolean>(false);

  const form = useForm({
    mode: "onBlur",
    resolver: zodResolver(schema),
    defaultValues: { email: "", password: "", confirm_password: "" },
  });

  async function onSubmit(values: FormData) {
    const result = await JoinAction(values);

    if (!result.ok) {
      form.setError("root", { type: "server", message: result.error || "Произошла ошибка" });
      return;
    }
    setIsSuccess(true);
  }

  if (isSuccess) {
    return (
      <div className="py-6 text-center animate-in fade-in zoom-in duration-300">
        <div className="mx-auto mb-4 w-fit rounded-full bg-green-100 p-4">
          <MailCheck className="h-10 w-10 text-green-600" />
        </div>
        <h3 className="text-xl font-semibold tracking-tight mb-2">Аккаунт создан</h3>
        <p className="text-muted-foreground text-sm">
          Письмо для подтверждения отправлено на <br />
          <strong className="text-foreground">{form.getValues("email")}</strong>
        </p>
      </div>
    );
  }

  return (
    <form id="join-form" onSubmit={form.handleSubmit(onSubmit)} className="space-y-4">
      <FieldGroup className="space-y-3">
        <Controller
          name="email"
          control={form.control}
          render={({ field, fieldState }) => (
            <Field data-invalid={fieldState.invalid}>
              <FieldLabel htmlFor="join-email">Электронная почта</FieldLabel>
              <Input {...field} id="join-email" placeholder="Email" autoComplete="email" />
              {fieldState.invalid && <FieldError errors={[fieldState.error]} />}
            </Field>
          )}
        />
        <Controller
          name="password"
          control={form.control}
          render={({ field, fieldState }) => (
            <Field data-invalid={fieldState.invalid}>
              <FieldLabel htmlFor="join-password">Пароль</FieldLabel>
              <Input {...field} id="join-password" type="password" placeholder="Укажите пароль" />
              {fieldState.invalid && <FieldError errors={[fieldState.error]} />}
            </Field>
          )}
        />
        <Controller
          name="confirm_password"
          control={form.control}
          render={({ field, fieldState }) => (
            <Field data-invalid={fieldState.invalid}>
              <FieldLabel htmlFor="join-confirm-password">Подтвердите пароль</FieldLabel>
              <Input
                {...field}
                id="join-confirm-password"
                type="password"
                placeholder="Повторите пароль"
              />
              {fieldState.invalid && <FieldError errors={[fieldState.error]} />}
            </Field>
          )}
        />
      </FieldGroup>

      {form.formState.errors.root && (
        <div className="text-destructive bg-destructive/10 rounded-md p-2 text-center text-sm font-medium">
          {form.formState.errors.root.message}
        </div>
      )}

      <Button type="submit" disabled={form.formState.isSubmitting} className="w-full mt-2">
        {form.formState.isSubmitting ? "Загрузка..." : "Зарегистрироваться"}
      </Button>
    </form>
  );
}
