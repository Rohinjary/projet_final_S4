<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Accueil client | MobiPay</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
</head>
<body data-page="client-dashboard">

<main class="client-page">
<section class="client-shell">
<header class="client-header">
<div class="d-flex align-items-center justify-content-between gap-3">
<div>
<div class="client-brand"><i class="bi bi-phone me-1"></i>MobiPay</div>
<div class="client-phone"><?= esc($numero) ?></div>
</div>
<a class="client-logout" href="<?= base_url('client/deconnexion') ?>"><i class="bi bi-box-arrow-right me-1"></i>Deconnexion</a>
</div>
</header>
<div class="client-content">
<div class="client-content-container">

<?php if (session()->getFlashdata('succes')) : ?>
<div class="alert alert-success"><?= esc(session()->getFlashdata('succes')) ?></div>
<?php endif; ?>

<section class="balance-hero mb-4">
<div class="balance-label">Solde disponible</div>
<div class="balance-value"><?= number_format($solde, 0, ',', ' ') ?> Ar</div>
<div class="balance-number">N° <?= esc($numero) ?></div>
</section>

<div class="stat-label mb-2">Operations</div>
<div class="row g-2 mb-4">
<div class="col-4"><a class="quick-action deposit" href="<?= base_url('client/depot') ?>"><i class="bi bi-arrow-down-circle"></i>Depot</a></div>
<div class="col-4"><a class="quick-action withdraw" href="<?= base_url('client/retrait') ?>"><i class="bi bi-arrow-up-circle"></i>Retrait</a></div>
<div class="col-4"><a class="quick-action transfer" href="<?= base_url('client/transfert') ?>"><i class="bi bi-arrow-left-right"></i>Transfert</a></div>
</div>

<div class="d-flex align-items-center justify-content-between mb-2">
<div class="stat-label">Dernieres operations</div>
<a class="btn btn-sm btn-outline-secondary rounded-pill px-3" href="<?= base_url('client/historique') ?>">Voir tout</a>
</div>
<div id="recent-transactions" class="d-grid gap-2">
<?php if (empty($historique)) : ?>
<p class="text-mp-muted small">Aucune operation pour le moment.</p>
<?php endif; ?>
<?php foreach ($historique as $op) : ?>
<?php $recu = $op['destinataire_numero'] === $numero; ?>
<div class="mp-card mp-card-body d-flex justify-content-between align-items-center">
<div>
<div class="fw-bold"><?= esc(ucfirst($op['type_libelle'])) ?><?= $recu ? ' (recu)' : '' ?></div>
<div class="small text-mp-muted"><?= esc($op['date_operation']) ?></div>
</div>
<div class="fw-bold <?= $recu ? 'text-success' : '' ?>">
<?= $recu ? '+' : '-' ?><?= number_format($op['montant'], 0, ',', ' ') ?> Ar
</div>
</div>
<?php endforeach; ?>
</div>

</div>
</div>
<nav class="client-nav" aria-label="Navigation client">
<a href="<?= base_url('client/accueil') ?>" class="active"><i class="bi bi-house-door"></i><span>Accueil</span></a>
<a href="<?= base_url('client/depot') ?>"><i class="bi bi-arrow-down-circle"></i><span>Depot</span></a>
<a href="<?= base_url('client/retrait') ?>"><i class="bi bi-arrow-up-circle"></i><span>Retrait</span></a>
<a href="<?= base_url('client/transfert') ?>"><i class="bi bi-arrow-left-right"></i><span>Transfert</span></a>
<a href="<?= base_url('client/historique') ?>"><i class="bi bi-clock-history"></i><span>Historique</span></a>
</nav>
</section>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>