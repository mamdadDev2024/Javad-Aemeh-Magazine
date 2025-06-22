<?php
namespace App\Helpers;

class SweetAlert2
{
    public static function alert($title, $message, $type = 'success')
    {
        return [
            'title' => $title,
            'text' => $message,
            'icon' => $type,
        ];
    }
}
