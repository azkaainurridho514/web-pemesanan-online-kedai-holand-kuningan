<?php

if (!function_exists('alert')) {
    function alert($type, $message, $title = null)
    {
        session()->flash('sweetalert', [
            'type' => $type,    
            'title' => $title ?? ucfirst($type),
            'message' => $message,
        ]);
    }
}
