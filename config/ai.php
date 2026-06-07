<?php

return [

    'serper_api_key' => env('SERPER_API_KEY', ''),

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

    /*
    | Credential source order per provider: "env" (.env first) or "db" (admin settings first).
    | OpenAI and ChatPDF default to env on existing deployments; others default to db.
    */
    'default_source_priority' => [
        'openai' => 'env',
        'chatpdf' => 'env',
        'gemini' => 'db',
        'deepseek' => 'db',
        'custom' => 'db',
        'serper' => 'env',
    ],

    'provider_env_keys' => [
        'openai' => ['OPEN_API_KEY', 'OPENAI_MODEL'],
        'chatpdf' => ['CHAT_PDF_API_KEY'],
        'gemini' => ['GEMINI_API_KEY', 'GEMINI_MODEL'],
        'deepseek' => ['DEEPSEEK_API_KEY', 'DEEPSEEK_MODEL'],
        'custom' => ['AI_CUSTOM_BASE_URL', 'AI_CUSTOM_API_KEY', 'AI_CUSTOM_MODEL'],
        'serper' => ['SERPER_API_KEY'],
    ],

    'web_search_providers' => [
        'serper' => [
            'label' => 'Serper',
            'description' => 'Google web search API for the top matching internet results in AI search insights.',
            'default_enabled' => true,
            'icon' => 'fa-globe',
            'color' => '#2563eb',
        ],
    ],

    'providers' => [
        'openai' => [
            'label' => 'OpenAI',
            'description' => 'Default for forum summaries, insights, translation, and general chat completions.',
            'default_enabled' => true,
            'chat' => true,
            'icon' => 'fa-brain',
            'color' => '#10a37f',
            'capabilities' => ['forums', 'insights', 'translation', 'chat', 'title_formatting', 'ai_search'],
        ],
        'chatpdf' => [
            'label' => 'ChatPDF',
            'description' => 'PDF upload, document Q&A, and text extraction from publications.',
            'default_enabled' => true,
            'chat' => false,
            'icon' => 'fa-file-pdf',
            'color' => '#e74c3c',
            'capabilities' => ['pdf_documents'],
        ],
        'gemini' => [
            'label' => 'Google Gemini',
            'description' => 'Alternative chat completions provider.',
            'default_enabled' => false,
            'chat' => true,
            'icon' => 'fa-gem',
            'color' => '#4285f4',
            'capabilities' => ['forums', 'insights', 'translation', 'chat', 'title_formatting', 'ai_search'],
        ],
        'deepseek' => [
            'label' => 'DeepSeek',
            'description' => 'Cost-effective OpenAI-compatible chat API.',
            'default_enabled' => false,
            'chat' => true,
            'icon' => 'fa-bolt',
            'color' => '#4d6bfe',
            'capabilities' => ['forums', 'insights', 'translation', 'chat', 'title_formatting', 'ai_search'],
        ],
        'custom' => [
            'label' => 'Custom endpoint',
            'description' => 'Any OpenAI-compatible API (local LLM, Azure OpenAI, etc.).',
            'default_enabled' => false,
            'chat' => true,
            'icon' => 'fa-code',
            'color' => '#6c757d',
            'capabilities' => ['forums', 'insights', 'translation', 'chat', 'title_formatting', 'ai_search'],
        ],
    ],

  /**
   * Platform features and their default provider.
   * Values must match a built-in provider key or integration_{slug}.
   */
    'features' => [
        'forums' => [
            'label' => 'Forum summarization',
            'description' => 'Automatic summaries of forum discussions and threads.',
            'default_provider' => 'openai',
            'provider_types' => ['chat'],
        ],
        'insights' => [
            'label' => 'Insights & analytics',
            'description' => 'AI-generated insights across publications and platform content.',
            'default_provider' => 'openai',
            'provider_types' => ['chat'],
        ],
        'translation' => [
            'label' => 'UI translation',
            'description' => 'Batch translation of interface strings and labels.',
            'default_provider' => 'openai',
            'provider_types' => ['chat'],
        ],
        'chat' => [
            'label' => 'AI chat assistant',
            'description' => 'User-facing chat and Q&A on resources.',
            'default_provider' => 'openai',
            'provider_types' => ['chat'],
        ],
        'pdf_documents' => [
            'label' => 'PDF & document chat',
            'description' => 'ChatPDF-powered summarization and Q&A on uploaded PDFs.',
            'default_provider' => 'chatpdf',
            'provider_types' => ['document'],
        ],
        'title_formatting' => [
            'label' => 'Title formatting',
            'description' => 'AI-assisted publication and discussion title cleanup.',
            'default_provider' => 'openai',
            'provider_types' => ['chat'],
        ],
        'ai_search' => [
            'label' => 'AI search',
            'description' => 'Semantic / AI-enhanced search when enabled in settings.',
            'default_provider' => 'openai',
            'provider_types' => ['chat'],
        ],
    ],

    'default_feature_routing' => [
        'forums' => 'openai',
        'insights' => 'openai',
        'translation' => 'openai',
        'chat' => 'openai',
        'pdf_documents' => 'chatpdf',
        'title_formatting' => 'openai',
        'ai_search' => 'openai',
    ],

];
