import { ApiResponse } from "@/interfaces/response.interface";
import { apiFetch } from "@/lib/apiClient";
import { API } from "@/app/api";
import { TaskShort } from "@/interfaces/task.interface";
import { handleApiResponse } from "@/lib/handleApiResponse";

export async function fetchTasksAction(): Promise<ApiResponse<TaskShort[]>> {
  try {
    const response = await apiFetch(API.task.list(), {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
    });

    return await handleApiResponse<TaskShort[]>(response);
  } catch (error) {
    console.error("fetchParserResultsAction Fetch error:", error);
    return { ok: false, error: "Не удалось подключиться к серверу API." };
  }
}

export async function fetchTaskAction(taskId: string): Promise<ApiResponse<TaskShort>> {
  try {
    const response = await apiFetch(API.task.get(taskId), {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
    });

    return await handleApiResponse<TaskShort>(response);
  } catch (error) {
    console.error("fetchTaskAction Fetch error:", error);
    return { ok: false, error: "Не удалось подключиться к серверу API." };
  }
}
