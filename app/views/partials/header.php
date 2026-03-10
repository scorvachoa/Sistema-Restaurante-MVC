<?php $user = auth_user(); ?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= App::config('app', 'name'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= asset('css/app.css'); ?>" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="<?= url('/menu'); ?>">Restaurant</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav ms-auto">
                <?php if (!$user): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= url('/login'); ?>">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= url('/register'); ?>">Registro</a></li>
                <?php else: ?>
                    <?php if ($user['role'] === 'admin'): ?>
                        <li class="nav-item"><a class="nav-link" href="<?= url('/admin'); ?>">Dashboard</a></li>
                    <?php elseif ($user['role'] === 'rider'): ?>
                        <li class="nav-item"><a class="nav-link" href="<?= url('/rider'); ?>">Repartidor</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="<?= url('/cart'); ?>">Carrito</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= url('/orders'); ?>">Mis pedidos</a></li>
                    <?php endif; ?>
                    <li class="nav-item"><a class="nav-link" href="<?= url('/logout'); ?>">Salir</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<div class="container mt-4">
    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['flash_error']; ?></div>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>
</div>
<div class="container mt-3">
