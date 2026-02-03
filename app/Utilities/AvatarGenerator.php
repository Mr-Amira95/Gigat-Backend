<?php

namespace App\Utilities;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class AvatarGenerator
{
    protected static $arabicToEnglishMap = [
        'أ' => 'A',
        'ب' => 'B',
        'ت' => 'T',
        'ث' => 'TH',
        'ج' => 'J',
        'ح' => 'H',
        'خ' => 'KH',
        'د' => 'D',
        'ذ' => 'DH',
        'ر' => 'R',
        'ز' => 'Z',
        'س' => 'S',
        'ش' => 'SH',
        'ص' => 'S',
        'ض' => 'D',
        'ط' => 'T',
        'ظ' => 'Z',
        'ع' => 'A',
        'غ' => 'GH',
        'ف' => 'F',
        'ق' => 'Q',
        'ك' => 'K',
        'ل' => 'L',
        'م' => 'M',
        'ن' => 'N',
        'ه' => 'H',
        'و' => 'W',
        'ي' => 'Y'
    ];  
    public static function getEnglishLetter($char)
    {
        return self::$arabicToEnglishMap[$char] ?? strtoupper($char);
    }
    public static function generateAvatar($letter)
    {
        $letter = strtoupper($letter);
        $path = "avatars/{$letter}.png";

        if (Storage::disk('public')->exists($path)) {
            return Storage::url($path);
        }

        $image = Image::canvas(128, 128, '#49356E');

        $fontPath = public_path('build/assets/fonts/Tajawal-Bold.ttf'); 
        $image->text($letter, 64, 64, function ($font) use ($fontPath) {
            $font->file($fontPath); 
            $font->size(60); 
            $font->color('#ffffff');
            $font->align('center');
            $font->valign('middle');
        });

        $imageStream = $image->encode('png');
        $tempFile = tempnam(sys_get_temp_dir(), 'avatar');
        file_put_contents($tempFile, $imageStream);

        $uploadedFile = new \Illuminate\Http\UploadedFile(
            $tempFile,
            'avatar.png',
            'image/png',
            null,
            true
        );

        $path = FileManager::upload('avatars', $uploadedFile);
        @unlink($tempFile);

        return $path;
    }

    public static function getAvatarForName($name)
    {
        $firstLetter = mb_substr(trim($name), 0, 1);
        $englishLetter = self::getEnglishLetter($firstLetter);
        return self::generateAvatar($englishLetter);
    }
}
