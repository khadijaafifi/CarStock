<?php

namespace App\Services;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;
use Illuminate\Support\Facades\Log;

class OpelScraperService
{
    public function getModels()
    {
        try {
            $client = HttpClient::create();
            $response = $client->request('GET', 'https://www.opel.ma/fr/');

            $html = $response->getContent();
            $crawler = new Crawler($html);

            // À adapter à la structure réelle du site Opel
            $models = $crawler->filter('h3')->each(function (Crawler $node) {
                return trim($node->text());
            });

            return array_slice($models, 0, 5);
        } catch (\Exception $e) {
            Log::error('Erreur scraping Opel : ' . $e->getMessage());
            return ['Erreur de scraping Opel'];
        }
    }
}
