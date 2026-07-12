<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Response;
use Inertia\Inertia;

class QrCodeController extends Controller
{
    // GET /admin/qr-code → page admin qui affiche le QR code + son URL cible
    public function show()
    {
        return Inertia::render('Admin/QrCode', [
            'menuUrl' => url('/menu'),
        ]);
    }

    // GET /admin/qr-code/image → l'image PNG du QR code elle-même
    public function image(): Response
    {
        $result = (new Builder(
            writer: new PngWriter(),
            data: url('/menu'),
            size: 400,
            margin: 12,
        ))->build();

        return response($result->getString(), 200, [
            'Content-Type' => $result->getMimeType(),
        ]);
    }
}