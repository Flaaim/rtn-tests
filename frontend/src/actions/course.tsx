"use server";

import { ApiResponse } from "@/interfaces/response.interface";
import { apiFetch } from "@/lib/apiClient";
import { API } from "@/app/api";
import { handleApiResponse } from "@/lib/handleApiResponse";
import {
  AddCoursePayload,
  CourseFull,
  CourseSelectOption,
  PaginatedCourses,
  RenameCoursePayload,
  UpdateQuestionCoursePayload,
} from "@/interfaces/course.interface";
import { Question } from "@/interfaces/task.interface";

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
export async function renameCourseAction(payload: RenameCoursePayload): Promise<ApiResponse<void>> {
  try {
    const response = await apiFetch(API.course.rename(payload.id), {
      method: "PUT",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
      body: JSON.stringify({
        name: payload.name,
        cipher: payload.cipher,
      }),
    });

    return handleApiResponse<void>(response);
  } catch (error) {
    console.error("renameCourseAction Fetch error:", error);
    return { ok: false, error: "Не удалось подключиться к серверу API." };
  }
}

export async function updateQuestionsCourseAction(
  payload: UpdateQuestionCoursePayload
): Promise<ApiResponse<void>> {
  try {
    const response = await apiFetch(API.course.update(payload.id), {
      method: "PUT",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
      body: JSON.stringify({
        draft: payload.rawJson,
      }),
    });

    return handleApiResponse<void>(response);
  } catch (error) {
    console.error("updateQuestionsCourseAction Fetch error:", error);
    return { ok: false, error: "Не удалось подключиться к серверу API." };
  }
}

export async function removeCourseAction(id: string): Promise<ApiResponse<void>> {
  try {
    const response = await apiFetch(API.course.remove(id), {
      method: "DELETE",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
    });

    return handleApiResponse<void>(response);
  } catch (error) {
    console.error("removeCourseAction Fetch error:", error);
    return { ok: false, error: "Не удалось подключиться к серверу API." };
  }
}

export async function fetchCoursesToSelectAction(): Promise<ApiResponse<CourseSelectOption[]>> {
  try {
    const response = await apiFetch(API.course.lookup(), {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
    });

    return handleApiResponse<CourseSelectOption[]>(response);
  } catch (error) {
    console.error("fetchCoursesToSelectAction Fetch error:", error);
    return { ok: false, error: "Не удалось подключиться к серверу API." };
  }
}

export async function fetchCourseQuestionsByIdsAction(
  ids: string[]
): Promise<ApiResponse<Question[]>> {
  try {
    const response = await apiFetch(API.course.getQuestions(ids), {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
    });

    return handleApiResponse<Question[]>(response);
  } catch (error) {
    console.error("fetchCoursesToSelectAction Fetch error:", error);
    return { ok: false, error: "Не удалось подключиться к серверу API." };
  }
}
