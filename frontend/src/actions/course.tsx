"use server";

import { ApiResponse } from "@/interfaces/response.interface";
import { apiFetch } from "@/lib/apiClient";
import { API } from "@/app/api";
import { handleApiResponse } from "@/lib/handleApiResponse";
import {AddCoursePayload, CourseFull, CourseItem, PaginatedCourses} from "@/interfaces/course.interface";

export async function addCourseAction(payload: AddCoursePayload): Promise<ApiResponse<void>> {
  try {
    const response = await apiFetch(API.course.add(), {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
      body: JSON.stringify({
        name: payload.name,
        cipher: payload.cipher,
        draft: payload.draft,
      }),
    });

    return await handleApiResponse<void>(response);
  } catch (error) {
    console.error("addCourseAction Fetch error:", error);
    return { ok: false, error: "Не удалось подключиться к серверу API." };
  }
}

export async function fetchCoursesPaginatedAction(
  page: number,
  perPage: number,
  search?: string
): Promise<ApiResponse<PaginatedCourses>> {
  try {
    const response = await apiFetch(API.course.getPaginated(page, perPage, search), {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
    });

    return handleApiResponse<PaginatedCourses>(response);
  } catch (error) {
    console.error("fetchCoursesPaginatedAction Fetch error:", error);
    return { ok: false, error: "Не удалось подключиться к серверу API." };
  }
}

export async function fetchCourseAction(courseId: string): Promise<ApiResponse<CourseFull>> {
  try {
    const response = await apiFetch(API.course.get(courseId), {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
    });

    return handleApiResponse<CourseFull>(response);
  } catch (error) {
    console.error("fetchCourseAction Fetch error:", error);
    return { ok: false, error: "Не удалось подключиться к серверу API." };
  }
}
