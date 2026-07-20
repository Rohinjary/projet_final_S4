<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Vos informations | MobiPay</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
</head>
<body data-page="login">

<main class="login-page">
<section class="login-card">
<div class="text-center mb-4">
<div class="brand-mark"><i class="bi bi-person"></i></div>
<div class="brand-name">Vos informations</div>
<div class="brand-subtitle">Facultatif</div>
</div>

<form action="<?= base_url('client/enregistrer-info') ?>
<?= csrf_field() ?>" method="post">
<div class="mb-3">
<label class="form-label" for="nom">Nom</label>
<input class="form-control" id="nom" name="nom" type="text">
</div>
<div class="mb-3">
<label class="form-label" for="prenom">Prenom</label>
<input class="form-control" id="prenom" name="prenom" type="text">
</div>
<button class="btn btn-mp w-100 mb-2" type="submit">
<i class="bi bi-check-circle me-2"></i>Enregistrer
</button>
</form>
<a href="<?= base_url('client/passer-info') ?>" class="btn btn-link w-100">Passer cette etape</a>

</section>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>