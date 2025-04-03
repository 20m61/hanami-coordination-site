<div id="locations-content" class="tab-pane hidden">
    <h2 class="text-xl font-semibold text-pink-700 mb-4">場所候補</h2>
    
    <!-- 場所候補追加フォーム -->
    <div class="bg-pink-50 rounded-lg p-4 mb-6">
        <h3 class="text-lg font-medium text-pink-700 mb-3">新しい場所候補を追加</h3>
        
        <form id="addLocationForm" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
            <input type="hidden" name="event_id" value="<?= htmlspecialchars($event['event_id']) ?>">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-gray-700 font-medium mb-2">場所名 <span class="text-red-500">*</span></label>
                    <input type="text" id="location_name" name="name" required 
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500"
                           placeholder="例: 代々木公園（原宿口付近）">
                </div>
                
                <div>
                    <label for="member_name" class="block text-gray-700 font-medium mb-2">あなたの名前 <span class="text-red-500">*</span></label>
                    <input type="text" id="location_member_name" name="member_name" required 
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500"
                           placeholder="例: 山田太郎">
                </div>
            </div>
            
            <div>
                <label for="url" class="block text-gray-700 font-medium mb-2">地図URL <span class="text-gray-500 text-sm">(オプション)</span></label>
                <div class="flex">
                    <input type="url" id="location_url" name="url" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-pink-500"
                           placeholder="例: https://goo.gl/maps/...">
                    <button type="button" id="validateUrlButton" 
                            class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-r-md transition duration-300">
                        <i class="fas fa-check"></i>
                    </button>
                </div>
                <p id="urlValidationResult" class="mt-1 text-sm"></p>
                <p class="mt-1 text-xs text-gray-500">Google Maps、OpenStreetMap、Yahoo!地図などのURLを入力してください</p>
            </div>
            
            <div class="flex justify-end">
                <button type="submit" class="bg-pink-600 hover:bg-pink-700 text-white font-bold py-2 px-4 rounded-md transition duration-300">
                    候補を追加 <i class="fas fa-plus ml-1"></i>
                </button>
            </div>
        </form>
    </div>
    
    <!-- 場所候補リスト -->
    <div id="locationsList" class="mb-6">
        <h3 class="text-lg font-medium text-pink-700 mb-3">候補場所一覧</h3>
        
        <!-- 候補がない場合のメッセージ -->
        <div id="noLocationMessage" class="<?= !empty($locations) ? 'hidden' : '' ?> bg-gray-50 p-4 rounded-lg text-center">
            <p class="text-gray-500">場所候補が登録されていません。上のフォームから候補を追加してください。</p>
        </div>
        
        <!-- 候補リスト -->
        <div id="locationsContainer" class="<?= empty($locations) ? 'hidden' : '' ?> space-y-3">
            <?php if (!empty($locations)): ?>
                <?php foreach ($locations as $location): ?>
                    <div class="location-item bg-white border border-gray-200 rounded-lg p-4" data-location-id="<?= htmlspecialchars($location['id']) ?>">
                        <div class="flex flex-col md:flex-row md:items-center justify-between">
                            <div class="mb-3 md:mb-0">
                                <h4 class="text-lg font-medium text-gray-800">
                                    <?= htmlspecialchars($location['name']) ?>
                                    <?php if (!empty($location['url'])): ?>
                                        <a href="<?= htmlspecialchars($location['url']) ?>" target="_blank" class="text-blue-600 hover:underline ml-1">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </a>
                                    <?php endif; ?>
                                </h4>
                                
                                <!-- 投票者一覧（トグル表示） -->
                                <div class="mt-1">
                                    <button class="text-sm text-blue-600 hover:text-blue-800 show-voters">
                                        <i class="fas fa-user"></i> 
                                        投票者を表示 (<span class="vote-count"><?= $location['vote_count'] ?? 0 ?></span>名)
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
                                         style="width: <?= ($location['vote_count'] ?? 0) > 0 ? min(100, ($location['vote_count'] / max(1, count($members))) * 100) : 0 ?>%">
                                    </div>
                                    <div class="absolute inset-0 flex items-center justify-center text-sm font-medium">
                                        <span class="vote-percentage">
                                            <?= ($location['vote_count'] ?? 0) > 0 ? floor(($location['vote_count'] / max(1, count($members))) * 100) : 0 ?>%
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
    <div id="mostVotedContainer" class="<?= empty($locations) ? 'hidden' : '' ?> bg-green-50 border border-green-200 rounded-lg p-4">
        <h3 class="text-lg font-medium text-green-700 mb-2">現在の最有力候補</h3>
        
        <div id="mostVoted" class="flex items-center">
            <div class="text-3xl text-green-500 mr-4">
                <i class="fas fa-map-pin"></i>
            </div>
            <div>
                <p id="mostVotedLocation" class="text-lg font-medium text-gray-800">
                    <!-- 最多投票の場所が入ります -->
                    <?php 
                    $mostVoted = null;
                    $maxVotes = 0;
                    
                    if (!empty($locations)) {
                        foreach ($locations as $location) {
                            if (($location['vote_count'] ?? 0) > $maxVotes) {
                                $maxVotes = $location['vote_count'];
                                $mostVoted = $location;
                            }
                        }
                    }
                    
                    if ($mostVoted):
                    ?>
                        <?= htmlspecialchars($mostVoted['name']) ?>
                        <?php if (!empty($mostVoted['url'])): ?>
                            <a href="<?= htmlspecialchars($mostVoted['url']) ?>" target="_blank" class="text-blue-600 hover:underline ml-1">
                                <i class="fas fa-map-marker-alt"></i>
                            </a>
                        <?php endif; ?>
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
    // 名前入力の初期化（ローカルストレージから復元）
    const savedName = localStorage.getItem('member_name');
    if (savedName) {
        document.getElementById('location_member_name').value = savedName;
    }
    
    // 地図URLの検証
    const validateUrlButton = document.getElementById('validateUrlButton');
    const locationUrl = document.getElementById('location_url');
    const urlValidationResult = document.getElementById('urlValidationResult');
    
    if (validateUrlButton) {
        validateUrlButton.addEventListener('click', function() {
            const url = locationUrl.value.trim();
            
            if (!url) {
                urlValidationResult.textContent = 'URLが入力されていません';
                urlValidationResult.className = 'mt-1 text-sm text-gray-500';
                return;
            }
            
            // APIに検証リクエスト
            const formData = new FormData();
            formData.append('url', url);
            
            fetch('/api/locations/validate-map-url', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.valid) {
                        urlValidationResult.textContent = '✓ ' + data.message;
                        urlValidationResult.className = 'mt-1 text-sm text-green-600';
                    } else {
                        urlValidationResult.textContent = '! ' + data.message;
                        urlValidationResult.className = 'mt-1 text-sm text-orange-600';
                    }
                } else {
                    urlValidationResult.textContent = '検証中にエラーが発生しました';
                    urlValidationResult.className = 'mt-1 text-sm text-red-600';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                urlValidationResult.textContent = '検証中にエラーが発生しました';
                urlValidationResult.className = 'mt-1 text-sm text-red-600';
            });
        });
    }
    
    // 場所候補の追加処理
    const addLocationForm = document.getElementById('addLocationForm');
    if (addLocationForm) {
        addLocationForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(addLocationForm);
            const memberName = formData.get('member_name');
            
            // 名前をローカルストレージに保存
            if (memberName) {
                localStorage.setItem('member_name', memberName);
            }
            
            // 送信処理
            fetch('/event/<?= htmlspecialchars($event['event_id']) ?>/location/add', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // フォームをリセット（名前は保持）
                    const memberName = document.getElementById('location_member_name').value;
                    addLocationForm.reset();
                    document.getElementById('location_member_name').value = memberName;
                    urlValidationResult.textContent = '';
                    
                    // 成功メッセージ
                    showMessage('success', data.message);
                    
                    // 候補リストを更新（Pusherで自動更新されるので不要）
                    // updateLocationsList();
                } else {
                    showMessage('error', data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('error', '場所候補の追加に失敗しました。もう一度お試しください。');
            });
        });
    }
    
    // 投票処理の初期化
    initializeLocationVoting();
    
    // 投票者一覧の表示/非表示切り替え
    document.querySelectorAll('#locations-content .show-voters').forEach(button => {
        button.addEventListener('click', function() {
            const locationItem = this.closest('.location-item');
            const votersList = locationItem.querySelector('.voters-list');
            const votersNames = locationItem.querySelector('.voters-names');
            const votersLoading = locationItem.querySelector('.voters-loading');
            
            if (votersList.classList.contains('hidden')) {
                // 表示する
                votersList.classList.remove('hidden');
                
                // データ取得
                const locationId = locationItem.dataset.locationId;
                fetch(`/api/event/<?= htmlspecialchars($event['event_id']) ?>/locations/${locationId}/voters`)
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
                const voteCount = locationItem.querySelector('.vote-count').textContent;
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
        const channel = pusher.subscribe('hanami-<?= htmlspecialchars($event['event_id']) ?>-locations');
        
        // 場所候補の更新イベントをリッスン
        channel.bind('locations-updated', function(data) {
            updateLocationsFromData(data.locations);
        });
    }
});

// 投票ボタンの処理
function initializeLocationVoting() {
    document.querySelectorAll('#locations-content .vote-button').forEach(button => {
        button.addEventListener('click', function() {
            const locationItem = this.closest('.location-item');
            const locationId = locationItem.dataset.locationId;
            
            // 名前取得
            const memberName = localStorage.getItem('member_name');
            if (!memberName) {
                showMessage('error', '投票するには名前を入力してください');
                
                // 名前入力フォームにフォーカス
                document.getElementById('location_member_name').focus();
                return;
            }
            
            // CSRFトークン取得
            const csrfToken = document.querySelector('input[name="csrf_token"]').value;
            
            // 投票処理
            const formData = new FormData();
            formData.append('csrf_token', csrfToken);
            formData.append('event_id', '<?= htmlspecialchars($event['event_id']) ?>');
            formData.append('location_id', locationId);
            formData.append('member_name', memberName);
            
            fetch('/event/<?= htmlspecialchars($event['event_id']) ?>/location/vote', {
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
function updateLocationVoteButton(button, isVoted) {
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

// 場所候補リストの更新
function updateLocationsFromData(locations) {
    const container = document.getElementById('locationsContainer');
    const noLocationMessage = document.getElementById('noLocationMessage');
    const mostVotedContainer = document.getElementById('mostVotedContainer');
    
    if (!locations || locations.length === 0) {
        container.classList.add('hidden');
        noLocationMessage.classList.remove('hidden');
        mostVotedContainer.classList.add('hidden');
        return;
    }
    
    container.classList.remove('hidden');
    noLocationMessage.classList.add('hidden');
    mostVotedContainer.classList.remove('hidden');
    
    // 既存の場所候補をクリア
    container.innerHTML = '';
    
    // 参加者数の取得（簡易実装）
    const membersCount = <?= count($members) ?>;
    
    // 最多投票の候補を特定
    let mostVoted = null;
    let maxVotes = 0;
    
    locations.forEach(location => {
        if ((location.vote_count || 0) > maxVotes) {
            maxVotes = location.vote_count || 0;
            mostVoted = location;
        }
        
        // 場所候補要素の作成
        const locationItem = document.createElement('div');
        locationItem.className = 'location-item bg-white border border-gray-200 rounded-lg p-4';
        locationItem.dataset.locationId = location.id;
        
        // パーセンテージの計算
        const votePercentage = membersCount > 0 ? Math.floor((location.vote_count || 0) / membersCount * 100) : 0;
        
        // 地図リンクの作成
        const mapLink = location.url 
            ? `<a href="${location.url}" target="_blank" class="text-blue-600 hover:underline ml-1"><i class="fas fa-map-marker-alt"></i></a>` 
            : '';
        
        locationItem.innerHTML = `
            <div class="flex flex-col md:flex-row md:items-center justify-between">
                <div class="mb-3 md:mb-0">
                    <h4 class="text-lg font-medium text-gray-800">
                        ${location.name}
                        ${mapLink}
                    </h4>
                    
                    <div class="mt-1">
                        <button class="text-sm text-blue-600 hover:text-blue-800 show-voters">
                            <i class="fas fa-user"></i> 
                            投票者を表示 (<span class="vote-count">${location.vote_count || 0}</span>名)
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
        
        container.appendChild(locationItem);
    });
    
    // 最多投票候補の更新
    const mostVotedLocation = document.getElementById('mostVotedLocation');
    const mostVotedCount = document.getElementById('mostVotedCount');
    
    if (mostVoted) {
        // 地図リンクの作成
        const mapLink = mostVoted.url 
            ? `<a href="${mostVoted.url}" target="_blank" class="text-blue-600 hover:underline ml-1"><i class="fas fa-map-marker-alt"></i></a>` 
            : '';
            
        mostVotedLocation.innerHTML = mostVoted.name + mapLink;
        mostVotedCount.innerHTML = `<span class="font-medium">${maxVotes}票</span> / 参加者${membersCount}名中`;
    } else {
        mostVotedLocation.textContent = 'まだ投票がありません';
        mostVotedCount.textContent = '';
    }
    
    // イベントリスナーの再初期化
    initializeLocationVoting();
    
    // 投票者一覧の表示/非表示切り替え処理を再初期化
    document.querySelectorAll('#locations-content .show-voters').forEach(button => {
        button.addEventListener('click', function() {
            const locationItem = this.closest('.location-item');
            const votersList = locationItem.querySelector('.voters-list');
            const votersNames = locationItem.querySelector('.voters-names');
            const votersLoading = locationItem.querySelector('.voters-loading');
            
            if (votersList.classList.contains('hidden')) {
                // 表示する処理
                votersList.classList.remove('hidden');
                
                // データ取得
                const locationId = locationItem.dataset.locationId;
                fetch(`/api/event/<?= htmlspecialchars($event['event_id']) ?>/locations/${locationId}/voters`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // 以下略（既存のコードと同様）
                        }
                    });
                
                // ボタンのテキスト変更
                this.innerHTML = '<i class="fas fa-user"></i> 投票者を隠す';
            } else {
                // 非表示にする処理
                votersList.classList.add('hidden');
                votersNames.classList.add('hidden');
                votersLoading.classList.remove('hidden');
                
                // ボタンのテキスト変更
                const voteCount = locationItem.querySelector('.vote-count').textContent;
                this.innerHTML = `<i class="fas fa-user"></i> 投票者を表示 (${voteCount}名)`;
            }
        });
    });
}
</script>
