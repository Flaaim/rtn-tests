import { fetchProductsPaginationAction } from "@/actions/product";
import AdminBreadcrumbs from "@/components/Admin/AdminBreadcrumbs";
import { AlertCircle } from "lucide-react";
import AddProfileDialog from "@/components/Admin/Profile/AddProfileDialog";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import Link from "next/link";
import Pagination from "@/components/Pagination/Pagination";
import { ProductDTO } from "@/interfaces/product.interface";

interface AdminProductsPageProps {
  searchParams: Promise<{ page?: string; perPage?: string }>;
}

export default async function AdminProductsPage({ searchParams }: AdminProductsPageProps) {
  const currentPage = Number((await searchParams).page) || 1;
  const perPage = Number((await searchParams).perPage) || 15;
  const result = await fetchProductsPaginationAction(currentPage, perPage);

  if (!result.ok || !result.data) {
    return (
      <div className="space-y-6">
        <AdminBreadcrumbs items={[{ title: "Продукты" }]} />
        <div className="flex items-center justify-between">
          <h1 className="text-3xl font-bold">Продукты</h1>
        </div>
        <div className="mx-auto max-w-4xl p-4 md:p-8">
          <div className="flex min-h-[40vh] flex-col items-center justify-center space-y-4 text-center">
            <div className="flex size-16 items-center justify-center rounded-full bg-destructive/10 text-destructive">
              <AlertCircle className="size-8" />
            </div>
            <h2 className="text-xl font-semibold">Не удалось загрузить продукты</h2>
            <p className="text-sm text-muted-foreground">
              {result.error ?? "Произошла непредвиденная ошибка"}
            </p>
          </div>
        </div>
      </div>
    );
  }

  const paginatedProducts = result.data;
  const products = result.data.items;

  return (
    <div className="space-y-6">
      <AdminBreadcrumbs items={[{ title: "Продукты" }]} />
      <div className="flex items-center justify-between">
        <h1 className="text-3xl font-bold">Продукты</h1>
        <AddProfileDialog />
      </div>
      <div className="rounded-md border bg-white">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Id</TableHead>
              <TableHead>Шифр</TableHead>
              <TableHead>Стоимость</TableHead>
              <TableHead>Дата добавления</TableHead>
              <TableHead>Быстрые действия</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {products.map((product: ProductDTO) => (
              <TableRow key={product.productId}>
                <TableCell className="font-medium">
                  <Link href={`/admin/products/${product.productId}`} className="hover:underline">
                    {product.productId}
                  </Link>
                </TableCell>
                <TableCell className="font-medium">{product.cipher}</TableCell>
                <TableCell className="font-medium">
                  {product.amount} {product.currency}
                </TableCell>
                <TableCell className="font-medium">
                  {new Date(product.addedAt).toLocaleDateString("ru-RU")}
                </TableCell>
                <TableCell></TableCell>
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </div>
      <Pagination
        currentPage={currentPage}
        totalPages={paginatedProducts.totalPages}
        baseUrl="/admin/products"
      />
    </div>
  );
}
