<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Resources\MetaResource;
use App\Models\Page;
use Inertia\Inertia;

class PageController extends Controller
{
    public function show($slug)
    {
        $page = Page::where('slug', $slug)
            ->active()
            ->with('meta')
            ->firstOrFail();

        return Inertia::render('Page/Show', [
            'page' => [
                'id' => $page->id,
                'title' => $page->title,
                'content' => $page->content,
                'image_url' => $page->image ? asset('storage/' . $page->image) : null,
                'meta' => MetaResource::make($page->meta),
            ],
        ]);
    }
}
