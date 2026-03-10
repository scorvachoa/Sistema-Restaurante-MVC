<h3>Menú</h3>
<div class="row">
    <div class="col-md-3">
        <div class="list-group">
            <a class="list-group-item <?= $categoryId === 0 ? 'active' : ''; ?>" href="<?= url('/menu'); ?>">Todos</a>
            <?php foreach ($categories as $cat): ?>
                <a class="list-group-item <?= $categoryId === (int)$cat['id'] ? 'active' : ''; ?>" href="<?= url('/menu?category=' . $cat['id']); ?>">
                    <?= htmlspecialchars($cat['name']); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="col-md-9">
        <div class="row g-3">
            <?php foreach ($products as $product): ?>
                <div class="col-md-4">
                    <div class="card h-100">
                        <img src="<?= htmlspecialchars(product_image_url($product['image'])); ?>" class="card-img-top" alt="">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($product['name']); ?></h5>
                            <p class="card-text">S/ <?= number_format($product['price'], 2); ?></p>
                            <a class="btn btn-outline-primary" href="<?= url('/product?id=' . $product['id']); ?>">Ver</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>