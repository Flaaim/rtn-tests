import { cookies, headers } from "next/headers";
import Cookies from "js-cookie";

export async function apiFetch(url: string, options: RequestInit = {}): Promise<Response> {
  const { headers: customHeaders, ...restOptions } = options;
  const isServer = typeof window === "undefined";
  let guestId = "";

  const headersList = await headers();
  const cookieStore = await cookies();

  if (isServer) {
    guestId = cookieStore.get("guest_id")?.value || "";
  } else {
    guestId = Cookies.get("guest_id") || "";
  }

  const access_token = headersList.get("x-access-token") || cookieStore.get("access_token")?.value;

  const fetchHeaders = new Headers(customHeaders);

  if (access_token) {
    fetchHeaders.set("Authorization", `Bearer ${access_token}`);
  }

  if (guestId) {
    fetchHeaders.set("X-Guest-ID", guestId);
  }

  const fetchUrl = url.startsWith("http") ? url : `${process.env.INTERNAL_BACKEND_URL}${url}`;

  return await fetch(fetchUrl, {
    ...restOptions,
    headers: Object.fromEntries(fetchHeaders.entries()),
  });
}
