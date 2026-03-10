<h3>Pedidos asignados</h3>
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Cliente</th>
            <th>Estado</th>
            <th>Total</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($orders as $order): ?>
            <tr>
                <td>#<?= $order['id']; ?></td>
                <td><?= htmlspecialchars($order['user_name']); ?></td>
                <td><?= status_label($order['status']); ?></td>
                <td>S/ <?= number_format($order['total_amount'], 2); ?></td>
                <td>
                    <form method="post" action="<?= url('/rider/orders/status'); ?>">
                        <?= csrf_field(); ?>
                        <input type="hidden" name="order_id" value="<?= $order['id']; ?>">
                        <select class="form-select form-select-sm" name="status">
                            <option value="picked_up">picked_up</option>
                            <option value="on_the_way">on_the_way</option>
                            <option value="delivered">delivered</option>
                            <option value="cancelled">cancelled</option>
                        </select>
                        <button class="btn btn-sm btn-outline-primary mt-1">Actualizar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>