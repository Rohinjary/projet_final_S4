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
      <a href="<?= site_url('admin/baremes') ?>"><i class="bi bi-table"></i>Types & barèmes</a>
      <div class="admin-nav-label">Rapports</div>
      <a href="<?= site_url('admin/gains') ?>"><i class="bi bi-graph-up-arrow"></i>Situation gains</a>
      <a href="<?= site_url('admin/comptes') ?>"><i class="bi bi-people"></i>Comptes clients</a>
    </nav>
    <div class="admin-sidebar-footer"><a href="<?= site_url('logout') ?>"><i class="bi bi-box-arrow-left me-2"></i>Déconnexion</a></div>
  </aside>

  <div class="admin-main">
    <header class="admin-topbar">
      <div class="admin-page-title">Tableau de bord opérateur</div>
      <div class="admin-user"><span>Opérateur</span><div class="admin-avatar">OP</div></div>
    </header>

    <main class="admin-content">
      <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
      <?php endif; ?>

      <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
          <section class="mp-card mp-card-body h-100">
            <div class="stat-label">Préfixes actifs</div>
            <div class="stat-value"><?= count($prefixes ?? []) ?></div>
            <div class="stat-sub">
              <?php if (! empty($prefixes)): ?>
                <?= esc(implode(', ', array_column($prefixes, 'prefixe'))) ?>
              <?php else: ?>
                Aucun préfixe configuré
              <?php endif; ?>
            </div>
          </section>
        </div>

        <div class="col-sm-6 col-xl-3">
          <section class="mp-card mp-card-body h-100">
            <div class="stat-label">Types d’opérations</div>
            <div class="stat-value"><?= count($types ?? []) ?></div>
            <div class="stat-sub">
              <?php if (! empty($types)): ?>
                <?= esc(implode(', ', array_map(static fn ($type) => ucfirst($type['libelle']), $types))) ?>
              <?php else: ?>
                Aucun type configuré
              <?php endif; ?>
            </div>
          </section>
        </div>

        <div class="col-sm-6 col-xl-3">
          <section class="mp-card mp-card-body h-100">
            <div class="stat-label">Comptes clients</div>
            <div class="stat-value"><?= (int) ($clientSummary['total'] ?? 0) ?></div>
            <div class="stat-sub"><?= (int) ($clientSummary['actifs'] ?? 0) ?> actif(s)</div>
          </section>
        </div>

        <div class="col-sm-6 col-xl-3">
          <section class="mp-card mp-card-body h-100">
            <div class="stat-label">Gains du mois</div>
            <div class="stat-value"><?= number_format((float) ($currentGains['total'] ?? 0), 0, ',', ' ') ?> Ar</div>
            <div class="stat-sub">Retrait + transfert</div>
          </section>
        </div>
      </div>

      
    </main>
  </div>
</div>
</body>
</html>
