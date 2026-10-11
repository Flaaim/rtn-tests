"use client";

import { useState } from "react";
import { useRouter, usePathname } from "next/navigation";
import { launchAttemptAction } from "@/actions/attempt";
import { Loader2, PlayCircle } from "lucide-react";
import { Button } from "@/components/ui/button";
import { toast } from "sonner";

interface PublicTicketButtonProps {
  testId: string;
  ticketNumber: number;
  isAuthenticated: boolean;
}

export default function PublicTicketButton({
  testId,
  ticketNumber,
  isAuthenticated,
}: PublicTicketButtonProps) {
  const [loading, setLoading] = useState<boolean>(false);
  const router = useRouter();
  const pathname = usePathname();

  const handleLaunch = async () => {
    setLoading(true);

    const result = await launchAttemptAction({ testId, ticketNumber });

    if (!result.ok || !result.data) {
      setLoading(false);
      const errorMessage = result.error ?? "Не удалось запустить тест.";

      if (errorMessage.includes("Демо-доступ исчерпан")) {
        toast.error("Демо-доступ исчерпан", {
          description:
            "Чтобы продолжить прохождение и сохранить прогресс, пожалуйста, зарегистрируйтесь.",
        });
        router.push(`/join/register?callbackUrl=${encodeURIComponent(pathname)}`);
        return;
      }

      setLoading(false);
      return;
    }

    router.push(`/attempts/${result.data.attemptId}`);
  };

  return (
    <Button
      onClick={handleLaunch}
      disabled={loading}
      variant={isAuthenticated ? "default" : "secondary"}
      className="group relative flex h-9 w-9 items-center justify-center rounded-full p-0 transition-colors cursor-pointer"
    >
      {loading ? <Loader2 className="h-4 w-4 animate-spin" /> : <PlayCircle className="h-4 w-4" />}

      {/* Кастомная всплывающая подсказка */}
      <span className="absolute -top-10 right-0 z-50 w-max scale-0 rounded-md bg-foreground px-3 py-1.5 text-xs font-medium text-background opacity-0 shadow-md transition-all duration-200 group-hover:scale-100 group-hover:opacity-100">
        {isAuthenticated ? "Начать тестирование" : "Демо-режим"}
      </span>
    </Button>
  );
}
