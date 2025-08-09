<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Telegram Bot Configuration
    |--------------------------------------------------------------------------
    |
    | Configuración para la integración con Telegram Bot API.
    | Estas variables deben estar definidas en el archivo .env
    |
    */

    'bot_token' => env('TELEGRAM_BOT_TOKEN'),

    'chat_id' => env('TELEGRAM_CHAT_ID'),

    /*
    |--------------------------------------------------------------------------
    | Configuración de notificaciones
    |--------------------------------------------------------------------------
    |
    | Configuración para diferentes tipos de notificaciones
    |
    */

    'notifications' => [
        
        // Notificaciones de ventas
        'ventas' => [
            'enabled' => env('TELEGRAM_NOTIFY_VENTAS', true),
            'chat_id' => env('TELEGRAM_CHAT_ID_VENTAS', env('TELEGRAM_CHAT_ID')),
        ],

        // Notificaciones de stock bajo
        'stock_low' => [
            'enabled' => env('TELEGRAM_NOTIFY_STOCK_LOW', true),
            'chat_id' => env('TELEGRAM_CHAT_ID_STOCK', env('TELEGRAM_CHAT_ID')),
            'threshold' => env('TELEGRAM_STOCK_THRESHOLD', 10), // Umbral para stock bajo
        ],

        // Notificaciones de errores del sistema
        'errors' => [
            'enabled' => env('TELEGRAM_NOTIFY_ERRORS', false),
            'chat_id' => env('TELEGRAM_CHAT_ID_ERRORS', env('TELEGRAM_CHAT_ID')),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de mensajes
    |--------------------------------------------------------------------------
    |
    | Plantillas y configuración para los mensajes
    |
    */

    'messages' => [
        
        // Emojis para diferentes tipos de notificaciones
        'emojis' => [
            'venta' => '🛒',
            'stock_low' => '⚠️',
            'error' => '❌',
            'success' => '✅',
            'info' => 'ℹ️',
        ],

        // Configuración de formato
        'format' => [
            'parse_mode' => 'HTML', // HTML, Markdown, MarkdownV2
            'disable_web_page_preview' => true,
            'disable_notification' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de reintentos
    |--------------------------------------------------------------------------
    |
    | Configuración para reintentos en caso de fallo
    |
    */

    'retry' => [
        'enabled' => env('TELEGRAM_RETRY_ENABLED', true),
        'max_attempts' => env('TELEGRAM_MAX_RETRY_ATTEMPTS', 3),
        'delay_seconds' => env('TELEGRAM_RETRY_DELAY', 5),
    ],

]; 