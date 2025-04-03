<div class="bg-white rounded-lg shadow-md p-8 text-center">
    <div class="mb-6">
        <i class="fas fa-exclamation-circle text-pink-500 text-6xl"></i>
    </div>
    
    <h1 class="text-3xl font-bold text-pink-700 mb-4">ページが見つかりません</h1>
    
    <p class="text-lg text-gray-600 mb-6">
        <?= $message ?? '指定されたページは存在しないか、削除されました。' ?>
    </p>
    
    <p class="mb-8">
        <span class="text-pink-600 mr-2">🌸</span>
        <span class="text-gray-500">お探しのイベントが見つからないようです。</span>
        <span class="text-pink-600 ml-2">🌸</span>
    </p>
    
    <div>
        <a href="/" class="bg-pink-600 hover:bg-pink-700 text-white font-bold py-2 px-6 rounded-md transition duration-300">
            ホームに戻る
        </a>
    </div>
</div>
