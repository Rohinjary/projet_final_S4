<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Gestion des préfixes de l'opérateur Mobile Money">
  <title><?= esc($title) ?> | MobiPay</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
</head>
<body data-page="admin-prefixes">
<div class="admin-layout">
  <aside class="admin-sidebar">
    <div class="admin-logo">
      <strong><i class="bi bi-phone me-1"></i>MobiPay</strong>
      <small>Espace Opérateur</small>
    </div>
    <nav class="admin-nav">
      <div class="admin-nav-label">Tableau de bord</div>
      <a href="<?= site_url('admin/dashboard') ?>"><i class="bi bi-grid-1x2"></i>Dashboard</a>
      <div class="admin-nav-label">Configuration</div>
      <a href="<?= site_url('admin/prefixes') ?>" class="active"><i class="bi bi-hash"></i>Préfixes</a>
      <a class="active" href="<?= site_url('admin/baremes') ?>"><i class="bi bi-table"></i>Types & barèmes</a>
      <div class="admin-nav-label">Rapports</div>
      <a class="active" href="<?= site_url('admin/gains') ?>"><i class="bi bi-graph-up-arrow"></i>Situation gains</a>
      <a href="<?= site_url('admin/comptes') ?>"><i class="bi bi-people"></i>Comptes clients</a>
    </nav>
    <div class="admin-sidebar-footer">
      <a href="<?= site_url('logout') ?>"><i class="bi bi-box-arrow-left me-2"></i>Déconnexion</a>
    </div>
  </aside>

  <div class="sidebar-backdrop" data-sidebar-toggle></div>

  <div class="admin-main">
    <header class="admin-topbar">
      <div class="d-flex align-items-center gap-2">
        <button class="btn btn-sm btn-outline-secondary sidebar-toggle" type="button" data-sidebar-toggle aria-label="Ouvrir le menu">
          <i class="bi bi-list"></i>
        </button>
        <div class="admin-page-title">Configuration des préfixes</div>
      </div>
      <div class="admin-user"><span>Opérateur</span><div class="admin-avatar">OP</div></div>
    </header>

    <main class="admin-content">
      <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
        <div>
          <div class="mp-section-title">Préfixes de l'opérateur</div>
          <div class="mp-section-subtitle mt-1">Définissez les préfixes autorisés pour la connexion et les transferts.</div>
        </div>
      </div>

      <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <i class="bi bi-check-circle me-2"></i><?= esc(session()->getFlashdata('success')) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
        </div>
      <?php endif; ?>

      <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <i class="bi bi-exclamation-triangle me-2"></i><?= esc(session()->getFlashdata('error')) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
        </div>
      <?php endif; ?>

      <section class="mp-card mb-4">
        <div class="mp-card-body">
          <form action="<?= site_url('admin/prefixes') ?>" method="post" class="row g-3 align-items-end">
            <?= csrf_field() ?>
            <div class="col-md-8 col-lg-5">
              <label class="form-label" for="prefixe">Nouveau préfixe</label>
              <input
                class="form-control <?= session('error') ? 'is-invalid' : '' ?>"
                id="prefixe"
                name="prefixe"
                type="text"
                inputmode="numeric"
                pattern="0[0-9]{2}"
                maxlength="3"
                value="<?= esc(old('prefixe')) ?>"
                placeholder="Ex. 032"
                required
              >
              <div class="form-text">Saisissez exactement 3 chiffres, par exemple 033 ou 037.</div>
            </div>
            <div class="col-md-4 col-lg-3 d-grid">
              <button class="btn btn-mp" type="submit">
                <i class="bi bi-plus-lg me-1"></i>Ajouter
              </button>
            </div>
          </form>
        </div>
      </section>

      <section class="mp-card">
        <div class="table-responsive">
          <table class="table table-mp align-middle mb-0">
            <thead>
              <tr>
                <th>#</th>
                <th>Préfixe</th>
                <th>Date d'ajout</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($prefixes)): ?>
                <tr>
                  <td colspan="4" class="text-center py-5 text-muted">
                    <i class="bi bi-inbox d-block fs-2 mb-2"></i>
                    Aucun préfixe enregistré.
                  </td>
                </tr>
              <?php else: ?>
                <?php foreach ($prefixes as $index => $prefixe): ?>
                  <?php
                    $id = is_array($prefixe) ? $prefixe['id'] : $prefixe->id;
                    $valeur = is_array($prefixe) ? $prefixe['prefixe'] : $prefixe->prefixe;
                    $dateAjout = is_array($prefixe) ? $prefixe['date_ajout'] : $prefixe->date_ajout;
                  ?>
                  <tr>
                    <td><?= $index + 1 ?></td>
                    <td><span class="badge text-bg-primary fs-6 px-3 py-2"><?= esc($valeur) ?></span></td>
                    <td><?= esc(date('d/m/Y à H:i', strtotime($dateAjout))) ?></td>
                    <td class="text-end">
                      <form action="<?= site_url('admin/prefixes/' . $id . '/delete') ?>" method="post" class="d-inline" onsubmit="return confirm('Supprimer le préfixe <?= esc($valeur, 'js') ?> ?');">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                          <i class="bi bi-trash"></i>
                          <span class="d-none d-md-inline ms-1">Supprimer</span>
                        </button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
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
