import { NextRequest, NextResponse } from "next/server";
import { RefreshSessionAction } from "@/actions/auth";

export async function middleware(request: NextRequest) {
  let accessToken = request.cookies.get("access_token")?.value;
  const refreshToken = request.cookies.get("refresh_token")?.value;

  const { pathname } = request.nextUrl;
  const isProtected = pathname.startsWith("/user") || pathname.startsWith("/admin");

  let newTokens = null;
  let refreshFailed = false;

  if (!accessToken && refreshToken) {
    try {
      newTokens = await RefreshSessionAction(refreshToken);
      if (newTokens) {
        accessToken = newTokens.access_token; // Подменяем переменную, чтобы пройти проверки ниже
      } else {
        refreshFailed = true;
      }
    } catch {
      refreshFailed = true;
    }
  }

  if (isProtected && !accessToken) {
    const redirectResponse = NextResponse.redirect(new URL("/join/login", request.url));
    if (refreshFailed) {
      redirectResponse.cookies.delete("access_token");
      redirectResponse.cookies.delete("refresh_token");
    }
    return redirectResponse;
  }

  if (pathname === "/join/login" && accessToken) {
    return NextResponse.redirect(new URL("/user/dashboard", request.url));
  }

  const requestHeaders = new Headers(request.headers);
  if (newTokens) {
    requestHeaders.set("x-access-token", newTokens.access_token);
  }

  const response = NextResponse.next({
    request: {
      headers: requestHeaders,
    },
  });

  if (!request.cookies.has("guest_id")) {
    response.cookies.set("guest_id", crypto.randomUUID(), {
      maxAge: 60 * 60 * 24 * 30,
      path: "/",
      httpOnly: false,
      sameSite: "lax",
    });
  }

  if (refreshFailed && !isProtected) {
    response.cookies.delete("access_token");
    response.cookies.delete("refresh_token");
  }

  if (newTokens) {
    response.cookies.set({
      name: "access_token",
      value: newTokens.access_token,
      httpOnly: true,
      path: "/",
      secure: process.env.NODE_ENV === "production",
      maxAge: newTokens.expires_in,
    });

    response.cookies.set({
      name: "refresh_token",
      value: newTokens.refresh_token,
      httpOnly: true,
      path: "/",
      secure: process.env.NODE_ENV === "production",
      maxAge: 2592000,
    });
  }

  return response;
}

export const config = {
  matcher: ["/((?!api|_next/static|_next/image|favicon.ico|sitemap.xml|robots.txt).*)"],
};
