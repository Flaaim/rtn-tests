"use client";

import { ProfileDTO } from "@/interfaces/user.interface";
import { useEffect, useState } from "react";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import UserStatusBadge from "@/components/Admin/Domain/User/UserStatusBadge";
import UserRoleBadge from "@/components/Admin/Domain/User/UserRoleBadge";
import RemoveProfileDialog from "@/components/Admin/Profile/RemoveProfileDialog";
import Link from "next/link";
import AddProfileDialog from "@/components/Admin/Profile/AddProfileDialog";
import { useRouter } from "next/navigation";

interface ProfilesClientWrapperProps {
  initialProfiles: ProfileDTO[];
}

export default function ProfilesClientWrapper({ initialProfiles }: ProfilesClientWrapperProps) {
  const router = useRouter();

  const [deletedIds, setDeletedIds] = useState<Set<string>>(new Set());
  const [addedProfiles, setAddedProfiles] = useState<ProfileDTO[]>([]);
  const visibleServerProfiles = initialProfiles.filter((p) => !deletedIds.has(p.id));

  const serverEmails = new Set(visibleServerProfiles.map((p) => p.email));

  const activeAddedProfiles = addedProfiles.filter((p) => !serverEmails.has(p.email));
  const unresolvedDeletions = initialProfiles.filter((p) => deletedIds.has(p.id));

  useEffect(() => {
    const hasDiscrepancies = activeAddedProfiles.length > 0 || unresolvedDeletions.length > 0;

    if (hasDiscrepancies) {
      const interval = setInterval(() => {
        router.refresh();
      }, 1500);

      return () => clearInterval(interval);
    }

    return () => undefined;
  }, [activeAddedProfiles.length, unresolvedDeletions.length, router]);

  const displayProfiles = [...activeAddedProfiles, ...visibleServerProfiles];

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <h1 className="text-3xl font-bold">Пользователи</h1>
        <AddProfileDialog
          onOptimisticAdd={(newProfile) => setAddedProfiles((prev) => [newProfile, ...prev])}
          onRevertAdd={(fakeId) => setAddedProfiles((prev) => prev.filter((p) => p.id !== fakeId))}
        />
      </div>
      <div className="rounded-md border bg-white">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Id</TableHead>
              <TableHead>Email</TableHead>
              <TableHead>Статус</TableHead>
              <TableHead>Роль</TableHead>
              <TableHead>Дата</TableHead>
              <TableHead>Быстрые действия</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {displayProfiles.map((profile: ProfileDTO) => (
              <TableRow
                key={profile.id}
                className={profile.id.startsWith("temp-") ? "opacity-70" : ""}
              >
                <TableCell className="font-medium">
                  {profile.id.startsWith("temp-") ? (
                    <span className="text-muted-foreground">Создается...</span>
                  ) : (
                    <Link href={`/admin/profiles/${profile.id}`} className="hover:underline">
                      {profile.id}
                    </Link>
                  )}
                </TableCell>
                <TableCell className="font-medium">{profile.email}</TableCell>
                <TableCell className="font-medium">
                  <UserStatusBadge type={profile.status} />
                </TableCell>
                <TableCell className="font-medium">
                  <UserRoleBadge type={profile.role} />
                </TableCell>
                <TableCell className="font-medium">
                  {new Date(profile.date).toLocaleDateString("ru-RU")}
                </TableCell>
                <TableCell>
                  {/* Прячем кнопку удаления для фейковых строк */}
                  {!profile.id.startsWith("temp-") && (
                    <RemoveProfileDialog
                      id={profile.id}
                      onHide={() => setDeletedIds((prev) => new Set(prev).add(profile.id))}
                      onRevert={() =>
                        setDeletedIds((prev) => {
                          const next = new Set(prev);
                          next.delete(profile.id);
                          return next;
                        })
                      }
                    />
                  )}
                </TableCell>
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </div>
    </div>
  );
}
