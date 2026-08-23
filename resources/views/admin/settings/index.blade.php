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

        #settings-main-form {
            display: contents;
        }

        #settingsTabContent .tab-pane {
            display: none;
        }

        #settingsTabContent .tab-pane.active.show {
            display: block;
        }

        .settings-grid-row {
            margin-bottom: 0.25rem;
        }

        .settings-grid-row > [class*="col-"] {
            margin-bottom: 1rem;
        }

        .settings-field--compact label {
            margin-bottom: 0.35rem;
        }

        .settings-toggle-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0.75rem;
        }

        .settings-toggle-card {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 0.85rem 0.95rem;
            background: #f8fafc;
            min-height: 3.25rem;
        }

        .settings-color-picker__controls {
            display: flex;
            align-items: stretch;
        }

        .settings-color-native {
            width: 44px;
            min-width: 44px;
            height: auto;
            min-height: 48px;
            padding: 0.15rem;
            border: none;
            border-left: 1px solid #e2e8f0;
            background: #fff;
            cursor: pointer;
        }

        .settings-color-picker .settings-color-text {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 0.875rem;
        }

        .branding-asset-preview--banner {
            width: 168px;
            height: 58px;
        }

        .branding-asset-card--feature .branding-asset-card__header {
            align-items: center;
        }

        .branding-asset-card--feature .branding-asset-preview--banner {
            width: min(100%, 280px);
            height: auto;
            aspect-ratio: 1894 / 658;
            max-height: 96px;
        }

        @media (max-width: 991.98px) {
            .settings-toggle-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 575.98px) {
            .settings-toggle-grid {
                grid-template-columns: 1fr;
            }
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
                    <button class="nav-link" id="branding-tab" data-tab="branding" type="button" role="tab" aria-controls="branding" aria-selected="false">
                        <i class="fa fa-image me-2"></i>Branding
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="colors-tab" data-tab="colors" type="button" role="tab" aria-controls="colors" aria-selected="false">
                        <i class="fa fa-palette me-2"></i>Colors &amp; Typography
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="navigation-tab" data-tab="navigation" type="button" role="tab" aria-controls="navigation" aria-selected="false">
                        <i class="fa fa-bars me-2"></i>Navigation
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="homepage-tab" data-tab="homepage" type="button" role="tab" aria-controls="homepage" aria-selected="false">
                        <i class="fa fa-home me-2"></i>Homepage
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="search-tab" data-tab="search" type="button" role="tab" aria-controls="search" aria-selected="false">
                        <i class="fa fa-search me-2"></i>Search
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
                @if(!empty($ssoFields))
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="social-login-tab" data-tab="social-login" type="button" role="tab" aria-controls="social-login" aria-selected="false">
                        <i class="fa fa-sign-in-alt me-2"></i>Social Login
                    </button>
                </li>
                @endif
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
                <form action="{{ route('admin.config.save') }}" method="post" enctype="multipart/form-data" id="settings-main-form">
                    @csrf
                @include('admin.settings.partials.tab_general')
                @include('admin.settings.partials.tab_branding', ['configGalleryImages' => $configGalleryImages ?? []])
                @include('admin.settings.partials.tab_colors')
                @include('admin.settings.partials.tab_navigation')
                @include('admin.settings.partials.tab_homepage')
                @include('admin.settings.partials.tab_search')

                <!-- Contact Tab -->
                <div class="tab-pane fade" id="contact" role="tabpanel">
                    <div class="form-section-title">
                        <i class="fa fa-envelope"></i>
                        Contact Information
                    </div>

                    <div class="row settings-grid-row">
                        <div class="col-md-4">
                            <div class="form-group settings-field--compact mb-0">
                                <label>Email</label>
                                <input type="email" name="email" value="{{ $settings->email }}" class="form-control" placeholder="contact@example.com">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group settings-field--compact mb-0">
                                <label>Phone</label>
                                <input type="text" name="phone" value="{{ $settings->phone }}" class="form-control" placeholder="+1 234 567 8900">
                </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group settings-field--compact mb-0">
                                <label>Timezone</label>
                                @include('partials.general.timezones', ['selected' => $settings->timezone])
                </div>
                        </div>
                    </div>

                <div class="form-group">
                        <label>Address</label>
                        <textarea name="address" rows="3" class="form-control" placeholder="Enter site address">{{ $settings->address }}</textarea>
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
                                    Microsoft, Google, and LinkedIn credentials are configured on the <strong>Social Login</strong> tab. Saving general settings does not change sign-in provider toggles.
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
                                    <div class="d-flex flex-wrap align-items-stretch" style="gap:.35rem;">
                                        <input type="text"
                                               name="federation_api_token"
                                               id="federation_api_token"
                                               class="form-control font-monospace"
                                               style="min-width:12rem;flex:1 1 16rem;"
                                               value="{{ old('federation_api_token', $settings->federation_api_token ?? '') }}"
                                               autocomplete="off"
                                               spellcheck="false"
                                               placeholder="Generate a token, then copy it for other hubs">
                                        <button type="button" class="btn btn-outline-secondary" id="copy-federation-token" title="Copy token">
                                            <i class="fa fa-copy"></i> Copy
                                        </button>
                                        <button type="button" class="btn btn-primary" id="generate-federation-token" title="Generate a new token as the signed-in admin">
                                            <i class="fa fa-key"></i> Generate
                                        </button>
                                    </div>
                                    <small class="info-text d-block mt-2" id="federation-token-status">
                                        Generate a token using your signed-in admin account, then copy it into other hubs as
                                        <code>Authorization: Bearer …</code> when they call <code>/api/federation/*</code> on this site.
                                        @if(!empty($settings->federation_api_token))
                                            A token is currently saved for this hub.
                                        @else
                                            No token is set — federation endpoints are currently open.
                                        @endif
                                    </small>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        @if(\Illuminate\Support\Facades\Schema::hasColumn('setting', 'convert_office_uploads_to_pdf'))
                        <div class="col-lg-6">
                            <div class="settings-group-card">
                                <div class="settings-group-title">
                                    <i class="fa fa-file-pdf"></i>
                                    Uploads &amp; attachments
                                </div>
                                <div class="form-group mb-0">
                                    <input type="hidden" name="convert_office_uploads_to_pdf" value="0">
                                    <div class="form-check">
                                        <input type="checkbox"
                                               class="form-check-input"
                                               id="convert_office_uploads_to_pdf"
                                               name="convert_office_uploads_to_pdf"
                                               value="1"
                                               @if(old('convert_office_uploads_to_pdf', $settings->convert_office_uploads_to_pdf ?? true)) checked @endif>
                                        <label class="form-check-label" for="convert_office_uploads_to_pdf">
                                            Convert Office documents to PDF on upload
                                        </label>
                                    </div>
                                    <small class="info-text d-block mt-2">
                                        When enabled, Word, Excel, PowerPoint, OpenDocument, and RTF uploads are converted to PDF for
                                        <strong>resource publications</strong> (account publish wizard), <strong>forum</strong> attachments, and
                                        <strong>community</strong> comment uploads. Requires LibreOffice (<code>soffice</code>) on the server for best results;
                                        <code>.docx</code> can fall back to PhpWord when LibreOffice is unavailable.
                                        If conversion fails, the <strong>original file is still saved</strong> — uploads are never blocked.
                                    </small>
                                </div>
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
                </form>

                <form id="settings-custom-font-upload-form" action="{{ route('admin.config.custom-font.store') }}" method="post" enctype="multipart/form-data" class="d-none" aria-hidden="true">
                    @csrf
                </form>
                @if(isset($customFonts) && $customFonts->count() > 0)
                    @foreach($customFonts as $cf)
                        <form id="custom-font-delete-{{ $cf->id }}" action="{{ route('admin.config.custom-font.delete', $cf->id) }}" method="post" class="d-none" aria-hidden="true">
                            @csrf
                        </form>
                    @endforeach
                @endif

                @include('admin.settings.partials.tab_social_login', ['ssoFields' => $ssoFields ?? []])
            </div>

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

        (function bindFederationTokenActions() {
            const input = document.getElementById('federation_api_token');
            const copyBtn = document.getElementById('copy-federation-token');
            const generateBtn = document.getElementById('generate-federation-token');
            const status = document.getElementById('federation-token-status');
            if (!input || !copyBtn || !generateBtn) {
                return;
            }

            const setStatus = function (message) {
                if (status) {
                    status.textContent = message;
                }
            };

            const copyToken = function () {
                const token = (input.value || '').trim();
                if (!token) {
                    setStatus('Generate a token first, then copy it for other hubs.');
                    return;
                }
                const onCopied = function () {
                    const previous = copyBtn.innerHTML;
                    copyBtn.innerHTML = '<i class="fa fa-check"></i> Copied';
                    setStatus('Token copied. Paste it on other hubs as Authorization: Bearer ' + token.substring(0, 8) + '…');
                    setTimeout(function () { copyBtn.innerHTML = previous; }, 1600);
                };
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(token).then(onCopied).catch(function () {
                        input.select();
                        document.execCommand('copy');
                        onCopied();
                    });
                    return;
                }
                input.select();
                document.execCommand('copy');
                onCopied();
            };

            copyBtn.addEventListener('click', copyToken);

            generateBtn.addEventListener('click', function () {
                if (input.value && !confirm('Replace the current federation token? Other hubs using the old token will stop working until you update them.')) {
                    return;
                }
                const previous = generateBtn.innerHTML;
                generateBtn.disabled = true;
                generateBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Generating';

                fetch('{{ route("admin.config.federation-token") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(function (response) { return response.json().then(function (data) { return { ok: response.ok, data: data }; }); })
                .then(function (result) {
                    if (!result.ok || !result.data.token) {
                        throw new Error(result.data['alert-danger'] || 'Could not generate the federation token.');
                    }
                    input.value = result.data.token;
                    setStatus(result.data['alert-success'] || ('Token generated as ' + (result.data.generated_by || 'admin') + '. Click Copy to use it on other hubs.'));
                    copyToken();
                })
                .catch(function (error) {
                    setStatus(error.message || 'Could not generate the federation token.');
                    alert(error.message || 'Could not generate the federation token.');
                })
                .finally(function () {
                    generateBtn.disabled = false;
                    generateBtn.innerHTML = previous;
                });
            });
        })();

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

            function activateSettingsTab(tabId) {
                if (!tabId) {
                    return;
                }
                var $pane = $('#settingsTabContent #' + tabId);
                if (!$pane.length) {
                    return;
                }
                $('.settings-tabs .nav-link').removeClass('active').attr('aria-selected', 'false');
                $('#settingsTabContent .tab-pane').removeClass('show active');
                $('.settings-tabs .nav-link[data-tab="' + tabId + '"]').addClass('active').attr('aria-selected', 'true');
                $pane.addClass('show active');
            }

            var legacyTabMap = { appearance: 'colors', typography: 'colors' };

            $('.settings-tabs .nav-link').on('click', function(e) {
                e.preventDefault();
                var targetTab = $(this).data('tab');
                activateSettingsTab(targetTab);
                if (window.history && window.history.replaceState) {
                    window.history.replaceState(null, '', '#' + targetTab);
                }
            });

            var initialHash = window.location.hash.replace('#', '');
            if (initialHash) {
                activateSettingsTab(legacyTabMap[initialHash] || initialHash);
            }

            function normalizeHexColor(value, fallback) {
                var v = String(value || '').trim();
                if (v === '') {
                    return fallback || '#119A48';
                }
                if (!v.startsWith('#')) {
                    v = '#' + v;
                }
                if (/^#[0-9A-Fa-f]{3}$/.test(v)) {
                    var r = v.charAt(1), g = v.charAt(2), b = v.charAt(3);
                    v = '#' + r + r + g + g + b + b;
                }
                return /^#[0-9A-Fa-f]{6}$/.test(v) ? v : (fallback || '#119A48');
            }

            function syncColorPicker($picker) {
                var $text = $picker.find('.settings-color-text, input[type="text"]').first();
                var $native = $picker.find('.settings-color-native, input[type="color"]').first();
                var $preview = $picker.find('.settings-color-preview, .color-preview').first();
                if (!$text.length) {
                    return;
                }

                var hex = normalizeHexColor($text.val(), $native.val() || '#119A48');
                $text.val(hex);
                if ($native.length) {
                    $native.val(hex);
                }
                if ($preview.length) {
                    $preview.css('background-color', hex);
                        }
                        if ($picker.attr('id') === 'gradientStartPicker' || $picker.attr('id') === 'gradientEndPicker') {
                            updateGradientPreview();
                        }
            }

            $('.colorPicker').each(function() {
                var $picker = $(this);
                var $text = $picker.find('.settings-color-text, input[type="text"]').first();
                var $native = $picker.find('.settings-color-native, input[type="color"]').first();
                var $preview = $picker.find('.settings-color-preview, .color-preview').first();
                var defaultColor = normalizeHexColor($text.val(), '#119A48');

                syncColorPicker($picker);

                $text.on('input change blur', function() {
                    syncColorPicker($picker);
                });

                if ($native.length) {
                    $native.on('input change', function() {
                        $text.val(this.value);
                        syncColorPicker($picker);
                    });
                }

                if ($preview.length) {
                    $preview.on('click', function() {
                        if ($native.length) {
                            $native.trigger('click');
                        }
                    });
                }
            });

            function updateGradientPreview() {
                var startColor = normalizeHexColor($('#gradientStartPicker .settings-color-text').val(), '#119A48');
                var endColor = normalizeHexColor($('#gradientEndPicker .settings-color-text').val(), '#16c653');
                $('#gradientPreview').css('background', 'linear-gradient(135deg, ' + startColor + ' 0%, ' + endColor + ' 100%)');
            }

            updateGradientPreview();

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

            $('.branding-asset-card input[type="file"]').on('change', function (e) {
                var file = e.target.files[0];
                if (!file) return;
                    var $input = $(this);
                var existingId = $input.attr('id') + '_existing';
                if (document.getElementById(existingId)) {
                    document.getElementById(existingId).value = '';
                }
                var reader = new FileReader();
                reader.onload = function (event) {
                    updateBrandingAssetPreview($input, event.target.result, file.name);
                    };
                    reader.readAsDataURL(file);
            });

            $('.js-branding-gallery-select').on('change', function () {
                var selected = $(this).val();
                if (!selected) return;
                var $card = $(this).closest('.branding-asset-card');
                $card.find('.js-branding-filename').text('Gallery: ' + selected).addClass('has-file');
            });

            $('#font_files').on('change', function () {
                var file = this.files && this.files[0];
                var $filename = $('#font_files-filename');
                if (file && $filename.length) {
                    $filename.text(file.name).addClass('has-file');
                }
            });
        });
    </script>
@endsection
