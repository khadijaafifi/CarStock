<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\OpenAIService;

class TestOpenAI extends Command
{
    protected $signature = 'openai:test';
    protected $description = 'Test the OpenAI API connection';

    protected $openAIService;

    public function __construct(OpenAIService $openAIService)
    {
        parent::__construct();
        $this->openAIService = $openAIService;
    }

    public function handle()
    {
        $question = "What is the capital of France?";
        $answer = $this->openAIService->askQuestion($question);
        $this->info($answer);  // Afficher la réponse de l'API dans la console
    }
}
