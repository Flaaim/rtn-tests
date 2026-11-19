"use client";

import { useState, useTransition } from "react";
import { toast } from "sonner";
import { Switch } from "@/components/ui/switch";

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

  return (
    <div className="flex items-center gap-2">
      <Switch
        id={`switch-protected-${id}`}
        checked={isProtected}
        disabled={isPending}
        onCheckedChange={handleToggle}
      />
      {isPending && (
        <span className="text-sm text-muted-foreground animate-pulse">Обновление...</span>
      )}
    </div>
  );
}
