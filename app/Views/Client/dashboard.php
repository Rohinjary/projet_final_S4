<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= esc($title) ?> | MobiPay</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
</head>
<body class="client-page">
<div class="client-shell">
  <header class="client-header d-flex justify-content-between align-items-center">
    <div><div class="client-brand">MobiPay</div><div class="client-phone"><?= esc($numero) ?></div></div>
    <a class="client-logout" href="<?= site_url('logout') ?>"><i class="bi bi-box-arrow-right me-1"></i>Déconnexion</a>
  </header>
  <main class="client-content">
    <div class="alert alert-success">Connexion client réussie.</div>
    <section class="balance-hero mb-4">
      <div class="balance-label">Solde disponible</div>
      <div class="balance-value">0 Ar</div>
    </section>
    <div class="row g-3">
      <div class="col-4"><button class="btn btn-light border w-100 py-3"><i class="bi bi-arrow-down-circle d-block fs-4"></i>Dépôt</button></div>
      <div class="col-4"><button class="btn btn-light border w-100 py-3"><i class="bi bi-arrow-up-circle d-block fs-4"></i>Retrait</button></div>
      <div class="col-4"><button class="btn btn-light border w-100 py-3"><i class="bi bi-arrow-left-right d-block fs-4"></i>Transfert</button></div>
    </div>
  </main>
</div>
</body>
</html>
