    </main>
    
    <footer class="bg-pink-100 mt-8 py-6">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-4 md:mb-0">
                    <p class="text-pink-700">&copy; <?= date('Y') ?> 花見調整サイト</p>
                </div>
                <div>
                    <ul class="flex space-x-4">
                        <li><a href="/terms" class="text-pink-700 hover:text-pink-500">利用規約</a></li>
                        <li><a href="/privacy" class="text-pink-700 hover:text-pink-500">プライバシーポリシー</a></li>
                        <li><a href="/contact" class="text-pink-700 hover:text-pink-500">お問い合わせ</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // フラットピッカーの初期化（日本語化）
        flatpickr.localize(flatpickr.l10ns.ja);
        flatpickr(".datepicker", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            minDate: "today",
            time_24hr: true
        });
        
        // フラッシュメッセージの自動非表示
        const flashMessages = document.querySelectorAll('.flash-message');
        flashMessages.forEach(function(message) {
            setTimeout(function() {
                message.classList.add('opacity-0');
                setTimeout(function() {
                    message.remove();
                }, 300);
            }, 5000);
        });
    });
    </script>
</body>
</html>
