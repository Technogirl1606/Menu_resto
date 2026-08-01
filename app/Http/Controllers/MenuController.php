<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Inertia\Inertia;

class MenuController extends Controller
{
    // GET /  → page publique affichée après le scan du QR code
    public function index()
    {
       $categories = Category::where('is_active', true)
        ->orderBy('position')
        ->with(['items' => fn ($q) => $q->where('is_available', true)])
        ->get()
        ->filter(fn ($category) => $category->isCurrentlyAvailable())
        ->values();

    return Inertia::render('Menu', [
        'categories' => $categories,
    ]);
    }
}