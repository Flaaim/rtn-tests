import React from "react";
import { Metadata } from "next";
import { Toaster } from "sonner";
import { SidebarProvider, SidebarTrigger } from "@/components/ui/sidebar";
import { DashboardSidebar } from "@/components/User/Dashboard/DashboardSidebar";
import { fetchProfile } from "@/actions/profile";
import { redirect } from "next/navigation";
import { ProfileDTO } from "@/interfaces/user.interface";

export const metadata: Metadata = {
  title: "Панель пользователя",
  description: "Описание страницы",
};

export default async function UserDashboardLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  const result = await fetchProfile();

  if (!result.ok || !result.data) {
    redirect("/join/logout");
  }

  const profile: ProfileDTO = result.data;

  return (
    <SidebarProvider>
      <div className="grid min-h-screen w-full grid-cols-[auto_1fr] max-[765px]:grid-cols-1">
        <DashboardSidebar email={profile.email} />
        <div className="flex min-h-screen flex-col">
          <header className="bg-background flex h-16 shrink-0 items-center gap-2 border-b px-4">
            <SidebarTrigger className="-ml-1" />
            <div className="bg-border mx-2 my-auto h-4 w-px" />
            <span className="font-medium">Личный кабинет</span>
          </header>
          <main className="flex-1 p-6 max-[765px]:p-2.5">{children}</main>
          <footer className="col-start-2 col-end-4 row-start-3 mb-8 mx-3 text-sm text-muted-foreground max-[765px]:col-start-1 max-[765px]:col-end-2 max-[765px]:mb-4">
            <div>
              © {new Date().getFullYear()} Платформа тестов Ростехнадзора. Все права защищены.
            </div>
            <div className="mt-1 text-xs text-muted-foreground/80">
              Григорьев Александр Иванович, ИНН 272497691420. Вопросы и предложения направлять по
              адресу:{" "}
              <a
                href="mailto:flaeim@gmail.com"
                className="hover:underline text-primary font-medium"
              >
                flaeim@gmail.com
              </a>
            </div>
          </footer>
        </div>
      </div>

      <Toaster position="top-center" richColors />
    </SidebarProvider>
  );
}
