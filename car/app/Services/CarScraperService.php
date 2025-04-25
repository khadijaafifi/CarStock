<?php

namespace App\Services;

use App\Models\Site;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class CarScraperService
{
    protected $apiKey;
    protected $apiUrl = 'https://api.openai.com/v1/chat/completions';
    protected $cacheTtl = 86400; // 24 heures

    public function __construct()
    {
        $this->apiKey = config('services.openai.key');
    }

    /**
     * Récupère les modèles de véhicules depuis un site en utilisant l'IA
     */
    public function getVehicleModels(string $brand): array
    {
        $site = Site::where('name', $brand)->first();

        if (!$site) {
            Log::error("Site non trouvé pour la marque: $brand");
            return $this->errorResponse("Site non configuré pour cette marque");
        }

        return Cache::remember("ai_scraped_models_{$brand}", $this->cacheTtl, function () use ($site, $brand) {
            try {
                // 1. Récupération du contenu HTML
                $html = $this->fetchSiteContent($site->url);
                if (!$html['success']) {
                    return $this->errorResponse($html['error']);
                }

                // 2. Nettoyage du HTML (optionnel mais recommandé)
                $cleanedHtml = $this->cleanHtml($html['content']);

                // 3. Extraction avec IA
                $prompt = $this->createPrompt($brand, $cleanedHtml);
                $extractedData = $this->callAiApi($prompt);

                // 4. Validation des données
                $validatedData = $this->validateAiResponse($extractedData, $brand);

                return [
                    'success' => true,
                    'brand' => $brand,
                    'models' => $validatedData['models'],
                    'features' => $validatedData['features'] ?? [],
                    'metadata' => [
                        'source_url' => $site->url,
                        'extraction_method' => 'AI Prompt',
                        'timestamp' => now()->toDateTimeString(),
                    ],
                ];

            } catch (\Exception $e) {
                Log::error("Erreur AI scraping pour $brand: " . $e->getMessage());
                return $this->errorResponse("Erreur de traitement: " . $e->getMessage());
            }
        });
    }

    /**
     * Récupère le contenu HTML d'un site
     */
    protected function fetchSiteContent(string $url): array
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
            ])->timeout(20)->get($url);

            if (!$response->successful()) {
                throw new \Exception("HTTP Error: " . $response->status());
            }

            return [
                'success' => true,
                'content' => $response->body()
            ];

        } catch (\Exception $e) {
            Log::error("Erreur de récupération du contenu: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Nettoie le HTML pour réduire la taille
     */
    protected function cleanHtml(string $html): string
    {
        // Supprime les scripts, styles, commentaires, etc.
        $html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $html);
        $html = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $html);
        $html = preg_replace('/<!--(.*?)-->/', '', $html);
        $html = preg_replace('/\s+/', ' ', $html);
        
        return substr(trim($html), 0, 15000); // Limite la taille pour l'API
    }

    /**
     * Crée le prompt pour l'IA
     */
    protected function createPrompt(string $brand, string $html): string
    {
        return <<<PROMPT
        Vous êtes un assistant spécialisé dans l'extraction d'informations sur les véhicules automobiles.
        À partir du code HTML suivant (d'une page de constructeur automobile), extrayez les informations demandées.

        Marque concernée: $brand

        Instructions:
        1. Identifiez tous les modèles de véhicules mentionnés
        2. Pour chaque modèle, extrayez les caractéristiques principales si disponibles
        3. Ignorez les informations non pertinentes (promotions, services, etc.)
        4. Retournez les données au format JSON strict avec la structure:
        {
            "models": [
                {
                    "name": "Nom du modèle",
                    "price": "Prix ou gamme de prix",
                    "motorization": "Type de motorisation",
                    "features": ["caractéristique 1", "caractéristique 2"]
                }
            ]
        }

        HTML à analyser:
        $html
        PROMPT;
    }

    /**
     * Appelle l'API d'IA
     */
    protected function callAiApi(string $prompt): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post($this->apiUrl, [
            'model' => 'gpt-4-turbo',
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0.2,
            'max_tokens' => 2000,
        ]);

        if (!$response->successful()) {
            throw new \Exception("API Error: " . $response->body());
        }

        $content = $response->json('choices.0.message.content');
        
        // Essaye d'extraire le JSON de la réponse
        if (preg_match('/```json\n(.*?)\n```/s', $content, $matches)) {
            $content = $matches[1];
        }

        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("Réponse JSON invalide de l'API");
        }

        return $data;
    }

    /**
     * Valide la réponse de l'IA
     */
    protected function validateAiResponse(array $data, string $brand): array
    {
        if (!isset($data['models']) || !is_array($data['models'])) {
            throw new \Exception("Structure de données invalide");
        }

        $validModels = [];
        foreach ($data['models'] as $model) {
            if (empty($model['name'])) continue;

            // Filtre basique
            $name = trim($model['name']);
            if (strlen($name) < 2) continue;
            if (stripos($name, $brand) !== false) continue;

            $validModels[] = [
                'name' => $name,
                'price' => $model['price'] ?? 'Non spécifié',
                'motorization' => $model['motorization'] ?? 'Non spécifiée',
                'features' => $model['features'] ?? [],
            ];
        }

        return [
            'models' => $validModels,
            'features' => array_unique(array_merge(...array_column($validModels, 'features')))
        ];
    }

    /**
     * Formatte une réponse d'erreur
     */
    protected function errorResponse(string $message): array
    {
        return [
            'success' => false,
            'error' => $message,
            'models' => [],
            'metadata' => [
                'timestamp' => now()->toDateTimeString(),
            ],
        ];
    }

    /**
     * Vide le cache pour une marque
     */
    public function clearCache(string $brand): bool
    {
        return Cache::forget("ai_scraped_models_{$brand}");
    }
}