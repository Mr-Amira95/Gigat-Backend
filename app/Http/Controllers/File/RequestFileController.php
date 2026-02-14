<?php

namespace App\Http\Controllers\File;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use ZipArchive;
use App\Models\RequestDeliveryAttachment;

class RequestFileController extends Controller
{
    public function deliveryFiles($id)
    {
        $attachments = RequestDeliveryAttachment::whereHas('delivery', function ($q) use ($id) {
            $q->where('request_id', $id);
        })
        ->orderBy('created_at')
        ->get()
        ->groupBy('request_delivery_id');
    
        return view('request-files.deliveries', [
            'groupedFiles' => $attachments,
            'requestId' => $id,
        ]);
    
        // $files = $this->getFiles("requests/{$id}/delivery");

        // return view('request-files.deliveries', [
        //     'files' => $files,
        //     'requestId' => $id,
        // ]);
    }

    public function feedbackFiles($id)
    {
        $files = $this->getFiles("requests/{$id}/feedback");

        return view('request-files.feedbacks', [
            'files' => $files,
            'requestId' => $id,
        ]);
    }

    public function downloadAllDelivery($id)
    {
        return $this->zipAndDownload("requests/{$id}/delivery", "delivery_Attachments.zip");
    }

    public function downloadAllFeedback($id)
    {
        return $this->zipAndDownload("requests/{$id}/feedback", "feedback_Attachments.zip");
    }

    /*========================================================
    |  SHARED METHODS
    ========================================================*/

    private function getFiles($folder)
    {
        $diskPath = public_path("storage/{$folder}");

        if (!is_dir($diskPath)) {
            abort(404, "{$folder} not found");
        }

        $files = collect(File::files($diskPath))
            ->map(function ($file) use ($folder) {
                return asset("storage/{$folder}/" . $file->getFilename());
            })
            ->values()
            ->toArray();

        return $files;
    }

    /**
     * Create a ZIP file for all files in the folder and download it
     */
    private function zipAndDownload($folder, $zipName)
    {
        $diskPath = public_path("storage/{$folder}");

        if (!is_dir($diskPath)) {
            abort(404, "Folder not found");
        }

        $files = File::files($diskPath);

        if (empty($files)) {
            abort(404, "No files found in this folder");
        }

        $zipPath = storage_path("app/temp/{$zipName}");

        if (!File::exists(dirname($zipPath))) {
            File::makeDirectory(dirname($zipPath), 0777, true);
        }

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
            abort(500, 'Could not create ZIP file.');
        }

        foreach ($files as $file) {
            $zip->addFile($file->getPathname(), $file->getFilename());
        }

        $zip->close();

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }
}
