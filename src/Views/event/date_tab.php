<div id="dates-content" class="tab-pane active">
    <h2 class="text-xl font-semibold text-pink-700 mb-4">日程調整</h2>
    
    <!-- 日時候補追加フォーム -->
    <div class="bg-pink-50 rounded-lg p-4 mb-6">
        <h3 class="text-lg font-medium text-pink-700 mb-3">新しい日時候補を追加</h3>
        
        <form id="addDateForm" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
            <input type="hidden" name="event_id" value="<?= htmlspecialchars($event['event_id']) ?>">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="datetime" class="block text-gray-700 font-medium mb-2">日時 <span class="text-red-500">*</span></label>
                    <input type="text" id="datetime" name="datetime" required 
                           class="datepicker w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500"
                           placeholder="候補日時を選択">
                </div>
                
                <div>
                    <label for="member_name" class="block text-gray-700 font-medium mb-2">あなたの名前 <span class="text-red-500">*</span></label>
                    <input type="text" id="date_member_name" name="member_name" required 
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500"
                           placeholder="例: 山田太郎">
                </div>
            </div>
            
            <div class="flex justify-end">
                <button type="submit" class="bg-pink-600 hover:bg-pink-700 text-white font-bold py-2 px-4 rounded-md transition duration-300">
                    候補を追加 <i class="fas fa-plus ml-1"></i>
                </button>
            </div>
        </form>
    </div>
    
    <!-- 日時候補リスト -->
    <div id="datesList" class="mb-6">
        <h3 class="text-lg font-medium text-pink-700 mb-3">候補日時一覧</h3>
        
        <!-- 候補がない場合のメッセージ -->
        <div id="noDateMessage" class="<?= !empty($dates) ? 'hidden' : '' ?> bg-gray-50 p-4 rounded-lg text-center">
            <p class="text-gray-500">日時候補が登録されていません。上のフォームから候補を追加してください。</p>
        </div>
        
        <!-- 候補リスト -->
        <div id="datesContainer" class="<?= empty($dates) ? 'hidden' : '' ?> space-y-3">
            <?php if (!empty($dates)): ?>
                <?php foreach ($dates as $date): ?>
                    <div class="date-item bg-white border border-gray-200 rounded-lg p-4" data-date-id="<?= htmlspecialchars($date['id']) ?>">
                        <div class="flex flex-col md:flex-row md:items-center justify-between">
                            <div class="mb-3 md:mb-0">
                                <h4 class="text-lg font-medium text-gray-800">
                                    <?= (new DateTime($date['datetime']))->format('Y年m月d日') ?>
                                    <span class="text-gray-600">
                                        <?= (new DateTime($date['datetime']))->format('H:i') ?>
                                    </span>
                                </h4>
                                
                                <!-- 投票者一覧（トグル表示） -->
                                <div class="mt-1">
                                    <button class="text-sm text-blue-600 hover:text-blue-800 show-voters">
                                        <i class="fas fa-user"></i> 
                                        投票者を表示 (<span class="vote-count"><?= $date['vote_count'] ?? 0 ?></span>名)
                                    </button>
                                    
                                    <div class="voters-list hidden mt-2 text-sm text-gray-600">
                                        <p class="voters-loading">読み込み中...</p>
                                        <ul class="voters-names hidden pl-5 list-disc"></ul>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center">
                                <!-- 投票ゲージ -->
                                <div class="relative h-8 w-32 bg-gray-200 rounded-full overflow-hidden mr-4">
                                    <div class="vote-gauge absolute h-full bg-green-500 transition-all duration-300" 
                                         style="width: <?= ($date['vote_count'] ?? 0) > 0 ? min(100, ($date['vote_count'] / max(1, count($members))) * 100) : 0 ?>%">
                                    </div>
                                    <div class="absolute inset-0 flex items-center justify-center text-sm font-medium">
                                        <span class="vote-percentage">
                                            <?= ($date['vote_count'] ?? 0) > 0 ? floor(($date['vote_count'] / max(1, count($members))) * 100) : 0 ?>%
                                        </span>
                                    </div>
                                </div>
                                
                                <!-- 投票ボタン -->
                                <button class="vote-button flex items-center bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 px-4 rounded-md transition duration-300">
                                    <i class="far fa-circle-check mr-1"></i> 投票する
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- 最多投票の候補表示 -->
    <div id="mostVotedContainer" class="<?= empty($dates) ? 'hidden' : '' ?> bg-green-50 border border-green-200 rounded-lg p-4">
        <h3 class="text-lg font-medium text-green-700 mb-2">現在の最有力候補</h3>
        
        <div id="mostVoted" class="flex items-center">
            <div class="text-3xl text-green-500 mr-4">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div>
                <p id="mostVotedDate" class="text-lg font-medium text-gray-800">
                    <!-- 最多投票の日時が入ります -->
                    <?php 
                    $mostVoted = null;
                    $maxVotes = 0;
                    
                    if (!empty($dates)) {
                        foreach ($dates as $date) {
                            if (($date['vote_count'] ?? 0) > $maxVotes) {
                                $maxVotes = $date['vote_count'];
                                $mostVoted = $date;
                            }
                        }
                    }
                    
                    if ($mostVoted):
                    ?>
                        <?= (new DateTime($mostVoted['datetime']))->format('Y年m月d日 H:i') ?>
                    <?php else: ?>
                        まだ投票がありません
                    <?php endif; ?>
                </p>
                <p id="mostVotedCount" class="text-sm text-gray-600">
                    <?php if ($maxVotes > 0): ?>
                        <span class="font-medium"><?= $maxVotes ?>票</span> / 参加者<?= count($members) ?>名中
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 日時選択の初期化
    flatpickr('.datepicker', {
        enableTime: true,
        dateFormat: 'Y-m-d H:i',
        minDate: 'today',
        time_24hr: true,
        locale: 'ja'
    });
    
    // 名前入力の初期化（ローカルストレージから復元）
    const savedName = localStorage.getItem('member_name');
    if (savedName) {
        document.getElementById('date_member_name').value = savedName;
    }
    
    // 日時候補の追加処理
    const addDateForm = document.getElementById('addDateForm');
    if (addDateForm) {
        addDateForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(addDateForm);
            const memberName = formData.get('member_name');
            
            // 名前をローカルストレージに保存
            if (memberName) {
                localStorage.setItem('member_name', memberName);
            }
            
            // 送信処理
            fetch('/event/<?= htmlspecialchars($event['event_id']) ?>/date/add', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // フォームをリセット（名前は保持）
                    const memberName = document.getElementById('date_member_name').value;
                    addDateForm.reset();
                    document.getElementById('date_member_name').value = memberName;
                    
                    // Flatpickrの内部状態もリセット
                    const datetimePicker = document.getElementById('datetime')._flatpickr;
                    datetimePicker.clear();
                    
                    // 成功メッセージ
                    showMessage('success', data.message);
                    
                    // 候補リストを更新（Pusherで自動更新されるので不要）
                    // updateDatesList();
                } else {
                    showMessage('error', data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('error', '日時候補の追加に失敗しました。もう一度お試しください。');
            });
        });
    }
    
    // 投票処理の初期化
    initializeVoting();
    
    // 投票者一覧の表示/非表示切り替え
    document.querySelectorAll('.show-voters').forEach(button => {
        button.addEventListener('click', function() {
            const dateItem = this.closest('.date-item');
            const votersList = dateItem.querySelector('.voters-list');
            const votersNames = dateItem.querySelector('.voters-names');
            const votersLoading = dateItem.querySelector('.voters-loading');
            
            if (votersList.classList.contains('hidden')) {
                // 表示する
                votersList.classList.remove('hidden');
                
                // データ取得
                const dateId = dateItem.dataset.dateId;
                fetch(`/api/event/<?= htmlspecialchars($event['event_id']) ?>/dates/${dateId}/voters`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            votersLoading.classList.add('hidden');
                            votersNames.classList.remove('hidden');
                            
                            // 投票者名を表示
                            votersNames.innerHTML = '';
                            if (data.voters.length > 0) {
                                data.voters.forEach(voter => {
                                    const li = document.createElement('li');
                                    li.textContent = voter.name;
                                    votersNames.appendChild(li);
                                });
                            } else {
                                votersNames.innerHTML = '<li>まだ投票がありません</li>';
                            }
                        } else {
                            votersLoading.textContent = '投票者情報の取得に失敗しました';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        votersLoading.textContent = '投票者情報の取得中にエラーが発生しました';
                    });
                
                // ボタンのテキスト変更
                this.innerHTML = '<i class="fas fa-user"></i> 投票者を隠す';
            } else {
                // 非表示にする
                votersList.classList.add('hidden');
                votersNames.classList.add('hidden');
                votersLoading.classList.remove('hidden');
                
                // ボタンのテキスト変更
                const voteCount = dateItem.querySelector('.vote-count').textContent;
                this.innerHTML = `<i class="fas fa-user"></i> 投票者を表示 (${voteCount}名)`;
            }
        });
    });
    
    // Pusherの初期化（リアルタイム更新）
    if (typeof Pusher !== 'undefined') {
        const pusher = new Pusher('<?= $_ENV['PUSHER_APP_KEY'] ?? 'your-app-key' ?>', {
            cluster: '<?= $_ENV['PUSHER_APP_CLUSTER'] ?? 'ap3' ?>',
            encrypted: true
        });
        
        // イベント固有のチャンネルを購読
        const channel = pusher.subscribe('hanami-<?= htmlspecialchars($event['event_id']) ?>-dates');
        
        // 日時候補の更新イベントをリッスン
        channel.bind('dates-updated', function(data) {
            updateDatesFromData(data.dates);
        });
    }
});

