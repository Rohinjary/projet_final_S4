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
  <?= view('Admin/partials/sidebar', ['activePage' => 'dashboard']) ?>
  <div class="sidebar-backdrop" data-sidebar-toggle></div>
  <div class="admin-main">
    <?= view('Admin/partials/topbar', ['pageTitle' => 'Tableau de bord opérateur']) ?>
    <main class="admin-content">
      <?php if (session()->getFlashdata('success')): ?><div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div><?php endif; ?>

      <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
          <section class="mp-card mp-card-body h-100">
            <div class="stat-label">Opérateurs configurés</div>
            <div class="stat-value"><?= count($operateurs ?? []) ?></div>
            <div class="stat-sub">Principal et partenaires</div>
          </section>
        </div>
        <div class="col-sm-6 col-xl-3">
          <section class="mp-card mp-card-body h-100">
            <div class="stat-label">Préfixes actifs</div>
            <div class="stat-value"><?= count($prefixes ?? []) ?></div>
            <div class="stat-sub"><?= ! empty($prefixes) ? esc(implode(', ', array_column($prefixes, 'prefixe'))) : 'Aucun préfixe configuré' ?></div>
          </section>
        </div>
        <div class="col-sm-6 col-xl-3">
          <section class="mp-card mp-card-body stat-success h-100">
            <div class="stat-label">Mes gains du mois</div>
            <div class="stat-value"><?= number_format((float) ($currentGains['mes_gains'] ?? 0), 0, ',', ' ') ?> Ar</div>
            <div class="stat-sub">Frais propres + gains nets partenaires</div>
          </section>
        </div>
        <div class="col-sm-6 col-xl-3">
          <section class="mp-card mp-card-body stat-warning h-100">
            <div class="stat-label">À envoyer ce mois</div>
            <div class="stat-value"><?= number_format((float) ($currentGains['a_reverser'] ?? 0), 0, ',', ' ') ?> Ar</div>
            <div class="stat-sub">Montant dû aux autres opérateurs</div>
          </section>
        </div>
      </div>

      <div class="row g-3">
        <div class="col-lg-7">
          <section class="mp-card h-100">
            <div class="mp-card-body pb-2">
              <div class="mp-section-title">Configuration des opérateurs</div>
              <div class="mp-section-subtitle mt-1">Vue d’ensemble des préfixes et commissions.</div>
            </div>
            <div class="table-responsive">
              <table class="table table-mp mb-0">
                <thead><tr><th>Opérateur</th><th>Type</th><th class="text-end">Préfixes</th><th class="text-end">Commission</th></tr></thead>
                <tbody>
                <?php if (empty($operateurs)): ?>
                  <tr><td colspan="4" class="text-center text-muted py-4">Aucun opérateur configuré.</td></tr>
                <?php else: foreach ($operateurs as $operateur): ?>
                  <tr>
                    <td class="fw-semibold"><?= esc($operateur['nom']) ?></td>
                    <td><span class="badge <?= (int) $operateur['est_principal'] === 1 ? 'badge-soft-success' : 'badge-soft-primary' ?>"><?= (int) $operateur['est_principal'] === 1 ? 'Principal' : 'Partenaire' ?></span></td>
                    <td class="text-end"><?= (int) $operateur['nombre_prefixes'] ?></td>
                    <td class="text-end"><?= (int) $operateur['est_principal'] === 1 ? '100,00 % des frais' : number_format((float) $operateur['pourcentage'], 2, ',', ' ') . ' % du montant' ?></td>
                  </tr>
                <?php endforeach; endif; ?>
                </tbody>
              </table>
            </div>
          </section>
        </div>
        <div class="col-lg-5">
          <section class="mp-card mp-card-body h-100">
            <div class="mp-section-title mb-3">Accès rapides</div>
            <div class="d-grid gap-2">
              <a class="btn btn-outline-primary text-start" href="<?= site_url('admin/operateurs') ?>"><i class="bi bi-building me-2"></i>Ajouter un opérateur</a>
              <a class="btn btn-outline-primary text-start" href="<?= site_url('admin/prefixes') ?>"><i class="bi bi-hash me-2"></i>Affecter un préfixe</a>
              <a class="btn btn-outline-primary text-start" href="<?= site_url('admin/commissions') ?>"><i class="bi bi-percent me-2"></i>Modifier les commissions</a>
              <a class="btn btn-outline-warning text-start" href="<?= site_url('admin/reversements') ?>"><i class="bi bi-send-check me-2"></i>Voir les montants à envoyer</a>
            </div>
          </section>
        </div>
      </div>
    </main>
  </div>
</div>
<script src="<?= base_url('assets/js/app.js') ?>"></script>
</body>
</html>
