import JoinByEmail from "@/components/Auth/JoinByEmail/Request/JoinByEmail";
import { Metadata } from "next";
import MagicLinkForm from "@/components/Auth/MagicLink/Request/MagicLinkForm";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import {
  Card,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from "@/components/ui/card";
import Link from "next/link";

export const metadata: Metadata = {
  title: "Регистрация на сайте",
  description: "Страница регистрации на сайте",
};
export default function RegisterPage() {
  return (
    <div className="flex min-h-screen items-center justify-center p-4">
      <Card className="mx-auto w-full max-w-md shadow-sm">
        <CardHeader className="space-y-2 text-center">
          <CardTitle className="text-2xl font-semibold tracking-tight">Регистрация</CardTitle>
          <CardDescription>Выберите удобный способ создания аккаунта</CardDescription>
        </CardHeader>
        <CardContent>
          <Tabs defaultValue="magic" className="w-full">
            <TabsList className="grid w-full grid-cols-2 mb-6">
              <TabsTrigger value="magic">В 1 клик</TabsTrigger>
              <TabsTrigger value="default">По паролю</TabsTrigger>
            </TabsList>

            <TabsContent value="magic" className="space-y-4 outline-none">
              <MagicLinkForm />
            </TabsContent>
            <TabsContent value="default" className="space-y-4 outline-none">
              <JoinByEmail />
            </TabsContent>
          </Tabs>
        </CardContent>
        <CardFooter className="flex justify-center border-t p-4 mt-2">
          <div className="text-sm text-muted-foreground">
            Есть аккаунт?{" "}
            <Link className="text-primary hover:underline font-medium" href="/join/login">
              Войти в систему
            </Link>
          </div>
        </CardFooter>
      </Card>
    </div>
  );
}
