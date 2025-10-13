<?php

if (!function_exists('alertTypes')) {
    function alertTypes()
    {
        return [
            'success' => 'success',
            'error'   => 'error',
            'info'    => 'info',
            'warning' => 'warning',
        ];
    }
}

if (!isset($GLOBALS['success'])) $GLOBALS['success'] = alertTypes()['success'];
if (!isset($GLOBALS['error'])) $GLOBALS['error'] = alertTypes()['error'];
if (!isset($GLOBALS['info'])) $GLOBALS['info'] = alertTypes()['info'];
if (!isset($GLOBALS['warning'])) $GLOBALS['warning'] = alertTypes()['warning'];
