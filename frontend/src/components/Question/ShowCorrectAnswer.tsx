"use client";

import { PublicAnswerData } from "@/interfaces/question.interface";
import { CheckCircle2, Circle } from "lucide-react";
import React, { useState } from "react";
import { Button } from "@/components/ui/button";
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from "@/components/ui/dialog";
import MagicLinkForm from "@/components/Auth/MagicLink/Request/MagicLinkForm";

interface ShowCorrectAnswerProps {
  question: {
    answers: PublicAnswerData[];
  };
  isAuthenticated: boolean;
}

export default function ShowCorrectAnswer({ question, isAuthenticated }: ShowCorrectAnswerProps) {
  const [showCorrect, setShowCorrect] = useState<boolean>(false);
  const [open, setOpen] = useState<boolean>(false);

  return (
    <>
      {question.answers.map((answer: PublicAnswerData, index) => {
        const isHighlighted = showCorrect && answer.isCorrect;

        return (
          <div
            key={answer.id ?? index}
            className={`flex items-start gap-3 p-4 rounded-lg border transition-colors ${
              isHighlighted
                ? "bg-emerald-50/70 border-emerald-300 dark:bg-emerald-950/30 dark:border-emerald-800"
                : "bg-card border-border/60 text-muted-foreground"
            }`}
          >
            {isHighlighted ? (
              <CheckCircle2 className="h-5 w-5 text-emerald-600 dark:text-emerald-400 shrink-0 mt-0.5" />
            ) : (
              <Circle className="h-5 w-5 text-muted-foreground/40 shrink-0 mt-0.5" />
            )}
            <div className="space-y-1">
              <span
                className={
                  isHighlighted
                    ? "font-medium text-emerald-950 dark:text-emerald-100"
                    : "text-foreground/80"
                }
              >
                {answer.text}
              </span>
              {isHighlighted && (
                <span className="block text-xs font-semibold text-emerald-600 dark:text-emerald-400">
                  ✓ Правильный ответ
                </span>
              )}
            </div>
          </div>
        );
      })}
      {!showCorrect &&
        (isAuthenticated ? (
          <Button onClick={() => setShowCorrect(true)}>Показать правильный ответ</Button>
        ) : (
          <Dialog open={open} onOpenChange={setOpen}>
            <DialogTrigger>
              <Button onClick={() => setShowCorrect(true)}>Показать правильный ответ</Button>
            </DialogTrigger>
            <DialogContent>
              <DialogHeader>
                <DialogTitle>Готовитесь к аттестации в РТН?</DialogTitle>
                <DialogDescription>
                  Введите email, чтобы система запоминала ваши ошибки и строила индивидуальный план
                  подготовки
                </DialogDescription>
              </DialogHeader>
              <MagicLinkForm />
            </DialogContent>
          </Dialog>
        ))}
    </>
  );
}
