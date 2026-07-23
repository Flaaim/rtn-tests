import { fetchUser } from "@/actions/auth";
import { redirect } from "next/navigation";
import RequestChangeEmail from "@/components/Auth/Email/ChangeEmailForm";

export default async function changeEmailPage() {
  try {
    await fetchUser();
  } catch (error) {
    console.error("Ошибка авторизации в лейауте, перенаправление...", error);
    redirect("/join/login");
  }
  return <RequestChangeEmail />;
}
