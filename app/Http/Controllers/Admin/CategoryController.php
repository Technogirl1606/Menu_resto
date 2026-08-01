<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function updateAvailability(Request $request, Category $category)
    {
        $data = $request->validate([
            'available_from' => ['nullable', 'date_format:H:i'],
            'available_to' => ['nullable', 'date_format:H:i'],
        ]);

        $category->update($data);

        return redirect()->back()->with('success', 'Horaires mis à jour.');
    }
}