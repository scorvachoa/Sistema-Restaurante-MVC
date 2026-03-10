<?php
require_once __DIR__ . '/../core/App.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Router.php';
require_once __DIR__ . '/../core/Helpers.php';

spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/../app/controllers/' . $class . '.php',
        __DIR__ . '/../app/models/' . $class . '.php',
        __DIR__ . '/../core/' . $class . '.php',
    ];
    foreach ($paths as $path) {
        if (is_file($path)) {
            require_once $path;
            return;
        }
    }
});

App::loadConfig();

session_start();

require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/MenuController.php';
require_once __DIR__ . '/../app/controllers/CartController.php';
require_once __DIR__ . '/../app/controllers/OrderController.php';
require_once __DIR__ . '/../app/controllers/AdminController.php';
require_once __DIR__ . '/../app/controllers/RiderController.php';
require_once __DIR__ . '/../app/controllers/MediaController.php';

$router = new Router();

$router->get('/', [new MenuController(), 'index']);
$router->get('/menu', [new MenuController(), 'index']);
$router->get('/product', [new MenuController(), 'show']);

$router->get('/register', [new AuthController(), 'registerForm']);
$router->post('/register', [new AuthController(), 'register']);
$router->get('/login', [new AuthController(), 'loginForm']);
$router->post('/login', [new AuthController(), 'login']);
$router->get('/logout', [new AuthController(), 'logout']);

$router->get('/cart', [new CartController(), 'index']);
$router->post('/cart/add', [new CartController(), 'add']);
$router->post('/cart/update', [new CartController(), 'update']);
$router->post('/cart/remove', [new CartController(), 'remove']);

$router->get('/checkout', [new OrderController(), 'checkout']);
$router->post('/order/create', [new OrderController(), 'create']);
$router->get('/orders', [new OrderController(), 'history']);
$router->get('/order', [new OrderController(), 'show']);

$router->get('/admin', [new AdminController(), 'dashboard']);
$router->get('/admin/products', [new AdminController(), 'products']);
$router->post('/admin/products/create', [new AdminController(), 'createProduct']);
$router->post('/admin/products/update', [new AdminController(), 'updateProduct']);
$router->post('/admin/products/delete', [new AdminController(), 'deleteProduct']);
$router->get('/admin/orders', [new AdminController(), 'orders']);
$router->post('/admin/orders/confirm', [new AdminController(), 'confirmOrder']);
$router->post('/admin/orders/assign', [new AdminController(), 'assignRider']);
$router->get('/admin/payments', [new AdminController(), 'payments']);
$router->post('/admin/payments/update', [new AdminController(), 'updatePayment']);
$router->get('/admin/payments/proof', [new AdminController(), 'paymentProof']);

$router->get('/media/products', [new MediaController(), 'productImage']);

$router->get('/rider', [new RiderController(), 'dashboard']);
$router->post('/rider/orders/status', [new RiderController(), 'updateStatus']);

$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);