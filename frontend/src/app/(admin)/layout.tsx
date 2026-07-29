import { Metadata } from "next";
import { redirect } from "next/navigation";
import { fetchUser } from "@/actions/auth";
import { SidebarProvider, SidebarTrigger } from "@/components/ui/sidebar";
import { Toaster } from "sonner";
import { AdminSidebar } from "@/components/Admin/AdminSidebar";
import React from "react";

export const metadata: Metadata = {
  title: "Admin Panel",
  description: "Мониторинг rtn-test.ru",
};

export default async function AdminLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  let profile;
  try {
    profile = await fetchUser();
  } catch {
    redirect("/join/login");
  }

  if (profile.role !== "admin") {
    redirect("/user/dashboard");
  }
  return (
    <SidebarProvider>
      <div className="grid min-h-screen w-full grid-cols-[auto_1fr] max-[765px]:grid-cols-1">
        <AdminSidebar email={profile.email} />
        <div className="flex min-h-screen flex-col">
          <header className="bg-background flex h-16 shrink-0 items-center gap-2 border-b px-4">
            <SidebarTrigger className="-ml-1" />
            <div className="bg-border mx-2 my-auto h-4 w-px" />
            <span className="font-medium">Панель пользователя</span>
          </header>
          <main className="flex-1 p-6 max-[765px]:p-2.5">{children}</main>
          <footer className="text-muted-foreground border-t p-4 text-sm">Footer</footer>
        </div>
      </div>

      <Toaster position="top-center" richColors />
    </SidebarProvider>
  );
}
