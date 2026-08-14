"use client";

import {
  Brackets,
  ChevronRight,
  FileText,
  GraduationCap,
  LayoutDashboard,
  LogOut,
  NotepadText,
  Shield,
  Target,
  Users,
} from "lucide-react";
import {
  Sidebar,
  SidebarContent,
  SidebarFooter,
  SidebarGroup,
  SidebarGroupContent,
  SidebarGroupLabel,
  SidebarHeader,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
  SidebarMenuSub,
  SidebarMenuSubButton,
  SidebarMenuSubItem,
} from "@/components/ui/sidebar";
import { Logout } from "@/actions/auth";
import Link from "next/link";
import { usePathname } from "next/navigation";
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from "@/components/ui/collapsible";

const items = [
  { title: "Дашборд", url: "/admin", icon: LayoutDashboard },
  { title: "Пользователи", url: "/admin/users", icon: Users },
  {
    title: "Парсинг",
    url: "/admin/parsers",
    icon: Brackets,
    isActive: true,
    subItems: [
      { title: "Парсеры", url: "/admin/parsers", icon: Target },
      { title: "Результаты", url: "/admin/parsers/tasks", icon: FileText },
    ],
  },
  { title: "Курсы", url: "/admin/courses", icon: GraduationCap },
  { title: "Тесты", url: "/admin/tests", icon: NotepadText },
];

interface AdminSidebarProps {
  readonly email: string;
}

export function AdminSidebar({ email }: AdminSidebarProps) {
  const pathname = usePathname();

  const handleLogout = async () => {
    await Logout();
  };

  return (
    <Sidebar>
      <SidebarHeader>
        <div className="flex items-center gap-2 px-2 py-1.5 text-sm font-semibold">
          <Shield className="size-4 text-primary" />
          Admin Panel
        </div>
      </SidebarHeader>
      <SidebarContent>
        <SidebarGroup>
          <SidebarGroupLabel>Мониторинг</SidebarGroupLabel>
          <SidebarGroupContent>
            <SidebarMenu>
              {items.map((item) => {
                const isActive =
                  item.url === "/admin" ? pathname === "/admin" : pathname.startsWith(item.url);

                return item.subItems ? (
                  <Collapsible
                    key={item.title}
                    defaultOpen={item.isActive}
                    className="group/collapsible"
                  >
                    <SidebarMenuItem>
                      <CollapsibleTrigger render={<SidebarMenuButton tooltip={item.title} />}>
                        <item.icon />
                        <span>{item.title}</span>
                        <ChevronRight className="ml-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90" />
                      </CollapsibleTrigger>
                      <CollapsibleContent>
                        <SidebarMenuSub>
                          {item.subItems.map((subItem) => (
                            <SidebarMenuSubItem key={subItem.title}>
                              <SidebarMenuSubButton render={<Link href={subItem.url} />}>
                                <subItem.icon className="mr-2 size-4" />
                                <span>{subItem.title}</span>
                              </SidebarMenuSubButton>
                            </SidebarMenuSubItem>
                          ))}
                        </SidebarMenuSub>
                      </CollapsibleContent>
                    </SidebarMenuItem>
                  </Collapsible>
                ) : (
                  <SidebarMenuItem key={item.title}>
                    <SidebarMenuButton
                      isActive={isActive}
                      render={<Link href={item.url} />}
                      className="cursor-pointer"
                    >
                      <item.icon />
                      <span>{item.title}</span>
                    </SidebarMenuButton>
                  </SidebarMenuItem>
                );
              })}
            </SidebarMenu>
          </SidebarGroupContent>
        </SidebarGroup>
      </SidebarContent>
      <SidebarFooter>
        <SidebarMenu>
          <SidebarMenuItem>
            <SidebarMenuButton size="lg">
              <div className="grid flex-1 text-left text-sm leading-tight">
                <span className="truncate text-xs text-muted-foreground">{email}</span>
              </div>
            </SidebarMenuButton>
            <SidebarMenuButton
              onClick={() => {
                void handleLogout();
              }}
              className="cursor-pointer text-destructive"
            >
              <LogOut className="mr-2 size-4" />
              <span>Выйти из системы</span>
            </SidebarMenuButton>
          </SidebarMenuItem>
        </SidebarMenu>
      </SidebarFooter>
    </Sidebar>
  );
}
