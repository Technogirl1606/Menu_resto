<?php

namespace App\Http\Middleware;

use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;


class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $request->user(),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            // Disponible dans toutes les pages admin, pour construire les liens de la sidebar.
            // Vide sur les pages publiques (utilisateur non connecté) pour éviter une requête inutile.
            'categories' => $request->user()
                ? Category::orderBy('position')->get(['id', 'name', 'slug'])
                : [],
        ];
    }
}