<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= esc($title) ?> | MobiPay</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
</head>
<body data-page="admin-prefixes">
<div class="admin-layout">
  <?= view('Admin/partials/sidebar', ['activePage' => 'prefixes']) ?>
  <div class="sidebar-backdrop" data-sidebar-toggle></div>
  <div class="admin-main">
    <?= view('Admin/partials/topbar', ['pageTitle' => 'Configuration des préfixes']) ?>
    <main class="admin-content">
      <?php if (session()->getFlashdata('success')): ?><div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div><?php endif; ?>
      <?php if (session()->getFlashdata('error')): ?><div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div><?php endif; ?>

      <section class="mp-card mb-4">
        <div class="mp-card-body">
          <div class="mp-section-title">Ajouter et affecter un préfixe</div>
          <div class="mp-section-subtitle mt-1 mb-3">Chaque préfixe doit appartenir à un opérateur. Cette affectation détermine la répartition des frais.</div>
          <?php if (empty($operateurs)): ?>
            <div class="alert alert-warning mb-0">Ajoutez d’abord un opérateur.</div>
          <?php else: ?>
          <form action="<?= site_url('admin/prefixes') ?>" method="post" class="row g-3 align-items-end">
            <?= csrf_field() ?>
            <div class="col-md-4">
              <label class="form-label" for="prefixe">Préfixe</label>
              <input class="form-control" id="prefixe" name="prefixe" type="text" inputmode="numeric" pattern="0[0-9]{2}" maxlength="3" value="<?= esc(old('prefixe')) ?>" placeholder="Ex. 032" required>
            </div>
            <div class="col-md-5">
              <label class="form-label" for="operateur_id">Opérateur propriétaire</label>
              <select class="form-select" id="operateur_id" name="operateur_id" required>
                <option value="">Sélectionner</option>
                <?php foreach ($operateurs as $operateur): ?>
                  <option value="<?= (int) $operateur['id'] ?>" <?= (string) old('operateur_id') === (string) $operateur['id'] ? 'selected' : '' ?>><?= esc($operateur['nom']) ?><?= (int) $operateur['est_principal'] === 1 ? ' (principal)' : '' ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-3 d-grid"><button class="btn btn-mp" type="submit"><i class="bi bi-plus-lg me-1"></i>Ajouter</button></div>
          </form>
          <?php endif; ?>
        </div>
      </section>

      <section class="mp-card">
        <div class="mp-card-body pb-2"><div class="mp-section-title">Préfixes configurés</div></div>
        <div class="table-responsive">
          <table class="table table-mp align-middle mb-0">
            <thead><tr><th>#</th><th>Préfixe</th><th>Opérateur</th><th>Type</th><th>Date d’ajout</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
            <?php if (empty($prefixes)): ?>
              <tr><td colspan="6" class="text-center py-5 text-muted">Aucun préfixe enregistré.</td></tr>
            <?php else: foreach ($prefixes as $index => $prefixe): ?>
              <tr>
                <td><?= $index + 1 ?></td>
                <td><span class="badge text-bg-primary fs-6 px-3 py-2"><?= esc($prefixe['prefixe']) ?></span></td>
                <td class="fw-semibold"><?= esc($prefixe['operateur_nom'] ?? 'Non affecté') ?></td>
                <td><span class="badge <?= (int) ($prefixe['est_principal'] ?? 0) === 1 ? 'badge-soft-success' : 'badge-soft-primary' ?>"><?= (int) ($prefixe['est_principal'] ?? 0) === 1 ? 'Principal' : 'Partenaire' ?></span></td>
                <td><?= esc(date('d/m/Y H:i', strtotime((string) $prefixe['date_ajout']))) ?></td>
                <td class="text-end">
                  <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editPrefix<?= (int) $prefixe['id'] ?>"><i class="bi bi-pencil"></i></button>
                  <form action="<?= site_url('admin/prefixes/' . $prefixe['id'] . '/delete') ?>" method="post" class="d-inline" onsubmit="return confirm('Supprimer ce préfixe ?');">
                    <?= csrf_field() ?><button class="btn btn-sm btn-outline-danger" type="submit"><i class="bi bi-trash"></i></button>
                  </form>
                </td>
              </tr>
              <div class="modal fade" id="editPrefix<?= (int) $prefixe['id'] ?>" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
                  <form action="<?= site_url('admin/prefixes/' . $prefixe['id']) ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="modal-header"><h5 class="modal-title">Modifier le préfixe</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                      <div class="mb-3"><label class="form-label">Préfixe</label><input class="form-control" name="prefixe" pattern="0[0-9]{2}" maxlength="3" value="<?= esc($prefixe['prefixe']) ?>" required></div>
                      <div><label class="form-label">Opérateur</label><select class="form-select" name="operateur_id" required>
                        <?php foreach ($operateurs as $operateur): ?><option value="<?= (int) $operateur['id'] ?>" <?= (int) $operateur['id'] === (int) $prefixe['operateur_id'] ? 'selected' : '' ?>><?= esc($operateur['nom']) ?></option><?php endforeach; ?>
                      </select></div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button><button class="btn btn-primary" type="submit">Enregistrer</button></div>
                  </form>
                </div></div>
              </div>
            <?php endforeach; endif; ?>
            </tbody>
          </table>
        </div>
      </section>
    </main>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= base_url('assets/js/app.js') ?>"></script>
</body>
</html>
