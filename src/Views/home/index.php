<div class="bg-white rounded-lg shadow-md p-6 mb-8">
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-bold text-pink-700 mb-4">花見調整サイト</h1>
        <p class="text-lg text-gray-600">花見イベントの日程調整、場所選び、持ち物管理を簡単に！</p>
    </div>
    
    <div class="bg-pink-50 rounded-lg p-6 mb-8">
        <h2 class="text-2xl font-semibold text-pink-700 mb-4">新しい花見イベントを作成</h2>
        
        <form id="createEventForm" method="post" action="/event/create" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
            
            <div>
                <label for="event_name" class="block text-gray-700 font-medium mb-2">イベント名 <span class="text-red-500">*</span></label>
                <input type="text" id="event_name" name="event_name" required 
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500"
                       placeholder="例: 会社花見2025">
            </div>
            
            <div>
                <label for="description" class="block text-gray-700 font-medium mb-2">イベントの説明</label>
                <textarea id="description" name="description" rows="4"
                          class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500"
                          placeholder="例: 会社の花見パーティーです。持ち寄り歓迎！途中参加・退出OK！"></textarea>
            </div>
            
            <div class="text-center">
                <button type="submit" class="bg-pink-600 hover:bg-pink-700 text-white font-bold py-2 px-6 rounded-md transition duration-300">
                    イベントを作成 <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </div>
        </form>
    </div>
    
    <?php if (!empty($recentEvents)): ?>
    <div>
        <h2 class="text-2xl font-semibold text-pink-700 mb-4">最近作成されたイベント</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <?php foreach ($recentEvents as $event): ?>
            <a href="/event/<?= htmlspecialchars($event['event_id']) ?>" 
               class="block bg-white border border-pink-200 rounded-lg hover:shadow-md transition duration-300 p-4">
                <h3 class="text-lg font-semibold text-pink-700"><?= htmlspecialchars($event['event_name']) ?></h3>
                <p class="text-gray-600 text-sm mb-2">
                    作成日: <?= (new DateTime($event['created_at']))->format('Y年m月d日') ?>
                </p>
                <?php if (!empty($event['description'])): ?>
                <p class="text-gray-700 text-sm truncate"><?= htmlspecialchars($event['description']) ?></p>
                <?php endif; ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-2xl font-semibold text-pink-700 mb-4">花見調整サイトの特徴</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-pink-50 rounded-lg p-4">
            <div class="text-pink-600 text-center mb-2">
                <i class="fas fa-calendar-alt text-3xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-pink-700 text-center mb-2">日程調整</h3>
            <p class="text-gray-700 text-sm">
                複数の候補日から参加可能な日程を投票で決められます。みんなが参加しやすい日を簡単に見つけられます。
            </p>
        </div>
        
        <div class="bg-pink-50 rounded-lg p-4">
            <div class="text-pink-600 text-center mb-2">
                <i class="fas fa-map-marker-alt text-3xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-pink-700 text-center mb-2">場所選び</h3>
            <p class="text-gray-700 text-sm">
                お花見スポットの候補を複数提案し、投票で決めることができます。場所情報や地図リンクも共有できます。
            </p>
        </div>
        
        <div class="bg-pink-50 rounded-lg p-4">
            <div class="text-pink-600 text-center mb-2">
                <i class="fas fa-shopping-basket text-3xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-pink-700 text-center mb-2">持ち物管理</h3>
            <p class="text-gray-700 text-sm">
                必要なアイテムリストを作成し、誰が何を持ってくるか管理できます。重複や忘れ物を防止できます。
            </p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('createEventForm');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        
        fetch('/event/create', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.redirect) {
                window.location.href = data.redirect;
            } else if (data.error) {
                alert(data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('イベントの作成中にエラーが発生しました。もう一度お試しください。');
        });
    });
});
</script>
