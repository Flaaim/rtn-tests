import AdminBreadcrumbs from "@/components/Admin/AdminBreadcrumbs";

interface TestOverviewPageProps {
  params: Promise<{ testId: string }>;
}

export default async function TestOverviewPage({ params }: TestOverviewPageProps) {
  const { testId } = await params;
  const items = [{ title: "Тесты", href: "/admin/tests" }];

  return (
    <div className="space-y-6">
      <AdminBreadcrumbs items={items} />
    </div>
  );
}
