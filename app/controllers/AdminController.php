<?php
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/../models/User.php';

class AdminController extends Controller
{
    public function dashboard(): void
    {
        require_role(['admin']);
        $orders = Order::all();
        $payments = Payment::all();
        $clients = User::allByRole('client');
        $riders = User::allByRole('rider');
        $this->view('admin/dashboard', compact('orders', 'payments', 'clients', 'riders'));
    }

    public function products(): void
    {
        require_role(['admin']);
        $products = Product::all();
        $categories = Category::all();
        $editId = (int)($_GET['edit'] ?? 0);
        $editProduct = $editId ? Product::find($editId) : null;
        $this->view('admin/products', compact('products', 'categories', 'editProduct'));
    }

    public function createProduct(): void
    {
        require_role(['admin']);
        $image = $this->saveProductImage($_FILES['image_file'] ?? null);
        if (!$image) {
            $_SESSION['flash_error'] = 'Imagen invÃ¡lida.';
            $this->redirect('/admin/products');
        }
        $data = $_POST;
        $data['image'] = $image;
        Product::create($data);
        $this->redirect('/admin/products');
    }

    public function updateProduct(): void
    {
        require_role(['admin']);
        $id = (int)($_POST['id'] ?? 0);
        $existing = Product::find($id);
        if (!$existing) {
            $_SESSION['flash_error'] = 'Producto no encontrado.';
            $this->redirect('/admin/products');
        }

        $image = $this->saveProductImage($_FILES['image_file'] ?? null);
        $data = $_POST;
        $data['image'] = $image ?: $existing['image'];

        Product::update($id, $data);
        $this->redirect('/admin/products');
    }

    public function deleteProduct(): void
    {
        require_role(['admin']);
        $id = (int)($_POST['id'] ?? 0);
        Product::delete($id);
        $this->redirect('/admin/products');
    }

    public function orders(): void
    {
        require_role(['admin']);
        $filters = [
            'status' => $_GET['status'] ?? '',
            'q' => $_GET['q'] ?? ''
        ];
        $orders = Order::all($filters);
        $riders = User::allByRole('rider');
        $this->view('admin/orders', compact('orders', 'riders', 'filters'));
    }

    public function confirmOrder(): void
    {
        require_role(['admin']);
        $orderId = (int)($_POST['order_id'] ?? 0);
        if (!Order::updateStatus($orderId, 'confirmed')) {
            $_SESSION['flash_error'] = 'No se pudo confirmar el pedido.';
        }
        $this->redirect('/admin/orders');
    }

    public function assignRider(): void
    {
        require_role(['admin']);
        $orderId = (int)($_POST['order_id'] ?? 0);
        $riderId = (int)($_POST['rider_id'] ?? 0);
        Order::assignRider($orderId, $riderId);
        $this->redirect('/admin/orders');
    }

    public function payments(): void
    {
        require_role(['admin']);
        $filters = [
            'status' => $_GET['status'] ?? '',
            'method' => $_GET['method'] ?? ''
        ];
        $payments = Payment::all($filters);
        $this->view('admin/payments', compact('payments', 'filters'));
    }

    public function updatePayment(): void
    {
        require_role(['admin']);
        $paymentId = (int)($_POST['payment_id'] ?? 0);
        $status = $_POST['status'] ?? 'review';
        $allowed = ['review', 'approved', 'rejected'];
        if (!in_array($status, $allowed, true)) {
            $_SESSION['flash_error'] = 'Estado invÃ¡lido.';
            $this->redirect('/admin/payments');
        }
        Payment::updateStatus($paymentId, $status);
        $this->redirect('/admin/payments');
    }

    public function paymentProof(): void
    {
        require_role(['admin']);
        $paymentId = (int)($_GET['id'] ?? 0);
        $payment = Payment::find($paymentId);
        if (!$payment || !$payment['proof_image']) {
            http_response_code(404);
            echo 'Archivo no encontrado';
            return;
        }

        $path = App::config('app', 'uploads_dir') . '/payments/' . $payment['proof_image'];
        if (!is_file($path)) {
            http_response_code(404);
            echo 'Archivo no encontrado';
            return;
        }

        $mime = mime_content_type($path) ?: 'application/octet-stream';
        header('Content-Type: ' . $mime);
        header('Content-Length: ' . filesize($path));
        readfile($path);
    }

    private function saveProductImage(?array $file): ?string
    {
        if (!$file || $file['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }
        if ($file['size'] > 5 * 1024 * 1024) {
            return null;
        }

        $allowedExt = ['jpg', 'jpeg', 'png', 'webp'];
        $allowedMime = ['image/jpeg', 'image/png', 'image/webp'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExt, true)) {
            return null;
        }

        $info = @getimagesize($file['tmp_name']);
        if (!$info || !in_array($info['mime'], $allowedMime, true)) {
            return null;
        }

        $targetDir = App::config('app', 'product_uploads_dir');
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0775, true);
        }

        $filename = uniqid('product_', true) . '.' . $ext;
        $target = $targetDir . '/' . $filename;
        if (!move_uploaded_file($file['tmp_name'], $target)) {
            return null;
        }

        return $filename;
    }
}