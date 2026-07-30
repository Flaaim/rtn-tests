import { fetchParserAction } from "@/actions/parser";
import AdminBreadcrumbs from "@/components/Admin/AdminBreadcrumbs";
import LaunchParserForm from "@/components/Admin/Parser/LaunchParserForm";

interface ParserOverviewPageProps {
  params: Promise<{ parserId: string }>;
}

export default async function ParserOverviewPage({ params }: ParserOverviewPageProps) {
  const { parserId } = await params;

  const result = await fetchParserAction(parserId);

  if (!result.ok || !result.data) {
    return null;
  }

  const parser = result.data;
  const items = [{ title: "Парсеры", href: "/admin/parsers" }, { title: parser.id }];

  return (
    <div className="space-y-6">
      <AdminBreadcrumbs items={items} />
      <div className="flex items-center justify-between">
        <h1 className="text-3xl font-bold">Парсер: {parser.id}</h1>
      </div>
      <LaunchParserForm parser={parser} />
    </div>
  );
}
