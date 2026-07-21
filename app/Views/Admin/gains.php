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
  <?= view('Admin/partials/sidebar', ['activePage' => 'gains']) ?>
  <div class="sidebar-backdrop" data-sidebar-toggle></div>
  <div class="admin-main">
    <?= view('Admin/partials/topbar', ['pageTitle' => 'Situation des gains via les différents frais']) ?>
    <main class="admin-content">
      <?php $nomsMois = [1=>'Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre']; ?>

      <section class="mp-card mb-4"><div class="mp-card-body">
        <form class="row g-3 align-items-end" method="get" action="<?= site_url('admin/gains') ?>">
          <div class="col-sm-5 col-md-4"><label class="form-label">Mois</label><select class="form-select" name="mois">
            <?php foreach ($nomsMois as $numero => $nom): ?><option value="<?= $numero ?>" <?= $numero === $mois ? 'selected' : '' ?>><?= esc($nom) ?></option><?php endforeach; ?>
          </select></div>
          <div class="col-sm-4 col-md-3"><label class="form-label">Année</label><input class="form-control" type="number" name="annee" min="2000" max="2100" value="<?= esc((string) $annee) ?>"></div>
          <div class="col-sm-3 col-md-2"><button class="btn btn-primary w-100" type="submit"><i class="bi bi-funnel me-1"></i>Afficher</button></div>
        </form>
      </div></section>

      <div class="row g-3 mb-4">
        <div class="col-md-3"><section class="mp-card mp-card-body h-100"><div class="stat-label">Frais bruts collectés</div><div class="stat-value"><?= number_format((float) $gains['total'], 0, ',', ' ') ?> Ar</div><div class="stat-sub"><?= (int) $gains['operations_avec_frais'] ?> opération(s) avec frais</div></section></div>
        <div class="col-md-3"><section class="mp-card mp-card-body stat-success h-100"><div class="stat-label">Mes gains propres</div><div class="stat-value"><?= number_format((float) $gains['gain_prefixes_principaux'], 0, ',', ' ') ?> Ar</div><div class="stat-sub">Frais de mes propres préfixes</div></section></div>
        <div class="col-md-3"><section class="mp-card mp-card-body stat-success h-100"><div class="stat-label">Gains sur partenaires</div><div class="stat-value"><?= number_format((float) $gains['gains_operations_partenaires'], 0, ',', ' ') ?> Ar</div><div class="stat-sub">Frais de transfert conservés intégralement</div></section></div>
        <div class="col-md-3"><section class="mp-card mp-card-body stat-warning h-100"><div class="stat-label">À envoyer aux partenaires</div><div class="stat-value"><?= number_format((float) $gains['a_reverser'], 0, ',', ' ') ?> Ar</div><div class="stat-sub"><a href="<?= site_url('admin/reversements?mois=' . $mois . '&annee=' . $annee) ?>">Voir le détail</a></div></section></div>
      </div>

      <div class="alert alert-success d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span><strong>Total de mes gains :</strong> total des frais de transfert encaissés, sans déduction des commissions partenaires</span>
        <span class="fs-5 fw-bold"><?= number_format((float) $gains['mes_gains'], 0, ',', ' ') ?> Ar</span>
      </div>

      <section class="mp-card mb-4">
        <div class="mp-card-body pb-2"><div class="mp-section-title">Répartition par opérateur</div><div class="mp-section-subtitle mt-1">Les frais de transfert constituent le gain brut MobiPay. La commission partenaire, calculée sur le montant transféré, est comptabilisée séparément et ajoutée au reversement.</div></div>
        <div class="table-responsive">
          <table class="table table-mp align-middle mb-0">
            <thead><tr><th>Opérateur</th><th>Préfixes</th><th class="text-end">Taux</th><th class="text-end">Opérations</th><th class="text-end">Frais bruts</th><th class="text-end">Montant transféré</th><th class="text-end">Commission partenaire</th><th class="text-end">Gain brut MobiPay</th><th class="text-end">À envoyer</th></tr></thead>
            <tbody>
            <?php if (empty($gains['par_operateur'])): ?>
              <tr><td colspan="9" class="text-center text-muted py-5">Aucun frais encaissé pour cette période.</td></tr>
            <?php else: foreach ($gains['par_operateur'] as $ligne): ?>
              <tr>
                <td><span class="fw-semibold"><?= esc($ligne['nom']) ?></span> <span class="badge <?= $ligne['est_principal'] ? 'badge-soft-success' : 'badge-soft-primary' ?>"><?= $ligne['est_principal'] ? 'Principal' : 'Partenaire' ?></span></td>
                <td><?= esc($ligne['prefixes'] ? implode(', ', $ligne['prefixes']) : '—') ?></td>
                <td class="text-end"><?= $ligne['est_principal'] ? '—' : number_format((float) $ligne['pourcentage'], 2, ',', ' ') . ' %' ?></td>
                <td class="text-end"><?= (int) $ligne['nombre_operations'] ?></td>
                <td class="text-end"><?= number_format((float) $ligne['frais_bruts'], 0, ',', ' ') ?> Ar</td>
                <td class="text-end"><?= number_format((float) $ligne['montant_transfere'], 0, ',', ' ') ?> Ar</td>
                <td class="text-end text-primary"><?= number_format((float) $ligne['commission_operateur'], 0, ',', ' ') ?> Ar</td>
                <td class="text-end text-success fw-semibold"><?= number_format((float) $ligne['gain_retenu'], 0, ',', ' ') ?> Ar</td>
                <td class="text-end <?= (float) $ligne['montant_a_envoyer'] > 0 ? 'text-warning fw-semibold' : 'text-muted' ?>"><?= number_format((float) $ligne['montant_a_envoyer'], 0, ',', ' ') ?> Ar</td>
              </tr>
            <?php endforeach; endif; ?>
            </tbody>
          </table>
        </div>
      </section>

      <section class="mp-card">
        <div class="mp-card-body pb-2"><div class="mp-section-title">Récapitulatif annuel <?= esc((string) $annee) ?></div></div>
        <div class="table-responsive"><table class="table table-mp mb-0">
          <thead><tr><th>Mois</th><th class="text-end">Frais bruts</th><th class="text-end">Mes gains</th><th class="text-end">À envoyer</th></tr></thead>
          <tbody><?php foreach ($gainsAnnuels as $numeroMois => $ligne): ?><tr class="<?= $numeroMois === $mois ? 'table-primary' : '' ?>"><td><?= esc($nomsMois[$numeroMois]) ?></td><td class="text-end"><?= number_format((float) $ligne['total'], 0, ',', ' ') ?> Ar</td><td class="text-end text-success"><?= number_format((float) $ligne['mes_gains'], 0, ',', ' ') ?> Ar</td><td class="text-end text-warning"><?= number_format((float) $ligne['a_reverser'], 0, ',', ' ') ?> Ar</td></tr><?php endforeach; ?></tbody>
          <tfoot><tr><th>Total annuel</th><th class="text-end"><?= number_format(array_sum(array_column($gainsAnnuels, 'total')), 0, ',', ' ') ?> Ar</th><th class="text-end"><?= number_format(array_sum(array_column($gainsAnnuels, 'mes_gains')), 0, ',', ' ') ?> Ar</th><th class="text-end"><?= number_format(array_sum(array_column($gainsAnnuels, 'a_reverser')), 0, ',', ' ') ?> Ar</th></tr></tfoot>
        </table></div>
      </section>
    </main>
  </div>
</div>
<script src="<?= base_url('assets/js/app.js') ?>"></script>
</body>
</html>
