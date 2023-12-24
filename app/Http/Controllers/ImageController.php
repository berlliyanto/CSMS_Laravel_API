<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\AssignNotification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ImageController extends Controller
{
    public function show($file)
    {
        $path = public_path('storage/images/' . $file); //-----> Use in Development
        //$path = "/home/apli9687/public_html/storage/images/" . $file; // ----> Use in Production

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
        $user = User::where('id', $request->id)->first();
        $user->notify(new AssignNotification("CSMS Tugas","test notification"));
        return response()->json([
            'query' => $user
        ]);
    }

    public function downloadFile($file) {

        $path = public_path('storage/images/' . $file); // -----> Use in Development
        //$path = "/home/apli9687/public_html/storage/images/" . $file; // ----> Use in Production

        if (!File::exists($path)) {
            abort(404);
        }

        return response()->download($path);
    }
}
