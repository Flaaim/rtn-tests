"use client";

import { useState, useTransition } from "react";
import { toast } from "sonner";
import { Switch } from "@/components/ui/switch";
import { updateStatusAction } from "@/actions/test";
import { TableCell } from "@/components/ui/table";
import TestStatusBadge from "@/components/Admin/Test/Status/TestStatusBadge";

const STATUS_CONFIG: Record<string, boolean> = {
  active: true,
  inactive: false,
};
interface SwitchStatusProps {
  id: string;
  initialStatus: string;
}

export default function TestStatusControl({ id, initialStatus }: SwitchStatusProps) {
  const [isPending, startTransition] = useTransition();

  const [currentStatus, setCurrentStatus] = useState<string>(initialStatus);
  const isChecked = STATUS_CONFIG[currentStatus] ?? false;

  const handleToggle = (nextChecked: boolean) => {
    const nextStatus = nextChecked ? "active" : "inactive";
    setCurrentStatus(nextStatus);

    startTransition(async () => {
      const result = await updateStatusAction(id, nextChecked);

      if (!result.ok) {
        toast.error("Ошибка при обновлении статуса");
        setCurrentStatus(initialStatus);
      }
    });
  };

  return (
    <>
      <TableCell className="font-medium">
        <TestStatusBadge status={currentStatus} />
      </TableCell>
      <TableCell>
        <div className="flex items-center gap-2">
          <Switch
            id={`switch-${id}`}
            checked={isChecked}
            disabled={isPending}
            onCheckedChange={handleToggle}
          />
          {isPending && (
            <span className="text-sm text-muted-foreground animate-pulse">Обновление...</span>
          )}
        </div>
      </TableCell>
    </>
  );
}
