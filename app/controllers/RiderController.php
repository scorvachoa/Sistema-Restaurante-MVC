<?php
require_once __DIR__ . '/../models/Order.php';

class RiderController extends Controller
{
    public function dashboard(): void
    {
        require_role(['rider']);
        $orders = Order::assignedTo(auth_user()['id']);
        $this->view('rider/dashboard', compact('orders'));
    }

    public function updateStatus(): void
    {
        require_role(['rider']);
        $orderId = (int)($_POST['order_id'] ?? 0);
        $status = $_POST['status'] ?? 'picked_up';
        $allowed = ['picked_up', 'on_the_way', 'delivered', 'cancelled'];
        if (!in_array($status, $allowed, true)) {
            $_SESSION['flash_error'] = 'Estado inválido.';
            $this->redirect('/rider');
        }

        $order = Order::find($orderId);
        if (!$order || (int)$order['rider_id'] !== (int)auth_user()['id']) {
            http_response_code(403);
            echo 'Acceso denegado';
            return;
        }

        if (!Order::updateStatus($orderId, $status)) {
            $_SESSION['flash_error'] = 'No se pudo actualizar el estado.';
        }
        $this->redirect('/rider');
    }
}