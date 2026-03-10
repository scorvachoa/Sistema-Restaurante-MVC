<h3>Mis pedidos</h3>
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Estado</th>
            <th>Total</th>
            <th>Fecha</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($orders as $order): ?>
            <tr>
                <td>#<?= $order['id']; ?></td>
                <td><?= status_label($order['status']); ?></td>
                <td>S/ <?= number_format($order['total_amount'], 2); ?></td>
                <td><?= $order['created_at']; ?></td>
                <td><a class="btn btn-sm btn-outline-primary" href="<?= url('/order?id=' . $order['id']); ?>">Ver</a></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>