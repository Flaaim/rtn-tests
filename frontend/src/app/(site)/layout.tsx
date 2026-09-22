import React from "react";
import { Metadata } from "next";
import { Toaster } from "sonner";
import { Header } from "@/components/Home/Header";

export const metadata: Metadata = {
  title: "Тесты Ростехнадзора",
  description: "Готовые и актуальные тесты по Ростехнадзора по всем областям аттестации.",
};

export default function SiteLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <div className="flex min-h-screen flex-col">
      {/* Центрированный ограничивающий контейнер. Ширина 1024px (max-w-5xl) примерно равна вашим 270+700+gap */}
      <div className="mx-auto flex w-full max-w-5xl flex-1 flex-col px-4 sm:px-6 md:px-8">
        <header className="pt-4 sm:pt-6">
          <Header />
        </header>

        {/* flex-1 заставляет main занимать всё доступное место, прижимая футер к низу */}
        <main className="flex-1 py-8 sm:py-12">
          {children}
          <Toaster position="top-center" richColors />
        </main>

        <footer className="pb-4 sm:pb-8 text-sm text-muted-foreground text-center sm:text-left">
          © {new Date().getFullYear()} Платформа тестов Ростехнадзора. Все права защищены.
        </footer>
      </div>
    </div>
  );
}
