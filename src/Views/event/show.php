<div class="bg-white rounded-lg shadow-md p-6 mb-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-pink-700 mb-2"><?= htmlspecialchars($event['event_name']) ?></h1>
        
        <?php if (!empty($event['description'])): ?>
        <p class="text-gray-600 mb-4"><?= nl2br(htmlspecialchars($event['description'])) ?></p>
        <?php endif; ?>
        
        <div class="bg-pink-50 rounded-lg p-4 mt-4">
            <h2 class="text-lg font-semibold text-pink-700 mb-2">イベント情報</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-gray-700">
                        <span class="font-medium">日時:</span> 
                        <?php if ($event['confirmed_date']): ?>
                            <?= (new DateTime($event['confirmed_date']['datetime']))->format('Y年m月d日 H:i') ?>
                        <?php else: ?>
                            <span class="text-orange-600">未定（投票受付中）</span>
                        <?php endif; ?>
                    </p>
                </div>
                
                <div>
                    <p class="text-gray-700">
                        <span class="font-medium">場所:</span> 
                        <?php if ($event['confirmed_location']): ?>
                            <?= htmlspecialchars($event['confirmed_location']['name']) ?>
                            <?php if (!empty($event['confirmed_location']['url'])): ?>
                                <a href="<?= htmlspecialchars($event['confirmed_location']['url']) ?>" target="_blank" class="text-blue-600 hover:underline ml-1">
                                    <i class="fas fa-map-marker-alt"></i>
                                </a>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="text-orange-600">未定（投票受付中）</span>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
            
            <div class="mt-2">
                <p class="text-gray-700">
                    <span class="font-medium">参加者数:</span> <?= count($members) ?>名
                </p>
            </div>
            
            <div class="mt-4 text-center">
                <!-- イベントシェアボタン -->
                <button id="shareButton" class="bg-pink-600 hover:bg-pink-700 text-white font-bold py-2 px-4 rounded-md transition duration-300">
                    <i class="fas fa-share-alt mr-2"></i> このイベントをシェア
                </button>
            </div>
        </div>
    </div>
    
    <!-- タブナビゲーション -->
    <div class="border-b border-gray-200 mb-6">
        <ul class="flex flex-wrap -mb-px" id="eventTabs" role="tablist">
            <li class="mr-2" role="presentation">
                <button class="inline-block py-2 px-4 text-pink-600 hover:text-pink-800 font-medium border-b-2 border-pink-600 active" 
                        id="dates-tab" data-tab="dates" type="button">
                    <i class="far fa-calendar-alt mr-2"></i>日程調整
                </button>
            </li>
            <li class="mr-2" role="presentation">
                <button class="inline-block py-2 px-4 text-gray-500 hover:text-pink-600 font-medium border-b-2 border-transparent hover:border-pink-300" 
                        id="locations-tab" data-tab="locations" type="button">
                    <i class="fas fa-map-marker-alt mr-2"></i>場所
                </button>
            </li>
            <li class="mr-2" role="presentation">
                <button class="inline-block py-2 px-4 text-gray-500 hover:text-pink-600 font-medium border-b-2 border-transparent hover:border-pink-300" 
                        id="items-tab" data-tab="items" type="button">
                    <i class="fas fa-shopping-basket mr-2"></i>持ち物
                </button>
            </li>
            <li class="mr-2" role="presentation">
                <button class="inline-block py-2 px-4 text-gray-500 hover:text-pink-600 font-medium border-b-2 border-transparent hover:border-pink-300" 
                        id="chat-tab" data-tab="chat" type="button">
                    <i class="far fa-comments mr-2"></i>チャット
                </button>
            </li>
        </ul>
    </div>
    
    <!-- タブコンテンツ -->
    <div class="tab-content">
        <!-- 日程調整タブ -->
        <div id="dates-content" class="tab-pane active">
            <h2 class="text-xl font-semibold text-pink-700 mb-4">日程調整</h2>
            
            <!-- 日程の追加フォームと日程候補リストはこちら -->
            <p class="text-gray-700 mb-4">この機能は今後実装予定です。</p>
        </div>
        
        <!-- 場所タブ -->
        <div id="locations-content" class="tab-pane hidden">
            <h2 class="text-xl font-semibold text-pink-700 mb-4">場所候補</h2>
            
            <!-- 場所の追加フォームと場所候補リストはこちら -->
            <p class="text-gray-700 mb-4">この機能は今後実装予定です。</p>
        </div>
        
        <!-- 持ち物タブ -->
        <div id="items-content" class="tab-pane hidden">
            <h2 class="text-xl font-semibold text-pink-700 mb-4">持ち物リスト</h2>
            
            <!-- 持ち物の追加フォームと持ち物リストはこちら -->
            <p class="text-gray-700 mb-4">この機能は今後実装予定です。</p>
        </div>
        
        <!-- チャットタブ -->
        <div id="chat-content" class="tab-pane hidden">
            <h2 class="text-xl font-semibold text-pink-700 mb-4">チャット</h2>
            
            <!-- チャットインターフェースはこちら -->
            <p class="text-gray-700 mb-4">この機能は今後実装予定です。</p>
        </div>
    </div>
