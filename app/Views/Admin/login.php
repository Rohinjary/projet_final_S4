<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= esc($title ?? 'Connexion opérateur') ?> | MobiPay</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
</head>
<body>
<main class="login-page">
  <section class="login-card" aria-labelledby="login-title">
    <div class="text-center mb-4">
      <div class="brand-mark"><i class="bi bi-shield-lock"></i></div>
      <div class="brand-name" id="login-title">MobiPay</div>
      <div class="brand-subtitle">Espace opérateur</div>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
      <div class="alert alert-danger"><i class="bi bi-exclamation-circle me-1"></i><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('success')): ?>
      <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>

    <form action="<?= site_url('admin/login') ?>" method="post">
      <?= csrf_field() ?>
      <div class="mb-3">
        <label class="form-label" for="password">Mot de passe opérateur</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-key"></i></span>
          <input class="form-control" id="password" name="password" type="password" autocomplete="current-password" required autofocus>
          <button class="btn btn-outline-secondary" id="toggle-password" type="button" aria-label="Afficher le mot de passe"><i class="bi bi-eye"></i></button>
        </div>
      </div>
      <button class="btn btn-mp w-100" type="submit"><i class="bi bi-shield-check me-2"></i>Se connecter</button>
    </form>

    <div class="text-center border-top mt-4 pt-3">
      <a href="<?= site_url('client/login') ?>" class="btn btn-link btn-sm text-decoration-none text-mp-muted"><i class="bi bi-arrow-left me-1"></i>Retour à l’espace client</a>
    </div>
  </section>
</main>
<script>
document.getElementById('toggle-password').addEventListener('click', function () {
  const input = document.getElementById('password');
  const visible = input.type === 'text';
  input.type = visible ? 'password' : 'text';
  this.querySelector('i').className = visible ? 'bi bi-eye' : 'bi bi-eye-slash';
});
</script>
</body>
</html>
