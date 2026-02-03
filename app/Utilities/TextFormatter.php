<?php

namespace App\Utilities;

class TextFormatter
{
    public static function convertTextToHtml(string $text): string
    {
        // Escape special characters
        $text = e($text);

        // Convert URLs to clickable links
        $text = preg_replace(
            '/(https?:\/\/[^\s]+)/',
            '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>',
            $text
        );

        // Wrap in paragraph
        return "<p>{$text}</p>";
    }
}
