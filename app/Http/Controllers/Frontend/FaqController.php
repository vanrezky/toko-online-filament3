<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Resources\FaqResource;
use App\Models\Faq;
use Inertia\Inertia;

class FaqController extends Controller
{
    public function __invoke()
    {
        $faqs = Faq::all();

        return Inertia::render('Faq/Index', [
            'faqs' => FaqResource::collection($faqs),
        ]);
    }
}
