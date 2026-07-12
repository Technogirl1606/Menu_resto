<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Item;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    // GET /admin/items → page Vue "Admin/Items/Index.vue"
    public function index()
    {
        return Inertia::render('Admin/Items/Index', [
            'items'      => Item::with('category')->orderBy('position')->get(),
            'categories' => Category::orderBy('position')->get(['id', 'name']),
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
 
        // Inertia recharge automatiquement la page avec les données à jour
        return redirect()->back()->with('success', 'Plat ajouté.');
    }
 
    // POST /admin/items/{item}  (avec _method=PUT dans le FormData, requis pour l'upload de fichier)
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
