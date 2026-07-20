<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Historique | MobiPay</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
</head>
<body data-page="client-historique">

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

<div class="page-heading mb-3">
<h1><i class="bi bi-clock-history me-1"></i>Historique des operations</h1>
<p>Consultez et filtrez vos mouvements recents.</p>
</div>

<div class="d-flex flex-wrap gap-2 mb-3">
<a class="filter-pill <?= !$typeActif ? 'active' : '' ?>" href="<?= base_url('client/historique') ?>">Tous</a>
<?php foreach ($types as $t) : ?>
<a class="filter-pill <?= $typeActif == $t['id'] ? 'active' : '' ?>" href="<?= base_url('client/historique') ?>?type=<?= $t['id'] ?>"><?= esc(ucfirst($t['libelle'])) ?>s</a>
<?php endforeach; ?>
</div>

<div id="history-list" class="d-grid gap-2">
<?php if (empty($historique)) : ?>
<p class="text-mp-muted small">Aucune operation trouvee.</p>
<?php endif; ?>
<?php foreach ($historique as $op) : ?>
<?php
$estDepot = $op['type_libelle'] === 'depot';
$estEntree = ! empty($op['est_entree']);
$estSortie = ! empty($op['est_sortie']);
$montantAffiche = (float) $op['montant'];
if ($op['type_libelle'] === 'transfert' && $estEntree) {
	$montantAffiche += (float) ($op['frais_retrait'] ?? 0);
}
$nbDestinataires = (int) ($op['nb_destinataires'] ?? 1);
$destinatairesAffiches = trim((string) ($op['destinataires_affiches'] ?? ''));
$dateOperation = $op['date_operation'];
if ($dateOperation) {
	$timestamp = strtotime($dateOperation);
	if ($timestamp !== false) {
		$dateOperation = date('d/m/Y H:i', $timestamp);
	}
}
?>
<div class="mp-card mp-card-body d-flex justify-content-between align-items-center">
<div>
<div class="fw-bold">
<?= esc(ucfirst($op['type_libelle'])) ?>
<?php if ($op['type_libelle'] === 'transfert' && $nbDestinataires > 1 && $estSortie) : ?>
(<?= $nbDestinataires ?> destinataires)
<?php elseif ($op['type_libelle'] === 'transfert' && $estEntree) : ?>
(recu)
<?php endif; ?>
</div>
<div class="small text-mp-muted"><?= esc($dateOperation) ?></div>
<?php if ($op['type_libelle'] === 'transfert') : ?>
<div class="small text-mp-muted">
<?php if ($estEntree) : ?>
De <?= esc($op['client_numero']) ?>
<?php elseif ($nbDestinataires > 1) : ?>
Vers <?= $nbDestinataires ?> destinataires
<?php else : ?>
Vers <?= esc($op['destinataire_numero']) ?>
<?php endif; ?>
</div>
<?php if ($destinatairesAffiches !== '' && $nbDestinataires > 1 && $estSortie) : ?>
<div class="small text-mp-muted"><?= esc($destinatairesAffiches) ?></div>
<?php endif; ?>
<?php endif; ?>
</div>
<div class="text-end">
<div class="fw-bold <?= $estDepot || $estEntree ? 'text-success' : '' ?>">
<?= $estDepot || $estEntree ? '+' : '-' ?><?= number_format($montantAffiche, 0, ',', ' ') ?> Ar
</div>
<?php if ((float) $op['frais'] > 0 && $estSortie) : ?>
<div class="small text-mp-muted">frais transfert : <?= number_format($op['frais'], 0, ',', ' ') ?> Ar</div>
<?php endif; ?>
<?php if ((float) ($op['frais_retrait'] ?? 0) > 0 && $estSortie) : ?>
<div class="small text-mp-muted">frais retrait : <?= number_format($op['frais_retrait'], 0, ',', ' ') ?> Ar</div>
<?php endif; ?>
</div>
</div>
<?php endforeach; ?>
</div>

</div>
</div>
<nav class="client-nav" aria-label="Navigation client">
<a href="<?= base_url('client/accueil') ?>"><i class="bi bi-house-door"></i><span>Accueil</span></a>
<a href="<?= base_url('client/depot') ?>"><i class="bi bi-arrow-down-circle"></i><span>Depot</span></a>
<a href="<?= base_url('client/retrait') ?>"><i class="bi bi-arrow-up-circle"></i><span>Retrait</span></a>
<a href="<?= base_url('client/transfert') ?>"><i class="bi bi-arrow-left-right"></i><span>Transfert</span></a>
<a href="<?= base_url('client/historique') ?>" class="active"><i class="bi bi-clock-history"></i><span>Historique</span></a>
</nav>
</section>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>