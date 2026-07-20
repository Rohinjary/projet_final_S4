<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Connexion client | MobiPay</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
</head>
<body data-page="login">
<main class="login-page">
<section class="login-card" aria-labelledby="login-title">
<div class="text-center mb-4">
<div class="brand-mark"><i class="bi bi-phone"></i></div>
<div class="brand-name" id="login-title">MobiPay</div>
<div class="brand-subtitle">Connexion client Mobile Money</div>
</div>

<?php
$prefixeSaisi = session()->getFlashdata('prefixe_saisi');
$suiteSaisie = session()->getFlashdata('suite_saisie');
$prefixes = $prefixes ?? [];
?>
<?php if (session()->getFlashdata('erreur')): ?>
<div class="alert alert-danger"><i class="bi bi-exclamation-circle me-1"></i><?= esc(session()->getFlashdata('erreur')) ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('success')): ?>
<div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
<?php endif; ?>

<?php if ($prefixes === []): ?>
<div class="alert alert-warning">Aucun préfixe n’est encore configuré. Contactez l’opérateur.</div>
<?php else: ?>
<form action="<?= site_url('client/login') ?>" method="post">
<?= csrf_field() ?>
<div class="mb-3">
<label class="form-label" for="prefixe">Préfixe et opérateur</label>
<select class="form-select" id="prefixe" name="prefixe" required autofocus>
<option value="" disabled <?= $prefixeSaisi ? '' : 'selected' ?>>Choisir un préfixe</option>
<?php foreach ($prefixes as $prefixe): ?>
<option value="<?= esc($prefixe['prefixe']) ?>" <?= $prefixeSaisi === $prefixe['prefixe'] ? 'selected' : '' ?>>
<?= esc($prefixe['prefixe']) ?><?= ! empty($prefixe['operateur_nom']) ? ' — ' . esc($prefixe['operateur_nom']) : '' ?>
</option>
<?php endforeach; ?>
</select>
</div>
<div class="mb-3">
<label class="form-label" for="suite">Reste du numéro</label>
<div class="input-group">
<span class="input-group-text">7 chiffres</span>
<input class="form-control" id="suite" name="suite" type="tel" inputmode="numeric" pattern="[0-9]{7}" placeholder="1234567" maxlength="7" required value="<?= esc((string) $suiteSaisie) ?>">
</div>
<div class="form-text">Un compte est créé automatiquement lors de la première connexion.</div>
</div>
<button class="btn btn-mp w-100" type="submit"><i class="bi bi-box-arrow-in-right me-2"></i>Accéder à mon compte</button>
</form>
<?php endif; ?>

<div class="text-center border-top mt-4 pt-3">
<a href="<?= site_url('admin/login') ?>" class="btn btn-link btn-sm text-decoration-none text-mp-muted"><i class="bi bi-shield-lock me-1"></i>Espace opérateur</a>
</div>
</section>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
