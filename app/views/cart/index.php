<h3>Carrito</h3>
<?php if (!$cart): ?>
    <p>No hay productos en el carrito.</p>
<?php else: ?>
<table class="table">
    <thead>
        <tr>
            <th>Producto</th>
            <th>Extras</th>
            <th>Cantidad</th>
            <th>Precio</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php $total = 0; ?>
        <?php foreach ($cart as $item): ?>
            <?php
                $extrasTotal = array_sum(array_column($item['extras'], 'price'));
                $lineTotal = ($item['unit_price'] + $extrasTotal) * $item['quantity'];
                $total += $lineTotal;
            ?>
            <tr>
                <td><?= htmlspecialchars($item['name']); ?></td>
                <td>
                    <?php foreach ($item['extras'] as $extra): ?>
                        <div><?= htmlspecialchars($extra['name']); ?> (S/ <?= number_format($extra['price'], 2); ?>)</div>
                    <?php endforeach; ?>
                </td>
                <td>
                    <form class="d-flex" method="post" action="<?= url('/cart/update'); ?>">
                        <?= csrf_field(); ?>
                        <input type="hidden" name="item_id" value="<?= $item['id']; ?>">
                        <input class="form-control me-2" type="number" name="quantity" value="<?= $item['quantity']; ?>" min="1" style="width: 80px;">
                        <button class="btn btn-sm btn-outline-primary">Actualizar</button>
                    </form>
                </td>
                <td>S/ <?= number_format($lineTotal, 2); ?></td>
                <td>
                    <form method="post" action="<?= url('/cart/remove'); ?>">
                        <?= csrf_field(); ?>
                        <input type="hidden" name="item_id" value="<?= $item['id']; ?>">
                        <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<div class="text-end fw-bold">Total: S/ <?= number_format($total, 2); ?></div>
<div class="mt-3">
    <a class="btn btn-success" href="<?= url('/checkout'); ?>">Continuar al pago</a>
</div>
<?php endif; ?>