<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HelpCategory;
use App\Models\HelpArticle;
use App\Models\FAQ;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HelpController extends Controller
{
    // Yardım Kategorilerini Getir
    public function getCategories(): JsonResponse
    {
        $categories = HelpCategory::with('articles')
            ->orderBy('order')
            ->get();

        return response()->json([
            'ok' => true,
            'data' => $categories,
        ]);
    }

    // Yardım Makalesini Getir
    public function getArticle(string $slug): JsonResponse
    {
        $article = HelpArticle::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        $article->incrementViews();

        return response()->json([
            'ok' => true,
            'data' => $article,
        ]);
    }

    // Kategorideki Makaleleri Getir
    public function getCategoryArticles(string $categorySlug): JsonResponse
    {
        $category = HelpCategory::where('slug', $categorySlug)
            ->firstOrFail();

        $articles = HelpArticle::where('category_id', $category->id)
            ->where('is_published', true)
            ->orderBy('order')
            ->paginate(10);

        return response()->json([
            'ok' => true,
            'category' => $category,
            'data' => $articles,
        ]);
    }

    // Makaleyi Faydalı İşaretle
    public function markArticleHelpful(string $slug): JsonResponse
    {
        $article = HelpArticle::where('slug', $slug)->firstOrFail();
        $article->markHelpful();

        return response()->json([
            'ok' => true,
            'message' => 'Geri bildiriminiz kaydedildi.',
        ]);
    }

    // Makaleyi Faydalı Değil İşaretle
    public function markArticleUnhelpful(string $slug): JsonResponse
    {
        $article = HelpArticle::where('slug', $slug)->firstOrFail();
        $article->markUnhelpful();

        return response()->json([
            'ok' => true,
            'message' => 'Geri bildiriminiz kaydedildi.',
        ]);
    }

    // FAQ Listesi (User Type'a göre)
    public function getFAQ(Request $request): JsonResponse
    {
        $userType = $request->user()->role ?? 'all';

        $faq = FAQ::where('is_active', true)
            ->where(function($q) use ($userType) {
                $q->where('user_type', $userType)
                  ->orWhere('user_type', 'all');
            })
            ->orderBy('order')
            ->paginate(15);

        return response()->json([
            'ok' => true,
            'data' => $faq,
        ]);
    }

    // FAQ'yu Faydalı İşaretle
    public function markFAQHelpful(int $faqId): JsonResponse
    {
        $faq = FAQ::findOrFail($faqId);
        $faq->markHelpful();

        return response()->json([
            'ok' => true,
            'message' => 'Geri bildiriminiz kaydedildi.',
        ]);
    }

    // Yardım Ara
    public function search(Request $request): JsonResponse
    {
        $query = $request->input('q');

        $articles = HelpArticle::where('is_published', true)
            ->where(function($q) use ($query) {
                $q->where('title', 'like', "%$query%")
                  ->orWhere('content', 'like', "%$query%")
                  ->orWhere('meta_description', 'like', "%$query%");
            })
            ->paginate(10);

        return response()->json([
            'ok' => true,
            'query' => $query,
            'data' => $articles,
        ]);
    }
}
