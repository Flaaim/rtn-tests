"use server";

import { ApiResponse } from "@/interfaces/response.interface";
import { apiFetch } from "@/lib/apiClient";
import { API } from "@/app/api";
import { handleApiResponse } from "@/lib/handleApiResponse";
import {
  AdminAttemptsStats,
  AdminSubscriptionsStats,
  AdminUsersStats,
} from "@/interfaces/admin.interface";

export async function fetchAdminUsersStatsAction(): Promise<ApiResponse<AdminUsersStats>> {
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

export async function fetchAdminSubscriptionsStatsAction(): Promise<
  ApiResponse<AdminSubscriptionsStats>
> {
  try {
    const response = await apiFetch(API.admin.getSubscriptionsStats(), {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
    });
    return handleApiResponse<AdminSubscriptionsStats>(response);
  } catch (error) {
    console.error("fetchAdminSubscriptionsStatsAction Fetch error:", error);
    return { ok: false, error: "Не удалось подключиться к серверу API." };
  }
}

export async function fetchAdminAttemptsStatsAction(): Promise<ApiResponse<AdminAttemptsStats>> {
  try {
    const response = await apiFetch(API.admin.getAttemptsStats(), {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
    });
    return handleApiResponse<AdminAttemptsStats>(response);
  } catch (error) {
    console.error("fetchAdminAttemptsStatsAction Fetch error:", error);
    return { ok: false, error: "Не удалось подключиться к серверу API." };
  }
}
