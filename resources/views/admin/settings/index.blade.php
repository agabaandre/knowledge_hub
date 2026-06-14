@extends(admin_layout())
@section('styles')
    @if((settings()->site_theme ?? '') !== 'theme1.')
    <!-- Bootstrap Colorpicker CSS (Bootstrap 4 only; Theme1 uses Bootstrap 5) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/3.2.0/css/bootstrap-colorpicker.min.css"
        rel="stylesheet">
    @endif
    <style>
        .settings-container {
            background: #ffffff;
            border-radius: 0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .settings-header {
            background: linear-gradient(135deg, var(--theme-color-primary, #119A48) 0%, #16c653 100%);
            padding: 2rem;
            color: white;
        }

        .settings-header h2 {
            margin: 0;
            font-size: 1.75rem;
            font-weight: 600;
        }

        .settings-header p {
            margin: 0.5rem 0 0 0;
            opacity: 0.9;
        }

        .settings-tabs {
            border-bottom: 2px solid #e2e8f0;
            background: #f8f9fa;
            padding: 0 2rem;
        }

        .settings-tabs .nav-link {
            border: none;
            border-bottom: 3px solid transparent;
            color: #64748b;
            font-weight: 500;
            padding: 1rem 1.5rem;
            transition: all 0.3s ease;
        }

        .settings-tabs .nav-link:hover {
            color: var(--theme-color-primary, #119A48);
            background: transparent;
            border-bottom-color: rgba(17, 154, 72, 0.3);
        }

        .settings-tabs .nav-link.active {
            color: var(--theme-color-primary, #119A48);
            background: #ffffff;
        }
        /* Theme1 (Nifty) admin: unique tab styling */
        .settings-theme1 .settings-tabs {
            display: flex;
            border-bottom: none;
            background: transparent;
            padding: 0 1.5rem;
            gap: 0.25rem;
            flex-wrap: wrap;
        }
        .settings-theme1 .settings-tabs .nav-item {
            margin-bottom: 0;
        }
        .settings-theme1 .settings-tabs .nav-link {
            border: none;
            border-radius: 0;
            padding: 0.65rem 1.25rem;
            font-weight: 500;
            color: #64748b;
            background: transparent;
            transition: color 0.2s, background 0.2s;
        }
        .settings-theme1 .settings-tabs .nav-link:hover {
            color: var(--theme-color-primary, #119A48);
            background: rgba(17, 154, 72, 0.08);
        }
        .settings-theme1 .settings-tabs .nav-link.active {
            color: var(--theme-color-primary, #119A48);
            background: #ffffff;
            border-bottom: none;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        }

        .settings-content {
            padding: 2rem;
        }

        .settings-section {
            display: none;
        }

        .settings-section.active {
            display: block;
        }

        .form-section-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-section-title i {
            color: var(--theme-color-primary, #119A48);
            font-size: 1.25rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            font-weight: 600;
            color: #2d3748;
            font-size: 0.9375rem;
            margin-bottom: 0.5rem;
            display: block;
        }

        .form-control {
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            color: #2d3748;
            background-color: #ffffff;
        }

        .form-control:focus {
            border-color: var(--theme-color-primary, #119A48);
            box-shadow: 0 0 0 3px rgba(17, 154, 72, 0.1);
            outline: none;
        }

        /* Select dropdown styling */
        .form-control select,
        select.form-control {
            color: #2d3748 !important;
            background-color: #ffffff !important;
        }

        select.form-control option {
            color: #2d3748 !important;
            background-color: #ffffff !important;
            padding: 0.5rem;
        }

        .colorPicker {
            border-radius: 8px;
            overflow: hidden;
            border: 2px solid #e2e8f0;
            display: flex;
        }

        /* Remove border from form-control inside colorPicker to avoid doubled border */
        .colorPicker .form-control {
            border: none !important;
            border-radius: 0;
            border-top-left-radius: 8px;
            border-bottom-left-radius: 8px;
        }

        .colorPicker:focus-within {
            border-color: var(--theme-color-primary, #119A48);
            box-shadow: 0 0 0 3px rgba(17, 154, 72, 0.1);
        }

        .colorPicker .input-group-append {
            display: flex;
        }

        .colorPicker .input-group-text {
            width: 60px;
            border: none !important;
            border-left: 1px solid #e2e8f0 !important;
            padding: 0;
            background-color: #ffffff;
        }

        .colorPicker:focus-within .input-group-text {
            border-left-color: var(--theme-color-primary, #119A48) !important;
        }

        .color-preview {
            width: 100%;
            height: 100%;
            min-height: 48px;
            border-radius: 0;
            border-top-right-radius: 8px;
            border-bottom-right-radius: 8px;
            cursor: pointer;
        }

        .gradient-preview {
            height: 80px;
            border-radius: 8px;
            margin-top: 0.5rem;
            border: 2px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .gradient-preview:hover {
            border-color: var(--theme-color-primary, #119A48);
        }

        .image-preview {
            margin-top: 0.75rem;
            border-radius: 8px;
            overflow: hidden;
            border: 2px solid #e2e8f0;
            display: inline-block;
            max-width: 150px;
        }

        .image-preview img {
            width: 100%;
            height: auto;
            display: block;
        }

        .branding-section-intro {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 0.85rem 1rem;
            margin-bottom: 1.25rem;
            font-size: 0.85rem;
            color: #64748b;
            line-height: 1.5;
        }

        .branding-section-intro i {
            color: var(--theme-color-primary, #119A48);
            margin-right: 0.35rem;
        }

        .branding-asset-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
            height: 100%;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .branding-asset-card:hover {
            border-color: rgba(17, 154, 72, 0.25);
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06);
        }

        .branding-asset-card__header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            padding: 1rem 1.1rem 0.75rem;
            border-bottom: 1px solid #f1f5f9;
            background: linear-gradient(180deg, #fafbfc 0%, #fff 100%);
        }

        .branding-asset-card__title {
            font-size: 0.95rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 0.35rem;
        }

        .branding-asset-spec {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.03em;
            text-transform: uppercase;
            color: #64748b;
            background: #f1f5f9;
            border-radius: 999px;
            padding: 0.2rem 0.55rem;
        }

        .branding-asset-preview {
            flex-shrink: 0;
            width: 112px;
            height: 72px;
            border-radius: 10px;
            border: 1px dashed #cbd5e1;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            padding: 0.35rem;
        }

        .branding-asset-preview--square {
            width: 72px;
            height: 72px;
        }

        .branding-asset-preview img {
            max-width: 100%;
            max-height: 100%;
            width: auto;
            height: auto;
            object-fit: contain;
            display: block;
        }

        .branding-asset-preview__placeholder {
            font-size: 0.68rem;
            color: #94a3b8;
            text-align: center;
            line-height: 1.3;
            padding: 0.25rem;
        }

        .branding-asset-card__body {
            padding: 1rem 1.1rem 1.1rem;
        }

        .branding-field-label {
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 0.35rem;
            display: block;
        }

        .branding-upload-row {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.5rem;
            margin-top: 0.65rem;
            padding: 0.55rem;
            border: 1px dashed #cbd5e1;
            border-radius: 10px;
            background: #f8fafc;
        }

        .branding-upload-row input[type="file"] {
            position: absolute;
            width: 0.1px;
            height: 0.1px;
            opacity: 0;
            overflow: hidden;
            z-index: -1;
        }

        .branding-upload-btn {
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 0.45rem 0.8rem;
            font-size: 0.8rem;
            font-weight: 600;
            color: #475569;
            background: #fff;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .branding-upload-btn:hover {
            border-color: var(--theme-color-primary, #119A48);
            color: var(--theme-color-primary, #119A48);
            background: rgba(17, 154, 72, 0.06);
        }

        .branding-upload-filename {
            font-size: 0.78rem;
            color: #94a3b8;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            flex: 1 1 120px;
            min-width: 0;
        }

        .branding-upload-filename.has-file {
            color: #0f172a;
            font-weight: 600;
        }

        .branding-options-panel {
            margin-top: 1.25rem;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 1rem 1.1rem;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
        }

        .branding-options-panel__title {
            font-size: 0.95rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 0.85rem;
            display: flex;
            align-items: center;
            gap: 0.45rem;
        }

        .branding-options-panel__title i {
            color: var(--theme-color-primary, #119A48);
        }

        .branding-toggle-card {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 0.85rem 0.95rem;
            background: #f8fafc;
            height: 100%;
        }

        .branding-toggle-card .form-check {
            margin-bottom: 0.35rem;
        }

        .branding-toggle-card .info-text {
            margin-top: 0;
        }

        @media (max-width: 767px) {
            .branding-asset-card__header {
                flex-direction: column;
            }

            .branding-asset-preview {
                width: 100%;
                max-width: 180px;
            }
        }

        .email-section-intro {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 0.85rem 1rem;
            margin-bottom: 1.25rem;
            font-size: 0.85rem;
            color: #64748b;
            line-height: 1.5;
        }

        .email-driver-switch {
            display: inline-flex;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            overflow: hidden;
            background: #f8fafc;
            margin-bottom: 1rem;
        }

        .email-driver-switch input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .email-driver-switch label {
            margin: 0;
            padding: 0.65rem 1.15rem;
            font-size: 0.875rem;
            font-weight: 600;
            color: #64748b;
            cursor: pointer;
            transition: all 0.2s ease;
            border-right: 1px solid #e2e8f0;
        }

        .email-driver-switch label:last-of-type {
            border-right: none;
        }

        .email-driver-switch input:checked + label {
            background: var(--theme-color-primary, #119A48);
            color: #fff;
        }

        .email-driver-switch.is-locked {
            opacity: 0.75;
            pointer-events: none;
        }

        .email-config-panel {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 1rem 1.1rem;
            background: #fff;
            margin-bottom: 1rem;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
        }

        .email-config-panel__title {
            font-size: 0.95rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 0.85rem;
            display: flex;
            align-items: center;
            gap: 0.45rem;
        }

        .email-config-panel__title i {
            color: var(--theme-color-primary, #119A48);
        }

        .email-env-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.03em;
            text-transform: uppercase;
            color: #b45309;
            background: #fffbeb;
            border: 1px solid #fcd34d;
            border-radius: 999px;
            padding: 0.15rem 0.5rem;
            margin-left: 0.35rem;
            vertical-align: middle;
        }

        .email-effective-hint {
            font-size: 0.78rem;
            color: #64748b;
            margin-top: 0.25rem;
        }

        .email-effective-hint strong {
            color: #0f172a;
        }

        .btn-save {
            background: var(--theme-color-primary, #119A48);
            border: none;
            border-radius: 8px;
            padding: 0.875rem 2rem;
            font-size: 1rem;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
        }

        .btn-save:hover {
            background: color-mix(in srgb, var(--theme-color-primary, #119A48) 85%, black);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(17, 154, 72, 0.3);
            color: white;
        }

        .btn-outline-secondary {
            border: 2px solid #64748b;
            border-radius: 8px;
            padding: 0.875rem 2rem;
            font-size: 1rem;
            font-weight: 600;
            color: #64748b;
            background: white;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-outline-secondary:hover {
            background: #64748b;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(100, 116, 139, 0.3);
        }

        .settings-action-bar {
            border-top: 2px solid #e2e8f0;
            background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
            padding: 1.25rem 2rem 1rem;
        }

        .settings-action-bar__inner {
            display: flex;
            flex-wrap: wrap;
            align-items: stretch;
            justify-content: space-between;
            gap: 1.25rem;
        }

        .settings-action-bar__primary {
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 0.35rem;
            min-width: 220px;
        }

        .settings-action-bar__primary-label {
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: #64748b;
            margin: 0;
        }

        .settings-action-bar__primary-hint {
            font-size: 0.8rem;
            color: #94a3b8;
            margin: 0;
            max-width: 280px;
            line-height: 1.4;
        }

        .settings-tools-panel {
            flex: 1 1 420px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 0.85rem 1rem;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
        }

        .settings-tools-panel__title {
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: #64748b;
            margin: 0 0 0.65rem;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .settings-tools-panel__title i {
            color: var(--theme-color-primary, #119A48);
        }

        .settings-tools-panel__actions {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-tool {
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 0.55rem 0.95rem;
            font-size: 0.875rem;
            font-weight: 600;
            color: #475569;
            background: #fff;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            white-space: nowrap;
            line-height: 1.2;
        }

        .btn-tool:hover {
            border-color: var(--theme-color-primary, #119A48);
            color: var(--theme-color-primary, #119A48);
            background: rgba(17, 154, 72, 0.06);
            text-decoration: none;
            transform: translateY(-1px);
        }

        .btn-tool--accent {
            border-color: rgba(17, 154, 72, 0.35);
            color: var(--theme-color-primary, #119A48);
            background: rgba(17, 154, 72, 0.08);
        }

        .btn-tool--accent:hover {
            background: var(--theme-color-primary, #119A48);
            border-color: var(--theme-color-primary, #119A48);
            color: #fff;
        }

        .btn-tool:disabled {
            opacity: 0.55;
            cursor: not-allowed;
            transform: none;
        }

        .settings-import-group {
            display: inline-flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.45rem;
            padding: 0.35rem;
            border: 1px dashed #cbd5e1;
            border-radius: 10px;
            background: #f8fafc;
        }

        .settings-import-group input[type="file"] {
            position: absolute;
            width: 0.1px;
            height: 0.1px;
            opacity: 0;
            overflow: hidden;
            z-index: -1;
        }

        .settings-import-filename {
            font-size: 0.78rem;
            color: #64748b;
            max-width: 160px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            padding: 0 0.25rem;
        }

        .settings-import-filename.has-file {
            color: #0f172a;
            font-weight: 600;
        }

        .settings-action-footnote {
            margin: 0.85rem 0 0;
            padding: 0.75rem 0.9rem;
            font-size: 0.8rem;
            color: #64748b;
            background: #f1f5f9;
            border-radius: 8px;
            line-height: 1.5;
        }

        .settings-action-footnote i {
            color: var(--theme-color-primary, #119A48);
            margin-right: 0.35rem;
        }

        @media (max-width: 767px) {
            .settings-action-bar {
                padding: 1rem;
            }

            .settings-action-bar__inner {
                flex-direction: column;
            }

            .settings-tools-panel {
                flex-basis: 100%;
            }

            .settings-tools-panel__actions {
                flex-direction: column;
                align-items: stretch;
            }

            .settings-import-group {
                flex-direction: column;
                align-items: stretch;
            }

            .settings-import-filename {
                max-width: 100%;
                text-align: center;
            }
        }

        .info-text {
            font-size: 0.875rem;
            color: #64748b;
            margin-top: 0.25rem;
        }

        .terminal-output {
            background: #1e1e1e;
            color: #d4d4d4;
            font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
            font-size: 0.875rem;
            padding: 1rem;
            border-radius: 4px;
            min-height: 300px;
            max-height: 500px;
            overflow-y: auto;
            white-space: pre-wrap;
            word-wrap: break-word;
            line-height: 1.6;
        }

        .terminal-line {
            margin-bottom: 0.25rem;
        }

        .terminal-prompt {
            color: #4ec9b0;
            font-weight: bold;
            margin-right: 0.5rem;
        }

        .terminal-text {
            color: #d4d4d4;
        }

        .terminal-success {
            color: #4ec9b0;
        }

        .terminal-error {
            color: #f48771;
        }

        .terminal-info {
            color: #569cd6;
        }

        #cache-output-terminal::-webkit-scrollbar {
            width: 8px;
        }

        #cache-output-terminal::-webkit-scrollbar-track {
            background: #252526;
        }

        #cache-output-terminal::-webkit-scrollbar-thumb {
            background: #424242;
            border-radius: 4px;
        }

        #cache-output-terminal::-webkit-scrollbar-thumb:hover {
            background: #4e4e4e;
        }

        .settings-group-card {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            background: #ffffff;
            padding: 1.25rem;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
            height: 100%;
        }

        .settings-group-title {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.75rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .settings-group-title i {
            color: var(--theme-color-primary, #119A48);
        }

        .settings-group-help {
            color: #64748b;
            font-size: 0.875rem;
            margin-bottom: 1rem;
        }
    </style>
@endsection

@section('content')
    <div class="page-header">
        <h1 class="page-title">System Configuration</h1>
        <div>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">System Configuration</li>
            </ol>
        </div>
    </div>

    @if(Session::has('alert-success') || Session::has('alert-danger'))
        <div class="alert alert-{{ Session::has('alert-success') ? 'success' : 'danger' }} alert-dismissible fade show" role="alert">
            {{ Session::get('alert-success') ?? Session::get('alert-danger') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="alert alert-info d-flex flex-column flex-md-row align-items-md-center justify-content-between flex-wrap gap-2 mb-3">
        <span><i class="fa fa-language me-2"></i><strong>{{ __('admin_nav.site_languages') }}</strong> — add locales, display names, and Google Translate codes (stored in the database). <strong>{{ __('admin_nav.language_management') }}</strong> — edit UI strings per locale.</span>
        <span class="d-flex gap-2 flex-shrink-0">
            <a href="{{ route('admin.site-languages.index') }}" class="btn btn-sm btn-outline-primary text-nowrap">{{ __('admin_nav.site_languages') }}</a>
            <a href="{{ route('admin.language-management.index') }}" class="btn btn-sm btn-outline-primary text-nowrap">{{ __('admin_nav.language_management') }}</a>
        </span>
    </div>

    <form action="{{ route('admin.config.save') }}" method="post" enctype="multipart/form-data" id="settings-main-form">
            @csrf
        <div class="settings-container {{ (settings()->site_theme ?? '') === 'theme1.' ? 'settings-theme1' : '' }}">
            <div class="settings-header">
                <h2><i class="fa fa-cog me-2"></i>System Configuration</h2>
                <p>Manage your site settings and preferences</p>
            </div>

            <ul class="nav nav-tabs settings-tabs" id="settingsTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="general-tab" data-tab="general" type="button" role="tab" aria-controls="general" aria-selected="true">
                        <i class="fa fa-info-circle me-2"></i>General
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="appearance-tab" data-tab="appearance" type="button" role="tab" aria-controls="appearance" aria-selected="false">
                        <i class="fa fa-palette me-2"></i>Appearance
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="contact-tab" data-tab="contact" type="button" role="tab" aria-controls="contact" aria-selected="false">
                        <i class="fa fa-address-book me-2"></i>Contact
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="email-tab" data-tab="email" type="button" role="tab" aria-controls="email" aria-selected="false">
                        <i class="fa fa-envelope me-2"></i>Email
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="badges-tab" data-tab="badges" type="button" role="tab" aria-controls="badges" aria-selected="false">
                        <i class="fa fa-trophy me-2"></i>Badges
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="advanced-tab" data-tab="advanced" type="button" role="tab" aria-controls="advanced" aria-selected="false">
                        <i class="fa fa-sliders me-2"></i>Advanced
                    </button>
                </li>
            </ul>

            <div class="tab-content settings-content" id="settingsTabContent">
                <!-- General Tab -->
                <div class="tab-pane fade show active" id="general" role="tabpanel">
                    <div class="form-section-title">
                        <i class="fa fa-bullhorn"></i>
                        Basic Information
                </div>

                    <div class="row">
                        <div class="col-md-6">
                <div class="form-group">
                                <label>Site Name <span class="text-danger">*</span></label>
                                <input type="text" name="site_name" value="{{ $settings->site_name }}" class="form-control" required>
                </div>
                </div>
                        <div class="col-md-6">
                <div class="form-group">
                                <label>Title</label>
                                <input type="text" name="title" value="{{ $settings->title }}" class="form-control">
                </div>
                </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                <div class="form-group">
                                <label>Slogan</label>
                                <input type="text" name="slogan" value="{{ $settings->slogan }}" class="form-control" placeholder="Your site slogan">
                </div>
            </div>
                        <div class="col-md-6">
                <div class="form-group">
                                <label>Language</label>
                                <input type="text" name="language" value="{{ $settings->language }}" class="form-control" placeholder="en">
                </div>
                </div>
                </div>

                <div class="form-group">
                        <label>Site Description</label>
                        <textarea name="site_description" rows="4" class="form-control" placeholder="Brief description of your site">{{ $settings->site_description }}</textarea>
                        <small class="info-text">This description may be used by search engines.</small>
                </div>

                <div class="form-group">
                        <label>SEO Keywords</label>
                        <textarea name="seo_keywords" rows="3" class="form-control" placeholder="keyword1, keyword2, keyword3">{{ $settings->seo_keywords }}</textarea>
                        <small class="info-text">Separate keywords with commas.</small>
                    </div>

                <div class="form-group">
                        <label class="d-block">SEO-friendly URLs</label>
                        <input type="hidden" name="use_seo_friendly_urls" value="0">
                        <label class="mb-0">
                            <input type="checkbox" name="use_seo_friendly_urls" value="1" {{ ($settings->use_seo_friendly_urls ?? true) ? 'checked' : '' }}>
                            Use title-based URLs for publications, forums, communities, tags, health topics, and member states (instead of <code>?id=</code> links)
                        </label>
                        <small class="info-text d-block">When enabled, links use readable slugs and legacy ID URLs redirect to the slug URL.</small>
                </div>

                    @php
                        $currentLogoFile = settings()->logo ? basename(parse_url(settings()->logo, PHP_URL_PATH)) : '';
                        $currentFaviconFile = settings()->favicon ? basename(parse_url(settings()->favicon, PHP_URL_PATH)) : '';
                    @endphp

                    <div class="form-section-title mt-4">
                        <i class="fa fa-images"></i>
                        Branding
                    </div>

                    <div class="branding-section-intro">
                        <i class="fa fa-info-circle"></i>
                        Manage site logo, favicon, and how the logo appears in the header and footer. Pick from the config gallery or upload a new image. Settings are saved per theme when using Theme1.
                    </div>

                    <div class="row">
                        <div class="col-lg-6 mb-3 mb-lg-0">
                            <div class="branding-asset-card" data-branding-asset="logo">
                                <div class="branding-asset-card__header">
                                    <div>
                                        <h4 class="branding-asset-card__title">Site Logo</h4>
                                        <span class="branding-asset-spec"><i class="fa fa-arrows-alt"></i> 500 × 230 px</span>
                                        <small class="info-text d-block mt-2 mb-0">Displayed in the site header and footer.</small>
                                    </div>
                                    <div class="branding-asset-preview js-branding-preview">
                                        @if(settings()->logo)
                                            <img src="{{ settings()->logo }}" alt="Logo preview">
                                        @else
                                            <span class="branding-asset-preview__placeholder">No logo set</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="branding-asset-card__body">
                                    <label class="branding-field-label" for="logo_existing">Config gallery</label>
                                    <select name="logo_existing" id="logo_existing" class="form-control">
                                        <option value="">— Keep current / upload new —</option>
                                        @foreach($configGalleryImages ?? [] as $f)
                                            <option value="{{ $f }}" @if($f === $currentLogoFile) selected @endif>{{ $f }}</option>
                                        @endforeach
                                    </select>

                                    <div class="branding-upload-row">
                                        <input type="file" name="logo" id="logo" accept="image/*">
                                        <button type="button" class="branding-upload-btn js-branding-upload-trigger" data-target="logo">
                                            <i class="fa fa-upload mr-1"></i>Choose image
                                        </button>
                                        <span class="branding-upload-filename js-branding-filename" id="logo-filename">No file chosen</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="branding-asset-card" data-branding-asset="favicon">
                                <div class="branding-asset-card__header">
                                    <div>
                                        <h4 class="branding-asset-card__title">Favicon</h4>
                                        <span class="branding-asset-spec"><i class="fa fa-arrows-alt"></i> 350 × 350 px</span>
                                        <small class="info-text d-block mt-2 mb-0">Browser tab icon shown across the site.</small>
                                    </div>
                                    <div class="branding-asset-preview branding-asset-preview--square js-branding-preview">
                                        @if(settings()->favicon)
                                            <img src="{{ settings()->favicon }}" alt="Favicon preview">
                                        @else
                                            <span class="branding-asset-preview__placeholder">No favicon</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="branding-asset-card__body">
                                    <label class="branding-field-label" for="favicon_existing">Config gallery</label>
                                    <select name="favicon_existing" id="favicon_existing" class="form-control">
                                        <option value="">— Keep current / upload new —</option>
                                        @foreach($configGalleryImages ?? [] as $f)
                                            <option value="{{ $f }}" @if($f === $currentFaviconFile) selected @endif>{{ $f }}</option>
                                        @endforeach
                                    </select>

                                    <div class="branding-upload-row">
                                        <input type="file" name="favicon" id="favicon" accept="image/*">
                                        <button type="button" class="branding-upload-btn js-branding-upload-trigger" data-target="favicon">
                                            <i class="fa fa-upload mr-1"></i>Choose image
                                        </button>
                                        <span class="branding-upload-filename js-branding-filename" id="favicon-filename">No file chosen</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="branding-options-panel">
                        <h4 class="branding-options-panel__title">
                            <i class="fa fa-sliders"></i>Logo display options
                        </h4>
                        <div class="row">
                            <div class="col-md-4 mb-3 mb-md-0">
                                <div class="branding-toggle-card h-100">
                                    <label class="branding-field-label" for="logo_scale">Logo size</label>
                                    <select name="logo_scale" id="logo_scale" class="form-control">
                                        @foreach([40, 50, 60, 70, 80, 100, 120] as $px)
                                            <option value="{{ $px }}" @if((settings()->logo_scale ?? 80) == $px) selected @endif>{{ $px }}px height</option>
                                        @endforeach
                                    </select>
                                    <small class="info-text d-block mt-2">Header and footer logo height on front and admin.</small>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3 mb-md-0">
                                <div class="branding-toggle-card h-100">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="header_logo_inverse" name="header_logo_inverse" value="1" @if(settings()->header_logo_inverse ?? false) checked @endif>
                                        <label class="form-check-label" for="header_logo_inverse">Inverse logo in header</label>
                                    </div>
                                    <small class="info-text">Shows a light/inverted logo style in the header on front and admin.</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="branding-toggle-card h-100">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="footer_logo_inverse" name="footer_logo_inverse" value="1" @if(settings()->footer_logo_inverse ?? false) checked @endif>
                                        <label class="form-check-label" for="footer_logo_inverse">Inverse logo in footer</label>
                                    </div>
                                    <small class="info-text">Shows a light/inverted logo style in the site footer.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>

                <!-- Appearance Tab -->
                <div class="tab-pane fade" id="appearance" role="tabpanel">
                    <div class="form-section-title">
                        <i class="fa fa-palette"></i>
                        AU (African Union) Color Palette & Scheme
                    </div>
                    <small class="info-text mb-3 d-block">Primary, secondary, navigation, and official AU colors. Applied to both front and admin. Saved per theme (Default vs Theme1).</small>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Primary Color</label>
                    <div class="input-group colorPicker">
                                    <input type="text" name="primary_color" value="{{ $settings->primary_color ?? '#119A48' }}" class="form-control" />
                        <div class="input-group-append">
                                        <span class="input-group-text color-preview" style="background-color: {{ $settings->primary_color ?? '#119A48' }}"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Secondary Color</label>
                                <div class="input-group colorPicker">
                                    <input type="text" name="secondary_color" value="{{ $settings->secondary_color }}" class="form-control" />
                                    <div class="input-group-append">
                                        <span class="input-group-text color-preview" style="background-color: {{ $settings->secondary_color }}"></span>
                                    </div>
                    </div>
                </div>
                </div>
            </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Primary Text Color</label>
                    <div class="input-group colorPicker">
                                    <input type="text" name="primary_text_color" value="{{ $settings->primary_text_color }}" class="form-control" />
                        <div class="input-group-append">
                                        <span class="input-group-text color-preview" style="background-color: {{ $settings->primary_text_color }}"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Links Active Color</label>
                                <div class="input-group colorPicker">
                                    <input type="text" name="links_active_color" value="{{ $settings->links_active_color }}" class="form-control" />
                                    <div class="input-group-append">
                                        <span class="input-group-text color-preview" style="background-color: {{ $settings->links_active_color }}"></span>
                                    </div>
                    </div>
                </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Icon Font Color</label>
                                <small class="form-text text-muted d-block mb-1">Publication card file-type icons and other icon accents. Default: Agenda 2063 — PANTONE 3415 C (<code>#007749</code>).</small>
                                <div class="input-group colorPicker">
                                    <input type="text" name="icon_font_color" value="{{ $settings->icon_font_color }}" class="form-control" />
                                    <div class="input-group-append">
                                        <span class="input-group-text color-preview" style="background-color: {{ $settings->icon_font_color }}"></span>
                                    </div>
                    </div>
                </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-12">
                            <div class="form-section-subtitle mb-2">Navigation bar</div>
                        </div>
                        <div class="col-md-6">
                <div class="form-group">
                                <label>Front nav style <span class="text-muted">(public site only)</span></label>
                                <select name="nav_style" class="form-control">
                                    <option value="colored" @if(($settings->nav_style ?? 'colored') === 'colored') selected @endif>Colored (primary/secondary background)</option>
                                    <option value="light" @if(($settings->nav_style ?? '') === 'light') selected @endif>Light (light background, dark text)</option>
                                </select>
                                <small class="info-text">Navigation bar style on the public (front) site only.</small>
                </div>
                        </div>
                        <div class="col-md-6">
                <div class="form-group">
                                <label>Admin nav style <span class="text-muted">(admin panel only)</span></label>
                                <select name="admin_nav_style" class="form-control">
                                    <option value="colored" @if(($settings->admin_nav_style ?? 'colored') === 'colored') selected @endif>Colored (primary background)</option>
                                    <option value="light" @if(($settings->admin_nav_style ?? '') === 'light') selected @endif>Light (light background, dark text)</option>
                                </select>
                                <small class="info-text">Navigation bar style in the admin panel only. Does not affect the public site.</small>
                </div>
                        </div>
                        <div class="col-md-2">
                <div class="form-group">
                                <label>Nav link color</label>
                                <div class="input-group colorPicker">
                                    <input type="text" name="nav_link_color" value="{{ $settings->nav_link_color ?? '' }}" class="form-control" placeholder="{{ ($settings->nav_style ?? 'colored') === 'light' ? '#334155' : '#fff' }}" />
                                    <div class="input-group-append">
                                        <span class="input-group-text color-preview" style="background-color: {{ $settings->nav_link_color ?? (($settings->nav_style ?? 'colored') === 'light' ? '#334155' : '#ffffff') }}"></span>
                </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                <div class="form-group">
                                <label>Nav link hover</label>
                                <div class="input-group colorPicker">
                                    <input type="text" name="nav_link_hover_color" value="{{ $settings->nav_link_hover_color ?? '' }}" class="form-control" placeholder="{{ ($settings->nav_style ?? 'colored') === 'light' ? '#119A48' : '#e2e8f0' }}" />
                                    <div class="input-group-append">
                                        <span class="input-group-text color-preview" style="background-color: {{ $settings->nav_link_hover_color ?? (($settings->nav_style ?? 'colored') === 'light' ? ($settings->primary_color ?? '#119A48') : '#e2e8f0') }}"></span>
                </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                <div class="form-group">
                                <label>Nav link active</label>
                                <div class="input-group colorPicker">
                                    <input type="text" name="nav_link_active_color" value="{{ $settings->nav_link_active_color ?? '' }}" class="form-control" placeholder="{{ ($settings->primary_color ?? '#119A48') }}" />
                                    <div class="input-group-append">
                                        <span class="input-group-text color-preview" style="background-color: {{ $settings->nav_link_active_color ?? ($settings->primary_color ?? '#119A48') }}"></span>
                </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                <div class="form-group">
                                <label>Nav font weight</label>
                                <select name="nav_font_weight" class="form-control">
                                    @foreach([['400', 'Normal'], ['500', 'Medium'], ['600', 'Semibold'], ['700', 'Bold']] as $opt)
                                        <option value="{{ $opt[0] }}" @if(($settings->nav_font_weight ?? '500') == $opt[0]) selected @endif>{{ $opt[1] }}</option>
                                    @endforeach
                                </select>
                                <small class="info-text">Applies to front and admin nav links.</small>
                            </div>
                        </div>
                </div>

                    <div class="row mt-2">
                        <div class="col-md-6">
                <div class="form-group">
                                <label>Show Icons in Main Menu</label>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="menu_icons_enabled" name="menu_icons_enabled" value="1" @if($settings->menu_icons_enabled) checked @endif>
                                    <label class="form-check-label" for="menu_icons_enabled">Show icons in main menu</label>
                                </div>
                                <small class="info-text">When enabled, compact icons appear above nav labels (front header and admin sidebar). When off, text-only navigation is shown.</small>
                </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Homepage Sections</label>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="show_featured" name="show_featured" value="1" @if(!empty($settings->show_featured)) checked @endif>
                                    <label class="form-check-label" for="show_featured">Show Featured content</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="show_events" name="show_events" value="1" @if(!empty($settings->show_events)) checked @endif>
                                    <label class="form-check-label" for="show_events">Show Events slider</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="show_top_searches" name="show_top_searches" value="1" @if(!empty($settings->show_top_searches)) checked @endif>
                                    <label class="form-check-label" for="show_top_searches">Show Top Searches</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="show_tags" name="show_tags" value="1" @if(!empty($settings->show_tags)) checked @endif>
                                    <label class="form-check-label" for="show_tags">Show Tags strip</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="show_quotes" name="show_quotes" value="1" @if(!empty($settings->show_quotes)) checked @endif>
                                    <label class="form-check-label" for="show_quotes">Show Quotes banner</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="show_health_themes" name="show_health_themes" value="1" @if($settings->show_health_themes ?? true) checked @endif>
                                    <label class="form-check-label" for="show_health_themes">Show health theme tiles below search</label>
                                </div>
                                <small class="info-text d-block mb-2">Uncheck to hide the thematic area tiles in the homepage spotlight (under the search box).</small>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="show_quiz" name="show_quiz" value="1" @if(!empty($settings->show_quiz)) checked @endif>
                                    <label class="form-check-label" for="show_quiz">Show Quiz button on search results</label>
                                </div>
                                <small class="info-text">Toggle which sections appear on the homepage.</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Search Page (Records Search)</label>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="search_show_forums" name="search_show_forums" value="1" @if($settings->search_show_forums ?? true) checked @endif>
                                    <label class="form-check-label" for="search_show_forums">Show forums in search results</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="search_show_communities" name="search_show_communities" value="1" @if($settings->search_show_communities ?? true) checked @endif>
                                    <label class="form-check-label" for="search_show_communities">Show communities in search results</label>
                                </div>
                                @if(Schema::hasColumn('setting', 'communities_listing_show_participants'))
                                <div class="form-check mt-3">
                                    <input type="hidden" name="communities_listing_show_participants" value="0">
                                    <input type="checkbox" class="form-check-input" id="communities_listing_show_participants" name="communities_listing_show_participants" value="1" @if($settings->communities_listing_show_participants ?? true) checked @endif>
                                    <label class="form-check-label" for="communities_listing_show_participants">Show participants on community directory cards</label>
                                </div>
                                @endif
                                @if(Schema::hasColumn('setting', 'communities_listing_max_faces'))
                                <div class="form-group mt-2 mb-0">
                                    <label for="communities_listing_max_faces">Max participant faces per community card</label>
                                    <input type="number" class="form-control" id="communities_listing_max_faces" name="communities_listing_max_faces" min="1" max="24" value="{{ (int) ($settings->communities_listing_max_faces ?? 8) }}">
                                    <small class="text-muted">Between 1 and 24. Default 8. Applies to the public communities listing and “My communities” cards.</small>
                                </div>
                                @endif
                                @if(Schema::hasColumn('setting', 'communities_listing_cards_per_row'))
                                <div class="form-group mt-2 mb-0">
                                    <label for="communities_listing_cards_per_row">Community cards per row (desktop)</label>
                                    <select class="form-control" id="communities_listing_cards_per_row" name="communities_listing_cards_per_row">
                                        @foreach([1 => '1 per row — widest cards, longest descriptions', 2 => '2 per row (default)', 3 => '3 per row — compact cards, shortest descriptions'] as $val => $label)
                                            <option value="{{ $val }}" @selected((int) ($settings->communities_listing_cards_per_row ?? 2) === $val)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Controls the public communities directory and “My communities” grid. Wider layouts show more description text.</small>
                                </div>
                                @endif
                                @if(Schema::hasColumn('setting', 'show_publication_card_file_type_badge'))
                                <div class="form-check mt-2">
                                    <input type="hidden" name="show_publication_card_file_type_badge" value="0">
                                    <input type="checkbox" class="form-check-input" id="show_publication_card_file_type_badge" name="show_publication_card_file_type_badge" value="1" @if($settings->show_publication_card_file_type_badge ?? true) checked @endif>
                                    <label class="form-check-label" for="show_publication_card_file_type_badge">Show file type icon on publication cards</label>
                                </div>
                                @endif
                                <small class="info-text d-block mt-1">When enabled, forums and communities appear on the main search page alongside publications.</small>
                                @if(Schema::hasColumn('setting', 'show_publication_card_file_type_badge'))
                                <small class="text-muted d-block mt-1">File type badges use <strong>Icon Font Color</strong> (Appearance).</small>
                                @endif
                    </div>
                </div>
            </div>

                    <div class="form-section-title mt-4">
                        <i class="fa fa-heading"></i>
                        Home page section titles
                    </div>
                    <small class="info-text mb-2 d-block">Customise the heading text for homepage sections. Leave blank to use the default text shown in the placeholders.</small>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Health themes section</label>
                                <input type="text" name="section_title_health_themes" class="form-control" value="{{ $settings->section_title_health_themes ?? '' }}" placeholder="Choose a Health Theme to Explore">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Top Searches section</label>
                                <input type="text" name="section_title_top_searches" class="form-control" value="{{ $settings->section_title_top_searches ?? '' }}" placeholder="Top Searches">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Recommended / Featured section</label>
                                <input type="text" name="section_title_recommended" class="form-control" value="{{ $settings->section_title_recommended ?? '' }}" placeholder="Recommended">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Flagship Initiatives section</label>
                                <input type="text" name="section_title_flagship_initiatives" class="form-control" value="{{ $settings->section_title_flagship_initiatives ?? '' }}" placeholder="Flagship Initiatives">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Theme cards opacity</label>
                                <select name="theme_card_opacity" class="form-control">
                                    @foreach(['0.5' => '50%', '0.6' => '60%', '0.7' => '70%', '0.8' => '80%', '0.9' => '90%', '1' => '100% (no transparency)'] as $val => $label)
                                        <option value="{{ $val }}" @if(($settings->theme_card_opacity ?? '1') == $val) selected @endif>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <small class="info-text">Opacity of the health theme cards on the homepage (default: 100%).</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Theme cards per row (desktop)</label>
                                <input type="number" name="theme_cards_per_row" class="form-control" min="2" max="8" value="{{ (int) ($settings->theme_cards_per_row ?? 4) }}">
                                <small class="info-text">Controls how many health theme cards appear per row on desktop (default: 4).</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-section-title mt-4">
                        <i class="fa fa-comments"></i>
                        Comment Moderation
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="auto_approve_comments" name="auto_approve_comments" value="1" @if($settings->auto_approve_comments ?? 1) checked @endif>
                                    <label class="form-check-label" for="auto_approve_comments">Auto-approve comments on submission</label>
                                </div>
                                <small class="info-text">When enabled, forum and publication comments will be automatically approved upon submission. When disabled, comments will require manual approval.</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-section-title mt-4">
                        <i class="fa fa-search"></i>
                        Search Settings
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="enable_ai_search" name="enable_ai_search" value="1" @if($settings->enable_ai_search ?? 1) checked @endif>
                                    <label class="form-check-label" for="enable_ai_search">Enable AI Search</label>
                                </div>
                                <small class="info-text">When enabled, the AI Search button will be displayed in the search bar. When disabled, only the regular search button will be shown.</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="search_pagination_mode">Search results loading</label>
                                <select class="form-control" id="search_pagination_mode" name="search_pagination_mode">
                                    <option value="pagination" @if(($settings->search_pagination_mode ?? 'pagination') === 'pagination') selected @endif>Classic pagination (page numbers)</option>
                                    <option value="infinite_scroll" @if(($settings->search_pagination_mode ?? 'pagination') === 'infinite_scroll') selected @endif>Infinite scroll (load more as you scroll)</option>
                                </select>
                                <small class="info-text">Controls how publication results load on the records search page. Infinite scroll appends results automatically until all matches are shown.</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="forums_pagination_mode">Forums listing loading</label>
                                <select class="form-control" id="forums_pagination_mode" name="forums_pagination_mode">
                                    <option value="pagination" @if(($settings->forums_pagination_mode ?? 'pagination') === 'pagination') selected @endif>Classic pagination (page numbers)</option>
                                    <option value="infinite_scroll" @if(($settings->forums_pagination_mode ?? 'pagination') === 'infinite_scroll') selected @endif>Infinite scroll (load more as you scroll)</option>
                                </select>
                                <small class="info-text">Controls how discussions load on the forums page. Infinite scroll loads 10 threads at a time and appends more as you scroll.</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="communities_pagination_mode">Communities listing loading</label>
                                <select class="form-control" id="communities_pagination_mode" name="communities_pagination_mode">
                                    <option value="pagination" @if(($settings->communities_pagination_mode ?? 'infinite_scroll') === 'pagination') selected @endif>Classic pagination (page numbers)</option>
                                    <option value="infinite_scroll" @if(($settings->communities_pagination_mode ?? 'infinite_scroll') === 'infinite_scroll') selected @endif>Infinite scroll (load more as you scroll)</option>
                                </select>
                                <small class="info-text">Controls how communities load on the communities page. Infinite scroll loads 6 communities at a time and appends more as you scroll.</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="courses_pagination_mode">Courses listing loading</label>
                                <select class="form-control" id="courses_pagination_mode" name="courses_pagination_mode">
                                    <option value="pagination" @if(($settings->courses_pagination_mode ?? 'infinite_scroll') === 'pagination') selected @endif>Classic pagination (page numbers)</option>
                                    <option value="infinite_scroll" @if(($settings->courses_pagination_mode ?? 'infinite_scroll') === 'infinite_scroll') selected @endif>Infinite scroll (load more as you scroll)</option>
                                </select>
                                <small class="info-text">Infinite scroll loads 6 courses per batch on the courses page.</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="faqs_pagination_mode">FAQs listing loading</label>
                                <select class="form-control" id="faqs_pagination_mode" name="faqs_pagination_mode">
                                    <option value="pagination" @if(($settings->faqs_pagination_mode ?? 'infinite_scroll') === 'pagination') selected @endif>Classic pagination (page numbers)</option>
                                    <option value="infinite_scroll" @if(($settings->faqs_pagination_mode ?? 'infinite_scroll') === 'infinite_scroll') selected @endif>Infinite scroll (load more as you scroll)</option>
                                </select>
                                <small class="info-text">Infinite scroll loads 6 FAQs per batch.</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="health_topics_pagination_mode">Health topics listing loading</label>
                                <select class="form-control" id="health_topics_pagination_mode" name="health_topics_pagination_mode">
                                    <option value="pagination" @if(($settings->health_topics_pagination_mode ?? 'infinite_scroll') === 'pagination') selected @endif>Classic pagination (page numbers)</option>
                                    <option value="infinite_scroll" @if(($settings->health_topics_pagination_mode ?? 'infinite_scroll') === 'infinite_scroll') selected @endif>Infinite scroll (load more as you scroll)</option>
                                </select>
                                <small class="info-text">Infinite scroll loads 6 topics per batch while preserving A–Z letter groups.</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="home_events_pagination_mode">Homepage events loading</label>
                                <select class="form-control" id="home_events_pagination_mode" name="home_events_pagination_mode">
                                    <option value="pagination" @if(($settings->home_events_pagination_mode ?? 'infinite_scroll') === 'pagination') selected @endif>Fixed batch (first 12 events)</option>
                                    <option value="infinite_scroll" @if(($settings->home_events_pagination_mode ?? 'infinite_scroll') === 'infinite_scroll') selected @endif>Infinite scroll (load more as you scroll)</option>
                                </select>
                                <small class="info-text">Infinite scroll loads 6 events per batch into the homepage events slider.</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="authors_pagination_mode">Contributors &amp; authors listing loading</label>
                                <select class="form-control" id="authors_pagination_mode" name="authors_pagination_mode">
                                    <option value="pagination" @if(($settings->authors_pagination_mode ?? 'infinite_scroll') === 'pagination') selected @endif>Classic pagination (page numbers)</option>
                                    <option value="infinite_scroll" @if(($settings->authors_pagination_mode ?? 'infinite_scroll') === 'infinite_scroll') selected @endif>Infinite scroll (load more as you scroll)</option>
                                </select>
                                <small class="info-text">Infinite scroll loads 12 contributors per batch on browse/authors.</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="country_publications_pagination_mode">Country page publications loading</label>
                                <select class="form-control" id="country_publications_pagination_mode" name="country_publications_pagination_mode">
                                    <option value="pagination" @if(($settings->country_publications_pagination_mode ?? 'infinite_scroll') === 'pagination') selected @endif>Classic pagination (page numbers)</option>
                                    <option value="infinite_scroll" @if(($settings->country_publications_pagination_mode ?? 'infinite_scroll') === 'infinite_scroll') selected @endif>Infinite scroll (load more as you scroll)</option>
                                </select>
                                <small class="info-text">Infinite scroll loads 5 publications per batch on member state detail pages.</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="enable_ai_chat_prune" name="enable_ai_chat_prune" value="1" @if($settings->enable_ai_chat_prune ?? 1) checked @endif>
                                    <label class="form-check-label" for="enable_ai_chat_prune">Enable scheduled AI chat cleanup</label>
                                </div>
                                <small class="info-text">When enabled, old AI chats are automatically deleted by the daily cleanup task. Disable this to retain chat history for further synthesis and user understanding.</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-section-title mt-4">
                        <i class="fa fa-image"></i>
                        Background & Gradient
                </div>

                    <div class="form-group">
                        <label>Spotlight Banner Image</label>
                        <label class="small text-muted d-block mb-1">Browse from existing gallery</label>
                        @php $currentBannerFile = settings()->spotlight_banner ? basename(parse_url(settings()->spotlight_banner, PHP_URL_PATH)) : ''; @endphp
                        <select name="spotlight_banner_existing" id="spotlight_banner_existing" class="form-control mb-2">
                            <option value="" @if(!$currentBannerFile) selected @endif>— Keep current / upload new —</option>
                            @foreach($configGalleryImages ?? [] as $f)
                                <option value="{{ $f }}" @if($f === $currentBannerFile) selected @endif>{{ $f }}</option>
                            @endforeach
                        </select>
                        <label class="small text-muted d-block mb-1">Or upload new file</label>
                        <input type="file" name="spotlight_banner" id="spotlight_banner" class="form-control" accept="image/*">
                        <small class="info-text">Recommended: 1894x658 pixels. This image will be used for the search area background.</small>
                        @if(settings()->spotlight_banner)
                            <div class="image-preview mt-2">
                                <img src="{{ settings()->spotlight_banner }}" alt="Banner Preview">
                            </div>
                        @endif
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Gradient Start Color</label>
                                <div class="input-group colorPicker" id="gradientStartPicker">
                                    <input type="text" name="gradient_start_color" value="{{ $settings->gradient_start_color ?? '#119A48' }}" class="form-control" />
                                    <div class="input-group-append">
                                        <span class="input-group-text color-preview gradient-start-preview" style="background-color: {{ $settings->gradient_start_color ?? '#119A48' }}"></span>
                                    </div>
                                </div>
                                <small class="info-text">Used when no banner image is set</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Gradient End Color</label>
                                <div class="input-group colorPicker" id="gradientEndPicker">
                                    <input type="text" name="gradient_end_color" value="{{ $settings->gradient_end_color ?? '#16c653' }}" class="form-control" />
                                    <div class="input-group-append">
                                        <span class="input-group-text color-preview gradient-end-preview" style="background-color: {{ $settings->gradient_end_color ?? '#16c653' }}"></span>
                                    </div>
                                </div>
                                <small class="info-text">Used when no banner image is set</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Gradient Preview</label>
                        <div class="gradient-preview" id="gradientPreview"></div>
                        <small class="info-text">Preview of the gradient (shown when no banner image is set)</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Spotlight Image Overlay Color</label>
                                <div class="input-group colorPicker">
                                    <input type="text" name="spotlight_overlay_color" value="{{ $settings->spotlight_overlay_color ?? '#000000' }}" class="form-control" />
                                    <div class="input-group-append">
                                        <span class="input-group-text color-preview" style="background-color: {{ $settings->spotlight_overlay_color ?? '#000000' }}"></span>
                                    </div>
                                </div>
                                <small class="info-text">Overlay color applied on top of the spotlight image.</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Spotlight Image Overlay Darkness (%)</label>
                                <input type="number"
                                       name="spotlight_overlay_opacity"
                                       min="0"
                                       max="100"
                                       step="1"
                                       class="form-control"
                                       value="{{ (int) ($settings->spotlight_overlay_opacity ?? 35) }}" />
                                <small class="info-text">0 = no overlay, 100 = fully solid overlay.</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-section-subtitle mt-4 mb-2">AU official colors</div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>AU Red <small class="text-muted">(PANTONE 7420 C)</small></label>
                    <div class="input-group colorPicker">
                                    <input type="text" name="au_red" value="{{ $settings->au_red ?? '#9F2241' }}" class="form-control" />
                        <div class="input-group-append">
                                        <span class="input-group-text color-preview" style="background-color: {{ $settings->au_red ?? '#9F2241' }}"></span>
                        </div>
                                </div>
                                <small class="info-text">RGB: 159, 34, 65 | CMYK: 27, 98, 66, 18</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>AU Gold <small class="text-muted">(PANTONE 4515 C)</small></label>
                                <div class="input-group colorPicker">
                                    <input type="text" name="au_gold" value="{{ $settings->au_gold ?? '#B4A269' }}" class="form-control" />
                                    <div class="input-group-append">
                                        <span class="input-group-text color-preview" style="background-color: {{ $settings->au_gold ?? '#B4A269' }}"></span>
                                    </div>
                                </div>
                                <small class="info-text">RGB: 180, 162, 105 | CMYK: 31, 31, 69, 2</small>
                            </div>
                    </div>
                </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>AU Corporate Green <small class="text-muted">(PANTONE 3415 C)</small></label>
                    <div class="input-group colorPicker">
                                    <input type="text" name="au_corporate_green" value="{{ $settings->au_corporate_green ?? '#1A5632' }}" class="form-control" />
                        <div class="input-group-append">
                                        <span class="input-group-text color-preview" style="background-color: {{ $settings->au_corporate_green ?? '#1A5632' }}"></span>
                        </div>
                    </div>
                                <small class="info-text">RGB: 26, 86, 50 | CMYK: 86, 40, 91, 39</small>
                </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>AU Green <small class="text-muted">(PANTONE 7740 C)</small></label>
                    <div class="input-group colorPicker">
                                    <input type="text" name="au_green" value="{{ $settings->au_green ?? '#1A5632' }}" class="form-control" />
                        <div class="input-group-append">
                                        <span class="input-group-text color-preview" style="background-color: {{ $settings->au_green ?? '#1A5632' }}"></span>
                        </div>
                    </div>
                                <small class="info-text">Official AU Green color</small>
                </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Agenda 2063 Plum <small class="text-muted">(PANTONE 3415 C)</small></label>
                    <div class="input-group colorPicker">
                                    <input type="text" name="au_plum" value="{{ $settings->au_plum ?? '#522B39' }}" class="form-control" />
                        <div class="input-group-append">
                                        <span class="input-group-text color-preview" style="background-color: {{ $settings->au_plum ?? '#522B39' }}"></span>
                        </div>
                    </div>
                                <small class="info-text">RGB: 82, 43, 57 | CMYK: 54, 86, 50, 48</small>
                </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Grey Text <small class="text-muted">(PANTONE 425 C)</small></label>
                    <div class="input-group colorPicker">
                                    <input type="text" name="au_grey_text" value="{{ $settings->au_grey_text ?? '#58595B' }}" class="form-control" />
                        <div class="input-group-append">
                                        <span class="input-group-text color-preview" style="background-color: {{ $settings->au_grey_text ?? '#58595B' }}"></span>
                        </div>
                    </div>
                                <small class="info-text">RGB: 83, 87, 90 | CMYK: 65, 56, 53, 29</small>
                </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Bright White</label>
                    <div class="input-group colorPicker">
                                    <input type="text" name="au_white" value="{{ $settings->au_white ?? '#FFFFFF' }}" class="form-control" />
                        <div class="input-group-append">
                                        <span class="input-group-text color-preview" style="background-color: {{ $settings->au_white ?? '#FFFFFF' }}; border: 1px solid #ddd;"></span>
                        </div>
                                </div>
                                <small class="info-text">RGB: 255, 255, 255 | CMYK: 0, 0, 0, 0</small>
                            </div>
                    </div>
                </div>

                    <div class="row">
                        <div class="col-md-6">
                    <div class="form-group">
                                <label>Banner Text Color</label>
                                <div class="input-group colorPicker">
                                    <input type="text" name="banner_text" value="{{ $settings->banner_text ?? '#FFFFFF' }}" class="form-control" />
                                    <div class="input-group-append">
                                        <span class="input-group-text color-preview" style="background-color: {{ $settings->banner_text ?? '#FFFFFF' }}"></span>
                        </div>
                    </div>
                </div>
                </div>
                        <div class="col-md-6">
                    <div class="form-group">
                        <label>Footer Style</label>
                        <select class="form-control" name="footer_style">
                            <option value="light-footer" @if (settings()->footer_style == 'light-footer') selected @endif>Light</option>
                            <option value="dark-footer" @if (settings()->footer_style == 'dark-footer') selected @endif>Dark</option>
                        </select>
                            </div>
                    </div>
                </div>

                    <div class="form-group">
                        <label>Config name</label>
                        <input type="text" name="config_name" class="form-control" value="{{ settings()->config_name ?? 'Default' }}" placeholder="e.g. Default, Theme1, ET">
                        <small class="info-text">Name for this configuration (used to identify this theme's config). Saved with the selected theme.</small>
                    </div>
                    <div class="form-group">
                        <label>Site Theme</label>
                        <select class="form-control" name="site_theme">
                            <option value="" @if ((settings()->site_theme ?? '') == '') selected @endif>Default Theme</option>
                            <option value="theme1." @if ((settings()->site_theme ?? '') == 'theme1.') selected @endif>Theme1</option>
                            <option value="et" @if ((settings()->site_theme ?? '') == 'et') selected @endif>ET</option>
                        </select>
                        <small class="info-text">Changing theme and saving creates or loads that theme's configuration row; the previous theme's config is left unchanged.</small>
                    </div>

                    <div class="form-section-title mt-4">
                        <i class="fa fa-language"></i>
                        Translate Button
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="form-check">
                                    <input type="hidden" name="translate_button_filled" value="0">
                                    <input type="checkbox" class="form-check-input" id="translate_button_filled" name="translate_button_filled" value="1" @if(settings()->translate_button_filled ?? true) checked @endif>
                                    <label class="form-check-label" for="translate_button_filled">Filled translate button (with background color)</label>
                                </div>
                                <small class="info-text">When enabled, the language/translate button uses the primary color as background. When disabled, it appears as outline only.</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Translate button text color</label>
                                <div class="input-group colorPicker">
                                    <input type="text" name="translate_button_text_color" value="{{ settings()->translate_button_text_color ?? '#ffffff' }}" class="form-control" />
                                    <div class="input-group-append">
                                        <span class="input-group-text color-preview" style="background-color: {{ settings()->translate_button_text_color ?? '#ffffff' }}"></span>
                                    </div>
                                </div>
                                <small class="info-text">Text color of the translate/language selector button.</small>
                            </div>
                    </div>
                </div>

                    <div class="form-section-title mt-4">
                        <i class="fa fa-font"></i>
                        Typography
                    </div>
                    <small class="info-text mb-3 d-block">Primary font and default text color applied across the site. Saved per theme.</small>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Primary font</label>
                                <select name="primary_font" id="primary_font" class="form-control">
                                    <option value="">— Default (system) —</option>
                                    <option value="univers_45_light" @if((settings()->primary_font ?? '') === 'univers_45_light') selected @endif>Univers 45 Light</option>
                                    <option value="arial" @if((settings()->primary_font ?? '') === 'arial') selected @endif>Arial</option>
                                    <option value="times_new_roman" @if((settings()->primary_font ?? '') === 'times_new_roman') selected @endif>Times New Roman</option>
                                    <option value="montserrat" @if((settings()->primary_font ?? '') === 'montserrat') selected @endif>Montserrat</option>
                                    <option value="brandon_text" @if((settings()->primary_font ?? '') === 'brandon_text') selected @endif>Brandon Text</option>
                                    @if(isset($customFonts) && $customFonts->count() > 0)
                                        <option disabled>— Custom fonts —</option>
                                        @foreach($customFonts as $cf)
                                            <option value="custom_{{ $cf->id }}" @if((settings()->primary_font ?? '') === 'custom_' . $cf->id) selected @endif>{{ $cf->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Default font color</label>
                                <div class="input-group colorPicker">
                                    <input type="text" name="default_font_color" value="{{ $settings->default_font_color ?? '#212529' }}" class="form-control" placeholder="#212529" />
                                    <div class="input-group-append">
                                        <span class="input-group-text color-preview" style="background-color: {{ $settings->default_font_color ?? '#212529' }}"></span>
                                    </div>
                                </div>
                                <small class="info-text">Default body text color.</small>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Front-end body font size (px)</label>
                                <input type="number" name="front_body_font_size" value="{{ $settings->front_body_font_size ?? '14' }}" class="form-control" min="10" max="24" step="1" placeholder="14">
                                <small class="info-text">Default body font size on the public site. Default: 14px.</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Admin panel body font size (px)</label>
                                <input type="number" name="admin_body_font_size" value="{{ $settings->admin_body_font_size ?? '14' }}" class="form-control" min="10" max="24" step="1" placeholder="14">
                                <small class="info-text">Default body font size in the admin panel. Default: 14px.</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Main navigation font size (px)</label>
                                <input type="number" name="nav_font_size" value="{{ $settings->nav_font_size ?? '11' }}" class="form-control" min="9" max="16" step="1" placeholder="11">
                                <small class="info-text">Top menu labels on the public site and admin sidebar. Default: 11px (LinkedIn-style compact nav).</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-section-subtitle mt-4 mb-2">Custom fonts (upload your own)</div>
                    <p class="text-muted small">Upload font files to add them to the Primary font dropdown. Formats: .woff2, .woff, .ttf, .otf</p>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <form action="{{ route('admin.config.custom-font.store') }}" method="post" enctype="multipart/form-data" class="card card-body bg-light">
                                @csrf
                                <div class="row">
                                    <div class="col-md-3">
                                        <input type="text" name="font_name" class="form-control" placeholder="Display name (optional)">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" name="font_family" class="form-control" placeholder="Font family / CSS name (optional)">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="file" name="font_files[]" class="form-control" accept=".woff,.woff2,.ttf,.otf" multiple>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-primary btn-sm">Add font</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    @if(isset($customFonts) && $customFonts->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead><tr><th>Name</th><th>Font family</th><th>Files</th><th></th></tr></thead>
                            <tbody>
                                @foreach($customFonts as $cf)
                                <tr>
                                    <td>{{ $cf->name }}</td>
                                    <td><code>{{ $cf->font_family }}</code></td>
                                    <td>{{ $cf->font_files ? implode(', ', array_keys($cf->font_files)) : '—' }}</td>
                                    <td>
                                        <form action="{{ route('admin.config.custom-font.delete', $cf->id) }}" method="post" class="d-inline" onsubmit="return confirm('Remove this font?');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>

                <!-- Contact Tab -->
                <div class="tab-pane fade" id="contact" role="tabpanel">
                    <div class="form-section-title">
                        <i class="fa fa-envelope"></i>
                        Contact Information
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" value="{{ $settings->email }}" class="form-control" placeholder="contact@example.com">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Phone</label>
                                <input type="text" name="phone" value="{{ $settings->phone }}" class="form-control" placeholder="+1 234 567 8900">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Address</label>
                        <textarea name="address" rows="3" class="form-control" placeholder="Enter site address">{{ $settings->address }}</textarea>
                </div>

                <div class="form-group">
                        <label>Timezone</label>
                        @include('partials.general.timezones', ['selected' => $settings->timezone])
                    </div>
                </div>

                <!-- Email Tab -->
                <div class="tab-pane fade" id="email" role="tabpanel">
                    @if(empty($emailFields))
                        <div class="alert alert-warning mb-0">
                            <i class="fa fa-exclamation-triangle mr-1"></i>
                            Email settings require a database migration. Run <code>php artisan migrate</code> to enable this section.
                        </div>
                    @else
                        @php
                            $emailDriverForm = $emailFields['email_driver']['form_value'] ?? $emailFields['email_driver']['value'] ?? 'exchange';
                            $emailDriverEffective = $emailFields['email_driver']['value'] ?? 'exchange';
                            $showExchange = $emailDriverForm === 'exchange';
                        @endphp

                        <div class="form-section-title">
                            <i class="fa fa-paper-plane"></i>
                            Outbound Email
                        </div>

                        <div class="email-section-intro">
                            <i class="fa fa-info-circle"></i>
                            Choose the default sending method for system emails (password reset, notifications, reminders).
                            Values saved here are stored in the database and <strong>override</strong> any matching <code>.env</code> mail settings.
                        </div>

                        <div class="form-group" style="max-width: 360px;">
                            <label class="branding-field-label d-block" for="email_driver">Default sending method</label>
                            <select name="email_driver" id="email_driver" class="form-control">
                                <option value="exchange" {{ $emailDriverForm === 'exchange' ? 'selected' : '' }}>Microsoft Exchange</option>
                                <option value="smtp" {{ $emailDriverForm === 'smtp' ? 'selected' : '' }}>SMTP</option>
                            </select>
                        </div>
                        <p class="email-effective-hint mb-3">Currently active: <strong>{{ strtoupper($emailDriverEffective) }}</strong></p>

                        <div class="email-config-panel" id="email-panel-shared">
                            <h4 class="email-config-panel__title"><i class="fa fa-user"></i>Sender identity</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>From address
                                            @if($emailFields['mail_from_address']['env_locked'] ?? false)
                                                <span class="email-env-badge"><i class="fa fa-lock"></i>.env</span>
                                            @endif
                                        </label>
                                        <input type="email" name="mail_from_address" class="form-control"
                                               value="{{ $emailFields['mail_from_address']['form_value'] ?? '' }}"
                                               placeholder="{{ $emailFields['mail_from_address']['value'] ?? 'noreply@example.com' }}">
                                        @if($emailFields['mail_from_address']['has_env_override'] ?? false)
                                            <div class="email-effective-hint">.env fallback: <strong>{{ $emailFields['mail_from_address']['value'] }}</strong> (database value overrides after save)</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-0">
                                        <label>From name
                                            @if($emailFields['mail_from_name']['env_locked'] ?? false)
                                                <span class="email-env-badge"><i class="fa fa-lock"></i>.env</span>
                                            @endif
                                        </label>
                                        <input type="text" name="mail_from_name" class="form-control"
                                               value="{{ $emailFields['mail_from_name']['form_value'] ?? '' }}"
                                               placeholder="{{ $emailFields['mail_from_name']['value'] ?? config('app.name') }}">
                                        @if($emailFields['mail_from_name']['has_env_override'] ?? false)
                                            <div class="email-effective-hint">.env fallback: <strong>{{ $emailFields['mail_from_name']['value'] }}</strong> (database value overrides after save)</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="email-config-panel js-email-driver-panel" id="email-panel-exchange" style="{{ $showExchange ? '' : 'display:none;' }}">
                            <h4 class="email-config-panel__title"><i class="fa fa-windows"></i>Microsoft Exchange / Graph API</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Tenant ID
                                            @if($emailFields['exchange_tenant_id']['env_locked'] ?? false)<span class="email-env-badge"><i class="fa fa-lock"></i>.env</span>@endif
                                        </label>
                                        <input type="text" name="exchange_tenant_id" class="form-control"
                                               value="{{ $emailFields['exchange_tenant_id']['form_value'] ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Client ID
                                            @if($emailFields['exchange_client_id']['env_locked'] ?? false)<span class="email-env-badge"><i class="fa fa-lock"></i>.env</span>@endif
                                        </label>
                                        <input type="text" name="exchange_client_id" class="form-control"
                                               value="{{ $emailFields['exchange_client_id']['form_value'] ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Client secret
                                            @if($emailFields['exchange_client_secret']['env_locked'] ?? false)<span class="email-env-badge"><i class="fa fa-lock"></i>.env</span>@endif
                                        </label>
                                        <input type="password" name="exchange_client_secret" class="form-control" autocomplete="new-password"
                                               placeholder="{{ !empty($emailFields['exchange_client_secret']['db_value']) ? '•••••••• (leave blank to keep)' : 'Enter client secret' }}"
                                               {{ ($emailFields['exchange_client_secret']['env_locked'] ?? false) ? 'readonly' : '' }}>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Auth method</label>
                                        @php $exchangeAuth = $emailFields['exchange_auth_method']['form_value'] ?? 'client_credentials'; @endphp
                                        <select name="exchange_auth_method" class="form-control">
                                            <option value="client_credentials" {{ $exchangeAuth === 'client_credentials' ? 'selected' : '' }}>Client credentials</option>
                                            <option value="authorization_code" {{ $exchangeAuth === 'authorization_code' ? 'selected' : '' }}>Authorization code</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Redirect URI
                                            @if($emailFields['exchange_redirect_uri']['env_locked'] ?? false)<span class="email-env-badge"><i class="fa fa-lock"></i>.env</span>@endif
                                        </label>
                                        <input type="text" name="exchange_redirect_uri" class="form-control"
                                               value="{{ $emailFields['exchange_redirect_uri']['form_value'] ?? '' }}"
                                               placeholder="{{ url('/auth/microsoft/callback') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-0">
                                        <label>Scope
                                            @if($emailFields['exchange_scope']['env_locked'] ?? false)<span class="email-env-badge"><i class="fa fa-lock"></i>.env</span>@endif
                                        </label>
                                        <input type="text" name="exchange_scope" class="form-control"
                                               value="{{ $emailFields['exchange_scope']['form_value'] ?? '' }}"
                                               placeholder="https://graph.microsoft.com/.default">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="email-config-panel js-email-driver-panel" id="email-panel-smtp" style="{{ $showExchange ? 'display:none;' : '' }}">
                            <h4 class="email-config-panel__title"><i class="fa fa-server"></i>SMTP server</h4>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label>Host
                                            @if($emailFields['mail_host']['env_locked'] ?? false)<span class="email-env-badge"><i class="fa fa-lock"></i>.env</span>@endif
                                        </label>
                                        <input type="text" name="mail_host" class="form-control"
                                               value="{{ $emailFields['mail_host']['form_value'] ?? '' }}"
                                               placeholder="{{ $emailFields['mail_host']['value'] ?? 'smtp.office365.com' }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Port
                                            @if($emailFields['mail_port']['env_locked'] ?? false)<span class="email-env-badge"><i class="fa fa-lock"></i>.env</span>@endif
                                        </label>
                                        <input type="text" name="mail_port" class="form-control"
                                               value="{{ $emailFields['mail_port']['form_value'] ?? '' }}"
                                               placeholder="{{ $emailFields['mail_port']['value'] ?? '587' }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Username
                                            @if($emailFields['mail_username']['env_locked'] ?? false)<span class="email-env-badge"><i class="fa fa-lock"></i>.env</span>@endif
                                        </label>
                                        <input type="text" name="mail_username" class="form-control"
                                               value="{{ $emailFields['mail_username']['form_value'] ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Password
                                            @if($emailFields['mail_password']['env_locked'] ?? false)<span class="email-env-badge"><i class="fa fa-lock"></i>.env</span>@endif
                                        </label>
                                        <input type="password" name="mail_password" class="form-control" autocomplete="new-password"
                                               placeholder="{{ !empty($emailFields['mail_password']['db_value']) ? '•••••••• (leave blank to keep)' : 'Enter SMTP password' }}"
                                               {{ ($emailFields['mail_password']['env_locked'] ?? false) ? 'readonly' : '' }}>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-0">
                                        <label>Encryption
                                            @if($emailFields['mail_encryption']['env_locked'] ?? false)<span class="email-env-badge"><i class="fa fa-lock"></i>.env</span>@endif
                                        </label>
                                        @php $encForm = $emailFields['mail_encryption']['form_value'] ?? 'tls'; @endphp
                                        <select name="mail_encryption" class="form-control">
                                            <option value="tls" {{ $encForm === 'tls' ? 'selected' : '' }}>TLS</option>
                                            <option value="ssl" {{ $encForm === 'ssl' ? 'selected' : '' }}>SSL</option>
                                            <option value="none" {{ in_array($encForm, ['none', ''], true) ? 'selected' : '' }}>None</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Badges Tab -->
                <div class="tab-pane fade" id="badges" role="tabpanel">
                    <div class="form-section-title">
                        <i class="fa fa-trophy"></i>
                        Community Contribution Badges
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle me-2"></i>
                        <strong>Badge Configuration:</strong> Adjust the contribution thresholds required to earn each badge. Badges are awarded monthly based on the total contributions (publications + forum posts + forum comments) in a community. Changes will apply to future badge awards.
                    </div>

                    @if(isset($badgeTypes) && $badgeTypes->count() > 0)
                        <div class="row">
                            @foreach($badgeTypes as $badgeType)
                                <div class="col-md-6 mb-4">
                                    <div class="card" style="border-left: 4px solid {{ $badgeType->badge_color }}; border-radius: 8px;">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                <span style="font-size: 2em; margin-right: 12px;">
                                                    @if($badgeType->slug === 'silver')🥈
                                                    @elseif($badgeType->slug === 'gold')🥇
                                                    @elseif($badgeType->slug === 'platinum')💎
                                                    @elseif($badgeType->slug === 'diamond')💠
                                                    @else🏅
                                                    @endif
                                                </span>
                                                <div>
                                                    <h5 class="mb-0" style="color: {{ $badgeType->badge_color }};">
                                                        {{ $badgeType->name }}
                                                        @if($badgeType->slug === 'diamond')
                                                            <span class="badge badge-secondary ml-2" style="font-size: 0.7rem;">Hall of Honor</span>
                                                        @endif
                                                    </h5>
                                                    <small class="text-muted">{{ $badgeType->description }}</small>
                                                </div>
                                            </div>

                                            <input type="hidden" name="badge_ids[]" value="{{ $badgeType->id }}">
                                            
                                            <div class="form-group mb-3">
                                                <label for="threshold_{{ $badgeType->id }}" style="font-weight: 600;">
                                                    Contribution Threshold <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <input type="number" 
                                                           name="badge_thresholds[{{ $badgeType->id }}]" 
                                                           id="threshold_{{ $badgeType->id }}"
                                                           value="{{ $badgeType->contribution_threshold }}" 
                                                           class="form-control" 
                                                           min="1" 
                                                           max="1000" 
                                                           step="1"
                                                           required>
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">contributions/month</span>
                                                    </div>
                                                </div>
                                                <small class="info-text">
                                                    Minimum number of contributions (publications + forum posts + comments) required in a month.
                                                </small>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="badge_name_{{ $badgeType->id }}" style="font-weight: 600;">
                                                    Badge Name <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" 
                                                       name="badge_names[{{ $badgeType->id }}]" 
                                                       id="badge_name_{{ $badgeType->id }}"
                                                       value="{{ $badgeType->name }}" 
                                                       class="form-control" 
                                                       required>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="badge_desc_{{ $badgeType->id }}" style="font-weight: 600;">
                                                    Description
                                                </label>
                                                <textarea name="badge_descriptions[{{ $badgeType->id }}]" 
                                                          id="badge_desc_{{ $badgeType->id }}"
                                                          rows="2" 
                                                          class="form-control">{{ $badgeType->description }}</textarea>
                                            </div>

                                            <div class="form-group mb-0">
                                                <label for="badge_color_{{ $badgeType->id }}" style="font-weight: 600;">
                                                    Badge Color
                                                </label>
                                                <div class="colorPicker" id="badgeColorPicker{{ $badgeType->id }}">
                                                    <input type="text" 
                                                           name="badge_colors[{{ $badgeType->id }}]" 
                                                           id="badge_color_{{ $badgeType->id }}"
                                                           value="{{ $badgeType->badge_color }}" 
                                                           class="form-control">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">
                                                            <div class="color-preview" style="background-color: {{ $badgeType->badge_color }};"></div>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group mt-3 mb-0">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" 
                                                           class="custom-control-input" 
                                                           id="badge_active_{{ $badgeType->id }}"
                                                           name="badge_active[{{ $badgeType->id }}]"
                                                           value="1"
                                                           {{ $badgeType->is_active ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="badge_active_{{ $badgeType->id }}">
                                                        Active (Award this badge)
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="alert alert-warning mt-4">
                            <i class="fa fa-exclamation-triangle me-2"></i>
                            <strong>Important:</strong> Badge thresholds should be in ascending order (e.g., 5, 10, 20, 40). The system awards the highest badge a user qualifies for based on their contribution count.
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="fa fa-exclamation-triangle me-2"></i>
                            No badge types found. Please run the migrations to create default badges.
                        </div>
                    @endif
                </div>

                <!-- Advanced Tab -->
                <div class="tab-pane fade" id="advanced" role="tabpanel">
                    <div class="form-section-title">
                        <i class="fa fa-code"></i>
                        Advanced Settings
                    </div>
                    <p class="settings-group-help">Grouped controls make governance and day-to-day maintenance clearer. Related options are now bundled by function.</p>

                    <div class="alert alert-light border mb-4">
                        <i class="fa fa-graduation-cap me-2 text-primary"></i>
                        <strong>{{ __('admin_nav.learning_integrations_moved_title') }}</strong>
                        {{ __('admin_nav.learning_integrations_moved_body') }}
                        <a href="{{ route('admin.courses.integrations') }}">{{ __('admin_nav.learning_integrations_moved_link') }}</a>.
                    </div>
                    @if(isset($settingKeyGroups) && $settingKeyGroups->count() > 0)
                        <div class="mb-3 d-flex flex-wrap gap-2">
                            @foreach($settingKeyGroups as $groupName => $rows)
                                <span class="badge rounded-pill text-bg-light border px-3 py-2">
                                    {{ $groupName }} <span class="text-muted">({{ $rows->count() }})</span>
                                </span>
                            @endforeach
                        </div>
                    @endif

                    <div class="row g-3">
                        <div class="col-lg-6">
                            <div class="settings-group-card">
                                <div class="settings-group-title">
                                    <i class="fa fa-bullhorn"></i>
                                    Platform Content & Tracking
                                </div>
                                <div class="form-group">
                                    <label>Google Analytics Script</label>
                                    <textarea name="analytics_script" rows="6" class="form-control" placeholder="Paste your Google Analytics script here">{{ $settings->analytics_script }}</textarea>
                                    <small class="info-text">Paste the complete Google Analytics tracking code.</small>
                                </div>
                                <div class="form-group mb-0">
                                    <label>Content Disclaimer</label>
                                    <textarea name="content_disclaimer" rows="5" class="form-control" placeholder="Enter content disclaimer text">{{ $settings->content_disclaimer }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="settings-group-card">
                                <div class="settings-group-title">
                                    <i class="fa fa-sign-in-alt"></i>
                                    Authentication & Social Sign-In
                                </div>
                                <div class="form-group">
                                    <div class="form-check mt-2">
                                        <input type="checkbox" class="form-check-input" id="allow_email_password_accounts_social_login" name="allow_email_password_accounts_social_login" value="1" @if(!isset($settings->allow_email_password_accounts_social_login) || $settings->allow_email_password_accounts_social_login) checked @endif>
                                        <label class="form-check-label" for="allow_email_password_accounts_social_login">
                                            Allow email/password accounts to sign in with social login
                                        </label>
                                    </div>
                                    <small class="info-text d-block">When enabled, users who originally registered with email/password can use social login if the email matches. A successful social login verifies unverified accounts and updates sign-in method to social.</small>
                                </div>
                                <p class="text-muted small mb-0">
                                    Microsoft, Google, and LinkedIn credentials are saved in the <strong>Social Login</strong> section below the tabs. Saving general settings no longer changes sign-in provider toggles.
                                </p>
                                @if(\Illuminate\Support\Facades\Schema::hasColumn('setting', 'block_disposable_email_registration'))
                                <hr class="my-3">
                                <div class="form-group mb-2">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="block_disposable_email_registration" name="block_disposable_email_registration" value="1" @if(!isset($settings->block_disposable_email_registration) || $settings->block_disposable_email_registration) checked @endif>
                                        <label class="form-check-label" for="block_disposable_email_registration">
                                            Block temporary / disposable email addresses at registration
                                        </label>
                                    </div>
                                    <small class="info-text d-block">Rejects sign-ups from known throwaway providers (built-in list plus domains below). Applies to web <code>/register</code> and API registration.</small>
                                </div>
                                @if(\Illuminate\Support\Facades\Schema::hasColumn('setting', 'blocked_email_domains'))
                                <div class="form-group mb-0">
                                    <label for="blocked_email_domains">Additional blocked email domains</label>
                                    <textarea name="blocked_email_domains" id="blocked_email_domains" rows="8" class="form-control font-monospace" placeholder="hidingmail.net&#10;example-temp-mail.com">{{ $settings->blocked_email_domains ?? '' }}</textarea>
                                    <small class="info-text d-block mt-1">One domain per line (or comma-separated). Subdomains are blocked too (e.g. <code>mail.hidingmail.net</code> when <code>hidingmail.net</code> is listed). Example disposable provider: <a href="https://fidro.io/disposable-emails/hidingmail.net" target="_blank" rel="noopener">hidingmail.net</a>.</small>
                                </div>
                                @endif
                                @endif
                            </div>
                        </div>

                        @if(\Illuminate\Support\Facades\Schema::hasColumn('setting', 'admin_units_enabled'))
                        <div class="col-lg-6">
                            <div class="settings-group-card">
                                <div class="settings-group-title">
                                    <i class="fa fa-globe-africa"></i>
                                    Hub deployment &amp; federation
                                </div>
                                <p class="text-muted small mb-3">
                                    Configure whether this installation is a continental portal or a country hub, set the owner country/region defaults on publication forms, and optionally protect federation API endpoints with a token.
                                    Manage remote hubs under <a href="{{ route('admin.federation.index') }}">Federated Knowledge Hubs</a>.
                                </p>
                                <div class="form-group">
                                    <div class="form-check">
                                        <input type="checkbox"
                                               class="form-check-input"
                                               id="admin_units_enabled"
                                               name="admin_units_enabled"
                                               value="1"
                                               @if(old('admin_units_enabled', $settings->admin_units_enabled ?? (admin_units_enabled() ? 1 : 0))) checked @endif>
                                        <label class="form-check-label" for="admin_units_enabled">
                                            Use administrative units (country hub mode)
                                        </label>
                                    </div>
                                    <small class="info-text d-block">
                                        When unchecked, this hub behaves as a continental portal: new publications and forums are always publicly federated (<code>public_availability = 1</code>).
                                        When checked, authors can limit content to this country hub only.
                                    </small>
                                </div>
                                @if(\Illuminate\Support\Facades\Schema::hasColumn('setting', 'default_owner_country_id'))
                                <div class="form-group">
                                    <label for="default_owner_country_id">Default owner country</label>
                                    <select name="default_owner_country_id" id="default_owner_country_id" class="form-control select2">
                                        <option value="">— Use .env <code>HUB_OWNER_COUNTRY_ID</code> —</option>
                                        @foreach($hubCountries as $country)
                                            <option value="{{ $country->id }}" {{ (int) old('default_owner_country_id', $settings->default_owner_country_id ?? 0) === (int) $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                                        @endforeach
                                    </select>
                                    <small class="info-text">Pre-selected on publication forms for country hubs.</small>
                                </div>
                                @endif
                                @if(\Illuminate\Support\Facades\Schema::hasColumn('setting', 'default_owner_region_id'))
                                <div class="form-group">
                                    <label for="default_owner_region_id">Default owner region</label>
                                    <select name="default_owner_region_id" id="default_owner_region_id" class="form-control select2">
                                        <option value="">— Derive from country or <code>HUB_OWNER_REGION_ID</code> —</option>
                                        @foreach($hubRegions as $region)
                                            <option value="{{ $region->id }}" {{ (int) old('default_owner_region_id', $settings->default_owner_region_id ?? 0) === (int) $region->id ? 'selected' : '' }}>{{ $region->region_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif
                                @if(\Illuminate\Support\Facades\Schema::hasColumn('setting', 'federation_api_token'))
                                <div class="form-group mb-0">
                                    <label for="federation_api_token">Federation API token</label>
                                    <input type="text"
                                           name="federation_api_token"
                                           id="federation_api_token"
                                           class="form-control"
                                           value="{{ old('federation_api_token', $settings->federation_api_token ?? '') }}"
                                           placeholder="Optional — leave blank for open federation endpoints">
                                    <small class="info-text">Remote hubs use this as <code>Authorization: Bearer …</code> when calling <code>/api/federation/*</code>.</small>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        @if(\Illuminate\Support\Facades\Schema::hasColumn('setting', 'africa_map_version'))
                        <div class="col-lg-12">
                            <div class="settings-group-card">
                                <div class="settings-group-title">
                                    <i class="fa fa-map"></i>
                                    {{ __('admin_nav.choropleth_maps') }}
                                </div>
                                <p class="text-muted small mb-0">
                                    {{ __('admin_nav.maps_management_intro') }}
                                    <a href="{{ route('admin.maps.index') }}">{{ __('admin_nav.maps_management') }}</a>.
                                </p>
                            </div>
                        </div>
                        @endif

                        @if(\Illuminate\Support\Facades\Schema::hasColumn('setting', 'auto_profile_completion_reminder'))
                        <div class="col-lg-6">
                            <div class="settings-group-card">
                                <div class="settings-group-title">
                                    <i class="fa fa-user-edit"></i>
                                    Profile completion reminders
                                </div>
                                <p class="text-muted small mb-3">
                                    Email users who are missing a job title or {{ admin_units_enabled() ? 'administrative unit' : 'country' }} on their profile.
                                    Automatic reminders are <strong>off by default</strong>; use the manual action when you want to notify users.
                                </p>
                                <div class="form-group">
                                    <div class="form-check">
                                        <input type="checkbox"
                                               class="form-check-input"
                                               id="auto_profile_completion_reminder"
                                               name="auto_profile_completion_reminder"
                                               value="1"
                                               @if(!empty($settings->auto_profile_completion_reminder)) checked @endif>
                                        <label class="form-check-label" for="auto_profile_completion_reminder">
                                            Enable automatic monthly reminders
                                        </label>
                                    </div>
                                    <small class="info-text d-block">
                                        When enabled, <code>profiles:remind-incomplete</code> runs on the chosen day each month at 09:00 (server time). Requires the Laravel scheduler (<code>schedule:run</code>) to be active.
                                    </small>
                                </div>
                                @if(\Illuminate\Support\Facades\Schema::hasColumn('setting', 'profile_reminder_day_of_month'))
                                <div class="form-group" id="profile-reminder-day-wrap">
                                    <label for="profile_reminder_day_of_month">Day of month (automatic run)</label>
                                    <input type="number"
                                           name="profile_reminder_day_of_month"
                                           id="profile_reminder_day_of_month"
                                           class="form-control"
                                           min="1"
                                           max="28"
                                           value="{{ (int) ($settings->profile_reminder_day_of_month ?? 1) }}">
                                    <small class="info-text">Use 1–28 so the job runs reliably in every month.</small>
                                </div>
                                @endif
                                <div class="form-group mb-0">
                                    <label class="d-block mb-2">Manual send</label>
                                    <button type="button"
                                            class="btn btn-outline-primary btn-sm"
                                            id="send-profile-reminders-btn"
                                            onclick="sendProfileReminders()">
                                        <i class="fa fa-envelope me-1"></i> Send reminders now
                                    </button>
                                    <small class="info-text d-block mt-2">
                                        Queues reminder emails for all users with incomplete profiles (same as the artisan command). Save settings first if you changed the automatic schedule.
                                    </small>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="col-lg-12">
                            <div class="settings-group-card">
                                <div class="settings-group-title">
                                    <i class="fa fa-file-alt"></i>
                                    Publication Submission Workflow
                                </div>

                                <div class="form-group">
                                    <label>Minimum Publication Description Words</label>
                                    <input type="number" name="publication_min_words" value="{{ $settings->publication_min_words ?? 150 }}" class="form-control" min="10" max="1000" step="10">
                                    <small class="info-text">Set the minimum number of words required for publication descriptions. Default is 150 words.</small>
                                </div>

                                <div class="form-group">
                                    <label>Required Fields on Publication Form</label>
                                    <small class="info-text d-block mb-3">Select which fields should be required when users submit publications:</small>
                                    
                                    @php
                                        $requiredFields = json_decode($settings->publication_required_fields ?? '{}', true);
                                        if (empty($requiredFields)) {
                                            $requiredFields = [
                                                'title' => true,
                                                'description' => true,
                                                'associated_authors' => true,
                                                'tags' => true,
                                                'theme' => true,
                                                'sub_theme' => true,
                                                'data_category_id' => true,
                                            ];
                                        }
                                    @endphp

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-check mb-2">
                                                <input type="checkbox" class="form-check-input required-field-checkbox" name="required_fields[title]" value="1" id="req_title" @if($requiredFields['title'] ?? true) checked @endif>
                                                <label class="form-check-label" for="req_title">Title</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input type="checkbox" class="form-check-input required-field-checkbox" name="required_fields[description]" value="1" id="req_description" @if($requiredFields['description'] ?? true) checked @endif>
                                                <label class="form-check-label" for="req_description">Description</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input type="checkbox" class="form-check-input required-field-checkbox" name="required_fields[associated_authors]" value="1" id="req_associated_authors" @if($requiredFields['associated_authors'] ?? true) checked @endif>
                                                <label class="form-check-label" for="req_associated_authors">Associated Authors</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input type="checkbox" class="form-check-input required-field-checkbox" name="required_fields[tags]" value="1" id="req_tags" @if($requiredFields['tags'] ?? true) checked @endif>
                                                <label class="form-check-label" for="req_tags">Tags/Health Topics</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input type="checkbox" class="form-check-input required-field-checkbox" name="required_fields[theme]" value="1" id="req_theme" @if($requiredFields['theme'] ?? true) checked @endif>
                                                <label class="form-check-label" for="req_theme">Theme</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input type="checkbox" class="form-check-input required-field-checkbox" name="required_fields[sub_theme]" value="1" id="req_sub_theme" @if($requiredFields['sub_theme'] ?? true) checked @endif>
                                                <label class="form-check-label" for="req_sub_theme">Sub Theme</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input type="checkbox" class="form-check-input required-field-checkbox" name="required_fields[data_category_id]" value="1" id="req_data_category_id" @if($requiredFields['data_category_id'] ?? true) checked @endif>
                                                <label class="form-check-label" for="req_data_category_id">Category</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check mb-2">
                                                <input type="checkbox" class="form-check-input required-field-checkbox" name="required_fields[year_published]" value="1" id="req_year_published" @if($requiredFields['year_published'] ?? false) checked @endif>
                                                <label class="form-check-label" for="req_year_published">Year Published</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input type="checkbox" class="form-check-input required-field-checkbox" name="required_fields[author]" value="1" id="req_author" @if($requiredFields['author'] ?? false) checked @endif>
                                                <label class="form-check-label" for="req_author">Source/Author</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input type="checkbox" class="form-check-input required-field-checkbox" name="required_fields[doi]" value="1" id="req_doi" @if($requiredFields['doi'] ?? false) checked @endif>
                                                <label class="form-check-label" for="req_doi">DOI</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input type="checkbox" class="form-check-input required-field-checkbox" name="required_fields[issn]" value="1" id="req_issn" @if($requiredFields['issn'] ?? false) checked @endif>
                                                <label class="form-check-label" for="req_issn">ISSN</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input type="checkbox" class="form-check-input required-field-checkbox" name="required_fields[isbn]" value="1" id="req_isbn" @if($requiredFields['isbn'] ?? false) checked @endif>
                                                <label class="form-check-label" for="req_isbn">ISBN</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input type="checkbox" class="form-check-input required-field-checkbox" name="required_fields[license_id]" value="1" id="req_license_id" @if($requiredFields['license_id'] ?? false) checked @endif>
                                                <label class="form-check-label" for="req_license_id">License</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input type="checkbox" class="form-check-input required-field-checkbox" name="required_fields[copyright_info]" value="1" id="req_copyright_info" @if($requiredFields['copyright_info'] ?? false) checked @endif>
                                                <label class="form-check-label" for="req_copyright_info">Copyright Info</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-0">
                                    <label>Version Submission Settings</label>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="enable_version_submission" name="enable_version_submission" value="1" @if(!isset($settings->enable_version_submission) || $settings->enable_version_submission) checked @endif>
                                        <label class="form-check-label" for="enable_version_submission">
                                            <i class="fa fa-plus-circle me-2"></i>Enable Version Submission
                                        </label>
                                    </div>
                                    <small class="info-text">When enabled, users can submit new versions of publications. Only parent publications (non-versions) can have versions submitted. Versions themselves cannot have versions submitted.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
    </form>

    @if(!empty($ssoFields))
        <div class="settings-container mt-4 {{ (settings()->site_theme ?? '') === 'theme1.' ? 'settings-theme1' : '' }}" id="social-login-settings">
            <div class="settings-header">
                <h2><i class="fa fa-sign-in-alt me-2"></i>Social Login</h2>
                <p>Configure Microsoft, Google, and LinkedIn sign-in separately from general settings.</p>
            </div>
            <div class="p-4">
                @include('admin.settings.partials.sso_credentials_form', ['ssoFields' => $ssoFields, 'standalone' => true])
            </div>
        </div>
    @endif

            <div class="settings-action-bar">
                <div class="settings-action-bar__inner">
                    <div class="settings-action-bar__primary">
                        <p class="settings-action-bar__primary-label">Save configuration</p>
                        <button type="submit" class="btn btn-save" form="settings-main-form">
                            <i class="fa fa-save me-2"></i>Save All Changes
                        </button>
                        <p class="settings-action-bar__primary-hint">Applies updates from all tabs above.</p>
                    </div>

                    <div class="settings-tools-panel">
                        <p class="settings-tools-panel__title">
                            <i class="fa fa-wrench"></i>Backup &amp; maintenance
                        </p>
                        <div class="settings-tools-panel__actions">
                            <a href="{{ route('admin.config.export') }}" class="btn-tool" title="Download current configuration as XML (no images)">
                                <i class="fa fa-download"></i>Export Config (XML)
                            </a>

                            <form action="{{ route('admin.config.import') }}" method="post" enctype="multipart/form-data" class="settings-import-group" id="import-config-form">
                                @csrf
                                <input type="file" name="config_file" id="import-config-file" accept=".xml,application/xml,text/xml" required>
                                <button type="button" class="btn-tool" id="import-config-choose" title="Select a config XML file">
                                    <i class="fa fa-folder-open"></i>Choose XML
                                </button>
                                <span class="settings-import-filename" id="import-config-filename">No file selected</span>
                                <button type="submit" class="btn-tool btn-tool--accent" id="import-config-submit" disabled title="Import configuration from XML (overwrites current theme settings; images are not imported)">
                                    <i class="fa fa-upload"></i>Import Config
                                </button>
                            </form>

                            <button type="button"
                                    class="btn-tool"
                                    onclick="clearCache()"
                                    id="clear-cache-btn"
                                    title="Clear all cached data including settings">
                                <i class="fa fa-broom"></i>Clear Cache
                            </button>
                        </div>
                    </div>
                </div>

                <p class="settings-action-footnote mb-0">
                    <i class="fa fa-info-circle"></i>
                    Settings are cached for 24 hours for better performance — use <strong>Clear Cache</strong> to apply changes immediately.
                    <strong>Export</strong> saves the active theme as XML (images excluded).
                    <strong>Import</strong> overwrites the active theme from XML (images are not changed).
                </p>
            </div>
        </div>

    <!-- Cache Clear Output Modal -->
    <div class="modal fade" id="cacheOutputModal" tabindex="-1" aria-labelledby="cacheOutputModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cacheOutputModalLabel">
                        <i class="fa fa-terminal me-2"></i>Cache Clear Output
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="cache-output-terminal" class="terminal-output">
                        <div class="terminal-line">
                            <span class="terminal-prompt">$</span>
                            <span class="terminal-text">Waiting for output...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="copyCacheOutput()">
                        <i class="fa fa-copy me-2"></i>Copy Output
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @include('common.select2')
    @include('common.attachment_js')
    @if((settings()->site_theme ?? '') !== 'theme1.')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/3.2.0/js/bootstrap-colorpicker.min.js"></script>
    @endif

    <script>
        function toggleProfileReminderDayField() {
            const auto = document.getElementById('auto_profile_completion_reminder');
            const wrap = document.getElementById('profile-reminder-day-wrap');
            if (!auto || !wrap) return;
            wrap.style.display = auto.checked ? '' : 'none';
        }

        document.addEventListener('DOMContentLoaded', function () {
            toggleProfileReminderDayField();
            const auto = document.getElementById('auto_profile_completion_reminder');
            if (auto) {
                auto.addEventListener('change', toggleProfileReminderDayField);
            }

            const importFileInput = document.getElementById('import-config-file');
            const importChooseBtn = document.getElementById('import-config-choose');
            const importFilename = document.getElementById('import-config-filename');
            const importSubmitBtn = document.getElementById('import-config-submit');
            const importForm = document.getElementById('import-config-form');

            if (importChooseBtn && importFileInput) {
                importChooseBtn.addEventListener('click', function () {
                    importFileInput.click();
                });
            }

            if (importFileInput && importFilename && importSubmitBtn) {
                importFileInput.addEventListener('change', function () {
                    const file = importFileInput.files && importFileInput.files[0];
                    if (file) {
                        importFilename.textContent = file.name;
                        importFilename.classList.add('has-file');
                        importSubmitBtn.disabled = false;
                    } else {
                        importFilename.textContent = 'No file selected';
                        importFilename.classList.remove('has-file');
                        importSubmitBtn.disabled = true;
                    }
                });
            }

            if (importForm) {
                importForm.addEventListener('submit', function (e) {
                    if (!importFileInput || !importFileInput.files || !importFileInput.files.length) {
                        e.preventDefault();
                        return;
                    }
                    if (!confirm('Import configuration from this XML file? This will overwrite the current active theme settings (images are not changed).')) {
                        e.preventDefault();
                    }
                });
            }
        });

        function sendProfileReminders() {
            if (!confirm('Send profile completion reminder emails to all users with incomplete profiles now?')) {
                return;
            }
            const btn = document.getElementById('send-profile-reminders-btn');
            if (!btn) return;
            const original = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Sending...';

            fetch('{{ route("admin.config.send-profile-reminders") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                const msg = data['alert-success'] || data['alert-danger'] || 'Done.';
                alert(msg);
            })
            .catch(() => alert('Failed to send profile reminders. Please try again.'))
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = original;
            });
        }

        function clearCache() {
            const btn = document.getElementById('clear-cache-btn');
            const originalText = btn.innerHTML;
            
            // Disable button and show loading
            btn.disabled = true;
            btn.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i>Clearing...';
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('cacheOutputModal'));
            document.getElementById('cache-output-terminal').innerHTML = '<div class="terminal-line"><span class="terminal-prompt">$</span><span class="terminal-text">Initializing cache clear...</span></div>';
            modal.show();
            
            // Make AJAX request
            fetch('{{ route("admin.config.clear-cache") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Display output
                let outputHtml = '';
                if (data.output) {
                    const lines = data.output.split('\n');
                    lines.forEach(line => {
                        if (line.trim() === '') return;
                        
                        let className = 'terminal-text';
                        if (line.includes('✓') || line.includes('successfully')) {
                            className = 'terminal-success';
                        } else if (line.includes('✗') || line.includes('Error')) {
                            className = 'terminal-error';
                        } else if (line.includes('===') || line.includes('Clearing')) {
                            className = 'terminal-info';
                        }
                        
                        outputHtml += `<div class="terminal-line"><span class="terminal-text">${escapeHtml(line)}</span></div>`;
                    });
                } else {
                    outputHtml = '<div class="terminal-line"><span class="terminal-success">Cache cleared successfully!</span></div>';
                }
                
                document.getElementById('cache-output-terminal').innerHTML = outputHtml;
                
                // Scroll to bottom
                const terminal = document.getElementById('cache-output-terminal');
                terminal.scrollTop = terminal.scrollHeight;
                
                // Show success message
                if (data['alert-success']) {
                    showAlert('success', data['alert-success']);
                } else if (data['alert-danger']) {
                    showAlert('danger', data['alert-danger']);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('cache-output-terminal').innerHTML = 
                    '<div class="terminal-line"><span class="terminal-error">Error: ' + escapeHtml(error.message) + '</span></div>';
                showAlert('danger', 'Error clearing cache: ' + error.message);
            })
            .finally(() => {
                // Re-enable button
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function copyCacheOutput() {
            const terminal = document.getElementById('cache-output-terminal');
            const text = terminal.innerText || terminal.textContent;
            
            navigator.clipboard.writeText(text).then(() => {
                const btn = event.target;
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="fa fa-check me-2"></i>Copied!';
                btn.classList.add('btn-success');
                btn.classList.remove('btn-primary');
                
                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.classList.remove('btn-success');
                    btn.classList.add('btn-primary');
                }, 2000);
            }).catch(err => {
                alert('Failed to copy: ' + err);
            });
        }

        function showAlert(type, message) {
            // Remove existing alerts
            const existingAlerts = document.querySelectorAll('.alert');
            existingAlerts.forEach(alert => alert.remove());
            
            // Create new alert
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.setAttribute('role', 'alert');
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            
            // Insert at top of content
            const content = document.querySelector('.settings-container');
            if (content) {
                content.insertBefore(alertDiv, content.firstChild);
            }
        }

        $(function() {
            // Tab switching functionality
            function syncEmailDriverPanels() {
                var $driver = $('#email_driver');
                if (!$driver.length) {
                    return;
                }
                var driver = $driver.val() || 'exchange';
                $('.js-email-driver-panel').hide();
                if (driver === 'smtp') {
                    $('#email-panel-smtp').show();
                } else {
                    $('#email-panel-exchange').show();
                }
            }

            $('#email_driver').on('change', syncEmailDriverPanels);
            syncEmailDriverPanels();

            $('.settings-tabs .nav-link').on('click', function(e) {
                e.preventDefault();
                
                var targetTab = $(this).data('tab');
                
                // Remove active class from all tabs and panes
                $('.settings-tabs .nav-link').removeClass('active').attr('aria-selected', 'false');
                $('.tab-pane').removeClass('show active');
                
                // Add active class to clicked tab
                $(this).addClass('active').attr('aria-selected', 'true');
                
                // Show corresponding tab pane
                $('#' + targetTab).addClass('show active');
            });

            // Initialize color pickers (Theme1 uses Bootstrap 5 — use native color input; else use bootstrap-colorpicker)
            var isTheme1 = {{ (settings()->site_theme ?? '') === 'theme1.' ? 'true' : 'false' }};
            $('.colorPicker').each(function() {
                var $picker = $(this);
                var $input = $picker.find('input[type="text"]');
                var $preview = $picker.find('.color-preview');
                var defaultColor = $input.val() || '#119A48';

                if (isTheme1) {
                    // Theme1: native color input + sync (no bootstrap-colorpicker — incompatible with Bootstrap 5)
                    $preview.css('background-color', defaultColor);
                    $input.on('input change', function() {
                        var v = $(this).val();
                        if (/^#[0-9A-Fa-f]{6}$/.test(v) || /^#[0-9A-Fa-f]{3}$/.test(v)) {
                            $preview.css('background-color', v);
                        }
                        if ($picker.attr('id') === 'gradientStartPicker' || $picker.attr('id') === 'gradientEndPicker') {
                            updateGradientPreview();
                        }
                    });
                    // Add native color input beside preview for easy picking
                    var $append = $picker.find('.input-group-append');
                    if ($append.length && !$picker.find('input[type="color"]').length) {
                        var hex = /^#[0-9A-Fa-f]{6}$/.test(defaultColor) ? defaultColor : '#119A48';
                        var $native = $('<input type="color" class="form-control form-control-color border-0 p-0" value="' + hex + '" title="Choose color">');
                        $native.on('input', function() {
                            $input.val(this.value);
                            $preview.css('background-color', this.value);
                            if ($picker.attr('id') === 'gradientStartPicker' || $picker.attr('id') === 'gradientEndPicker') updateGradientPreview();
                        });
                        $append.prepend($native);
                    }
                } else {
                    $picker.colorpicker({
                        format: 'hex',
                        color: defaultColor
                    }).on('colorpickerChange colorpickerCreate', function(e) {
                        $preview.css('background-color', e.color.toString());
                        $input.val(e.color.toString());
                        if ($picker.attr('id') === 'gradientStartPicker' || $picker.attr('id') === 'gradientEndPicker') {
                            updateGradientPreview();
                        }
                    });
                    if ($preview.length) {
                        $preview.css('background-color', defaultColor);
                    }
                }
            });

            // Update gradient preview function
            function updateGradientPreview() {
                var startColor = $('#gradientStartPicker input').val() || '#119A48';
                var endColor = $('#gradientEndPicker input').val() || '#16c653';
                $('#gradientPreview').css('background', 'linear-gradient(135deg, ' + startColor + ' 0%, ' + endColor + ' 100%)');
            }

            // Initialize gradient preview on page load
            updateGradientPreview();

            // Update gradient preview when inputs change
            $('#gradientStartPicker input, #gradientEndPicker input').on('change', function() {
                updateGradientPreview();
            });

            $('.js-branding-upload-trigger').on('click', function () {
                var targetId = $(this).data('target');
                if (targetId) {
                    document.getElementById(targetId).click();
                }
            });

            function updateBrandingAssetPreview($input, dataUrl, fileName) {
                var $card = $input.closest('.branding-asset-card');
                var $preview = $card.find('.js-branding-preview');
                var $filename = $card.find('.js-branding-filename');

                if (dataUrl) {
                    $preview.html('<img src="' + dataUrl + '" alt="Preview">');
                }
                if (fileName) {
                    $filename.text(fileName).addClass('has-file');
                }
            }

            $('#logo, #favicon').on('change', function (e) {
                var file = e.target.files[0];
                if (!file) return;
                var $input = $(this);
                var reader = new FileReader();
                reader.onload = function (event) {
                    updateBrandingAssetPreview($input, event.target.result, file.name);
                };
                reader.readAsDataURL(file);
            });

            $('#logo_existing, #favicon_existing').on('change', function () {
                var selected = $(this).val();
                if (!selected) return;
                var $card = $(this).closest('.branding-asset-card');
                $card.find('.js-branding-filename').text('Gallery: ' + selected).addClass('has-file');
            });

            // Spotlight banner preview (legacy layout)
            $('#spotlight_banner').on('change', function(e) {
                var file = e.target.files[0];
                if (file) {
                    $('#spotlight_banner_existing').val('');
                    var reader = new FileReader();
                    var $input = $(this);
                    reader.onload = function(e) {
                        var previewHtml = '<div class="image-preview mt-2"><img src="' + e.target.result + '" alt="Preview"></div>';
                        $input.siblings('.image-preview').remove();
                        $input.after(previewHtml);
                    };
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>
@endsection
