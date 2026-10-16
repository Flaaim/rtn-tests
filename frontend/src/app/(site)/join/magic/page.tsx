"use client";

import { useEffect, useState, use } from "react";
import { useRouter } from "next/navigation";
import { confirmMagicLinkAction } from "@/actions/auth";
import { Loader2, AlertCircle } from "lucide-react";
import { Button } from "@/components/ui/button";
import Link from "next/link";

interface PageProps {
  searchParams: Promise<{ [key: string]: string | string[] | undefined }>;
}

export default function MagicLinkConfirmPage({ searchParams }: PageProps) {
  const router = useRouter();
  const [apiError, setApiError] = useState<string | null>(null);

  const params = use(searchParams);
  const token = typeof params.token === "string" ? params.token : null;

  useEffect(() => {
    let isMounted = true;

    if (token) {
      const confirmToken = async () => {
        const result = await confirmMagicLinkAction(token);

        if (!isMounted) return;

        if (result.ok) {
          router.push("/user/dashboard");
        } else {
          setApiError(result.error || "Ссылка недействительна или устарела.");
        }
      };

      void confirmToken();
    }

    return () => {
      isMounted = false;
    };
  }, [token, router]);

  const displayError = !token ? "Токен не найден в ссылке." : apiError;

  if (displayError) {
    return (
      <div className="flex flex-col items-center justify-center min-h-[60vh] px-4 text-center">
        <AlertCircle className="w-12 h-12 text-destructive mb-4" />
        <h1 className="text-2xl font-bold mb-2">Ошибка авторизации</h1>
        <p className="text-muted-foreground mb-6 max-w-md">{displayError}</p>
        <Button>
          <Link href="/join/login">Вернуться к авторизации</Link>
        </Button>
      </div>
    );
  }

  return (
    <div className="flex flex-col items-center justify-center min-h-[60vh] px-4 text-center">
      <Loader2 className="w-12 h-12 text-primary animate-spin mb-4" />
      <h1 className="text-2xl font-bold mb-2">Авторизация...</h1>
      <p className="text-muted-foreground">
        Пожалуйста, подождите. Мы проверяем вашу волшебную ссылку.
      </p>
    </div>
  );
}