</div>

<!-- 参加者モーダル（初回訪問時） -->
<div id="memberModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg shadow-lg p-6 max-w-md w-full">
        <h2 class="text-xl font-semibold text-pink-700 mb-4">あなたの名前を入力してください</h2>
        <p class="text-gray-600 mb-4">投票やチャットに参加するには名前が必要です。</p>
        
        <form id="memberForm" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
            <input type="hidden" name="event_id" value="<?= htmlspecialchars($event['event_id']) ?>">
            
            <div>
                <label for="member_name" class="block text-gray-700 font-medium mb-2">名前 <span class="text-red-500">*</span></label>
                <input type="text" id="member_name" name="member_name" required 
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500"
                       placeholder="例: 山田太郎">
            </div>
            
            <div class="flex justify-end">
                <button type="submit" class="bg-pink-600 hover:bg-pink-700 text-white font-bold py-2 px-6 rounded-md transition duration-300">
                    参加する
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // タブ切り替え処理
    const tabs = document.querySelectorAll('#eventTabs button');
    
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            // 全てのタブとコンテンツを非アクティブにする
            document.querySelectorAll('#eventTabs button').forEach(t => {
                t.classList.remove('text-pink-600', 'border-pink-600');
                t.classList.add('text-gray-500', 'border-transparent');
            });
            
            document.querySelectorAll('.tab-pane').forEach(pane => {
                pane.classList.add('hidden');
            });
            
            // クリックされたタブをアクティブにする
            this.classList.remove('text-gray-500', 'border-transparent');
            this.classList.add('text-pink-600', 'border-pink-600');
            
            // 対応するコンテンツを表示する
            const tabName = this.getAttribute('data-tab');
            document.getElementById(`${tabName}-content`).classList.remove('hidden');
        });
    });
    
    // URLをコピーする機能
    document.getElementById('shareButton').addEventListener('click', function() {
        const url = window.location.href;
        
        // クリップボードにコピー
        navigator.clipboard.writeText(url).then(() => {
            alert('URLがクリップボードにコピーされました！');
        }).catch(err => {
            console.error('URLのコピーに失敗しました:', err);
            alert('URLのコピーに失敗しました。');
        });
    });
    
    // 参加者モーダル処理
    // TODO: Cookieや小さなローカルストレージで既に参加済みか判定する
    // const memberModal = document.getElementById('memberModal');
    // memberModal.classList.remove('hidden');
    
    // document.getElementById('memberForm').addEventListener('submit', function(e) {
    //     e.preventDefault();
    //     // TODO: フォーム送信処理
    //     memberModal.classList.add('hidden');
    // });
});
</script>
