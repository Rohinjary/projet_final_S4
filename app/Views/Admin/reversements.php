<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= esc($title) ?> | MobiPay</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
</head>
<body>
<div class="admin-layout">
  <?= view('Admin/partials/sidebar', ['activePage' => 'reversements']) ?>
  <div class="sidebar-backdrop" data-sidebar-toggle></div>
  <div class="admin-main">
    <?= view('Admin/partials/topbar', ['pageTitle' => 'Montants à envoyer à chaque opérateur']) ?>
    <main class="admin-content">
      <?php $nomsMois = [1=>'Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre']; ?>
      <section class="mp-card mb-4"><div class="mp-card-body">
        <form class="row g-3 align-items-end" method="get" action="<?= site_url('admin/reversements') ?>">
          <div class="col-sm-5 col-md-4"><label class="form-label">Mois</label><select class="form-select" name="mois"><?php foreach ($nomsMois as $numero=>$nom): ?><option value="<?= $numero ?>" <?= $numero === $mois ? 'selected' : '' ?>><?= esc($nom) ?></option><?php endforeach; ?></select></div>
          <div class="col-sm-4 col-md-3"><label class="form-label">Année</label><input class="form-control" type="number" name="annee" min="2000" max="2100" value="<?= esc((string) $annee) ?>"></div>
          <div class="col-sm-3 col-md-2"><button class="btn btn-primary w-100" type="submit"><i class="bi bi-funnel me-1"></i>Afficher</button></div>
        </form>
      </div></section>

      <div class="row g-3 mb-4">
        <div class="col-md-6"><section class="mp-card mp-card-body stat-warning h-100"><div class="stat-label">Total à envoyer</div><div class="stat-value"><?= number_format($totalAEnvoyer, 0, ',', ' ') ?> Ar</div><div class="stat-sub"><?= esc($nomsMois[$mois]) ?> <?= esc((string) $annee) ?></div></section></div>
        <div class="col-md-6"><section class="mp-card mp-card-body h-100"><div class="stat-label">Opérateurs bénéficiaires</div><div class="stat-value"><?= count(array_filter($reversements, static fn($r) => (float) $r['montant_a_envoyer'] > 0)) ?></div><div class="stat-sub">Partenaires ayant un montant positif</div></section></div>
      </div>

      <section class="mp-card">
        <div class="mp-card-body pb-2"><div class="mp-section-title">Détail des reversements</div><div class="mp-section-subtitle mt-1">Montant à envoyer = montant transféré vers le partenaire + commission calculée sur le montant transféré. Les frais MobiPay restent séparés et ne sont pas déduits.</div></div>
        <div class="table-responsive"><table class="table table-mp align-middle mb-0">
          <thead><tr><th>Opérateur</th><th>Préfixes</th><th class="text-end">Taux</th><th class="text-end">Opérations</th><th class="text-end">Montant transféré</th><th class="text-end">Commission partenaire</th><th class="text-end">Montant à envoyer</th></tr></thead>
          <tbody>
          <?php if (empty($reversements)): ?><tr><td colspan="7" class="text-center text-muted py-5">Aucun opérateur partenaire configuré ou aucune opération sur la période.</td></tr>
          <?php else: foreach ($reversements as $ligne): ?>
            <tr>
              <td class="fw-semibold"><?= esc($ligne['nom']) ?></td>
              <td><?= esc($ligne['prefixes'] ? implode(', ', $ligne['prefixes']) : '—') ?></td>
              <td class="text-end"><?= number_format((float) $ligne['pourcentage'], 2, ',', ' ') ?> %</td>
              <td class="text-end"><?= (int) $ligne['nombre_operations'] ?></td>
              <td class="text-end"><?= number_format((float) $ligne['montant_transfere'], 0, ',', ' ') ?> Ar</td>
              <td class="text-end text-primary"><?= number_format((float) $ligne['commission_operateur'], 0, ',', ' ') ?> Ar</td>
              <td class="text-end fw-bold text-warning fs-6"><?= number_format((float) $ligne['montant_a_envoyer'], 0, ',', ' ') ?> Ar</td>
            </tr>
          <?php endforeach; endif; ?>
          </tbody>
          <tfoot><tr><th colspan="6">Total à envoyer</th><th class="text-end fs-6"><?= number_format($totalAEnvoyer, 0, ',', ' ') ?> Ar</th></tr></tfoot>
        </table></div>
      </section>
    </main>
  </div>
</div>
<script src="<?= base_url('assets/js/app.js') ?>"></script>
</body>
</html>
