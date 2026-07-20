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
      <a href="<?= site_url('admin/dashboard') ?>"><i class="bi bi-grid-1x2"></i>Dashboard</a>
      <div class="admin-nav-label">Configuration</div>
      <a href="<?= site_url('admin/prefixes') ?>"><i class="bi bi-hash"></i>Préfixes</a>
      <a href="<?= site_url('admin/baremes') ?>"><i class="bi bi-table"></i>Types & barèmes</a>
      <div class="admin-nav-label">Rapports</div>
      <a class="active" href="<?= site_url('admin/gains') ?>"><i class="bi bi-graph-up-arrow"></i>Situation gains</a>
      <a href="<?= site_url('admin/comptes') ?>"><i class="bi bi-people"></i>Comptes clients</a>
    </nav>
    <div class="admin-sidebar-footer"><a href="<?= site_url('logout') ?>"><i class="bi bi-box-arrow-left me-2"></i>Déconnexion</a></div>
  </aside>

  <div class="admin-main">
    <header class="admin-topbar">
      <div class="admin-page-title">Situation des gains</div>
      <div class="admin-user"><span>Opérateur</span><div class="admin-avatar">OP</div></div>
    </header>

    <main class="admin-content">
      <?php
        $nomsMois = [1 => 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
        $total = (float) $gains['total'];
        $pctRetrait = $total > 0 ? round(((float) $gains['retrait'] / $total) * 100) : 0;
        $pctTransfert = $total > 0 ? 100 - $pctRetrait : 0;
      ?>

      <section class="mp-card mb-4">
        <div class="mp-card-body">
          <form class="row g-3 align-items-end" method="get" action="<?= site_url('admin/gains') ?>">
            <div class="col-sm-5 col-md-4">
              <label class="form-label">Mois</label>
              <select class="form-select" name="mois">
                <?php foreach ($nomsMois as $numero => $nom): ?>
                  <option value="<?= $numero ?>" <?= $numero === $mois ? 'selected' : '' ?>><?= esc($nom) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-sm-4 col-md-3">
              <label class="form-label">Année</label>
              <input class="form-control" type="number" name="annee" min="2000" max="2100" value="<?= esc((string) $annee) ?>">
            </div>
            <div class="col-sm-3 col-md-2">
              <button class="btn btn-primary w-100" type="submit"><i class="bi bi-funnel me-1"></i>Afficher</button>
            </div>
          </form>
        </div>
      </section>

      <div class="row g-3 mb-4">
        <div class="col-md-4"><section class="mp-card mp-card-body stat-warning h-100"><div class="stat-label">Gains — Retraits</div><div class="stat-value"><?= number_format((float) $gains['retrait'], 0, ',', ' ') ?> Ar</div><div class="stat-sub"><?= esc($nomsMois[$mois]) ?> <?= esc((string) $annee) ?></div></section></div>
        <div class="col-md-4"><section class="mp-card mp-card-body h-100"><div class="stat-label">Gains — Transferts</div><div class="stat-value text-primary"><?= number_format((float) $gains['transfert'], 0, ',', ' ') ?> Ar</div><div class="stat-sub">Période sélectionnée</div></section></div>
        <div class="col-md-4"><section class="mp-card mp-card-body stat-success h-100"><div class="stat-label">Total gains du mois</div><div class="stat-value"><?= number_format($total, 0, ',', ' ') ?> Ar</div><div class="stat-sub">Retrait + transfert</div></section></div>
      </div>

      <section class="mp-card mb-4">
        <div class="mp-card-body">
          <div class="mp-section-title mb-3">Répartition des gains</div>
          <?php if ($total <= 0): ?>
            <div class="text-muted">Aucun frais encaissé pour cette période.</div>
          <?php else: ?>
            <div class="progress" style="height:32px">
              <div class="progress-bar bg-warning text-dark fw-semibold" style="width:<?= $pctRetrait ?>%">Retraits <?= $pctRetrait ?>%</div>
              <div class="progress-bar" style="width:<?= $pctTransfert ?>%">Transferts <?= $pctTransfert ?>%</div>
            </div>
          <?php endif; ?>
        </div>
      </section>

      <section class="mp-card">
        <div class="mp-card-body pb-2"><div class="mp-section-title">Récapitulatif annuel <?= esc((string) $annee) ?></div></div>
        <div class="table-responsive">
          <table class="table table-mp align-middle mb-0">
            <thead><tr><th>Mois</th><th class="text-end">Gains retraits</th><th class="text-end">Gains transferts</th><th class="text-end">Total</th></tr></thead>
            <tbody>
            <?php foreach ($gainsAnnuels as $numeroMois => $ligne): ?>
              <tr class="<?= $numeroMois === $mois ? 'table-primary' : '' ?>">
                <td><?= esc($nomsMois[$numeroMois]) ?></td>
                <td class="text-end"><?= number_format((float) $ligne['retrait'], 0, ',', ' ') ?> Ar</td>
                <td class="text-end"><?= number_format((float) $ligne['transfert'], 0, ',', ' ') ?> Ar</td>
                <td class="text-end fw-semibold"><?= number_format((float) $ligne['total'], 0, ',', ' ') ?> Ar</td>
              </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
              <tr>
                <th>Total annuel</th>
                <th class="text-end"><?= number_format(array_sum(array_column($gainsAnnuels, 'retrait')), 0, ',', ' ') ?> Ar</th>
                <th class="text-end"><?= number_format(array_sum(array_column($gainsAnnuels, 'transfert')), 0, ',', ' ') ?> Ar</th>
                <th class="text-end"><?= number_format(array_sum(array_column($gainsAnnuels, 'total')), 0, ',', ' ') ?> Ar</th>
              </tr>
            </tfoot>
          </table>
        </div>
      </section>
    </main>
  </div>
</div>
</body>
</html>