// 投票ボタンの処理
function initializeVoting() {
    document.querySelectorAll('.vote-button').forEach(button => {
        button.addEventListener('click', function() {
            const dateItem = this.closest('.date-item');
            const dateId = dateItem.dataset.dateId;
            
            // 名前取得
            const memberName = localStorage.getItem('member_name');
            if (!memberName) {
                showMessage('error', '投票するには名前を入力してください');
                
                // 名前入力フォームにフォーカス
                document.getElementById('date_member_name').focus();
                return;
            }
            
            // CSRFトークン取得
            const csrfToken = document.querySelector('input[name="csrf_token"]').value;
            
            // 投票処理
            const formData = new FormData();
            formData.append('csrf_token', csrfToken);
            formData.append('event_id', '<?= htmlspecialchars($event['event_id']) ?>');
            formData.append('date_id', dateId);
            formData.append('member_name', memberName);
            
            fetch('/event/<?= htmlspecialchars($event['event_id']) ?>/date/vote', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // 成功メッセージ
                    showMessage('success', data.message);
                    
                    // 投票ボタンの状態更新（Pusherで自動更新されるので不要）
                    // updateVoteButton(button, data.vote_added);
                } else {
                    showMessage('error', data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('error', '投票処理中にエラーが発生しました');
            });
        });
    });
}

