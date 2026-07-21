<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Transfert | MobiPay</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
</head>
<body data-page="client-transfert">

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
<h1><i class="bi bi-arrow-left-right me-1"></i>Faire un transfert</h1>
<p>Transferez de l'argent vers un ou plusieurs numeros MobiPay du meme operateur.</p>
</div>

<?php if (session()->getFlashdata('erreur')) : ?>
<div class="alert alert-danger"><?= esc(session()->getFlashdata('erreur')) ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('succes')) : ?>
<div class="alert alert-success"><?= esc(session()->getFlashdata('succes')) ?></div>
<?php endif; ?>

<div class="mp-card mp-card-body mb-3">
<div class="stat-label">Solde disponible</div>
<div class="stat-value"><?= number_format($solde, 0, ',', ' ') ?> Ar</div>
</div>

<div class="alert alert-info small">
Pour un transfert vers un autre operateur, la commission partenaire est calculee sur le montant envoye.
Le debit total correspond au montant transfere, aux frais MobiPay et a cette commission.
</div>

<form action="<?= base_url('client/traiter-transfert') ?>" method="post">
<?= csrf_field() ?>
<div class="mb-3">
<label class="form-label" for="recipients">Numero(s) destinataire(s)</label>
<textarea class="form-control" id="recipients" name="recipients" rows="4" placeholder="0331234567, puis un autre numero par ligne ou avec des virgules" required><?= esc(old('recipients') ?? old('recipient') ?? '') ?></textarea>
<div class="form-text">Saisissez un ou plusieurs numeros du meme operateur. Un numero par ligne, espace, virgule ou point-virgule.</div>
</div>
<div class="mb-3">
<label class="form-label" for="amount">Montant a envoyer (Ar)</label>
<input class="form-control" id="amount" name="amount" type="number" min="100" max="2000000" step="100" placeholder="Ex. 10 000" value="<?= esc(old('amount')) ?>" required>
</div>
<div class="form-check mb-3">
<input class="form-check-input" type="checkbox" value="1" id="inclure_frais_retrait" name="inclure_frais_retrait" <?= old('inclure_frais_retrait') ? 'checked' : '' ?>>
<label class="form-check-label" for="inclure_frais_retrait">
Inclure les frais de retrait dans l envoi
</label>
<div class="form-text">Option disponible uniquement pour les numeros du meme operateur.</div>
</div>
<button class="btn btn-primary w-100 fw-bold py-2" type="submit"><i class="bi bi-send me-1"></i>Envoyer le transfert</button>
</form>

</div>
</div>
<nav class="client-nav" aria-label="Navigation client">
<a href="<?= base_url('client/accueil') ?>"><i class="bi bi-house-door"></i><span>Accueil</span></a>
<a href="<?= base_url('client/depot') ?>"><i class="bi bi-arrow-down-circle"></i><span>Depot</span></a>
<a href="<?= base_url('client/retrait') ?>"><i class="bi bi-arrow-up-circle"></i><span>Retrait</span></a>
<a href="<?= base_url('client/transfert') ?>" class="active"><i class="bi bi-arrow-left-right"></i><span>Transfert</span></a>
<a href="<?= base_url('client/historique') ?>"><i class="bi bi-clock-history"></i><span>Historique</span></a>
</nav>
</section>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>