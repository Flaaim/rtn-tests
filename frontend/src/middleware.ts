import { NextRequest, NextResponse } from "next/server";
import { RefreshSessionAction } from "@/actions/auth";

export async function middleware(request: NextRequest) {
  const accessToken = request.cookies.get("access_token")?.value;
  const refreshToken = request.cookies.get("refresh_token")?.value;

  const { pathname } = request.nextUrl;
  const isProtected = pathname.startsWith("/user") || pathname.startsWith("/admin");

  if (isProtected && !accessToken && !refreshToken) {
    return NextResponse.redirect(new URL("/join/login", request.url));
  }

  if (pathname === "/join/login" && (accessToken || refreshToken)) {
    return NextResponse.redirect(new URL("/user/dashboard", request.url));
  }

  const requestHeaders = new Headers(request.headers);
  let response = NextResponse.next({
    request: {
      headers: requestHeaders,
    },
  });

  if (!request.cookies.has("guest_id")) {
    const guestId = crypto.randomUUID();

    response.cookies.set("guest_id", guestId, {
      maxAge: 60 * 60 * 24 * 30,
      path: "/",
      httpOnly: false,
      sameSite: "lax",
    });
  }

  if (isProtected && !accessToken && refreshToken) {
    try {
      const newTokens = await RefreshSessionAction(refreshToken as string);

      if (newTokens === null) {
        const redirectResponse = NextResponse.redirect(new URL("/join/login", request.url));
        redirectResponse.cookies.delete("access_token");
        redirectResponse.cookies.delete("refresh_token");
        return redirectResponse;
      }

      const requestHeaders = new Headers(request.headers);
      requestHeaders.set("x-access-token", newTokens.access_token);

      response = NextResponse.next({
        request: { headers: requestHeaders },
      });

      const guestId = request.cookies.get("guest_id")?.value;
      if (guestId) {
        response.cookies.set("guest_id", guestId, {
          maxAge: 60 * 60 * 24 * 30,
          path: "/",
          httpOnly: false,
        });
      }

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
    } catch {
      return NextResponse.redirect(new URL("/join/login", request.url));
    }
  }

  if (pathname === "/join/login" && (accessToken || refreshToken)) {
    return NextResponse.redirect(new URL("/user/dashboard", request.url));
  }

  return response;
}

export const config = {
  matcher: ["/((?!api|_next/static|_next/image|favicon.ico|sitemap.xml|robots.txt).*)"],
};
