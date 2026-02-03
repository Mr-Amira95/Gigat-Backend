<?php

namespace App\Http\Controllers;

use App\Traits\BaseResponse;

abstract class Controller
{
    use BaseResponse;
    public function uploadFiles($image, $directory): string
    {
        $base_path = "files/" . $directory;
        $filename = md5(strtotime(now()) . rand(1111, 9999)) . "." . $image->getClientOriginalExtension();
        $image->move(public_path($base_path), $filename);
        return $base_path . "/" . $filename;
    }
}
