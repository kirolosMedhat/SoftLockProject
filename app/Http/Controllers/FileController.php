<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class FileController extends Controller
{
    // Function to return the view for file upload
    public function create()
    {
        return view('index');
    }

    // Function to handle chunked file upload
    public function upload(Request $request)
    {
        $chunk = $request->file('file');
        $fileName = $request->input('fileName');
        $chunkIndex = $request->input('chunkIndex');
        $totalChunks = $request->input('totalChunks');

        $tempDir = storage_path('app/temp/' . $fileName);
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        $chunk->move($tempDir, $chunkIndex);

        if ($chunkIndex + 1 == $totalChunks) {
            // Combine chunks
            $finalPath = storage_path('app/' . $fileName);
            $output = fopen($finalPath, 'w');
            for ($i = 0; $i < $totalChunks; $i++) {
                $chunkPath = $tempDir . '/' . $i;
                $chunkContent = file_get_contents($chunkPath);
                fwrite($output, $chunkContent);
                unlink($chunkPath); // Delete the chunk
            }

            fclose($output);
            rmdir($tempDir);
            // Remove temp directory


        }

        return response()->json(['message' => 'success']);
    }

    // Function to encrypt the uploaded file
    public function encrypt(Request $request)
    {
        $encryptionKey = config('app.key');
        $fileName = $request->fileName;
        $filePath = storage_path('app/' . $fileName);
        $encFileName = 'enc_' . $fileName;

        $iv = 'dsfa9p8y098hasdf';

        // Open the file for reading in a stream
        $stream = fopen($filePath, 'r');

        // Open a stream for writing the encrypted content
        $encryptedStream = fopen(storage_path('app/' . $encFileName), 'w');

        // Write IV to the beginning of the file
        fwrite($encryptedStream, $iv);

        // Encrypt the file content
        while (!feof($stream)) {
            $chunk = fread($stream, 8192);
            $encryptedChunk = openssl_encrypt($chunk, 'aes-256-cbc', $encryptionKey, 0, $iv);
            fwrite($encryptedStream, $encryptedChunk);
        }
        // Close the streams
        fclose($stream);
        fclose($encryptedStream);

        // Download the encrypted file
        return response()->download(storage_path('app/' . $encFileName))->deleteFileAfterSend(true);
    }

    // Function to decrypt the uploaded file
    public function decrypt(Request $request)
    {

        $encryptionKey = config('app.key');
        $fileName = $request->fileName;
        $encFilePath = storage_path('app/' . $fileName);
        $decFileName = 'dec_' . $fileName;

        // Open the encrypted file for reading in a stream
        $encryptedStream = fopen($encFilePath, 'r');

        // Read the IV from the beginning of the file
        $iv = 'dsfa9p8y098hasdf'; // Assuming the IV length is 16 bytes for AES-256-CBC

        // Open a stream for writing the decrypted content
        $decryptedStream = fopen(storage_path('app/' . $decFileName), 'w');

        // Decrypt the file content
        while (!feof($encryptedStream)) {

            $chunk = fread($encryptedStream, 8192 + 16); // Encrypted chunk is larger than original chunk
            $decryptedChunk = openssl_decrypt($chunk, 'aes-256-cbc', $encryptionKey, 0, $iv);
            fwrite($decryptedStream, $decryptedChunk);
        }

        // Close the streams
        fclose($encryptedStream);
        fclose($decryptedStream);

        // Download the decrypted file
        return response()->download(storage_path('app/' . $decFileName))->deleteFileAfterSend(true);
    }
}
