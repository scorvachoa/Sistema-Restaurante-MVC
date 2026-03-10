<div class="row">
    <div class="col-md-5">
        <img class="img-fluid rounded" src="<?= htmlspecialchars(product_image_url($product['image'])); ?>" alt="">
    </div>
    <div class="col-md-7">
        <h3><?= htmlspecialchars($product['name']); ?></h3>
        <p><?= htmlspecialchars($product['description']); ?></p>
        <p class="fw-bold">S/ <?= number_format($product['price'], 2); ?></p>
        <form method="post" action="<?= url('/cart/add'); ?>">
            <?= csrf_field(); ?>
            <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
            <div class="mb-3">
                <label class="form-label">Cantidad</label>
                <input class="form-control" type="number" name="quantity" value="1" min="1">
            </div>
            <?php if ($extras): ?>
                <div class="mb-3">
                    <label class="form-label">Extras</label>
                    <?php foreach ($extras as $extra): ?>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="extras[]" value="<?= $extra['id']; ?>">
                            <label class="form-check-label">
                                <?= htmlspecialchars($extra['name']); ?> (S/ <?= number_format($extra['price'], 2); ?>)
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <button class="btn btn-primary">Agregar al carrito</button>
        </form>
    </div>
</div>