"use client";

import { Question } from "@/interfaces/course.interface";
import React, { useState } from "react";
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from "@/components/ui/dialog";
import { Button } from "@/components/ui/button";
import { CheckCircle2, Info, XCircle } from "lucide-react";
import {
  Table,
  TableBody,
  TableHead,
  TableHeader,
  TableRow,
  TableCell,
} from "@/components/ui/table";
import { Answer } from "@/components/Admin/Course/UpdateQuestionsCourseDialog";
import { PUBLIC_ASSETS_URL } from "@/app/api";
import QuestionFormTypeBadge from "@/components/Admin/Domain/QuestionFormTypeBadge";

interface ShowDetailQuestionDialogProps {
  question: Question;
}

export function ShowDetailQuestionDialog({ question }: ShowDetailQuestionDialogProps) {
  const [open, setOpen] = useState<boolean>(false);

  return (
    <Dialog open={open} onOpenChange={setOpen}>
      <DialogTrigger render={<Button variant="ghost" size="icon" />}>
        <Info className="h-4 w-4" />
      </DialogTrigger>

      <DialogContent className="max-w-[95vw] md:max-w-3xl max-h-[90vh] flex flex-col p-4 md:p-6 overflow-hidden">
        <DialogHeader>
          <DialogTitle className="text-lg leading-relaxed break-words">{question.text}</DialogTitle>
        </DialogHeader>

        <div className="flex items-center gap-2 mb-4">
          <span className="text-sm text-muted-foreground">Тип вопроса:</span>
          <QuestionFormTypeBadge type={question.form} />
        </div>
        <div className="flex-1 overflow-y-auto overflow-x-auto min-h-0 pr-1">
          {question.questionImg && (
            <div className="mb-4">
              {/* eslint-disable-next-line @next/next/no-img-element */}
              <img
                src={`${PUBLIC_ASSETS_URL}${process.env.NEXT_PUBLIC_QUESTION_IMAGES}/${question.questionImg}`}
                alt="К вопросу"
                className="max-h-48 max-w-full object-contain rounded-md border"
              />
            </div>
          )}
          <div className="border rounded-md min-w-full">
            <Table>
              <TableHeader className="bg-muted/50">
                <TableRow>
                  <TableHead className="w-2/3">Вариант ответа</TableHead>
                  <TableHead>Правильный ответ</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {question.answers.map((answer: Answer) => (
                  <TableRow key={answer.id}>
                    <TableCell className="font-medium align-top">
                      <p className="text-sm leading-relaxed whitespace-normal break-words">
                        {answer.text}
                      </p>
                      {answer.answerImg && (
                        <div className="relative rounded-md overflow-hidden border inline-block mt-2">
                          {/* eslint-disable-next-line @next/next/no-img-element */}
                          <img
                            src={`${PUBLIC_ASSETS_URL}${process.env.NEXT_PUBLIC_QUESTION_IMAGES}/${question.questionImg}/${answer.answerImg}`}
                            alt="Изображение ответа"
                            className="max-h-24 max-w-full object-contain"
                          />
                        </div>
                      )}
                    </TableCell>
                    <TableCell className="align-middle text-center">
                      <div className="flex justify-center">
                        {answer.isCorrect ? (
                          <CheckCircle2 className="h-5 w-5 text-green-500" />
                        ) : (
                          <XCircle className="h-5 w-5 text-muted-foreground opacity-30" />
                        )}
                      </div>
                    </TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          </div>
        </div>
      </DialogContent>
    </Dialog>
  );
}
