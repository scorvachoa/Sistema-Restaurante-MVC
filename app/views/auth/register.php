<div class="row justify-content-center">
    <div class="col-md-6">
        <h3>Registro</h3>
        <form method="post" action="<?= url('/register'); ?>">
            <?= csrf_field(); ?>
            <div class="mb-3">
                <label class="form-label">Nombre</label>
                <input class="form-control" name="name" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input class="form-control" type="email" name="email" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <input class="form-control" type="password" name="password" required>
            </div>
            <button class="btn btn-primary">Crear cuenta</button>
        </form>
    </div>
</div>