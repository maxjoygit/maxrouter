<?php
namespace Max\Router;
class Router {
    protected $routes = [];
    protected $callback404;
    public function get($route, $callback) {
        $this->routes['GET'][$route] = $callback;
    }
    public function post($route, $callback) {
        $this->routes['POST'][$route] = $callback;
    }
    public function put($route, $callback) {
        $this->routes['PUT'][$route] = $callback;
    }
    public function patch($route, $callback) {
        $this->routes['PATCH'][$route] = $callback;
    }
    public function delete($route, $callback) {
        $this->routes['DELETE'][$route] = $callback;
    }
    public function run() {
        $url = trim(strtok(filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL), '?'), '/') == '' ? '/' : trim(strtok(filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL), '?'), '/');
        $allRoutes = $this->routes[$_SERVER['REQUEST_METHOD']] ?? [];
        if(!empty($allRoutes)) {
            foreach( $allRoutes as $routes => $callback ) {
                $routes = trim(filter_var($routes, FILTER_SANITIZE_URL), '/') == '' ? '/' : trim(filter_var($routes, FILTER_SANITIZE_URL), '/');
                if(preg_match("#" . $routes . "#i", $url, $matches)) {
                    if($matches[0] == $url) {
                        $urlArray = explode('/', $url);
                        $routesArray = explode('/', $routes);
                        $args = array_diff($urlArray, $routesArray );
                        call_user_func_array($callback, $args);
                        return;
                    }  
                }
            }
        }
        $this->error404();
    }
    function set404($callback404) {
        $this->callback404 = $callback404;
    }
    function error404() {
        http_response_code(404);
        header_remove('x-powered-by');
        if(!empty($this->callback404)) {
            call_user_func_array($this->callback404, []);
        }else {
            header("Content-Type: text/html;charset=UTF-8");
            echo '<h1 style="text-align: center">404 Page Not Found!</h1>';
        }
    }
}