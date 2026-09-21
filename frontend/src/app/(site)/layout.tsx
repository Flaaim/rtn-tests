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
    <div className="grid min-h-screen grid-cols-[1fr_270px_700px_1fr] grid-rows-[auto_1fr_auto] gap-x-10 gap-y-12 max-[765px]:grid-cols-1 max-[765px]:grid-rows-[auto_auto_auto] max-[765px]:p-2.5">
      <header className="col-start-2 col-end-4 row-start-1 mt-6 max-[765px]:col-start-1 max-[765px]:col-end-2 max-[765px]:mt-0">
        <Header />
      </header>
      <main className="col-start-2 col-end-4">
        {children}
        <Toaster position="top-center" richColors />
      </main>
      <footer className="col-start-2 col-end-4 row-start-3 mb-8 text-sm text-muted-foreground max-[765px]:col-start-1 max-[765px]:col-end-2 max-[765px]:mb-4">
        © {new Date().getFullYear()} Платформа тестов Ростехнадзора. Все права защищены.
      </footer>
    </div>
  );
}
