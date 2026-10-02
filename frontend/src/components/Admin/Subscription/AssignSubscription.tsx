"use client";

import { z } from "zod";
import { ProfileSelectOption, SubscriptionType } from "@/interfaces/subscription.interface";
import { useEffect, useState } from "react";
import { useRouter } from "next/navigation";
import { fetchProfilesToSelectAction } from "@/actions/subscriptions";
import { toast } from "sonner";

const schema = z.object({
  userId: z.string().uuid(),
  durationDays: z.number().min(1),
  plan: z.enum(["basic", "trial"]) as z.ZodType<SubscriptionType>,
});

type AssignSubscriptionFormData = z.infer<typeof schema>;

export default function AssignSubscription() {
  const [open, setOpen] = useState<boolean>(false);
  const [loading, setLoading] = useState<boolean>(false);
  const [profiles, setProfiles] = useState<ProfileSelectOption[]>([]);
  const isProfilesLoaded = profiles.length > 0;

  const router = useRouter();

  useEffect(() => {
    if (open) {
      const initData = async () => {
        setLoading(true);
        try {
          const response = await fetchProfilesToSelectAction();
          if (response.ok && response.data) {
            setProfiles(response.data);
          }
        } catch (error) {
          const err = error instanceof Error ? error : new Error("Ошибка при получении данных");
          toast.error(err.message);
        } finally {
          setLoading(false);
        }
      };
      void initData();
    }
  }, [open]);
}
