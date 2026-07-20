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
<body>
<div class="admin-layout">
  <aside class="admin-sidebar">
    <div class="admin-logo"><strong><i class="bi bi-phone me-1"></i>MobiPay</strong><small>Espace Opérateur</small></div>
    <nav class="admin-nav">
      <div class="admin-nav-label">Tableau de bord</div>
      <a class="active" href="<?= site_url('admin/dashboard') ?>"><i class="bi bi-grid-1x2"></i>Dashboard</a>
      <div class="admin-nav-label">Configuration</div>
      <a href="<?= site_url('admin/prefixes') ?>"><i class="bi bi-hash"></i>Préfixes</a>
      <a class="active" href="<?= site_url('admin/baremes') ?>"><i class="bi bi-table"></i>Types & barèmes</a>
      <div class="admin-nav-label">Rapports</div>
      <a href="#"><i class="bi bi-graph-up-arrow"></i>Situation gains</a>
      <a href="#"><i class="bi bi-people"></i>Comptes clients</a>
    </nav>
    <div class="admin-sidebar-footer"><a href="<?= site_url('logout') ?>"><i class="bi bi-box-arrow-left me-2"></i>Déconnexion</a></div>
  </aside>
  <div class="admin-main">
    <header class="admin-topbar">
      <div class="admin-page-title">Tableau de bord opérateur</div>
      <div class="admin-user"><span>Opérateur</span><div class="admin-avatar">OP</div></div>
    </header>
    <main class="admin-content">
      <div class="alert alert-success">Connexion opérateur réussie.</div>
      <div class="row g-3">
        <div class="col-md-4"><section class="mp-card mp-card-body"><div class="stat-label">Préfixes actifs</div><div class="stat-value">2</div><div class="stat-sub">033 et 037</div></section></div>
        <div class="col-md-4"><section class="mp-card mp-card-body"><div class="stat-label">Types d’opérations</div><div class="stat-value">3</div><div class="stat-sub">Dépôt, retrait, transfert</div></section></div>
        <div class="col-md-4"><section class="mp-card mp-card-body"><div class="stat-label">Statut</div><div class="stat-value text-success">Connecté</div><div class="stat-sub">Session opérateur active</div></section></div>
      </div>
    </main>
  </div>
</div>
</body>
</html>
