<?php
namespace Hanami\Controllers;

/**
 * すべてのコントローラーの基底クラス
 */
class Controller
{
    /**
     * ビューの表示
     * 
     * @param string $view ビューファイル名
     * @param array $data ビューに渡すデータ
     * @return void
     */
    protected function view(string $view, array $data = []): void
    {
        // データを変数として展開
        extract($data);
        
        // ヘッダーを読み込み
        include __DIR__ . '/../Views/layouts/header.php';
        
        // ビューを読み込み
        include __DIR__ . "/../Views/{$view}.php";
        
        // フッターを読み込み
        include __DIR__ . '/../Views/layouts/footer.php';
    }
    
    /**
     * JSONレスポンスの送信
     * 
     * @param array $data 応答データ
     * @param int $statusCode HTTPステータスコード
     * @return void
     */
    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    /**
     * リダイレクト
     * 
     * @param string $path リダイレクト先のパス
     * @return void
     */
    protected function redirect(string $path): void
    {
        header("Location: {$path}");
        exit;
    }
    
    /**
     * 404エラーページの表示
     * 
     * @return void
     */
    protected function notFound(): void
    {
        http_response_code(404);
        $this->view('404');
        exit;
    }
    
    /**
     * CSRF対策のトークン生成
     * 
     * @return string
     */
    protected function generateCsrfToken(): string
    {
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
        return $token;
    }
    
    /**
     * CSRF対策のトークン検証
     * 
     * @param string $token 検証するトークン
     * @return bool
     */
    protected function validateCsrfToken(string $token): bool
    {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * POST変数の取得（XSS対策）
     * 
     * @param string $key 取得する変数名
     * @param mixed $default デフォルト値
     * @return mixed
     */
    protected function post(string $key, $default = null)
    {
        return isset($_POST[$key]) ? htmlspecialchars($_POST[$key], ENT_QUOTES, 'UTF-8') : $default;
    }
    
    /**
     * GET変数の取得（XSS対策）
     * 
     * @param string $key 取得する変数名
     * @param mixed $default デフォルト値
     * @return mixed
     */
    protected function get(string $key, $default = null)
    {
        return isset($_GET[$key]) ? htmlspecialchars($_GET[$key], ENT_QUOTES, 'UTF-8') : $default;
    }
}
