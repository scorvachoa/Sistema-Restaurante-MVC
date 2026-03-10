<h3>Pagos</h3>
<form class="row g-2 mb-3" method="get" action="<?= url('/admin/payments'); ?>">
    <div class="col-md-3">
        <select class="form-select" name="status">
            <option value="">Todos</option>
            <?php foreach (['pending','review','approved','rejected'] as $st): ?>
                <option value="<?= $st; ?>" <?= ($filters['status'] ?? '') === $st ? 'selected' : ''; ?>><?= status_label($st); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-3">
        <select class="form-select" name="method">
            <option value="">Todos</option>
            <?php foreach (['cash','yape','plin','bank_transfer'] as $m): ?>
                <option value="<?= $m; ?>" <?= ($filters['method'] ?? '') === $m ? 'selected' : ''; ?>><?= $m; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2">
        <button class="btn btn-outline-primary">Filtrar</button>
    </div>
</form>
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Pedido</th>
            <th>Cliente</th>
            <th>Método</th>
            <th>Estado</th>
            <th>Comprobante</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($payments as $pay): ?>
            <tr>
                <td>#<?= $pay['id']; ?></td>
                <td>#<?= $pay['order_id']; ?></td>
                <td><?= htmlspecialchars($pay['user_name']); ?></td>
                <td><?= $pay['method']; ?></td>
                <td><?= status_label($pay['status']); ?></td>
                <td>
                    <?php if ($pay['proof_image']): ?>
                        <a target="_blank" href="<?= url('/admin/payments/proof?id=' . $pay['id']); ?>">Ver</a>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
                <td>
                    <form method="post" action="<?= url('/admin/payments/update'); ?>">
                        <?= csrf_field(); ?>
                        <input type="hidden" name="payment_id" value="<?= $pay['id']; ?>">
                        <select class="form-select form-select-sm" name="status">
                            <option value="review">review</option>
                            <option value="approved">approved</option>
                            <option value="rejected">rejected</option>
                        </select>
                        <button class="btn btn-sm btn-outline-primary mt-1">Actualizar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>