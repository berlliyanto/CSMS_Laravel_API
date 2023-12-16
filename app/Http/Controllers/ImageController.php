<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ImageController extends Controller
{
    public function show($file)
    {
        $path = public_path('storage/images/' . $file);

        if (!File::exists($path)) {
            abort(404);
        }

        $files = File::get($path);
        $type = File::mimeType($path);
        $response = Response::make($files, 200);
        $response->header("Content-Type", $type);

        return $response;
    }

    public function tes(Request $request)
    {
        $query = $request->query('query');
        return response()->json([
            'query' => $query
        ]);
    }
}
