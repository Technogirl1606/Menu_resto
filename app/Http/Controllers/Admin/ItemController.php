<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Log;


class ItemController extends Controller
{
    // GET /admin/items/{category?} → page Vue "Admin/Items/Index.vue"
    // Sans paramètre : affiche la première catégorie par défaut.
    public function index(?Category $category = null)
    {
        $category ??= Category::orderBy('position')->firstOrFail();

        return Inertia::render('Admin/Items/Index', [
            'activeCategory' => $category->only(['id', 'name', 'slug']),
            'items' => Item::where('category_id', $category->id)
                ->orderBy('position')
                ->get(),
            'categoryOptions' => Category::orderBy('position')->get(['id', 'name']),
        ]);
    }

    // POST /admin/items  (envoyé via useForm().post, multipart/form-data)
    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id'  => ['required', 'exists:categories,id'],
            'name'         => ['required', 'string', 'max:255'],
            'description'  => ['nullable', 'string'],
            'price'        => ['required', 'numeric', 'min:0'],
            'is_available' => ['boolean'],
            'image'        => ['nullable', 'image', 'max:4096'], // 4 Mo max
        ]);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('items', 'public');
        }

        Item::create($data);

        return redirect()->back()->with('success', 'Plat ajouté.');
    }

    // POST /admin/items/{item}/update  (avec forceFormData, requis pour l'upload de fichier)
    public function update(Request $request, Item $item)
    {
        $data = $request->validate([
            'category_id'  => ['sometimes', 'exists:categories,id'],
            'name'         => ['sometimes', 'string', 'max:255'],
            'description'  => ['nullable', 'string'],
            'price'        => ['sometimes', 'numeric', 'min:0'],
            'is_available' => ['boolean'],
            'image'        => ['nullable', 'image', 'max:4096'],
        ]);

        if ($request->hasFile('image')) {
            if ($item->image_path) {
                Storage::disk('public')->delete($item->image_path);
            }
            $data['image_path'] = $request->file('image')->store('items', 'public');
        }

        $item->update($data);

        return redirect()->back()->with('success', 'Plat mis à jour.');
    }

    // DELETE /admin/items/{item}
    public function destroy(Item $item)
    {
        if ($item->image_path) {
            Storage::disk('public')->delete($item->image_path);
        }
        $item->delete();

        return redirect()->back()->with('success', 'Plat supprimé.');
    }
}