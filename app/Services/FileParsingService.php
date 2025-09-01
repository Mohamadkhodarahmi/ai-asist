<?php

namespace App\Services;

use Spatie\PdfToText\Pdf;
use Exception;

class FileParsingService
{
    /**
     * Extracts text content from a file based on its extension.
     *
     * @param string $storagePath The path to the file within Laravel's storage.
     * @return string The extracted text content.
     * @throws \Exception If the file type is unsupported.
     */
    public function getTextFromFile(string $storagePath): string
    {
        // Get the full path to the file.
        $fullPath = storage_path('app/private/' . $storagePath);

        if (!file_exists($fullPath)) {
            throw new Exception("File not found at path: {$fullPath}");
        }

        // Determine the file extension to decide which parser to use.
        $extension = pathinfo($fullPath, PATHINFO_EXTENSION);

        switch (strtolower($extension)) {
            case 'pdf':
                // Use the spatie/pdf-to-text package for PDF files.
                return Pdf::getText($fullPath);

            case 'txt':
                // Use standard PHP function for plain text files.
                return file_get_contents($fullPath);

            // You can add more cases here for other file types like .docx
            // case 'docx':
            //     // Requires another library like PHPOffice/PHPWord
            //     return ...;

            default:
                throw new Exception("Unsupported file type: .{$extension}");
        }
    }
}
