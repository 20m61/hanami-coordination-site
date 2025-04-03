<?php
namespace Hanami\Core;

/**
 * ルーティングを管理するクラス
 */
class Router
{
    /**
     * 登録されたルート
     * 
     * @var array
     */
    private array $routes = [];

    /**
     * GETリクエスト用のルート登録
     * 
     * @param string $path ルートパス
     * @param string $handler コントローラとアクションの指定（例：'HomeController@index'）
     * @return void
     */
    public function get(string $path, string $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    /**
     * POSTリクエスト用のルート登録
     * 
     * @param string $path ルートパス
     * @param string $handler コントローラとアクションの指定（例：'HomeController@index'）
     * @return void
     */
    public function post(string $path, string $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    /**
     * ルートの追加
     * 
     * @param string $method HTTPメソッド
     * @param string $path ルートパス
     * @param string $handler コントローラとアクションの指定
     * @return void
     */
    private function addRoute(string $method, string $path, string $handler): void
    {
        // パスのパラメータを正規表現パターンに変換（例：/event/{id} -> /event/([^/]+)）
        $pattern = preg_replace('/{([^\/]+)}/', '([^/]+)', $path);
        $pattern = str_replace('/', '\/', $pattern);
        $pattern = '/^' . $pattern . '$/';

        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'pattern' => $pattern,
            'handler' => $handler
        ];
    }

    /**
     * リクエストを処理し、対応するコントローラーアクションを実行
     * 
     * @return void
     */
    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && preg_match($route['pattern'], $path, $matches)) {
                // 最初の要素（完全一致）を削除
                array_shift($matches);

                // コントローラとアクションを取得
                list($controller, $action) = explode('@', $route['handler']);
                $controllerClass = "\\Hanami\\Controllers\\{$controller}";

                // コントローラのインスタンス化と実行
                $controllerInstance = new $controllerClass();
                call_user_func_array([$controllerInstance, $action], $matches);
                return;
            }
        }

        // 一致するルートが見つからない場合は404エラー
        $this->handle404();
    }

    /**
     * 404エラーの処理
     * 
     * @return void
     */
    private function handle404(): void
    {
        header('HTTP/1.1 404 Not Found');
        include __DIR__ . '/../Views/404.php';
    }
}
