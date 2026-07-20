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
  <?= view('Admin/partials/sidebar', ['activePage' => 'baremes']) ?>
  <div class="sidebar-backdrop" data-sidebar-toggle></div>
<div class="admin-main">
    <?= view('Admin/partials/topbar', ['pageTitle' => 'Types d’opérations et barèmes']) ?>
<main class="admin-content">
      <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
        <div>
          <div class="mp-section-title">Types d’opérations et barèmes de frais</div>
          <div class="mp-section-subtitle mt-1">Toute modification clôture l’ancien barème et crée une nouvelle version active.</div>
        </div>
        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#typeModal">
          <i class="bi bi-plus-circle me-1"></i>Nouveau type
        </button>
      </div>

      <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <?= esc(session()->getFlashdata('success')) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>
      <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <?= esc(session()->getFlashdata('error')) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <?php if ($types === []): ?>
        <div class="alert alert-warning">Aucun type d’opération n’est encore disponible.</div>
      <?php else: ?>
        <div class="d-flex flex-wrap gap-2 mb-3">
          <?php foreach ($types as $type): ?>
            <a class="btn <?= (int) $selectedTypeId === (int) $type['id'] ? 'btn-primary' : 'btn-outline-secondary' ?>"
               href="<?= site_url('admin/baremes?type=' . $type['id']) ?>">
              <i class="bi bi-table me-1"></i><?= esc(ucfirst($type['libelle'])) ?>
            </a>
          <?php endforeach; ?>
        </div>

        <?php
          $selectedType = null;
          foreach ($types as $type) {
              if ((int) $type['id'] === (int) $selectedTypeId) { $selectedType = $type; break; }
          }
        ?>

        <section class="mp-card mb-4">
          <div class="mp-card-body pb-2">
            <div class="mp-section-title">Barème — <?= esc(ucfirst($selectedType['libelle'] ?? '')) ?></div>
            <div class="mp-section-subtitle mt-1">Seules les versions actives sont affichées.</div>
          </div>
          <div class="table-responsive">
            <table class="table table-mp align-middle mb-0">
              <thead>
                <tr><th>#</th><th>Montant min (Ar)</th><th>Montant max (Ar)</th><th>Frais (Ar)</th><th>Début</th><th class="text-end">Action</th></tr>
              </thead>
              <tbody>
              <?php if ($baremes === []): ?>
                <tr><td colspan="6" class="text-center text-muted py-4">Aucune tranche active pour ce type.</td></tr>
              <?php else: ?>
                <?php foreach ($baremes as $index => $bareme): ?>
                  <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= number_format((float) $bareme['montant_min'], 0, ',', ' ') ?></td>
                    <td><?= number_format((float) $bareme['montant_max'], 0, ',', ' ') ?></td>
                    <td class="fw-semibold"><?= number_format((float) $bareme['montant_frais'], 0, ',', ' ') ?></td>
                    <td><?= esc(date('d/m/Y H:i', strtotime($bareme['date_ajout']))) ?></td>
                    <td class="text-end">
                      <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal<?= $bareme['id'] ?>">
                        <i class="bi bi-pencil-square me-1"></i>Modifier
                      </button>
                    </td>
                  </tr>

                  <div class="modal fade" id="editModal<?= $bareme['id'] ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                      <div class="modal-content">
                        <form method="post" action="<?= site_url('admin/baremes/' . $bareme['id']) ?>">
                          <?= csrf_field() ?>
                          <input type="hidden" name="type_operation_id" value="<?= (int) $selectedTypeId ?>">
                          <div class="modal-header">
                            <h5 class="modal-title">Modifier la tranche</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                          </div>
                          <div class="modal-body">
                            <div class="alert alert-info small">L’ancien enregistrement recevra une date de fin. Une nouvelle ligne active sera créée.</div>
                            <div class="mb-3">
                              <label class="form-label">Montant minimum</label>
                              <input class="form-control" name="montant_min" type="number" min="0" step="1" value="<?= esc($bareme['montant_min']) ?>" required>
                            </div>
                            <div class="mb-3">
                              <label class="form-label">Montant maximum</label>
                              <input class="form-control" name="montant_max" type="number" min="0" step="1" value="<?= esc($bareme['montant_max']) ?>" required>
                            </div>
                            <div>
                              <label class="form-label">Frais</label>
                              <input class="form-control" name="montant_frais" type="number" min="0" step="1" value="<?= esc($bareme['montant_frais']) ?>" required>
                            </div>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                            <button class="btn btn-primary" type="submit">Enregistrer la nouvelle version</button>
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>
              </tbody>
            </table>
          </div>
        </section>

        <section class="mp-card">
          <div class="mp-card-body">
            <div class="fw-bold text-mp-primary mb-3"><i class="bi bi-plus-circle me-1"></i>Ajouter une tranche</div>
            <form method="post" action="<?= site_url('admin/baremes') ?>" class="row g-3 align-items-end">
              <?= csrf_field() ?>
              <input type="hidden" name="type_operation_id" value="<?= (int) $selectedTypeId ?>">
              <div class="col-md-3"><label class="form-label">Minimum</label><input class="form-control" name="montant_min" type="number" min="0" step="1" value="<?= old('montant_min') ?>" required></div>
              <div class="col-md-3"><label class="form-label">Maximum</label><input class="form-control" name="montant_max" type="number" min="0" step="1" value="<?= old('montant_max') ?>" required></div>
              <div class="col-md-3"><label class="form-label">Frais</label><input class="form-control" name="montant_frais" type="number" min="0" step="1" value="<?= old('montant_frais') ?>" required></div>
              <div class="col-md-3 d-grid"><button class="btn btn-primary" type="submit">Ajouter la tranche</button></div>
            </form>
          </div>
        </section>
      <?php endif; ?>
    </main>
  </div>
</div>

<div class="modal fade" id="typeModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="post" action="<?= site_url('admin/types') ?>">
        <?= csrf_field() ?>
        <div class="modal-header"><h5 class="modal-title">Ajouter un type d’opération</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body"><label class="form-label">Libellé</label><input class="form-control" name="libelle" maxlength="50" placeholder="Ex. paiement marchand" required></div>
        <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button><button class="btn btn-primary" type="submit">Ajouter</button></div>
      </form>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= base_url('assets/js/app.js') ?>"></script>
</body>
</html>
