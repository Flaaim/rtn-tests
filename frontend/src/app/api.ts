const isServer = typeof window === "undefined";
export const PUBLIC_ASSETS_URL = process.env.NEXT_PUBLIC_BACKEND_URL || "http://localhost:8081";
export const BASE_URL = isServer
  ? process.env.INTERNAL_BACKEND_URL || process.env.NEXT_PUBLIC_BACKEND_URL || "http://api"
  : process.env.NEXT_PUBLIC_BACKEND_URL || "http://localhost:8081";

export const API = {
  auth: {
    joinByEmail: () => BASE_URL + `/v1/auth/join/request`,
    login: () => BASE_URL + `/token`,
    refreshToken: () => BASE_URL + `/token`,
    revokeToken: () => BASE_URL + `/v1/auth/token/revoke`,
    joinConfirm: () => BASE_URL + `/v1/auth/join/confirm`,
    passwordResetRequest: () => BASE_URL + `/v1/auth/password/reset/request`,
    passwordResetConfirm: () => BASE_URL + `/v1/auth/password/reset`,
    changePassword: () => BASE_URL + `/v1/auth/user/password/change`,
    requestEmailChange: () => BASE_URL + `/v1/auth/email/change/request`,
    confirmEmailChange: () => BASE_URL + `/v1/auth/email/change/confirm`,
    socialLogin: () => BASE_URL + `/token`,
    attachNetwork: () => BASE_URL + `/v1/auth/network/attach`,
  },
  user: {
    profile: () => BASE_URL + `/v1/user/profile`,
  },
  parser: {
    add: () => BASE_URL + `/v1/admin/parsers`,
    list: () => BASE_URL + `/v1/admin/parsers`,
    get: (id: string) => BASE_URL + `/v1/admin/parsers/${id}`,
    launch: (id: string) => BASE_URL + `/v1/admin/parsers/${id}/launch`,
    remove: (id: string) => BASE_URL + `/v1/admin/parsers/${id}`,
  },
  task: {
    list: () => BASE_URL + `/v1/admin/tasks`,
    get: (id: string) => BASE_URL + `/v1/admin/tasks/${id}`,
    delete: (ids: string[]) => {
      const url = new URL(BASE_URL + `/v1/admin/tasks`);
      ids.forEach((id) => url.searchParams.append("ids[]", id));
      return url.toString();
    },
  },
  course: {
    add: () => BASE_URL + `/v1/admin/testing/courses`,
    getPaginated: (page: number, perPage: number, search?: string) => {
      const params = new URLSearchParams({
        page: String(page),
        perPage: String(perPage),
      });
      if (search) params.set("search", search);
      return BASE_URL + `/v1/admin/testing/courses?${params.toString()}`;
    },
    get: (courseId: string) => BASE_URL + `/v1/admin/testing/courses/${courseId}`,
  },
};
