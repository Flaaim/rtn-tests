import Link from "next/link";
import { ChevronLeft, ChevronRight } from "lucide-react";

interface PaginationProps {
  totalPages: number;
  currentPage: number;
  searchQuery?: string;
  baseUrl: string;
}

export default function Pagination({
  totalPages,
  currentPage,
  searchQuery,
  baseUrl,
}: PaginationProps) {
  if (totalPages <= 1) return null;

  const createPageUrl = (pageNumber: number) => {
    const params = new URLSearchParams();
    if (pageNumber > 1) params.set("page", pageNumber.toString());
    if (searchQuery) params.set("q", searchQuery);

    const queryString = params.toString();
    return `${baseUrl}${queryString ? `?${queryString}` : ""}`;
  };

  return (
    <nav aria-label="Пагинация курсов" className="flex items-center justify-center gap-1 mt-6">
      {/* Кнопка "Назад" */}
      {currentPage > 1 ? (
        <Link
          href={createPageUrl(currentPage - 1)}
          className="p-2 border rounded-md hover:bg-accent transition-colors"
          aria-label="Предыдущая страница"
        >
          <ChevronLeft className="h-4 w-4" />
        </Link>
      ) : (
        <span className="p-2 border rounded-md opacity-40 cursor-not-allowed">
          <ChevronLeft className="h-4 w-4" />
        </span>
      )}

      {/* Номера страниц */}
      <div className="flex items-center gap-1">
        {Array.from({ length: totalPages }, (_, i) => i + 1).map((page) => {
          // Простая логика отображения: показываем текущую, первую, последнюю и соседние
          if (
            page === 1 ||
            page === totalPages ||
            (page >= currentPage - 1 && page <= currentPage + 1)
          ) {
            return (
              <Link
                key={page}
                href={createPageUrl(page)}
                className={`min-w-[36px] h-9 px-3 py-1 flex items-center justify-center rounded-md border text-sm font-medium transition-colors ${
                  currentPage === page
                    ? "bg-primary text-primary-foreground border-primary pointer-events-none"
                    : "hover:bg-accent"
                }`}
              >
                {page}
              </Link>
            );
          }

          // Троеточие для пропуска
          if (page === currentPage - 2 || page === currentPage + 2) {
            return (
              <span key={page} className="px-1 text-muted-foreground">
                ...
              </span>
            );
          }

          return null;
        })}
      </div>

      {/* Кнопка "Вперед" */}
      {currentPage < totalPages ? (
        <Link
          href={createPageUrl(currentPage + 1)}
          className="p-2 border rounded-md hover:bg-accent transition-colors"
          aria-label="Следующая страница"
        >
          <ChevronRight className="h-4 w-4" />
        </Link>
      ) : (
        <span className="p-2 border rounded-md opacity-40 cursor-not-allowed">
          <ChevronRight className="h-4 w-4" />
        </span>
      )}
    </nav>
  );
}
