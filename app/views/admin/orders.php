<h3>Pedidos</h3>
<form class="row g-2 mb-3" method="get" action="<?= url('/admin/orders'); ?>">
    <div class="col-md-3">
        <select class="form-select" name="status">
            <option value="">Todos</option>
            <?php foreach (['pending','confirmed','assigned','picked_up','on_the_way','delivered','cancelled'] as $st): ?>
                <option value="<?= $st; ?>" <?= ($filters['status'] ?? '') === $st ? 'selected' : ''; ?>><?= status_label($st); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-4">
        <input class="form-control" name="q" placeholder="ID o cliente" value="<?= htmlspecialchars($filters['q'] ?? ''); ?>">
    </div>
    <div class="col-md-2">
        <button class="btn btn-outline-primary">Filtrar</button>
    </div>
</form>
<audio id="orderSound" src="<?= asset('js/notify.mp3'); ?>" preload="auto"></audio>
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Cliente</th>
            <th>Estado</th>
            <th>Total</th>
            <th>Acciones</th>
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
                    <form class="d-inline" method="post" action="<?= url('/admin/orders/confirm'); ?>">
                        <?= csrf_field(); ?>
                        <input type="hidden" name="order_id" value="<?= $order['id']; ?>">
                        <button class="btn btn-sm btn-outline-success">Confirmar</button>
                    </form>
                    <form class="d-inline" method="post" action="<?= url('/admin/orders/assign'); ?>">
                        <?= csrf_field(); ?>
                        <input type="hidden" name="order_id" value="<?= $order['id']; ?>">
                        <select class="form-select form-select-sm d-inline" name="rider_id" style="width:160px; display:inline-block;">
                            <?php foreach ($riders as $rider): ?>
                                <option value="<?= $rider['id']; ?>"><?= htmlspecialchars($rider['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button class="btn btn-sm btn-outline-primary">Asignar</button>
                    </form>
                    <a class="btn btn-sm btn-outline-secondary" href="<?= url('/order?id=' . $order['id']); ?>">Ver</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>