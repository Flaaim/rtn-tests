import AdminBreadcrumbs from "@/components/Admin/AdminBreadcrumbs";
import AddParserDialog from "@/components/Admin/Parser/AddParserDialog";

export default function AdminParsersPage() {
  return (
    <div className="space-y-6">
      <AdminBreadcrumbs items={[{ title: "Парсеры" }]} />
      <div className="flex items-center justify-between">
        <h1 className="text-3xl font-bold">Парсеры</h1>
        <AddParserDialog />
      </div>
    </div>
  );
}