// 投票状態の更新
function updateVoteButton(button, isVoted) {
    if (isVoted) {
        button.classList.remove('bg-gray-100', 'hover:bg-gray-200', 'text-gray-700');
        button.classList.add('bg-green-500', 'hover:bg-green-600', 'text-white');
        button.innerHTML = '<i class="fas fa-check-circle mr-1"></i> 投票済み';
    } else {
        button.classList.remove('bg-green-500', 'hover:bg-green-600', 'text-white');
        button.classList.add('bg-gray-100', 'hover:bg-gray-200', 'text-gray-700');
        button.innerHTML = '<i class="far fa-circle-check mr-1"></i> 投票する';
    }
}

// 日時候補リストの更新
function updateDatesFromData(dates) {
    const container = document.getElementById('datesContainer');
    const noDateMessage = document.getElementById('noDateMessage');
    const mostVotedContainer = document.getElementById('mostVotedContainer');
    
    if (!dates || dates.length === 0) {
        container.classList.add('hidden');
        noDateMessage.classList.remove('hidden');
        mostVotedContainer.classList.add('hidden');
        return;
    }
    
    container.classList.remove('hidden');
    noDateMessage.classList.add('hidden');
    mostVotedContainer.classList.remove('hidden');
    
    // 既存の日時候補をクリア
    container.innerHTML = '';
    
    // 参加者数の取得（簡易実装）
    const membersCount = <?= count($members) ?>;
    
    // 最多投票の候補を特定
    let mostVoted = null;
    let maxVotes = 0;
    
    dates.forEach(date => {
        if ((date.vote_count || 0) > maxVotes) {
            maxVotes = date.vote_count || 0;
            mostVoted = date;
        }
        
        // 日時候補要素の作成
        const dateItem = document.createElement('div');
        dateItem.className = 'date-item bg-white border border-gray-200 rounded-lg p-4';
        dateItem.dataset.dateId = date.id;
        
        // パーセンテージの計算
        const votePercentage = membersCount > 0 ? Math.floor((date.vote_count || 0) / membersCount * 100) : 0;
        
        dateItem.innerHTML = `
            <div class="flex flex-col md:flex-row md:items-center justify-between">
                <div class="mb-3 md:mb-0">
                    <h4 class="text-lg font-medium text-gray-800">
                        ${new Date(date.datetime).toLocaleDateString('ja-JP', { year: 'numeric', month: 'long', day: 'numeric' })}
                        <span class="text-gray-600">
                            ${new Date(date.datetime).toLocaleTimeString('ja-JP', { hour: '2-digit', minute: '2-digit' })}
                        </span>
                    </h4>
                    
                    <div class="mt-1">
                        <button class="text-sm text-blue-600 hover:text-blue-800 show-voters">
                            <i class="fas fa-user"></i> 
                            投票者を表示 (<span class="vote-count">${date.vote_count || 0}</span>名)
                        </button>
                        
                        <div class="voters-list hidden mt-2 text-sm text-gray-600">
                            <p class="voters-loading">読み込み中...</p>
                            <ul class="voters-names hidden pl-5 list-disc"></ul>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center">
                    <div class="relative h-8 w-32 bg-gray-200 rounded-full overflow-hidden mr-4">
                        <div class="vote-gauge absolute h-full bg-green-500 transition-all duration-300" 
                             style="width: ${votePercentage}%">
                        </div>
                        <div class="absolute inset-0 flex items-center justify-center text-sm font-medium">
                            <span class="vote-percentage">${votePercentage}%</span>
                        </div>
                    </div>
                    
                    <button class="vote-button flex items-center bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 px-4 rounded-md transition duration-300">
                        <i class="far fa-circle-check mr-1"></i> 投票する
                    </button>
                </div>
            </div>
        `;
        
        container.appendChild(dateItem);
    });
    
    // 最多投票候補の更新
    if (mostVoted) {
        document.getElementById('mostVotedDate').textContent = 
            new Date(mostVoted.datetime).toLocaleDateString('ja-JP', { 
                year: 'numeric', month: 'long', day: 'numeric', 
                hour: '2-digit', minute: '2-digit' 
            });
        
        document.getElementById('mostVotedCount').innerHTML = 
            `<span class="font-medium">${maxVotes}票</span> / 参加者${membersCount}名中`;
    } else {
        document.getElementById('mostVotedDate').textContent = 'まだ投票がありません';
        document.getElementById('mostVotedCount').textContent = '';
    }
    
    // イベントリスナーの再初期化
    initializeVoting();
    
    // 投票者一覧の表示/非表示切り替え処理を再初期化
    document.querySelectorAll('.show-voters').forEach(button => {
        button.addEventListener('click', function() {
            const dateItem = this.closest('.date-item');
            const votersList = dateItem.querySelector('.voters-list');
            const votersNames = dateItem.querySelector('.voters-names');
            const votersLoading = dateItem.querySelector('.voters-loading');
            
            if (votersList.classList.contains('hidden')) {
                // 表示する処理
                votersList.classList.remove('hidden');
                
                // 以下略（既存のコードと同様）
            } else {
                // 非表示にする処理
                // 以下略（既存のコードと同様）
            }
        });
    });
}

// メッセージ表示
function showMessage(type, message) {
    // メッセージコンテナの作成（なければ）
    let messageContainer = document.getElementById('messageContainer');
    if (!messageContainer) {
        messageContainer = document.createElement('div');
        messageContainer.id = 'messageContainer';
        messageContainer.className = 'fixed top-4 right-4 z-50';
        document.body.appendChild(messageContainer);
    }
    
    // メッセージ要素の作成
    const messageElement = document.createElement('div');
    messageElement.className = `mb-2 p-3 rounded-lg shadow-md transition-opacity duration-300 ${
        type === 'success' ? 'bg-green-50 text-green-800 border border-green-200' : 
        'bg-red-50 text-red-800 border border-red-200'
    }`;
    
    messageElement.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-${type === 'success' ? 'check-circle text-green-500' : 'exclamation-circle text-red-500'} mr-2"></i>
            <span>${message}</span>
        </div>
    `;
    
    // メッセージの追加
    messageContainer.appendChild(messageElement);
    
    // 数秒後に削除
    setTimeout(() => {
        messageElement.classList.add('opacity-0');
        setTimeout(() => {
            messageContainer.removeChild(messageElement);
        }, 300);
    }, 4000);
}
</script>
