<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class FileController extends Controller
{
    // Function to return the view for file upload
    function create()
    {
        return view('index');
    }

    // Function to process the uploaded file based on encryption/decryption type
    function process(Request $request)
    {
        if ($request->type == 'enc') {
            return $this->encrypt($request);
        } else if ($request->type == 'dec') {
            return $this->decrypt($request);
        } else {
            // Return error response if neither encryption nor decryption type is specified
            return response()->json(['status' => 'failed'], 401);
        }
    }

    // Function to encrypt the uploaded file
    function encrypt(Request $request)
    {
        $encryptionKey = config('app.key');

        $file = $request->file('file');
        $encFileName = $file->getClientOriginalName();
        $content = file_get_contents($file);
        $iv = 'dsfa9p8y098hasdf';
        // Encrypt the content using AES-256-CBC encryption algorithm
        $encryptedContent = openssl_encrypt($content, 'aes-256-cbc', $encryptionKey, 0, $iv);

        // Store the encrypted content
        Storage::put($encFileName, $encryptedContent);

        // Download the encrypted file
        return Storage::download($encFileName);
    }

    // Function to decrypt the uploaded file
    function decrypt(Request $request)
    {
        $encryptionKey = config('app.key');

        $file = $request->file('file');
        $decFileName = $file->getClientOriginalName();
        $content = file_get_contents($file);
        $iv = 'dsfa9p8y098hasdf';
        // Decrypt the content using AES-256-CBC decryption algorithm
        $decryptedContent = openssl_decrypt($content, 'aes-256-cbc', $encryptionKey, 0, $iv);

        // Store the decrypted content
        Storage::put($decFileName, $decryptedContent);

        // Download the decrypted file
        return Storage::download($decFileName);
    }
}
