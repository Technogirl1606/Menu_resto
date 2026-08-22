<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{

public function index(): Response
{
    return Inertia::render('Admin/Categories/Index', [
        'categories' => Category::orderBy('position')->get(),
    ]);
}
    public function updateAvailability(Request $request, Category $category)
    {
        $data = $request->validate([
            'available_from' => ['nullable', 'date_format:H:i'],
            'available_to' => ['nullable', 'date_format:H:i'],
        ]);

        $category->update($data);

        return redirect()->back()->with('success', 'Horaires mis à jour.');
    }

      public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $data['slug'] = Str::slug($data['name']);
        $data['is_active'] = true;
        $data['position'] = Category::max('position') + 1;

        Category::create($data);

        return redirect()->back()->with('success', 'Catégorie ajoutée.');
    }


    public function destroy(Category $category)
{
    $category->delete();
    return redirect()->back()->with('success', 'Catégorie supprimée.');
}
}