<!-- 持ち物タブ -->
<div id="items-content" class="tab-pane hidden">
    <h2 class="text-xl font-semibold text-pink-700 mb-4">持ち物リスト</h2>
    
    <!-- 持ち物追加フォーム -->
    <div class="bg-pink-50 rounded-lg p-4 mb-6">
        <h3 class="text-lg font-medium text-pink-700 mb-3">新しい持ち物を追加</h3>
        
        <form id="addItemForm" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
            <input type="hidden" name="event_id" value="<?= htmlspecialchars($event['event_id']) ?>">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="item_name" class="block text-gray-700 font-medium mb-2">持ち物名 <span class="text-red-500">*</span></label>
                    <input type="text" id="item_name" name="name" required 
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500"
                           placeholder="例: お花見シート">
                </div>
                
                <div>
                    <label for="item_category" class="block text-gray-700 font-medium mb-2">カテゴリー</label>
                    <select id="item_category" name="category" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                        <option value="">カテゴリーを選択</option>
                        <option value="食べ物">食べ物</option>
                        <option value="飲み物">飲み物</option>
                        <option value="レジャー用品">レジャー用品</option>
                        <option value="キッチン用品">キッチン用品</option>
                        <option value="緊急・安全">緊急・安全</option>
                        <option value="その他">その他</option>
                    </select>
                </div>
            </div>
            
            <div>
                <label for="item_member_name" class="block text-gray-700 font-medium mb-2">あなたの名前 <span class="text-red-500">*</span></label>
                <input type="text" id="item_member_name" name="member_name" required 
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500"
                       placeholder="例: 山田太郎">
            </div>
            
            <div class="flex justify-end">
                <button type="submit" class="bg-pink-600 hover:bg-pink-700 text-white font-bold py-2 px-4 rounded-md transition duration-300">
                    持ち物を追加 <i class="fas fa-plus ml-1"></i>
                </button>
            </div>
        </form>
    </div>
    
    <!-- 準備状況ダッシュボード -->
    <div class="bg-white border border-gray-200 rounded-lg p-4 mb-6">
        <h3 class="text-lg font-medium text-pink-700 mb-3">準備状況</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
            <div class="bg-pink-50 p-3 rounded-lg">
                <p class="text-sm text-gray-600">登録アイテム数</p>
                <p class="text-2xl font-bold text-pink-700" id="stats-total">0</p>
            </div>
            
            <div class="bg-green-50 p-3 rounded-lg">
                <p class="text-sm text-gray-600">準備完了</p>
                <p class="text-2xl font-bold text-green-600" id="stats-ready">0</p>
            </div>
            
            <div class="bg-yellow-50 p-3 rounded-lg">
                <p class="text-sm text-gray-600">準備中</p>
                <p class="text-2xl font-bold text-yellow-600" id="stats-pending">0</p>
            </div>
            
            <div class="bg-blue-50 p-3 rounded-lg">
                <p class="text-sm text-gray-600">担当者あり</p>
                <p class="text-2xl font-bold text-blue-600" id="stats-assigned">0</p>
            </div>
        </div>
        
        <div class="relative pt-1">
            <div class="flex mb-2 items-center justify-between">
                <div>
                    <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-green-600 bg-green-200">
                        準備進捗率
                    </span>
                </div>
                <div class="text-right">
                    <span class="text-xs font-semibold inline-block text-green-600" id="progress-percentage">0%</span>
                </div>
            </div>
            <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-gray-200">
                <div class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-green-500" id="progress-bar" style="width: 0%"></div>
            </div>
        </div>
    </div>
    
    <!-- 持ち物リスト -->
    <div id="itemsList" class="mb-6">
        <div class="flex justify-between items-center mb-3">
            <h3 class="text-lg font-medium text-pink-700">持ち物一覧</h3>
            
            <div class="flex space-x-2">
                <div class="relative">
                    <input type="text" id="itemSearchInput" placeholder="持ち物を検索" 
                           class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>
                
                <select id="itemCategoryFilter" class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                    <option value="">全てのカテゴリー</option>
                    <option value="食べ物">食べ物</option>
                    <option value="飲み物">飲み物</option>
                    <option value="レジャー用品">レジャー用品</option>
                    <option value="キッチン用品">キッチン用品</option>
                    <option value="緊急・安全">緊急・安全</option>
                    <option value="その他">その他</option>
                </select>
                
                <select id="itemStatusFilter" class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                    <option value="">全てのステータス</option>
                    <option value="ready">準備完了</option>
                    <option value="pending">準備中</option>
                </select>
            </div>
        </div>
        
        <!-- 持ち物がない場合のメッセージ -->
        <div id="noItemMessage" class="<?= !empty($items) ? 'hidden' : '' ?> bg-gray-50 p-4 rounded-lg text-center">
            <p class="text-gray-500">持ち物が登録されていません。上のフォームから持ち物を追加してください。</p>
        </div>
        
        <!-- カテゴリー別持ち物リスト -->
        <div id="itemsContainer" class="<?= empty($items) ? 'hidden' : '' ?> space-y-6">
            <!-- JavaScriptで動的に生成されます -->
        </div>
    </div>
    
    <!-- 担当者割り当てモーダル -->
    <div id="assignModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 max-w-md w-full">
            <h3 class="text-xl font-semibold text-pink-700 mb-4">担当者の割り当て</h3>
            <p id="assignItemName" class="text-gray-700 mb-4"></p>
            
            <form id="assignForm" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                <input type="hidden" id="assign_item_id" name="item_id">
                
                <div>
                    <label for="assign_member_name" class="block text-gray-700 font-medium mb-2">担当者名</label>
                    <input type="text" id="assign_member_name" name="member_name" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500"
                           placeholder="例: 山田太郎">
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" id="closeAssignModal" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-md transition duration-300">
                        キャンセル
                    </button>
                    <button type="submit" class="bg-pink-600 hover:bg-pink-700 text-white font-bold py-2 px-4 rounded-md transition duration-300">
                        担当者を変更
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 名前入力の初期化（ローカルストレージから復元）
    const savedName = localStorage.getItem('member_name');
    if (savedName && document.getElementById('item_member_name')) {
        document.getElementById('item_member_name').value = savedName;
    }
    
    // 持ち物追加フォームの処理
    const addItemForm = document.getElementById('addItemForm');
    if (addItemForm) {
        addItemForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(addItemForm);
            const memberName = formData.get('member_name');
            
            // 名前をローカルストレージに保存
            if (memberName) {
                localStorage.setItem('member_name', memberName);
            }
            
            // 送信処理
            fetch('/event/<?= htmlspecialchars($event['event_id']) ?>/item/add', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // フォームをリセット（名前は保持）
                    const memberName = document.getElementById('item_member_name').value;
                    addItemForm.reset();
                    document.getElementById('item_member_name').value = memberName;
                    
                    // 成功メッセージ
                    showMessage('success', data.message);
                    
                    // 持ち物リストを更新
                    loadItems();
                } else {
                    showMessage('error', data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('error', '持ち物の追加に失敗しました。もう一度お試しください。');
            });
        });
    }
    
    // イベント委任を使って動的に生成された要素にもイベントハンドラーを適用
    document.addEventListener('click', function(e) {
        // 持ち物の削除処理
        if (e.target.classList.contains('delete-item') || e.target.closest('.delete-item')) {
            const itemCard = e.target.closest('.item-card');
            const itemId = itemCard.dataset.itemId;
            const itemName = itemCard.querySelector('h5').textContent;
            
            if (confirm(`「${itemName}」を持ち物リストから削除しますか？`)) {
                const formData = new FormData();
                formData.append('csrf_token', '<?= $csrfToken ?>');
                
                fetch(`/event/<?= htmlspecialchars($event['event_id']) ?>/item/${itemId}/delete`, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showMessage('success', data.message);
                        loadItems(); // 持ち物リストを更新
                    } else {
                        showMessage('error', data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showMessage('error', '持ち物の削除に失敗しました。');
                });
            }
        }
        
        // ステータストグル（準備中 ⇄ 準備完了）
        if (e.target.classList.contains('status-toggle') || e.target.closest('.status-toggle')) {
            const itemCard = e.target.closest('.item-card');
            const itemId = itemCard.dataset.itemId;
            const currentStatus = itemCard.dataset.itemStatus;
            const newStatus = currentStatus === 'ready' ? 'pending' : 'ready';
            
            const formData = new FormData();
            formData.append('csrf_token', '<?= $csrfToken ?>');
            formData.append('status', newStatus);
            
            fetch(`/event/<?= htmlspecialchars($event['event_id']) ?>/item/${itemId}/status`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage('success', data.message);
                    loadItems(); // 持ち物リストを更新
                } else {
                    showMessage('error', data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('error', 'ステータスの変更に失敗しました。');
            });
        }
        
        // 担当者割り当てボタン
        if (e.target.classList.contains('assign-btn') || e.target.closest('.assign-btn')) {
            const itemCard = e.target.closest('.item-card');
            const itemId = itemCard.dataset.itemId;
            const itemName = itemCard.querySelector('h5').textContent;
            
            // モーダルを表示して担当者を設定
            document.getElementById('assignItemName').textContent = `「${itemName}」の担当者`;
            document.getElementById('assign_item_id').value = itemId;
            document.getElementById('assign_member_name').value = localStorage.getItem('member_name') || '';
            document.getElementById('assignModal').classList.remove('hidden');
        }
        
        // 担当者解除ボタン
        if (e.target.classList.contains('unassign-btn') || e.target.closest('.unassign-btn')) {
            const itemCard = e.target.closest('.item-card');
            const itemId = itemCard.dataset.itemId;
            const itemName = itemCard.querySelector('h5').textContent;
            const assigneeName = itemCard.querySelector('.assignee-name').textContent;
            
            if (confirm(`「${itemName}」の担当者「${assigneeName}」を解除しますか？`)) {
                const formData = new FormData();
                formData.append('csrf_token', '<?= $csrfToken ?>');
                formData.append('member_name', ''); // 空の名前で担当解除
                
                fetch(`/event/<?= htmlspecialchars($event['event_id']) ?>/item/${itemId}/assign`, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showMessage('success', data.message);
                        loadItems(); // 持ち物リストを更新
                    } else {
                        showMessage('error', data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showMessage('error', '担当者の解除に失敗しました。');
                });
            }
        }
    });
    
    // 担当者割り当てフォーム送信
    const assignForm = document.getElementById('assignForm');
    if (assignForm) {
        assignForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(assignForm);
            const itemId = formData.get('item_id');
            const memberName = formData.get('member_name');
            
            fetch(`/event/<?= htmlspecialchars($event['event_id']) ?>/item/${itemId}/assign`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('assignModal').classList.add('hidden');
                    showMessage('success', data.message);
                    loadItems(); // 持ち物リストを更新
                } else {
                    showMessage('error', data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('error', '担当者の割り当てに失敗しました。');
            });
        });
    }
    
    // 割り当てモーダルを閉じる
    const closeAssignModal = document.getElementById('closeAssignModal');
    if (closeAssignModal) {
        closeAssignModal.addEventListener('click', function() {
            document.getElementById('assignModal').classList.add('hidden');
        });
    }
    
    // 検索フィルター機能
    const searchInput = document.getElementById('itemSearchInput');
    const categoryFilter = document.getElementById('itemCategoryFilter');
    const statusFilter = document.getElementById('itemStatusFilter');
    
    function applyFilters() {
        const searchTerm = searchInput.value.toLowerCase();
        const categoryValue = categoryFilter.value;
        const statusValue = statusFilter.value;
        
        // カテゴリーグループのフィルタリング
        document.querySelectorAll('.category-group').forEach(group => {
            const groupCategory = group.dataset.category;
            
            // カテゴリーフィルターが指定されている場合にチェック
            const categoryMatch = !categoryValue || groupCategory === categoryValue;
            
            if (categoryMatch) {
                group.style.display = 'block';
                
                // カテゴリー内のアイテムをフィルタリング
                const items = group.querySelectorAll('.item-card');
                let visibleItems = 0;
                
                items.forEach(item => {
                    const itemName = item.querySelector('h5').textContent.toLowerCase();
                    const itemStatus = item.dataset.itemStatus;
                    
                    // 検索語句とステータスのマッチをチェック
                    const nameMatch = itemName.includes(searchTerm);
                    const statusMatch = !statusValue || itemStatus === statusValue;
                    
                    if (nameMatch && statusMatch) {
                        item.style.display = 'flex';
                        visibleItems++;
                    } else {
                        item.style.display = 'none';
                    }
                });
                
                // カテゴリー内に表示するアイテムがない場合はカテゴリー自体を非表示
                if (visibleItems === 0) {
                    group.style.display = 'none';
                }
            } else {
                group.style.display = 'none';
            }
        });
        
        // 全てのアイテムが非表示の場合は「アイテムがありません」メッセージを表示
        const visibleGroups = document.querySelectorAll('.category-group[style="display: block;"]');
        if (visibleGroups.length === 0) {
            const noItemMessage = document.getElementById('noItemMessage');
            if (noItemMessage) {
                noItemMessage.textContent = 'フィルター条件に一致するアイテムがありません。';
                noItemMessage.classList.remove('hidden');
            }
        } else {
            const noItemMessage = document.getElementById('noItemMessage');
            if (noItemMessage) {
                noItemMessage.classList.add('hidden');
            }
        }
    }
    
    // フィルター適用イベント
    if (searchInput) {
        searchInput.addEventListener('input', applyFilters);
    }
    
    if (categoryFilter) {
        categoryFilter.addEventListener('change', applyFilters);
    }
    
    if (statusFilter) {
        statusFilter.addEventListener('change', applyFilters);
    }
    
    // 持ち物一覧の読み込み
    function loadItems() {
        fetch(`/api/event/<?= htmlspecialchars($event['event_id']) ?>/items`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateItemsList(data);
                } else {
                    console.error('Error loading items:', data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }
    
    // 持ち物リストと統計情報の更新
    function updateItemsList(data) {
        const { items, items_by_category, statistics } = data;
        
        // 統計情報の更新
        const statsTotal = document.getElementById('stats-total');
        const statsReady = document.getElementById('stats-ready');
        const statsPending = document.getElementById('stats-pending');
        const statsAssigned = document.getElementById('stats-assigned');
        
        if (statsTotal) statsTotal.textContent = statistics.total || 0;
        if (statsReady) statsReady.textContent = statistics.ready || 0;
        if (statsPending) statsPending.textContent = statistics.pending || 0;
        if (statsAssigned) statsAssigned.textContent = statistics.assigned || 0;
        
        // 進捗バーの更新
        const progressPercentage = statistics.progress_percentage || 0;
        const progressText = document.getElementById('progress-percentage');
        const progressBar = document.getElementById('progress-bar');
        if (progressText) progressText.textContent = `${progressPercentage}%`;
        if (progressBar) progressBar.style.width = `${progressPercentage}%`;
        
        // 持ち物がない場合のメッセージ表示
        const noItemMessage = document.getElementById('noItemMessage');
        const itemsContainer = document.getElementById('itemsContainer');
        
        if (items.length === 0) {
            if (noItemMessage) noItemMessage.classList.remove('hidden');
            if (itemsContainer) itemsContainer.classList.add('hidden');
            return;
        }
        
        if (noItemMessage) noItemMessage.classList.add('hidden');
        if (itemsContainer) {
            itemsContainer.classList.remove('hidden');
            
            // 既存のリストをクリア
            itemsContainer.innerHTML = '';
            
            // カテゴリー別にアイテムを表示
            Object.entries(items_by_category).forEach(([category, categoryItems]) => {
                const categoryGroup = document.createElement('div');
                categoryGroup.className = 'category-group';
                categoryGroup.dataset.category = category;
                
                categoryGroup.innerHTML = `
                    <h4 class="text-md font-medium text-gray-800 border-b border-gray-300 pb-2 mb-3">
                        ${category}
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3" id="items-${category.replace(/\s+/g, '-')}">
                    </div>
                `;
                
                itemsContainer.appendChild(categoryGroup);
                
                // このカテゴリーのアイテムを追加
                const itemsGrid = categoryGroup.querySelector(`#items-${category.replace(/\s+/g, '-')}`);
                
                categoryItems.forEach(item => {
                    const itemCard = document.createElement('div');
                    itemCard.className = 'item-card bg-white border border-gray-200 rounded-lg p-3 flex items-center justify-between';
                    itemCard.dataset.itemId = item.id;
                    itemCard.dataset.itemStatus = item.status;
                    
                    const statusClass = item.status === 'ready' 
                        ? 'bg-green-500 border-green-500 text-white' 
                        : 'border-gray-300 text-transparent';
                    
                    const assigneeContent = item.assignee_name 
                        ? `<span class="assignee-name">${item.assignee_name}</span>
                           <button class="text-xs text-red-600 hover:text-red-800 ml-1 unassign-btn">
                               <i class="fas fa-times"></i>
                           </button>`
                        : `<span class="text-gray-400 assignee-name">未割り当て</span>
                           <button class="text-xs text-blue-600 hover:text-blue-800 ml-1 assign-btn">
                               <i class="fas fa-user-plus"></i>
                           </button>`;
                    
                    itemCard.innerHTML = `
                        <div class="flex items-center">
                            <div class="mr-3">
                                <button class="status-toggle w-6 h-6 flex items-center justify-center rounded-full border-2 ${statusClass}">
                                    <i class="fas fa-check text-sm"></i>
                                </button>
                            </div>
                            
                            <div>
                                <h5 class="text-gray-800 font-medium">${item.name}</h5>
                                <p class="text-sm text-gray-500">
                                    担当: ${assigneeContent}
                                </p>
                            </div>
                        </div>
                        
                        <div>
                            <button class="text-red-500 hover:text-red-700 delete-item">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `;
                    
                    itemsGrid.appendChild(itemCard);
                });
            });
            
            // フィルターが適用されている場合は再適用
            if (searchInput && searchInput.value || 
                categoryFilter && categoryFilter.value || 
                statusFilter && statusFilter.value) {
                applyFilters();
            }
        }
    }
    
    // 初回読み込み
    loadItems();
});
</script>