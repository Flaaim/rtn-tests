"use client";

import React, { useState } from "react";
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
  AlertDialogTrigger,
} from "@/components/ui/alert-dialog";
import { Button } from "@/components/ui/button";
import { Trash } from "lucide-react";
import { toast } from "sonner";
import { removeProfileAction } from "@/actions/profile";

interface RemoveProfileDialogProps {
  id: string;
  onHide: () => void;
  onRevert: () => void;
}

export default function RemoveProfileDialog({ id, onHide, onRevert }: RemoveProfileDialogProps) {
  const [open, setOpen] = useState<boolean>(false);

  const onSubmit = async (e: React.MouseEvent) => {
    e.preventDefault();
    setOpen(false);
    onHide();
    try {
      const result = await removeProfileAction(id);
      if (!result.ok) {
        toast.error(result.error ?? "Не удалось удалить данные.");
        onRevert();
        return;
      }
      toast.success("Профиль успешно удален!");
    } catch (error) {
      console.error("Error:", error);
      toast.error("Произошла системная ошибка.");
      onRevert();
    }
  };

  return (
    <AlertDialog open={open} onOpenChange={setOpen}>
      <AlertDialogTrigger render={<Button />}>
        <Trash className="h-4 w-4" />
      </AlertDialogTrigger>
      <AlertDialogContent>
        <AlertDialogHeader>
          <AlertDialogTitle>Удалить?</AlertDialogTitle>
          <AlertDialogDescription>
            Данное действие полностью удалит пользователя. Это действие нельзя отменить.
          </AlertDialogDescription>
        </AlertDialogHeader>
        <AlertDialogFooter>
          <AlertDialogCancel>Cancel</AlertDialogCancel>
          <AlertDialogAction onClick={onSubmit} className="bg-red-600 hover:bg-red-700 text-white">
            Удалить
          </AlertDialogAction>
        </AlertDialogFooter>
      </AlertDialogContent>
    </AlertDialog>
  );
}
