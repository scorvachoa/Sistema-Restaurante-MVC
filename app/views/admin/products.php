<h3>Productos</h3>
<?php if ($editProduct): ?>
<form class="row g-2 mb-4" method="post" action="<?= url('/admin/products/update'); ?>" enctype="multipart/form-data">
    <?= csrf_field(); ?>
    <input type="hidden" name="id" value="<?= $editProduct['id']; ?>">
    <div class="col-md-3"><input class="form-control" name="name" value="<?= htmlspecialchars($editProduct['name']); ?>" required></div>
    <div class="col-md-2"><input class="form-control" name="price" value="<?= $editProduct['price']; ?>" required></div>
    <div class="col-md-3">
        <input class="form-control" type="file" name="image_file" accept="image/*" id="image_file">
        <div class="form-text">Si no subes una imagen, se mantiene la actual.</div>
    </div>
    <div class="col-md-2"><input class="form-control" name="stock" value="<?= $editProduct['stock']; ?>" required></div>
    <div class="col-md-2">
        <select class="form-select" name="category_id">
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id']; ?>" <?= (int)$cat['id'] === (int)$editProduct['category_id'] ? 'selected' : ''; ?>><?= htmlspecialchars($cat['name']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-12"><input class="form-control" name="description" value="<?= htmlspecialchars($editProduct['description'] ?? ''); ?>" placeholder="Descripción"></div>
    <div class="col-12">
        <img id="image_preview" src="<?= htmlspecialchars(product_image_url($editProduct['image'])); ?>" alt="Preview" style="max-width: 220px; display: block;">
    </div>
    <div class="col-12 d-flex gap-2">
        <button class="btn btn-primary">Guardar cambios</button>
        <a class="btn btn-outline-secondary" href="<?= url('/admin/products'); ?>">Cancelar</a>
    </div>
</form>
<?php else: ?>
<form class="row g-2 mb-4" method="post" action="<?= url('/admin/products/create'); ?>" enctype="multipart/form-data">
    <?= csrf_field(); ?>
    <div class="col-md-3"><input class="form-control" name="name" placeholder="Nombre" required></div>
    <div class="col-md-2"><input class="form-control" name="price" placeholder="Precio" required></div>
    <div class="col-md-3">
        <input class="form-control" type="file" name="image_file" accept="image/*" id="image_file" required>
    </div>
    <div class="col-md-2"><input class="form-control" name="stock" placeholder="Stock" required></div>
    <div class="col-md-2">
        <select class="form-select" name="category_id">
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id']; ?>"><?= htmlspecialchars($cat['name']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-12"><input class="form-control" name="description" placeholder="Descripción"></div>
    <div class="col-12">
        <img id="image_preview" src="" alt="Preview" style="max-width: 220px; display: none;">
    </div>
    <div class="col-12"><button class="btn btn-primary">Crear</button></div>
</form>
<?php endif; ?>
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Precio</th>
            <th>Stock</th>
            <th>Categoria</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($products as $product): ?>
            <tr>
                <td><?= $product['id']; ?></td>
                <td><?= htmlspecialchars($product['name']); ?></td>
                <td>S/ <?= number_format($product['price'], 2); ?></td>
                <td><?= $product['stock']; ?></td>
                <td><?= htmlspecialchars($product['category_name']); ?></td>
                <td class="d-flex gap-2">
                    <a class="btn btn-sm btn-outline-primary" href="<?= url('/admin/products?edit=' . $product['id']); ?>">Editar</a>
                    <form method="post" action="<?= url('/admin/products/delete'); ?>" onsubmit="return confirm('Eliminar?');">
                        <?= csrf_field(); ?>
                        <input type="hidden" name="id" value="<?= $product['id']; ?>">
                        <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<script>
    (function () {
        const input = document.getElementById('image_file');
        const preview = document.getElementById('image_preview');
        if (!input || !preview) return;

        input.addEventListener('change', function () {
            const file = this.files && this.files[0];
            if (!file) {
                if (preview.dataset.original) {
                    preview.src = preview.dataset.original;
                    preview.style.display = 'block';
                } else {
                    preview.style.display = 'none';
                }
                return;
            }
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        });

        if (preview.src) {
            preview.dataset.original = preview.src;
        }
    })();
</script>