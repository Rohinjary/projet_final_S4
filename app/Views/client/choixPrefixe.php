<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Nouveau compte | MobiPay</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
</head>
<body data-page="login">

<main class="login-page">
<section class="login-card">
<div class="text-center mb-4">
<div class="brand-mark"><i class="bi bi-person-plus"></i></div>
<div class="brand-name">Creation de compte</div>
<div class="brand-subtitle">Numero <?= esc($numero_saisi) ?> introuvable</div>
</div>

<?php if (session()->getFlashdata('erreur')) : ?>
<div class="alert alert-danger"><?= esc(session()->getFlashdata('erreur')) ?></div>
<?php endif; ?>

<form action="<?= base_url('client/valider-nouveau-numero') ?>" method="post">
<div class="mb-3">
<label class="form-label" for="prefixe">Prefixe</label>
<select name="prefixe" id="prefixe" class="form-select" required>
<?php foreach ($prefixes as $p) : ?>
<option value="<?= esc($p['prefixe']) ?>"><?= esc($p['prefixe']) ?></option>
<?php endforeach; ?>
</select>
</div>
<div class="mb-3">
<label class="form-label" for="suite">Reste du numero (7 chiffres)</label>
<input class="form-control" id="suite" name="suite" type="tel" inputmode="numeric" maxlength="7" required>
</div>
<button class="btn btn-mp w-100" type="submit">
<i class="bi bi-check-circle me-2"></i>Creer mon compte
</button>
</form>

</section>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>