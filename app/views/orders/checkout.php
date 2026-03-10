<h3>Checkout</h3>
<?php
$subtotal = 0.0;
foreach ($cart as $item) {
    $extrasTotal = array_sum(array_column($item['extras'], 'price'));
    $lineTotal = ($item['unit_price'] + $extrasTotal) * $item['quantity'];
    $subtotal += $lineTotal;
}
?>
<form method="post" action="<?= url('/order/create'); ?>" enctype="multipart/form-data">
    <?= csrf_field(); ?>
    <div class="mb-3">
        <label class="form-label">Dirección guardada</label>
        <select class="form-select" name="address_id">
            <option value="0">Nueva dirección</option>
            <?php foreach ($addresses as $addr): ?>
                <option value="<?= $addr['id']; ?>"><?= htmlspecialchars($addr['address']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Dirección manual</label>
        <input class="form-control" name="address" placeholder="Av. Principal 123">
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Latitud</label>
            <input class="form-control" name="lat" placeholder="-12.0464">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Longitud</label>
            <input class="form-control" name="lng" placeholder="-77.0428">
        </div>
    </div>
    <div class="mb-3">
        <div class="form-text">El costo de delivery se calcula automáticamente en el servidor.</div>
    </div>
    <div class="mb-3">
        <label class="form-label">Método de pago</label>
        <select class="form-select" name="payment_method" id="payment_method">
            <option value="cash">Contra entrega</option>
            <option value="yape">Yape</option>
            <option value="plin">Plin</option>
            <option value="bank_transfer">Transferencia bancaria</option>
        </select>
    </div>
    <div class="mb-3" id="proof_group" style="display:none;">
        <label class="form-label">Subir captura de pago</label>
        <input class="form-control" type="file" name="payment_proof" accept="image/*">
    </div>
    <div class="mb-3">
        <label class="form-label">Notas</label>
        <textarea class="form-control" name="notes" rows="2"></textarea>
    </div>
    <div class="alert alert-light">
        <div>Subtotal: S/ <?= number_format($subtotal, 2); ?></div>
        <div class="small text-muted">Delivery calculado al confirmar.</div>
    </div>
    <button class="btn btn-primary">Confirmar pedido</button>
</form>
<script>
    document.getElementById('payment_method').addEventListener('change', function () {
        const show = this.value !== 'cash';
        document.getElementById('proof_group').style.display = show ? 'block' : 'none';
    });
</script>