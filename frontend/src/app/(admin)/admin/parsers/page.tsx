import AdminBreadcrumbs from "@/components/Admin/AdminBreadcrumbs";

export default function AdminParsersPage() {
  return (
    <div className="space-y-6">
      <AdminBreadcrumbs items={[{ title: "Парсеры" }]} />
    </div>
  );
}
