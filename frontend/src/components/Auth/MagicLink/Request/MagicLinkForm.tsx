"use client";

import { z } from "zod";
import { useState } from "react";
import { Controller, useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { requestMagicLinkAction } from "@/actions/auth";
import {
  Card,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from "@/components/ui/card";
import { MailCheck } from "lucide-react";
import { Button } from "@/components/ui/button";
import Link from "next/link";
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
    defaultValues: {
      email: "",
    },
  });

  async function onSubmit(values: FormData) {
    const result = await requestMagicLinkAction(values);

    if (!result.ok || !result.data) {
      form.setError("root", { type: "server", message: result.error });
      return;
    }
    setIsSuccess(true);
  }

  if (isSuccess) {
    return (
      <Card className="mx-auto w-full max-w-md py-6 text-center shadow-sm">
        <CardHeader className="space-y-4">
          <div className="mx-auto w-fit rounded-full bg-green-100 p-4">
            <MailCheck className="h-10 w-10 text-green-600" />
          </div>
          <CardTitle className="text-2xl font-semibold tracking-tight">
            <h1>Проверьте вашу почту</h1>
          </CardTitle>
          <CardDescription className="text-base">
            Мы отправили письмо со ссылкой для входа на сайт.
            <br />
            <strong className="text-foreground">{form.getValues("email")}</strong>.
          </CardDescription>
        </CardHeader>
        <CardContent>
          <p className="text-muted-foreground text-sm">
            Пожалуйста, перейдите по ссылке в письме, чтобы завершить активировать аккаунт.
          </p>
        </CardContent>
        <CardFooter>
          <Button variant="link" className="w-full">
            <Link className="link" href="/join/login">
              Вернуться на страницу входа
            </Link>
          </Button>
        </CardFooter>
      </Card>
    );
  }

  return (
    <div className="flex h-screen items-center justify-center">
      <Card className="mx-auto w-full max-w-md shadow-sm">
        <CardHeader className="space-y-2 text-center">
          <CardTitle className="text-2xl font-semibold tracking-tight">Вход в 1 клик</CardTitle>
          <CardDescription>
            Зарегистрируйтесь, чтобы получить полный доступ к функциям системы и управлять вашими
            данными.
          </CardDescription>
        </CardHeader>
        <CardContent>
          <form id="magic-link-form" onSubmit={form.handleSubmit(onSubmit)} method="POST">
            <FieldGroup>
              <Controller
                name="email"
                control={form.control}
                render={({ field, fieldState }) => (
                  <Field data-invalid={fieldState.invalid}>
                    <FieldLabel htmlFor="email">Электронная почта</FieldLabel>
                    <Input
                      {...field}
                      id="email"
                      value={field.value}
                      placeholder="Email"
                      aria-invalid={fieldState.invalid}
                      autoComplete="email"
                    />
                    {fieldState.invalid && <FieldError errors={[fieldState.error]} />}
                  </Field>
                )}
              />
            </FieldGroup>
          </form>
        </CardContent>
        <CardFooter>
          <div className="flex flex-col">
            <div className="space-y-4 pt-4">
              {form.formState.errors.root && (
                <div className="text-destructive bg-destructive/10 rounded-md p-2 text-center text-sm font-medium">
                  {form.formState.errors.root.message}
                </div>
              )}
              <Button
                type="submit"
                form="magic-link-form"
                disabled={form.formState.isSubmitting}
                className="cursor-pointer py-2"
              >
                {form.formState.isSubmitting ? "Загрузка..." : "Присоединиться"}
              </Button>
            </div>

            <div className="space-y-4 pt-4">
              Есть аккаунт?{" "}
              <Link className="link" href="/join/login">
                Войти в систему
              </Link>
            </div>
          </div>
        </CardFooter>
      </Card>
    </div>
  );
}
