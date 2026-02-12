<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductImageService
{
    protected array $config;

    public function __construct()
    {
        $this->config = [
            'enabled' => config('images.auto_fetch', true),
            'source' => config('images.source', 'unsplash'),
            'default_image' => config('images.default', '/images/producto-default.svg'),
            'cache_hours' => 24,
        ];
    }

    /**
     * Get image URL for a product
     * Priority: uploaded > manual > auto > default
     */
    public function getImageUrl($product): string
    {
        // 1. Uploaded image (imagen_path)
        if (!empty($product->imagen_path)) {
            return $this->ensureFullUrl($product->imagen_path);
        }

        // 2. Manual image (imagen_url)
        if (!empty($product->imagen_url) && $product->image_source === 'manual') {
            return $this->ensureFullUrl($product->imagen_url);
        }

        // 3. Auto image (cached)
        if ($this->config['enabled'] && $product->use_auto_image) {
            $categoria = $product->categoria?->nombre ?? 'producto';
            $unidad = $product->unidad ?? '';
            $autoImage = $this->getAutoImage($product->nombre, $categoria, $unidad, $product->id);
            if ($autoImage) {
                // Add cache-busting parameter based on product updated_at
                $timestamp = $product->updated_at ? $product->updated_at->timestamp : time();
                $separator = str_contains($autoImage, '?') ? '&' : '?';
                return $autoImage . $separator . 'v=' . $timestamp;
            }
        }

        // 4. Product has any imagen_url
        if (!empty($product->imagen_url)) {
            return $this->ensureFullUrl($product->imagen_url);
        }

        // 5. Default
        return $this->getDefaultImage();
    }

    /**
     * Get auto-generated image based on product name, category, and unit
     */
    public function getAutoImage(string $productName, string $category = 'producto', string $unidad = '', int $productId = 0): ?string
    {
        $cacheKey = "product_image_{$productId}";

        return Cache::remember($cacheKey, now()->addHours($this->config['cache_hours']), function () use ($productName, $category, $unidad, $productId) {
            return $this->fetchImageFromSource($productName, $category, $unidad, $productId);
        });
    }

    /**
     * Fetch image from configured source
     */
    protected function fetchImageFromSource(string $productName, string $category, string $unidad, int $productId): ?string
    {
        [$mainProduct, $variety] = $this->normalizeSearchTerm($productName);

        // Determine if we need bulk/group images based on unit
        $isBulk = $this->isBulkUnit($unidad);

        // Get category context for better image results
        $categoryContext = $this->getCategoryContext($category);

        switch ($this->config['source']) {
            case 'pixabay':
                return $this->fetchFromPixabay($mainProduct, $variety, $isBulk, $categoryContext);
            case 'pexels':
                return $this->fetchFromPexels($mainProduct, $variety, $isBulk, $categoryContext);
            case 'unsplash':
                return $this->fetchFromUnsplash($mainProduct, $variety, $isBulk, $categoryContext, $productId);
            default:
                return $this->getPlaceholderImage($mainProduct, $productId);
        }
    }

    /**
     * Get category context terms for better image search
     */
    protected function getCategoryContext(string $category): array
    {
        if (empty($category)) {
            return ['has_category' => false, 'terms' => ['food', 'fresh']];
        }

        $categoryLower = strtolower($category);

        // Map category to specific food terms
        $contextMap = [
            'fruta' => ['fruit', 'fresh fruit', 'ripe'],
            'verdura' => ['vegetable', 'fresh vegetable', 'produce'],
            'legumbre' => ['legume', 'vegetable', 'produce'],
            'carne' => ['meat', 'fresh meat', 'butcher'],
            'pescado' => ['fish', 'seafood', 'fresh fish'],
            'lacteo' => ['dairy', 'fresh dairy'],
            'cereal' => ['grain', 'cereal', 'food'],
            'bebida' => ['beverage', 'drink'],
        ];

        foreach ($contextMap as $key => $terms) {
            if (str_contains($categoryLower, $key)) {
                return ['has_category' => true, 'terms' => $terms];
            }
        }

        // Default: generic food terms
        return ['has_category' => true, 'terms' => ['food', 'fresh', 'market']];
    }

    /**
     * Check if unit requires bulk/group images
     */
    protected function isBulkUnit(string $unidad): bool
    {
        $bulkUnits = ['kg', 'kilo', 'kilogramo', 'lt', 'litro', 'lb', 'libra', 'ton', 'tonelada', 'costal', 'saco', 'caja'];
        $unidadLower = strtolower(trim($unidad));

        foreach ($bulkUnits as $bulk) {
            if (str_contains($unidadLower, $bulk)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Fetch from Unsplash API
     */
    protected function fetchFromUnsplash(string $mainProduct, string $variety, bool $isBulk, array $categoryContext, int $productId): ?string
    {
        $apiKey = config('services.unsplash.key');
        if (!$apiKey) {
            return $this->fetchFromPixabay($mainProduct, $variety, $isBulk, $categoryContext);
        }

        try {
            $contextTerm = $categoryContext['terms'][0] ?? 'food';
            $query = $variety ? "{$variety} {$mainProduct} {$contextTerm}" : "{$mainProduct} {$contextTerm}";

            if ($isBulk) {
                $query .= ' bunch';
            }

            $response = Http::withOptions([
                'verify' => false,
            ])->withHeaders([
                'Authorization' => 'Client-ID ' . $apiKey,
            ])->get('https://api.unsplash.com/search/photos', [
                'query' => $query,
                'per_page' => 1,
                'orientation' => 'landscape',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data['results'][0]['urls']['regular'])) {
                    return $data['results'][0]['urls']['regular'];
                }
            }
        } catch (\Exception $e) {
            \Log::warning("Unsplash API error: " . $e->getMessage());
        }

        return $this->fetchFromPixabay($mainProduct, $variety, $isBulk, $categoryContext);
    }

    /**
     * Fetch from Pexels API
     */
    protected function fetchFromPexels(string $mainProduct, string $variety, bool $isBulk, array $categoryContext): ?string
    {
        $apiKey = config('services.pexels.key');
        if (!$apiKey) {
            return $this->fetchFromPixabay($mainProduct, $variety, $isBulk, $categoryContext);
        }

        try {
            $contextTerm = $categoryContext['terms'][0] ?? 'food';
            $query = $variety ? "{$variety} {$mainProduct} {$contextTerm}" : "{$mainProduct} {$contextTerm}";

            if ($isBulk) {
                $query .= ' bunch';
            }

            $response = Http::withOptions([
                'verify' => false,
            ])->withHeaders([
                'Authorization' => $apiKey,
            ])->get('https://api.pexels.com/v1/search', [
                'query' => $query,
                'per_page' => 1,
                'orientation' => 'landscape',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data['photos'][0]['src']['medium'])) {
                    return $data['photos'][0]['src']['medium'];
                }
            }
        } catch (\Exception $e) {
            \Log::warning("Pexels API error: " . $e->getMessage());
        }

        return $this->fetchFromPixabay($mainProduct, $variety, $isBulk, $categoryContext);
    }

    /**
     * Fetch from Pixabay API (free, generous limits)
     */
    protected function fetchFromPixabay(string $mainProduct, string $variety, bool $isBulk, array $categoryContext): ?string
    {
        $apiKey = config('services.pixabay.key');
        if (!$apiKey) {
            return $this->getPlaceholderImage($mainProduct, 0);
        }

        try {
            // Build search queries with category context
            // Priority: variety + product + category context + presentation
            $queries = [];
            $contextTerms = $categoryContext['terms'];

            // Strategy for food products: focus on edible part, not the plant
            // Add "market" or "food" to avoid plant/tree images

            // If has variety, use it with category context
            if (!empty($variety)) {
                foreach ($contextTerms as $term) {
                    $queries[] = "{$variety} {$mainProduct} {$term}";
                }
                $queries[] = "{$mainProduct} {$variety} market";
            }

            // Add category-specific terms
            foreach ($contextTerms as $term) {
                if ($isBulk) {
                    $queries[] = "{$mainProduct} {$term} bunch";
                    $queries[] = "{$mainProduct} {$term} pile";
                } else {
                    $queries[] = "{$mainProduct} {$term}";
                }
            }

            // Market/food context to avoid plants
            $queries[] = "{$mainProduct} market fresh";
            $queries[] = "{$mainProduct} food";

            // Fallback
            $queries[] = $mainProduct;

            foreach ($queries as $query) {
                $response = Http::withOptions([
                    'verify' => false, // Disable SSL verification for Windows dev
                ])->timeout(10)->get('https://pixabay.com/api/', [
                    'key' => $apiKey,
                    'q' => $query,
                    'image_type' => 'photo',
                    'per_page' => 3,
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    if (!empty($data['hits'][0]['webformatURL']) && $data['totalHits'] > 0) {
                        return $data['hits'][0]['webformatURL'];
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::warning("Pixabay API error: " . $e->getMessage());
        }

        return $this->getPlaceholderImage($mainProduct, 0);
    }

    /**
     * Generate placeholder image URL
     */
    protected function getPlaceholderImage(string $searchTerm, int $productId): string
    {
        // Use placeholder service with product-specific colors
        $colors = ['4ade80', '22c55e', '16a34a', '15803d', '166534'];
        $color = $colors[$productId % count($colors)];
        $text = urlencode(Str::limit($searchTerm, 15));

        return "https://via.placeholder.com/400x300/{$color}/ffffff?text={$text}";
    }

    /**
     * Normalize product name for image search
     * Returns array with: [main_product, variety/brand, context]
     */
    protected function normalizeSearchTerm(string $name): array
    {
        // Remove measurements (but keep them tracked separately)
        $name = preg_replace('/\s*(kg|kilo|gr|gramo|lb|libra|pza|pieza|bolsa|caja)\s*/i', ' ', $name);

        // Remove size descriptors
        $name = preg_replace('/\s*(grande|chico|mediano|extra|super|jumbo)\s*/i', ' ', $name);

        // Remove quality descriptors
        $name = preg_replace('/\s*(primera|segunda|tercera|calidad|premium|select)\s*/i', ' ', $name);

        // Clean up spaces
        $name = preg_replace('/\s+/', ' ', trim($name));

        // Split into words
        $words = explode(' ', $name);

        // First word is always the main product
        $mainProduct = strtolower($words[0] ?? $name);

        // Second word (if exists) is variety/brand - keep it!
        $variety = isset($words[1]) ? strtolower($words[1]) : '';

        return [$mainProduct, $variety];
    }

    /**
     * Get default image URL
     */
    public function getDefaultImage(): string
    {
        return asset($this->config['default_image']);
    }

    /**
     * Ensure URL is absolute
     */
    protected function ensureFullUrl(string $url): string
    {
        if (Str::startsWith($url, ['http://', 'https://'])) {
            return $url;
        }

        if (Str::startsWith($url, '/storage/')) {
            return asset($url);
        }

        if (Str::startsWith($url, 'storage/')) {
            return asset('/' . $url);
        }

        return asset('/storage/' . ltrim($url, '/'));
    }

    /**
     * Upload product image
     */
    public function uploadImage($file, int $empresaId, int $productId): string
    {
        $filename = "producto_{$productId}_" . time() . '.' . $file->getClientOriginalExtension();
        $path = "productos/{$empresaId}";

        $file->storeAs($path, $filename, 'public');

        return "/storage/{$path}/{$filename}";
    }

    /**
     * Clear cached image for a product
     */
    public function clearCache(int $productId): void
    {
        Cache::forget("product_image_{$productId}");
    }
}
