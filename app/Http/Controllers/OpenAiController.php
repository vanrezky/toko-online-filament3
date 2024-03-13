<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;

class OpenAiController extends Controller
{

    public function index()
    {
    }

    public function ask($prompt)
    {
        $response = $this->askToChatGPT($prompt);
        return response($response, 200);
    }

    private function askToChatGPT($prompt)
    {
        try {
            $result = OpenAI::chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'user', 'content' => 'Hello!'],
                ],
            ]);

            return $result->choices[0]->message->content;
        } catch (\Exception $e) {
            Log::error($e);
            return $e->getMessage();
        }
    }
}
