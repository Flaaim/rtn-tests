import Link from "next/link";
import { Button } from "@/components/ui/button";
import { ShieldCheck, User } from "lucide-react";
import { checkIsAuthenticated } from "@/actions/auth";

export async function Header() {
  const isAuthenticated = await checkIsAuthenticated();

  return (
    <header className="sticky top-0 z-50 w-full border-b bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
      <div className="mx-auto flex h-16 max-w-6xl items-center justify-between px-4">
        <Link href="/" className="flex items-center space-x-2 transition-opacity hover:opacity-80">
          <ShieldCheck className="h-6 w-6 text-primary" />
          <span className="text-lg font-bold tracking-tight">RTN-tests</span>
        </Link>

        <nav className="flex items-center space-x-6">
          <Link
            href="/catalog"
            className="text-sm font-medium text-muted-foreground transition-colors hover:text-foreground"
          >
            Каталог
          </Link>
          <Button variant="default" size="sm">
            {isAuthenticated ? (
              <Link href="/user/dashboard">
                <User />
              </Link>
            ) : (
              <Link href="/join/login">Войти</Link>
            )}
          </Button>
        </nav>
      </div>
    </header>
  );
}
