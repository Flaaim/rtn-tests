import { MetadataRoute } from "next";
import { fetchPublicCategoryTreeAction } from "@/actions/category";
import { fetchPublicTestsByCategoryAction } from "@/actions/test";
import { fetchQuestionIdsForSitemapAction } from "@/actions/question";

export const revalidate = 86400; // Обновляем раз в сутки

export default async function sitemap(): Promise<MetadataRoute.Sitemap> {
  const baseUrl = "https://rtn-tests.ru";
  const now = new Date();

  const staticPages: MetadataRoute.Sitemap = [
    { url: `${baseUrl}/`, lastModified: now, changeFrequency: "daily", priority: 1.0 },
    { url: `${baseUrl}/catalog`, lastModified: now, changeFrequency: "weekly", priority: 0.8 },
  ];

  try {
    let dynamicUrls: MetadataRoute.Sitemap = [];
    const treeResult = await fetchPublicCategoryTreeAction();

    if (treeResult.ok && treeResult.data) {
      const dynamicUrlsArrays = await Promise.all(
        treeResult.data.map(async (parentCategory) => {
          const parentUrl: MetadataRoute.Sitemap[number] = {
            url: `${baseUrl}/catalog/${parentCategory.slug}`,
            lastModified: now,
            changeFrequency: "weekly",
            priority: 0.7,
          };

          const childrenAndTestsUrlsArrays = await Promise.all(
            (parentCategory.children ?? []).map(async (subcategory) => {
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
                  lastModified: test.createdAt, // Если есть дата обновления, лучше её
                  changeFrequency: "monthly",
                  priority: 0.8, // Тесты важнее категорий
                }));
              }

              return [subcategoryUrl, ...testUrls];
            })
          );

          return [parentUrl, ...childrenAndTestsUrlsArrays.flat()];
        })
      );
      dynamicUrls = dynamicUrlsArrays.flat();
    }

    let questionUrls: MetadataRoute.Sitemap = [];
    const questionsResult = await fetchQuestionIdsForSitemapAction();

    if (questionsResult.ok && questionsResult.data) {
      questionUrls = questionsResult.data.map((id) => ({
        url: `${baseUrl}/questions/${id}`,
        lastModified: now,
        changeFrequency: "monthly",
        priority: 0.5,
      }));
    }

    return [...staticPages, ...dynamicUrls, ...questionUrls];
  } catch (error) {
    console.error("Ошибка при генерации sitemap:", error);
    return staticPages;
  }
}
