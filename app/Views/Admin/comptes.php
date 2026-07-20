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
  <?= view('Admin/partials/sidebar', ['activePage' => 'comptes']) ?>
  <div class="sidebar-backdrop" data-sidebar-toggle></div>
<div class="admin-main">
    <?= view('Admin/partials/topbar', ['pageTitle' => 'Situation des comptes clients']) ?>
<main class="admin-content">
      <div class="row g-3 mb-4">
        <div class="col-md-4"><section class="mp-card mp-card-body h-100"><div class="stat-label">Total clients</div><div class="stat-value"><?= (int) $resume['total'] ?></div><div class="stat-sub"><?= (int) $resume['actifs'] ?> actif(s)</div></section></div>
        <div class="col-md-4"><section class="mp-card mp-card-body stat-success h-100"><div class="stat-label">Total soldes</div><div class="stat-value"><?= number_format((float) $resume['solde_total'], 0, ',', ' ') ?> Ar</div><div class="stat-sub">Tous comptes confondus</div></section></div>
        <div class="col-md-4"><section class="mp-card mp-card-body h-100"><div class="stat-label">Comptes inactifs</div><div class="stat-value"><?= (int) $resume['inactifs'] ?></div><div class="stat-sub">Aucune opération enregistrée</div></section></div>
      </div>

      <section class="mp-card">
        <div class="mp-card-body">
          <form class="row g-2 align-items-end" method="get" action="<?= site_url('admin/comptes') ?>">
            <div class="col-md-5">
              <label class="form-label">Rechercher</label>
              <input class="form-control" name="recherche" value="<?= esc($recherche) ?>" placeholder="Numéro, nom, prénom ou opérateur">
            </div>
            <div class="col-md-3">
              <label class="form-label">Statut</label>
              <select class="form-select" name="statut">
                <option value="tous" <?= $statut === 'tous' ? 'selected' : '' ?>>Tous</option>
                <option value="actif" <?= $statut === 'actif' ? 'selected' : '' ?>>Actifs</option>
                <option value="inactif" <?= $statut === 'inactif' ? 'selected' : '' ?>>Inactifs</option>
              </select>
            </div>
            <div class="col-md-2"><button class="btn btn-primary w-100" type="submit"><i class="bi bi-search me-1"></i>Filtrer</button></div>
            <div class="col-md-2"><a class="btn btn-outline-secondary w-100" href="<?= site_url('admin/comptes') ?>">Réinitialiser</a></div>
          </form>
        </div>

        <div class="table-responsive">
          <table class="table table-mp align-middle mb-0">
            <thead><tr><th>#</th><th>Client</th><th>Numéro</th><th>Opérateur</th><th class="text-end">Solde</th><th class="text-end">Opérations</th><th>Dernière activité</th><th>Statut</th></tr></thead>
            <tbody>
            <?php if ($comptes === []): ?>
              <tr><td colspan="8" class="text-center py-5 text-muted">Aucun compte client trouvé.</td></tr>
            <?php else: ?>
              <?php foreach ($comptes as $index => $compte): ?>
                <tr>
                  <td><?= $index + 1 ?></td>
                  <td><?= esc($compte['nom'] !== '' ? $compte['nom'] : 'Client sans nom') ?></td>
                  <td class="fw-semibold"><?= esc($compte['numero']) ?></td>
                  <td><?= esc($compte['operateur']) ?></td>
                  <td class="text-end fw-semibold <?= (float) $compte['solde'] < 0 ? 'text-danger' : 'text-success' ?>"><?= number_format((float) $compte['solde'], 0, ',', ' ') ?> Ar</td>
                  <td class="text-end"><?= (int) $compte['nb_operations'] ?></td>
                  <td><?= $compte['derniere_activite'] ? esc(date('d/m/Y H:i', strtotime($compte['derniere_activite']))) : 'Aucune' ?></td>
                  <td><span class="badge <?= $compte['statut'] === 'actif' ? 'text-bg-success' : 'text-bg-secondary' ?>"><?= esc(ucfirst($compte['statut'])) ?></span></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
          </table>
        </div>
        <div class="mp-card-body border-top py-3"><span class="small text-muted"><?= count($comptes) ?> résultat(s)</span></div>
      </section>
    </main>
  </div>
</div>
<script src="<?= base_url('assets/js/app.js') ?>"></script>
</body>
</html>
