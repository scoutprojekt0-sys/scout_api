<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\SEOService;
use Illuminate\Http\Request;

class SEOController extends Controller
{
    protected $seoService;

    public function __construct(SEOService $seoService)
    {
        $this->seoService = $seoService;
    }

    /**
     * Generate and return sitemap.xml
     */
    public function sitemap()
    {
        $xml = $this->seoService->generateSitemap();

        return response($xml, 200)
            ->header('Content-Type', 'application/xml');
    }

    /**
     * Generate and return robots.txt
     */
    public function robots()
    {
        $txt = $this->seoService->generateRobotsTxt();

        return response($txt, 200)
            ->header('Content-Type', 'text/plain');
    }

    /**
     * Get SEO meta for a page
     */
    public function getMeta(Request $request)
    {
        $request->validate([
            'page_type' => 'required|string',
            'page_id' => 'required|integer',
        ]);

        $meta = $this->seoService->getMeta($request->page_type, $request->page_id);

        if (!$meta) {
            return response()->json([
                'ok' => false,
                'message' => 'SEO meta not found',
            ], 404);
        }

        return response()->json([
            'ok' => true,
            'data' => $meta,
        ]);
    }

    /**
     * Update SEO meta for a page
     */
    public function updateMeta(Request $request)
    {
        $request->validate([
            'page_type' => 'required|string',
            'page_id' => 'required|integer',
            'title' => 'required|string|max:60',
            'description' => 'nullable|string|max:160',
            'keywords' => 'nullable|string',
        ]);

        $this->seoService->saveMeta(
            $request->page_type,
            $request->page_id,
            $request->except(['page_type', 'page_id'])
        );

        return response()->json([
            'ok' => true,
            'message' => 'SEO meta updated successfully',
        ]);
    }
}
