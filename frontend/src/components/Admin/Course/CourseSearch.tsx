"use client";

import { usePathname, useRouter, useSearchParams } from "next/navigation";
import { useEffect, useState, useTransition } from "react";
import { Loader2, Search } from "lucide-react";

export default function CourseSearch() {
  const searchParams = useSearchParams();
  const pathname = usePathname();
  const { replace } = useRouter();
  const [isPending, startTransition] = useTransition();

  const [term, setTerm] = useState(searchParams.get("q") || "");

  const currentQ = searchParams.get("q") || "";
  const searchParamsString = searchParams.toString();

  useEffect(() => {
    if (term === currentQ) {
      return () => {};
    }

    const handler = setTimeout(() => {
      const params = new URLSearchParams(searchParamsString);

      if (term) {
        params.set("q", term);
      } else {
        params.delete("q");
      }

      params.set("page", "1");
      const newQueryString = params.toString();

      if (newQueryString !== searchParamsString) {
        startTransition(() => {
          replace(`${pathname}?${newQueryString}`, { scroll: false });
        });
      }
    }, 400);

    return () => clearTimeout(handler);
  }, [term, pathname, replace, searchParamsString, currentQ]);

  return (
    <div className="relative max-w-md w-full">
      <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
      <input
        type="text"
        placeholder="Поиск по названию или файлу..."
        value={term}
        onChange={(e) => setTerm(e.target.value)}
        className="w-full pl-9 pr-9 py-2 text-sm rounded-md border bg-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all shadow-sm"
      />
      {isPending && (
        <Loader2 className="absolute right-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground animate-spin" />
      )}
      {!isPending && term && (
        <button
          onClick={() => setTerm("")}
          className="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-muted-foreground hover:text-foreground"
        >
          ✕
        </button>
      )}
    </div>
  );
}
