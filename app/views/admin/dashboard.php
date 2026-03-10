<h3>Dashboard Admin</h3>
<div class="row g-3">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="fw-bold">Pedidos</div>
                <div class="display-6"><?= count($orders); ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="fw-bold">Pagos</div>
                <div class="display-6"><?= count($payments); ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="fw-bold">Clientes</div>
                <div class="display-6"><?= count($clients); ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="fw-bold">Repartidores</div>
                <div class="display-6"><?= count($riders); ?></div>
            </div>
        </div>
    </div>
</div>
<div class="mt-4">
    <a class="btn btn-outline-primary" href="<?= url('/admin/orders'); ?>">Ver pedidos</a>
    <a class="btn btn-outline-primary" href="<?= url('/admin/products'); ?>">Productos</a>
    <a class="btn btn-outline-primary" href="<?= url('/admin/payments'); ?>">Pagos</a>
</div>
