<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Connexion | MobiPay</title>
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
<div class="brand-subtitle">Operateur Mobile Money</div>
</div>

<?php
$prefixeSaisi = session()->getFlashdata('prefixe_saisi');
$suiteSaisie  = session()->getFlashdata('suite_saisie');
$prefixes     = $prefixes ?? [];
?>

<?php if (session()->getFlashdata('erreur')) : ?>
<div class="alert alert-danger"><?= esc(session()->getFlashdata('erreur')) ?></div>
<?php endif; ?>

<form action="<?= base_url('client/valider-nouveau-numero') ?>" method="post">
<div class="mb-3">
<label class="form-label" for="prefixe">Prefixe</label>
<select class="form-select" id="prefixe" name="prefixe" required autofocus>
<option value="" disabled <?= $prefixeSaisi ? '' : 'selected' ?>>Choisir un prefixe</option>
<?php foreach ($prefixes as $prefixe) : ?>
<option value="<?= esc($prefixe['prefixe']) ?>" <?= $prefixeSaisi === $prefixe['prefixe'] ? 'selected' : '' ?>><?= esc($prefixe['prefixe']) ?></option>
<?php endforeach; ?>
</select>
</div>

<div class="mb-3">
<label class="form-label" for="suite">Reste du numero</label>
<div class="input-group">
<span class="input-group-text">7 chiffres</span>
<input class="form-control" id="suite" name="suite" type="tel" inputmode="numeric" placeholder="1234567" maxlength="7" autocomplete="tel-national" required value="<?= esc($suiteSaisie) ?>">
</div>
<div class="form-text">Le numero complet sera forme avec le prefixe choisi.</div>
</div>

<button class="btn btn-mp w-100" type="submit">
<i class="bi bi-box-arrow-in-right me-2"></i>Acceder ou creer mon compte
</button>
</form>

<div class="text-center border-top mt-4 pt-3">
<a href="<?= site_url('admin/login') ?>" class="btn btn-link btn-sm text-decoration-none text-mp-muted">
<i class="bi bi-shield-lock me-1"></i>Espace opérateur
</a>
</div>

</section>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>