"use client";

import { z } from "zod";
import { useState } from "react";
import { Controller, useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { requestMagicLinkAction } from "@/actions/auth";
import { MailCheck } from "lucide-react";
import { Button } from "@/components/ui/button";
import { Field, FieldError, FieldGroup, FieldLabel } from "@/components/ui/field";
import { Input } from "@/components/ui/input";

const schema = z.object({
  email: z.email("Пожалуйста, введите корректный email адрес."),
});

type FormData = z.infer<typeof schema>;

export default function MagicLinkForm() {
  const [isSuccess, setIsSuccess] = useState<boolean>(false);

  const form = useForm({
    mode: "onSubmit",
    resolver: zodResolver(schema),
    defaultValues: { email: "" },
  });

  async function onSubmit(values: FormData) {
    const result = await requestMagicLinkAction(values);
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
        <h3 className="text-xl font-semibold tracking-tight mb-2">Проверьте почту</h3>
        <p className="text-muted-foreground text-sm">
          Мы отправили волшебную ссылку на <br />
          <strong className="text-foreground">{form.getValues("email")}</strong>
        </p>
      </div>
    );
  }

  return (
    <form id="magic-link-form" onSubmit={form.handleSubmit(onSubmit)} className="space-y-4">
      <FieldGroup>
        <Controller
          name="email"
          control={form.control}
          render={({ field, fieldState }) => (
            <Field data-invalid={fieldState.invalid}>
              <FieldLabel htmlFor="magic-email">Электронная почта</FieldLabel>
              <Input
                {...field}
                id="magic-email"
                placeholder="name@example.com"
                aria-invalid={fieldState.invalid}
                autoComplete="email"
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

      <Button
        type="submit"
        disabled={form.formState.isSubmitting}
        className="w-full cursor-pointer py-2 mt-2"
      >
        {form.formState.isSubmitting ? "Отправка..." : "Получить ссылку"}
      </Button>
    </form>
  );
}
