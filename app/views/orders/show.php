<h3>Pedido #<?= $order['id']; ?></h3>
<p>Cliente: <?= htmlspecialchars($order['user_name']); ?></p>
<p>Estado: <?= status_label($order['status']); ?></p>
<table class="table">
    <thead>
        <tr>
            <th>Producto</th>
            <th>Extras</th>
            <th>Cantidad</th>
            <th>Precio</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($items as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['product_name']); ?></td>
                <td>
                    <?php foreach ($item['extras'] as $extra): ?>
                        <div><?= htmlspecialchars($extra['extra_name']); ?> (S/ <?= number_format($extra['extra_price'], 2); ?>)</div>
                    <?php endforeach; ?>
                </td>
                <td><?= $item['quantity']; ?></td>
                <td>S/ <?= number_format($item['unit_price'], 2); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>