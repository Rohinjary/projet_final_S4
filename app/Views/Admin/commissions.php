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
  <?= view('Admin/partials/sidebar', ['activePage' => 'commissions']) ?>
  <div class="sidebar-backdrop" data-sidebar-toggle></div>
  <div class="admin-main">
    <?= view('Admin/partials/topbar', ['pageTitle' => 'Commissions par opérateur']) ?>
    <main class="admin-content">
      <?php if (session()->getFlashdata('success')): ?><div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div><?php endif; ?>
      <?php if (session()->getFlashdata('error')): ?><div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div><?php endif; ?>

      <div class="alert alert-info">
        <strong>Règle de calcul :</strong> pour un partenaire, commission = montant transféré × taux / 100, puis montant à envoyer = montant transféré + commission. Les frais de transfert restent intégralement le gain brut de MobiPay. Aucun taux de commission n'est appliqué à MobiPay.
      </div>

      <section class="mp-card">
        <div class="mp-card-body pb-2"><div class="mp-section-title">Taux de commission</div></div>
        <div class="table-responsive">
          <table class="table table-mp align-middle mb-0">
            <thead><tr><th>Opérateur</th><th>Type</th><th class="text-end">Préfixes</th><th>Taux de commission partenaire</th><th class="text-end">Action</th></tr></thead>
            <tbody>
            <?php if (empty($operateurs)): ?>
              <tr><td colspan="5" class="text-center text-muted py-5">Aucun opérateur configuré.</td></tr>
            <?php else: foreach ($operateurs as $operateur): ?>
              <?php $principal = (int) $operateur['est_principal'] === 1; ?>
              <tr>
                <td class="fw-semibold"><?= esc($operateur['nom']) ?></td>
                <td><span class="badge <?= $principal ? 'badge-soft-success' : 'badge-soft-primary' ?>"><?= $principal ? 'Principal' : 'Partenaire' ?></span></td>
                <td class="text-end"><?= (int) $operateur['nombre_prefixes'] ?></td>
                <td style="min-width:240px">
                  <?php if ($principal): ?>
                    <span class="text-muted">Non applicable</span>
                  <?php else: ?>
                    <form class="d-flex gap-2 align-items-center" method="post" action="<?= site_url('admin/commissions/' . $operateur['id']) ?>">
                      <?= csrf_field() ?>
                      <div class="input-group input-group-sm">
                        <input class="form-control" type="number" name="pourcentage" min="0" max="100" step="0.01" value="<?= esc((string) $operateur['pourcentage']) ?>" required>
                        <span class="input-group-text">%</span>
                      </div>
                      <button class="btn btn-sm btn-primary" type="submit"><i class="bi bi-check-lg"></i></button>
                    </form>
                  <?php endif; ?>
                </td>
                <td class="text-end"><?= $principal ? '<span class="text-muted">Tous les frais restent à MobiPay</span>' : '<span class="text-muted">Sur le montant transféré</span>' ?></td>
              </tr>
            <?php endforeach; endif; ?>
            </tbody>
          </table>
        </div>
      </section>
    </main>
  </div>
</div>
<script src="<?= base_url('assets/js/app.js') ?>"></script>
</body>
</html>
