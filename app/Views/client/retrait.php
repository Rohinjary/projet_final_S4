<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Retrait | MobiPay</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
</head>
<body data-page="client-retrait">

<main class="client-page">
<section class="client-shell">
<header class="client-header">
<div class="d-flex align-items-center justify-content-between gap-3">
<div>
<div class="client-brand"><i class="bi bi-phone me-1"></i>MobiPay</div>
</div>
<a class="client-logout" href="<?= base_url('client/deconnexion') ?>"><i class="bi bi-box-arrow-right me-1"></i>Deconnexion</a>
</div>
</header>
<div class="client-content">
<div class="client-content-container">

<div class="page-heading">
<h1><i class="bi bi-arrow-up-circle me-1"></i>Faire un retrait</h1>
<p>Les frais sont calcules automatiquement selon le bareme actif.</p>
</div>

<?php if (session()->getFlashdata('erreur')) : ?>
<div class="alert alert-danger"><?= esc(session()->getFlashdata('erreur')) ?></div>
<?php endif; ?>

<div class="mp-card mp-card-body mb-3">
<div class="stat-label">Solde disponible</div>
<div class="stat-value"><?= number_format($solde, 0, ',', ' ') ?> Ar</div>
</div>

<form action="<?= base_url('client/traiter-retrait') ?>
<?= csrf_field() ?>" method="post">
<div class="mb-3">
<label class="form-label" for="amount">Montant a retirer (Ar)</label>
<input class="form-control" id="amount" name="amount" type="number" min="100" max="2000000" step="100" placeholder="Ex. 20 000" required>
<div class="form-text">Le montant et les frais seront debites du solde.</div>
</div>
<button class="btn btn-danger w-100 fw-bold py-2" type="submit"><i class="bi bi-check-circle me-1"></i>Confirmer le retrait</button>
</form>

</div>
</div>
<nav class="client-nav" aria-label="Navigation client">
<a href="<?= base_url('client/accueil') ?>"><i class="bi bi-house-door"></i><span>Accueil</span></a>
<a href="<?= base_url('client/depot') ?>"><i class="bi bi-arrow-down-circle"></i><span>Depot</span></a>
<a href="<?= base_url('client/retrait') ?>" class="active"><i class="bi bi-arrow-up-circle"></i><span>Retrait</span></a>
<a href="<?= base_url('client/transfert') ?>"><i class="bi bi-arrow-left-right"></i><span>Transfert</span></a>
<a href="<?= base_url('client/historique') ?>"><i class="bi bi-clock-history"></i><span>Historique</span></a>
</nav>
</section>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>