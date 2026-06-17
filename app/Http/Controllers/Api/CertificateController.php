<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Http\Controllers\Controller;
use App\Models\FreelancerCertificate;

class CertificateController extends Controller
{
    public function delete($id)
    {
        try {
            $certificate = FreelancerCertificate::findOrFail($id);

            if ($certificate->file_path && file_exists(public_path($certificate->file_path))) {
                unlink(public_path($certificate->file_path));
            }

            $certificate->delete();

            return $this->successResponse(__('success'));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
}
