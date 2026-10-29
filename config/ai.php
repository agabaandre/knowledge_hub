<?php

return [

    'serper_api_key' => env('SERPER_API_KEY', ''),

    'open_api_key' => env('OPEN_API_KEY', ''),
    'openai_model' => env('OPENAI_MODEL', 'gpt-3.5-turbo'),
    'chat_pdf_key' => env('CHAT_PDF_API_KEY', ''),

    'gemini_api_key' => env('GEMINI_API_KEY', ''),
    'gemini_model' => env('GEMINI_MODEL', 'gemini-1.5-flash'),
    'claude_api_key' => env('CLAUDE_API_KEY', env('ANTHROPIC_API_KEY', '')),
    'claude_model' => env('CLAUDE_MODEL', env('ANTHROPIC_MODEL', 'claude-sonnet-5-5')),
    'deepseek_api_key' => env('DEEPSEEK_API_KEY', ''),
    'deepseek_model' => env('DEEPSEEK_MODEL', 'deepseek-chat'),
    'custom_base_url' => env('AI_CUSTOM_BASE_URL', ''),
    'custom_api_key' => env('AI_CUSTOM_API_KEY', ''),
    'custom_model' => env('AI_CUSTOM_MODEL', ''),

    'default_primary_provider' => env('AI_PRIMARY_PROVIDER', 'openai'),
    'primary_provider' => null,

    /*
    | Known chat model IDs for the AI config dropdowns.
    | Admins can still type any model ID in the custom text field.
    */
    'model_catalogs' => [
        'openai' => [
            'gpt-6-astra' => 'GPT-6 Astra (flagship)',
            'gpt-6.1-sol' => 'GPT-6.1 Sol',
            'gpt-6-sol' => 'GPT-6 Sol',
            'gpt-6-luna' => 'GPT-6 Luna',
            'gpt-6.1-terra' => 'GPT-6.1 Terra',
            'gpt-5.6-sol' => 'GPT-5.6 Sol',
            'gpt-5.6-terra' => 'GPT-5.6 Terra',
            'gpt-5.6-luna' => 'GPT-5.6 Luna',
            'gpt-5.4' => 'GPT-5.4',
            'gpt-5.4-mini' => 'GPT-5.4 mini',
            'gpt-5.4-nano' => 'GPT-5.4 nano',
            'gpt-5.3-codex' => 'GPT-5.3 Codex',
            'gpt-5.2' => 'GPT-5.2',
            'gpt-5.1' => 'GPT-5.1',
            'gpt-5' => 'GPT-5',
            'gpt-5-mini' => 'GPT-5 mini',
            'gpt-5-nano' => 'GPT-5 nano',
            'gpt-5-pro' => 'GPT-5 pro',
            'o3' => 'o3',
            'o3-mini' => 'o3-mini',
            'o4-mini' => 'o4-mini',
            'o1' => 'o1',
            'o1-pro' => 'o1-pro',
            'o1-mini' => 'o1-mini',
            'gpt-4.1' => 'GPT-4.1',
            'gpt-4.1-mini' => 'GPT-4.1 mini',
            'gpt-4.1-nano' => 'GPT-4.1 nano',
            'gpt-4o' => 'GPT-4o',
            'gpt-4o-mini' => 'GPT-4o mini',
            'gpt-4-turbo' => 'GPT-4 Turbo',
            'gpt-4' => 'GPT-4',
            'gpt-3.5-turbo' => 'GPT-3.5 Turbo',
        ],
        'claude' => [
            'claude-fable-5-1' => 'Claude Fable 5.1',
            'claude-opus-5-5' => 'Claude Opus 5.5',
            'claude-sonnet-5-5' => 'Claude Sonnet 5.5',
            'claude-haiku-5-5' => 'Claude Haiku 5.5',
            'claude-fable-5' => 'Claude Fable 5',
            'claude-opus-5' => 'Claude Opus 5',
            'claude-sonnet-5' => 'Claude Sonnet 5',
            'claude-opus-4-8' => 'Claude Opus 4.8',
            'claude-opus-4-6' => 'Claude Opus 4.6',
            'claude-sonnet-4-6' => 'Claude Sonnet 4.6',
            'claude-haiku-4-5' => 'Claude Haiku 4.5',
            'claude-opus-4-5-20251101' => 'Claude Opus 4.5',
            'claude-sonnet-4-5-20250929' => 'Claude Sonnet 4.5',
            'claude-opus-4-1-20250805' => 'Claude Opus 4.1',
            'claude-opus-4-20250514' => 'Claude Opus 4',
            'claude-sonnet-4-20250514' => 'Claude Sonnet 4',
            'claude-3-7-sonnet-latest' => 'Claude 3.7 Sonnet (latest)',
            'claude-3-5-sonnet-latest' => 'Claude 3.5 Sonnet (latest)',
            'claude-3-5-haiku-latest' => 'Claude 3.5 Haiku (latest)',
            'claude-3-haiku-20240307' => 'Claude 3 Haiku',
        ],
        'gemini' => [
            'gemini-4-argon' => 'Gemini 4 Argon',
            'gemini-3.8-flash' => 'Gemini 3.8 Flash',
            'gemini-3.1-pro' => 'Gemini 3.1 Pro',
            'gemini-3.1-pro-preview' => 'Gemini 3.1 Pro (preview)',
            'gemini-3.1-flash-lite-preview' => 'Gemini 3.1 Flash-Lite (preview)',
            'gemini-3-pro-preview' => 'Gemini 3 Pro (preview)',
            'gemini-3-flash-preview' => 'Gemini 3 Flash (preview)',
            'gemini-2.5-pro' => 'Gemini 2.5 Pro',
            'gemini-2.5-flash' => 'Gemini 2.5 Flash',
            'gemini-2.5-flash-lite' => 'Gemini 2.5 Flash-Lite',
            'gemini-2.0-flash' => 'Gemini 2.0 Flash',
            'gemini-2.0-flash-lite' => 'Gemini 2.0 Flash-Lite',
            'gemini-1.5-pro' => 'Gemini 1.5 Pro',
            'gemini-1.5-flash' => 'Gemini 1.5 Flash',
            'gemini-pro' => 'Gemini Pro (legacy)',
        ],
        'deepseek' => [
            'deepseek-chat' => 'DeepSeek Chat',
            'deepseek-reasoner' => 'DeepSeek Reasoner',
            'deepseek-coder' => 'DeepSeek Coder',
        ],
    ],

    /*
    | Credential source order per provider: "env" (.env first) or "db" (admin settings first).
    | OpenAI and ChatPDF default to env on existing deployments; others default to db.
    */
    'default_source_priority' => [
        'openai' => 'env',
        'chatpdf' => 'env',
        'claude' => 'db',
        'gemini' => 'db',
        'deepseek' => 'db',
        'custom' => 'db',
        'serper' => 'env',
    ],

    'provider_env_keys' => [
        'openai' => ['OPEN_API_KEY', 'OPENAI_MODEL'],
        'chatpdf' => ['CHAT_PDF_API_KEY'],
        'claude' => ['CLAUDE_API_KEY', 'ANTHROPIC_API_KEY', 'CLAUDE_MODEL', 'ANTHROPIC_MODEL'],
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
            'capabilities' => ['forums', 'insights', 'translation', 'chat', 'title_formatting', 'ai_search', 'ai_search_chat'],
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
        'claude' => [
            'label' => 'Anthropic Claude',
            'description' => 'Claude models via the Anthropic Messages API for chat, translation, and insights.',
            'default_enabled' => false,
            'chat' => true,
            'icon' => 'fa-comment-dots',
            'color' => '#d97706',
            'capabilities' => ['forums', 'insights', 'translation', 'chat', 'title_formatting', 'ai_search', 'ai_search_chat'],
        ],
        'gemini' => [
            'label' => 'Google Gemini',
            'description' => 'Alternative chat completions provider.',
            'default_enabled' => false,
            'chat' => true,
            'icon' => 'fa-gem',
            'color' => '#4285f4',
            'capabilities' => ['forums', 'insights', 'translation', 'chat', 'title_formatting', 'ai_search', 'ai_search_chat'],
        ],
        'deepseek' => [
            'label' => 'DeepSeek',
            'description' => 'Cost-effective OpenAI-compatible chat API.',
            'default_enabled' => false,
            'chat' => true,
            'icon' => 'fa-bolt',
            'color' => '#4d6bfe',
            'capabilities' => ['forums', 'insights', 'translation', 'chat', 'title_formatting', 'ai_search', 'ai_search_chat'],
        ],
        'custom' => [
            'label' => 'Custom endpoint',
            'description' => 'Any OpenAI-compatible API (local LLM, Azure OpenAI, etc.).',
            'default_enabled' => false,
            'chat' => true,
            'icon' => 'fa-code',
            'color' => '#6c757d',
            'capabilities' => ['forums', 'insights', 'translation', 'chat', 'title_formatting', 'ai_search', 'ai_search_chat'],
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
        'ai_search_chat' => [
            'label' => 'AI search chat',
            'description' => 'Follow-up questions on search results using publication and forum descriptions.',
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
        'ai_search_chat' => 'openai',
    ],

    /*
    | Sites allowed for AI search web results and external resource links.
    | Admins can add more in Learning → AI config → Web search.
    */
    'default_ai_search_sites' => [
        [
            'id' => 'google_scholar',
            'label' => 'Google Scholar',
            'hosts' => ['scholar.google'],
            'search_url' => 'https://scholar.google.com/scholar?q={query}',
            'site_search' => '',
            'serper_mode' => 'scholar',
            'icon' => 'fa-graduation-cap',
            'snippet' => 'Peer-reviewed articles, theses, books, and conference papers.',
            'enabled' => true,
        ],
        [
            'id' => 'pubmed',
            'label' => 'PubMed',
            'hosts' => ['pubmed.ncbi.nlm.nih.gov', 'ncbi.nlm.nih.gov'],
            'search_url' => 'https://pubmed.ncbi.nlm.nih.gov/?term={query}',
            'site_search' => 'site:pubmed.ncbi.nlm.nih.gov',
            'serper_mode' => 'web',
            'icon' => 'fa-book-medical',
            'snippet' => 'Biomedical and life sciences literature from MEDLINE and related databases.',
            'enabled' => true,
        ],
        [
            'id' => 'jphia',
            'label' => 'Journal of Public Health in Africa',
            'hosts' => ['publichealthinafrica.org'],
            'search_url' => 'https://publichealthinafrica.org/index.php/jphia/search/search?simpleQuery={query}&searchField=query',
            'site_search' => 'site:publichealthinafrica.org',
            'serper_mode' => 'web',
            'icon' => 'fa-book-open',
            'snippet' => 'Open-access public health research and evidence from the African continent.',
            'enabled' => true,
        ],
    ],

];
