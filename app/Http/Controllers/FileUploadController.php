<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Smalot\PdfParser\Parser as PdfParser;
use thiagoalessio\TesseractOCR\TesseractOCR;

class FileUploadController extends Controller
{
    public function showForm()
    {
        return view('upload');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:pdf,docx,jpg,jpeg,png|max:10240',
        ]);

        $file = $request->file('file');
        $path = $file->store('uploads', 'public');

        $fullPath = storage_path('app/public/' . $path);

        $text = $this->extractTextFromFile($fullPath);

        return view('upload_result', compact('text'));
    }

    private function extractTextFromFile($filePath)
    {
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);

        if ($extension === 'pdf') {
            $parser = new PdfParser();
            return $parser->parseFile($filePath)->getText();
        } elseif (in_array($extension, ['jpg', 'jpeg', 'png'])) {
            return (new TesseractOCR($filePath))->run();
        } elseif ($extension === 'docx') {
            return $this->extractFromDocx($filePath);
        }
        return '';
    }

    private function extractFromDocx($filePath)
    {
        $zip = new \ZipArchive;
        if ($zip->open($filePath) === true) {
            $xml = $zip->getFromName('word/document.xml');
            $zip->close();
            return strip_tags($xml);
        }
        return '';
    }
}
