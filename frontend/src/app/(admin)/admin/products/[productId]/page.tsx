import { fetchProductAction } from "@/actions/product";
import { ProductFull } from "@/interfaces/product.interface";
import AdminBreadcrumbs from "@/components/Admin/AdminBreadcrumbs";
import { Card, CardContent, CardHeader } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { PUBLIC_ASSETS_URL } from "@/app/api";

interface ProductOverviewPageProps {
  params: Promise<{ productId: string }>;
}

export default async function ProductOverviewPage({ params }: ProductOverviewPageProps) {
  const { productId } = await params;
  const result = await fetchProductAction(productId);
  if (!result.ok || !result.data) {
    return null;
  }

  const product: ProductFull = result.data;

  const items = [{ title: "Продукты", href: "/admin/products" }, { title: product.cipher }];

  const formattedDate = new Date(product.addedAt).toLocaleString("ru-RU", {
    day: "2-digit",
    month: "2-digit",
    year: "numeric",
  });

  const pdfUrl = product
    ? `${PUBLIC_ASSETS_URL}${process.env.NEXT_PUBLIC_COURSE_ANSWERS}${product.filePath}`
    : "#";

  return (
    <div className="space-y-6">
      <AdminBreadcrumbs items={items} />
      <div className="flex items-center justify-between">
        <h1 className="text-3xl font-bold">Продукт {product.cipher}</h1>
      </div>
      <div className="space-y-6">
        <Card>
          <CardHeader>
            <h2 className="text-1xl font-bold">Информация о продукте</h2>
            <hr />
          </CardHeader>
          <CardContent className="space-y-4 text-sm">
            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
              <div>
                <p className="text-muted-foreground font-medium">Id</p>
                <p className="font-mono">{product.productId}</p>
              </div>
              <div>
                <p className="text-muted-foreground font-medium">Шифр</p>
                <p className="font-mono">{product.cipher}</p>
              </div>
              <div>
                <p className="text-muted-foreground font-medium">Курс Id</p>
                <p className="font-mono">{product.courseId}</p>
              </div>
              <div>
                <p className="text-muted-foreground font-medium">Стоимость</p>
                <p className="font-mono">
                  {product.amount} {product.currency}
                </p>
              </div>
              <div>
                <p className="text-muted-foreground font-medium">Путь к файлу</p>
                <p className="font-mono">{product.filePath}</p>
              </div>
              <div>
                <p className="text-muted-foreground font-medium">Дата добавления</p>
                <p className="font-mono">{formattedDate}</p>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>
      <div className="space-y-6">
        <Button size="sm" className="w-fit">
          <a href={pdfUrl} target="_blank" rel="noopener noreferrer">
            Посмотреть PDF
          </a>
        </Button>
      </div>
    </div>
  );
}
