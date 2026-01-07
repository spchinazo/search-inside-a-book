<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Search Inside a Book' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #f8fafc;
            --text-color: #1e293b;
            --accent-color: #3b82f6;
            --card-bg: #ffffff;
            --border-color: #e2e8f0;
            --highlight-bg: #fef08a;
            --highlight-text: #854d0e;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem;
        }

        header {
            text-align: center;
            margin-bottom: 3rem;
        }

        h1 {
            font-weight: 600;
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
            letter-spacing: -0.025em;
        }

        .subtitle {
            color: #64748b;
            font-weight: 300;
        }

        mark {
            background-color: var(--highlight-bg);
            color: var(--highlight-text);
            padding: 0 0.2rem;
            border-radius: 2px;
        }

        /* Glassmorphism effects */
        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .btn {
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .pagination-wrapper {
            margin-top: 3rem;
            display: flex;
            justify-content: center;
        }

        .custom-pagination {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .pagination-item {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 40px;
            height: 40px;
            padding: 0 0.5rem;
            border-radius: 10px;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            color: var(--text-color);
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            user-select: none;
        }

        .pagination-item:hover:not(.disabled):not(.active) {
            border-color: var(--accent-color);
            color: var(--accent-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
        }

        .pagination-item.active {
            background: var(--accent-color);
            color: white;
            border-color: var(--accent-color);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
            cursor: default;
        }

        .pagination-item.disabled {
            opacity: 0.4;
            cursor: not-allowed;
            background: #f1f5f9;
        }

        .pagination-item button { /* Reset in case it wraps */
            background: none;
            border: none;
            color: inherit;
            font: inherit;
            cursor: inherit;
            width: 100%;
            height: 100%;
        }
        .search-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .search-box-wrapper {
            position: relative;
            margin-bottom: 2rem;
        }

        .search-input {
            width: 100%;
            padding: 1.2rem 1.5rem;
            font-size: 1.1rem;
            border-radius: 16px;
            border: 1px solid var(--border-color);
            background: var(--card-bg);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            outline: none;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }

        .search-input:focus {
            border-color: var(--accent-color);
            box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.2);
        }

        .loading-indicator {
            position: absolute;
            right: 1.5rem;
            top: 50%;
            transform: translateY(-50%);
        }

        .spinner {
            width: 20px;
            height: 20px;
            border: 2px solid #e2e8f0;
            border-top-color: var(--accent-color);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .results-stats {
            margin-bottom: 1rem;
            font-size: 0.9rem;
            color: #64748b;
            font-weight: 400;
        }

        .result-card {
            margin-bottom: 1rem;
            padding: 1.5rem;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
            overflow: hidden;
        }

        .result-card:hover {
            transform: translateX(5px);
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .page-badge {
            display: inline-block;
            padding: 0.2rem 0.6rem;
            background: #eff6ff;
            color: #3b82f6;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 500;
            margin-bottom: 0.75rem;
        }

        .result-snippet {
            font-size: 1rem;
            color: #475569;
            margin-bottom: 0.75rem;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .result-action {
            text-align: right;
        }

        .view-link {
            font-size: 0.85rem;
            color: var(--accent-color);
            font-weight: 500;
        }

        .page-viewer {
            padding: 2.5rem;
            border-radius: 20px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 1rem;
        }

        .btn-close {
            background: none;
            border: none;
            font-size: 2rem;
            color: #94a3b8;
            cursor: pointer;
            line-height: 1;
        }

        .page-content {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #334155;
            white-space: pre-wrap;
            max-height: 60vh;
            overflow-y: auto;
            padding-right: 1rem;
        }

        .page-footer {
            margin-top: 2rem;
            text-align: center;
        }

        .btn-back {
            background: var(--accent-color);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            font-weight: 500;
        }

        .search-placeholder, .no-results {
            text-align: center;
            padding: 4rem 2rem;
            color: #94a3b8;
            font-weight: 300;
        }
    </style>
    @livewireStyles
</head>
<body>
    <div class="container">
        {{ $slot }}
    </div>
    @livewireScripts
</body>
</html>
