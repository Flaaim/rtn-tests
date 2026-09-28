"use server";

import { ApiResponse } from "@/interfaces/response.interface";
import { apiFetch } from "@/lib/apiClient";
import { API } from "@/app/api";
import { handleApiResponse } from "@/lib/handleApiResponse";
import { AdminUsersStats } from "@/interfaces/admin.interface";

export default async function fetchAdminUsersStatsAction(): Promise<ApiResponse<AdminUsersStats>> {
  try {
    const response = await apiFetch(API.admin.getUsersStats(), {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
    });
    return handleApiResponse<AdminUsersStats>(response);
  } catch (error) {
    console.error("fetchAdminUsersStatsAction Fetch error:", error);
    return { ok: false, error: "Не удалось подключиться к серверу API." };
  }
}
