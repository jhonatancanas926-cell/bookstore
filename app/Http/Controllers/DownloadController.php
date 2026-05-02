<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class DownloadController extends Controller
{
    public function index()
    {
        // Mientras no exista el modelo DownloadToken/etc, devolvemos colección vacía
        $downloads = collect();

        if (class_exists(\App\Models\DownloadToken::class)) {
            try {
                $downloads = auth()->user()->downloads()
                    ->with('book')
                    ->where('expires_at', '>', now())
                    ->get();
            } catch (\Exception $e) {
                // Ignore
            }
        }

        return view('downloads.index', compact('downloads'));
    }

    public function download($orderId, $bookId)
    {
        // TODO: Validar propiedad y descargar archivo
        return response()->json(['message' => 'Descarga iniciada']);
    }
}
