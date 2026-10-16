"use client";

import { z } from "zod";
import { Controller, useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import {
  Card,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from "@/components/ui/card";
import { Field, FieldError, FieldGroup, FieldLabel } from "@/components/ui/field";
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";
import Link from "next/link";
import { LoginAction } from "@/actions/auth";
import { useRouter } from "next/navigation";

const schema = z.object({
  email: z.email("Пожалуйста, введите корректный email адрес."),
  password: z
    .string()
    .min(8, "Пароль должен содержать минимум 8 символов.")
    .max(18, "Максимум 18 символов."),
});

type FormData = z.infer<typeof schema>;

export default function Login() {
  const router = useRouter();

  const form = useForm({
    mode: "onSubmit",
    resolver: zodResolver(schema),
    defaultValues: { email: "", password: "" },
  });

  const getYandexAuthUrl = () => {
    const rootUrl = "https://oauth.yandex.ru/authorize";
    const qs = new URLSearchParams({
      response_type: "code",
      client_id: process.env.NEXT_PUBLIC_YANDEX_CLIENT_ID || "",
      redirect_uri: process.env.NEXT_PUBLIC_YANDEX_REDIRECT_URI || "",
    });
    return `${rootUrl}?${qs.toString()}`;
  };

  async function onSubmit(values: FormData) {
    const result = await LoginAction(values);

    if (!result.ok) {
      form.setError("root", { type: "server", message: result.error || "Ошибка авторизации" });
      return;
    }
    router.push("/user/dashboard");
  }

  return (
    <div className="flex min-h-screen items-center justify-center p-4">
      <Card className="mx-auto w-full max-w-md shadow-sm">
        <CardHeader className="space-y-2 text-center">
          <CardTitle className="text-2xl font-semibold tracking-tight">Вход в систему</CardTitle>
          <CardDescription>Войдите в свой аккаунт, чтобы продолжить работу</CardDescription>
        </CardHeader>
        <CardContent>
          <form id="login-form" onSubmit={form.handleSubmit(onSubmit)} className="space-y-4">
            <FieldGroup className="space-y-3">
              <Controller
                name="email"
                control={form.control}
                render={({ field, fieldState }) => (
                  <Field data-invalid={fieldState.invalid}>
                    <FieldLabel htmlFor="login-email">Электронная почта</FieldLabel>
                    <Input
                      {...field}
                      id="login-email"
                      placeholder="name@example.com"
                      autoComplete="email"
                    />
                    {fieldState.invalid && <FieldError errors={[fieldState.error]} />}
                  </Field>
                )}
              />
              <Controller
                name="password"
                control={form.control}
                render={({ field, fieldState }) => (
                  <Field data-invalid={fieldState.invalid}>
                    <div className="flex items-center justify-between">
                      <FieldLabel htmlFor="login-password">Пароль</FieldLabel>
                      <Link
                        href="/join/reset/request"
                        className="text-xs text-primary hover:underline font-medium"
                      >
                        Забыли пароль?
                      </Link>
                    </div>
                    <Input
                      {...field}
                      id="login-password"
                      type="password"
                      placeholder="Пароль"
                      autoComplete="current-password"
                    />
                    {fieldState.invalid && <FieldError errors={[fieldState.error]} />}
                  </Field>
                )}
              />
            </FieldGroup>
          </form>
        </CardContent>
        <CardFooter className="flex flex-col space-y-4">
          <div className="w-full">
            {form.formState.errors.root && (
              <div className="mb-4 text-destructive bg-destructive/10 rounded-md p-2 text-center text-sm font-medium">
                {form.formState.errors.root.message}
              </div>
            )}
            <Button
              type="submit"
              form="login-form"
              disabled={form.formState.isSubmitting}
              className="w-full"
            >
              {form.formState.isSubmitting ? "Вход..." : "Войти"}
            </Button>
          </div>

          <div className="relative w-full">
            <div className="absolute inset-0 flex items-center">
              <span className="w-full border-t border-muted-foreground/20" />
            </div>
            <div className="relative flex justify-center text-xs uppercase">
              <span className="bg-card px-2 text-muted-foreground">Или продолжите через</span>
            </div>
          </div>

          <Button variant="outline" className="w-full">
            <Link href={getYandexAuthUrl()}>Яндекс ID</Link>
          </Button>

          <div className="w-full border-t pt-4 mt-2 text-center text-sm text-muted-foreground">
            Нет аккаунта?{" "}
            <Link className="text-primary hover:underline font-medium" href="/join/register">
              Зарегистрироваться
            </Link>
          </div>
        </CardFooter>
      </Card>
    </div>
  );
}
