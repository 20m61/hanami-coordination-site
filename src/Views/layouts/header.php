<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? '花見調整サイト') ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Flatpickr (日時選択) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_green.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ja.js"></script>
    
    <!-- Pusher (リアルタイム通信) -->
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    
    <style>
        .sakura {
            background-color: #ffcce6;
            opacity: 0.6;
            position: relative;
        }
        
        .sakura::before {
            content: '\1F338';
            position: absolute;
            font-size: 1.2rem;
            opacity: 0.7;
            top: -5px;
            left: 5px;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <header class="bg-pink-100 shadow-md">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <a href="/" class="text-2xl font-bold text-pink-700 flex items-center">
                <span class="mr-2">🌸</span>
                <span>花見調整サイト</span>
            </a>
            <nav>
                <ul class="flex space-x-4">
                    <li><a href="/" class="text-pink-700 hover:text-pink-500">ホーム</a></li>
                    <li><a href="/about" class="text-pink-700 hover:text-pink-500">サイトについて</a></li>
                </ul>
            </nav>
        </div>
    </header>
    
    <main class="container mx-auto px-4 py-8">
