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
  <?= view('Admin/partials/sidebar', ['activePage' => 'operateurs']) ?>
  <div class="sidebar-backdrop" data-sidebar-toggle></div>
  <div class="admin-main">
    <?= view('Admin/partials/topbar', ['pageTitle' => 'Gestion des opérateurs']) ?>
    <main class="admin-content">
      <?php if (session()->getFlashdata('success')): ?><div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div><?php endif; ?>
      <?php if (session()->getFlashdata('error')): ?><div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div><?php endif; ?>

      <div class="row g-4">
        <div class="col-lg-4">
          <section class="mp-card mp-card-body">
            <div class="mp-section-title">Nouvel opérateur</div>
            <div class="mp-section-subtitle mt-1 mb-3">Ajoutez un opérateur partenaire, puis configurez ses préfixes et sa commission.</div>
            <form method="post" action="<?= site_url('admin/operateurs') ?>">
              <?= csrf_field() ?>
              <div class="mb-3">
                <label class="form-label" for="nom">Nom de l’opérateur</label>
                <input class="form-control" id="nom" name="nom" maxlength="100" value="<?= esc(old('nom')) ?>" placeholder="Ex. Telma" required>
              </div>
              <button class="btn btn-mp w-100" type="submit"><i class="bi bi-plus-circle me-1"></i>Ajouter l’opérateur</button>
            </form>
          </section>
        </div>
        <div class="col-lg-8">
          <section class="mp-card">
            <div class="mp-card-body pb-2">
              <div class="mp-section-title">Opérateurs enregistrés</div>
              <div class="mp-section-subtitle mt-1">L’opérateur principal représente votre propre réseau.</div>
            </div>
            <div class="table-responsive">
              <table class="table table-mp mb-0">
                <thead><tr><th>#</th><th>Nom</th><th>Type</th><th class="text-end">Préfixes</th><th class="text-end">Commission</th><th class="text-end">Action</th></tr></thead>
                <tbody>
                <?php if (empty($operateurs)): ?>
                  <tr><td colspan="6" class="text-center text-muted py-5">Aucun opérateur enregistré.</td></tr>
                <?php else: foreach ($operateurs as $index => $operateur): ?>
                  <tr>
                    <td><?= $index + 1 ?></td>
                    <td class="fw-semibold"><?= esc($operateur['nom']) ?></td>
                    <td><span class="badge <?= (int) $operateur['est_principal'] === 1 ? 'badge-soft-success' : 'badge-soft-primary' ?>"><?= (int) $operateur['est_principal'] === 1 ? 'Principal' : 'Partenaire' ?></span></td>
                    <td class="text-end"><?= (int) $operateur['nombre_prefixes'] ?></td>
                    <td class="text-end"><?= (int) $operateur['est_principal'] === 1 ? '100,00 %' : number_format((float) $operateur['pourcentage'], 2, ',', ' ') . ' %' ?></td>
                    <td class="text-end"><button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editOperator<?= (int) $operateur['id'] ?>"><i class="bi bi-pencil"></i></button></td>
                  </tr>
                  <div class="modal fade" id="editOperator<?= (int) $operateur['id'] ?>" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
                      <form method="post" action="<?= site_url('admin/operateurs/' . $operateur['id']) ?>">
                        <?= csrf_field() ?>
                        <div class="modal-header"><h5 class="modal-title">Modifier l’opérateur</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                        <div class="modal-body"><label class="form-label">Nom</label><input class="form-control" name="nom" maxlength="100" value="<?= esc($operateur['nom']) ?>" required></div>
                        <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button><button class="btn btn-primary" type="submit">Enregistrer</button></div>
                      </form>
                    </div></div>
                  </div>
                <?php endforeach; endif; ?>
                </tbody>
              </table>
            </div>
          </section>
        </div>
      </div>
    </main>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= base_url('assets/js/app.js') ?>"></script>
</body>
</html>
