<?php

return [

    'open_api_key' => env('OPEN_API_KEY', ''),
    'openai_model' => env('OPENAI_MODEL', 'gpt-3.5-turbo'),
    'chat_pdf_key' => env('CHAT_PDF_API_KEY', ''),

    'gemini_api_key' => env('GEMINI_API_KEY', ''),
    'gemini_model' => env('GEMINI_MODEL', 'gemini-1.5-flash'),
    'deepseek_api_key' => env('DEEPSEEK_API_KEY', ''),
    'deepseek_model' => env('DEEPSEEK_MODEL', 'deepseek-chat'),
    'custom_base_url' => env('AI_CUSTOM_BASE_URL', ''),
    'custom_api_key' => env('AI_CUSTOM_API_KEY', ''),
    'custom_model' => env('AI_CUSTOM_MODEL', ''),

    'default_primary_provider' => env('AI_PRIMARY_PROVIDER', 'openai'),
    'primary_provider' => null,

    'providers' => [
        'openai' => [
            'label' => 'OpenAI',
            'default_enabled' => true,
            'chat' => true,
        ],
        'chatpdf' => [
            'label' => 'ChatPDF',
            'default_enabled' => true,
            'chat' => false,
        ],
        'gemini' => [
            'label' => 'Google Gemini',
            'default_enabled' => false,
            'chat' => true,
        ],
        'deepseek' => [
            'label' => 'DeepSeek',
            'default_enabled' => false,
            'chat' => true,
        ],
        'custom' => [
            'label' => 'Custom (OpenAI-compatible)',
            'default_enabled' => false,
            'chat' => true,
        ],
    ],

];
