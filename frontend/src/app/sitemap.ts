import { MetadataRoute } from "next";
import { fetchPublicCategoryTreeAction } from "@/actions/category";
import { fetchPublicTestsByCategoryAction } from "@/actions/test";

export const revalidate = 86400; // Обновляем раз в сутки

export default async function sitemap(): Promise<MetadataRoute.Sitemap> {
  const baseUrl = "https://rtn-tests.ru";
  const now = new Date();

  const staticPages: MetadataRoute.Sitemap = [
    { url: `${baseUrl}/`, lastModified: now, changeFrequency: "daily", priority: 1.0 },
    { url: `${baseUrl}/catalog`, lastModified: now, changeFrequency: "weekly", priority: 0.8 },
  ];

  try {
    const treeResult = await fetchPublicCategoryTreeAction();

    if (!treeResult.ok || !treeResult.data) {
      return staticPages;
    }

    // Используем Promise.all, так как внутри map будут асинхронные вызовы
    const dynamicUrlsArrays = await Promise.all(
      treeResult.data.map(async (parentCategory) => {
        // 1. URL родительской категории
        const parentUrl: MetadataRoute.Sitemap[number] = {
          url: `${baseUrl}/catalog/${parentCategory.slug}`,
          lastModified: now,
          changeFrequency: "weekly",
          priority: 0.6,
        };

        // 2. Получаем URL-ы подкатегорий и их тестов
        const childrenAndTestsUrlsArrays = await Promise.all(
          (parentCategory.children ?? []).map(async (subcategory) => {
            // URL самой подкатегории
            const subcategoryUrl: MetadataRoute.Sitemap[number] = {
              url: `${baseUrl}/catalog/${parentCategory.slug}/${subcategory.slug}`,
              lastModified: now,
              changeFrequency: "weekly",
              priority: 0.6,
            };

            const testsResult = await fetchPublicTestsByCategoryAction(subcategory.slug);

            let testUrls: MetadataRoute.Sitemap = [];
            if (testsResult.ok && testsResult.data) {
              testUrls = testsResult.data.map((test) => ({
                url: `${baseUrl}/catalog/${parentCategory.slug}/${subcategory.slug}/${test.slug}`,
                lastModified: test.createdAt,
                changeFrequency: "monthly",
                priority: 0.5,
              }));
            }

            return [subcategoryUrl, ...testUrls];
          })
        );

        // Объединяем родителя, подкатегории и их тесты
        return [parentUrl, ...childrenAndTestsUrlsArrays.flat()];
      })
    );

    const allDynamicUrls = dynamicUrlsArrays.flat();

    return [...staticPages, ...allDynamicUrls];
  } catch (error) {
    console.error("Ошибка при генерации sitemap:", error);
    return staticPages;
  }
}
