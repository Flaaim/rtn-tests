"use client";

import { useState, useTransition } from "react";
import { toast } from "sonner";
import { Switch } from "@/components/ui/switch";
import { updateProtectedAction } from "@/actions/course";
import { Label } from "@/components/ui/label";
import { Loader2, ShieldCheck, ShieldOff } from "lucide-react";

interface SwitchProtected {
  id: string;
  initialProtected: boolean;
}

export default function CourseProtectedControl({ id, initialProtected }: SwitchProtected) {
  const [isPending, startTransition] = useTransition();
  const [isProtected, setIsProtected] = useState<boolean>(initialProtected);

  const handleToggle = (nextChecked: boolean) => {
    const previousState = isProtected;
    setIsProtected(nextChecked);

    startTransition(async () => {
      const result = await updateProtectedAction(id, nextChecked);

      if (!result.ok) {
        toast.error("Ошибка при обновлении статуса защиты");
        setIsProtected(previousState);
      }
    });
  };

  const switchId = `switch-protected-${id}`;
  return (
    <div className="flex items-center justify-between rounded-lg border bg-muted/30 px-4 py-3">
      <div className="flex items-start gap-3">
        <div className="mt-0.5 text-muted-foreground">
          {isProtected ? (
            <ShieldCheck className="h-4 w-4 text-emerald-600 dark:text-emerald-500" />
          ) : (
            <ShieldOff className="h-4 w-4" />
          )}
        </div>
        <div className="space-y-0.5">
          <Label htmlFor={switchId} className="text-sm font-medium cursor-pointer">
            Защита правильных ответов
          </Label>
          <p className="text-xs text-muted-foreground">
            Запрещает пользователям публично смотреть правильные ответы в курсе.
          </p>
        </div>
      </div>

      <div className="flex items-center gap-2.5">
        {isPending && <Loader2 className="h-4 w-4 animate-spin text-muted-foreground" />}
        <Switch
          id={switchId}
          checked={isProtected}
          disabled={isPending}
          onCheckedChange={handleToggle}
        />
      </div>
    </div>
  );
}
