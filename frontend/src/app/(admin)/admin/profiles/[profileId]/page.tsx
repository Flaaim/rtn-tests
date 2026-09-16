import { fetchProfileAction } from "@/actions/profile";
import { ProfileFull } from "@/interfaces/user.interface";
import AdminBreadcrumbs from "@/components/Admin/AdminBreadcrumbs";
import { Card, CardContent, CardHeader } from "@/components/ui/card";
import UserRoleBadge from "@/components/Admin/Domain/User/UserRoleBadge";
import ProfileStatusBadge from "@/components/Admin/Domain/User/ProfileStatusBadge";
import UserStatusBadge from "@/components/Admin/Domain/User/UserStatusBadge";
import { NetworkItem } from "@/interfaces/auth.interface";
import ForceChangeUserPassword from "@/components/Admin/Profile/ForceChangeUserPassword";

interface ProfileOverviewPageProps {
  params: Promise<{ profileId: string }>;
}

export default async function ProfileOverviewPage({ params }: ProfileOverviewPageProps) {
  const { profileId } = await params;
  const result = await fetchProfileAction(profileId);
  if (!result.ok || !result.data) {
    return null;
  }

  const profile: ProfileFull = result.data;

  const items = [{ title: "Профили", href: "/admin/profiles" }, { title: profile.email }];

  const formattedDate = new Date(profile.date).toLocaleString("ru-RU", {
    day: "2-digit",
    month: "2-digit",
    year: "numeric",
  });

  return (
    <div className="space-y-6">
      <AdminBreadcrumbs items={items} />
      <div className="flex items-center justify-between">
        <h1 className="text-3xl font-bold">Профиль пользователя {profile.email}</h1>
      </div>
      <div className="space-y-6">
        <Card>
          <CardHeader>
            <CardHeader>
              <h2 className="text-1xl font-bold">Информация о профиле</h2>
              <hr />
            </CardHeader>
            <CardContent className="space-y-4 text-sm">
              <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                  <p className="text-muted-foreground font-medium">Email</p>
                  <p className="font-mono">{profile.email}</p>
                </div>
                <div>
                  <p className="text-muted-foreground font-medium">ID</p>
                  <p className="font-mono">{profile.id}</p>
                </div>
                <div>
                  <p className="text-muted-foreground font-medium">Роль</p>
                  <p className="font-mono">
                    <UserRoleBadge type={profile.role} />
                  </p>
                </div>
                <div>
                  <p className="text-muted-foreground font-medium">Статус</p>
                  <p className="font-mono">
                    <ProfileStatusBadge type={profile.profileStatus} />
                  </p>
                </div>
                <div>
                  <p className="text-muted-foreground font-medium">Имя</p>
                  <p className="font-mono">{profile.name || "Не заполнено"}</p>
                </div>
                <div>
                  <p className="text-muted-foreground font-medium">Фамилия</p>
                  <p className="font-mono">{profile.name || "Не заполнено"}</p>
                </div>
              </div>
            </CardContent>
          </CardHeader>
        </Card>
      </div>
      <div className="space-y-6">
        <Card>
          <CardHeader>
            <h2 className="text-1xl font-bold">Информация об аутентификации</h2>
            <hr />
          </CardHeader>
          <CardContent>
            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
              <div>
                <p className="text-muted-foreground font-medium">Статус</p>
                <p className="font-mono">
                  <UserStatusBadge type={profile.authStatus} />
                </p>
              </div>
              <div>
                <p className="text-muted-foreground font-medium">Пароль</p>
                <p className="font-mono">{profile.passwordHash ? "**********" : "Не установлен"}</p>
                <ForceChangeUserPassword userId={profile.id} />
              </div>
              <div>
                <p className="text-muted-foreground font-medium">Соцсети</p>
                {profile.networks.map((network: NetworkItem) => (
                  <p key={network.identity}>
                    {network.network} - {network.identity}
                  </p>
                ))}
              </div>
              <div>
                <p className="text-muted-foreground font-medium">Дата</p>
                <p className="font-mono">{formattedDate}</p>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  );
}
