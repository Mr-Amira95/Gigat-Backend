<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class ContractGeneratorService
{
    public function generate(array $data, string $fileName): string
    {

    $htmlTemplatePath = storage_path('app/public/contracts/templates/freelance_contract.html');

    // Load HTML content
    $html = file_get_contents($htmlTemplatePath);

    // Replace placeholders in the HTML content
    foreach ($data as $key => $value) {
        // \Log::info("Setting placeholder: {$key} => {$value}");
        $html = str_replace($key, $value, $html); // e.g., replace [client_name] with "John Doe"
    }

        // Generate PDF from HTML
        $pdf = Pdf::loadHTML($html)->setPaper('a4');
        $pdfOutput = $pdf->output();

        Storage::disk('public')->put("contracts/{$fileName}.pdf", $pdfOutput);

        return Storage::url("contracts/{$fileName}.pdf");
    }
}
