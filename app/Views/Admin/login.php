<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Connexion au service Mobile Money MobiPay">
  <title><?= esc($title ?? 'Connexion') ?> | MobiPay</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
</head>
<body>
<main class="login-page">
  <section class="login-card" aria-labelledby="login-title">
    <div class="text-center mb-4">
      <div class="brand-mark"><i class="bi bi-phone"></i></div>
      <div class="brand-name" id="login-title">MobiPay</div>
      <div class="brand-subtitle">Opérateur Mobile Money</div>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
      <div class="alert alert-danger py-2" role="alert">
        <i class="bi bi-exclamation-circle me-1"></i>
        <?= esc(session()->getFlashdata('error')) ?>
      </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
      <div class="alert alert-success py-2" role="alert">
        <?= esc(session()->getFlashdata('success')) ?>
      </div>
    <?php endif; ?>

    <!-- Connexion client affichée par défaut -->
    <div id="client-login-panel" class="<?= session()->getFlashdata('login_mode') === 'operator' ? 'd-none' : '' ?>">
      <div class="text-center mb-3">
        <span class="badge rounded-pill text-bg-light border">Espace client</span>
      </div>

      <form action="<?= site_url('client/login') ?>" method="post" novalidate>
        <?= csrf_field() ?>
        <div class="mb-3">
          <label class="form-label" for="phone">Numéro de téléphone</label>
          <div class="input-group">
            <span class="input-group-text">+261</span>
            <input class="form-control" id="phone" name="numero" type="tel"
                   inputmode="numeric" placeholder="033 12 345 67" maxlength="14"
                   autocomplete="tel" value="<?= esc(old('numero')) ?>" required autofocus>
          </div>
          <div class="form-text">Préfixes autorisés : 033 et 037</div>
        </div>

        <button class="btn btn-mp w-100" type="submit">
          <i class="bi bi-box-arrow-in-right me-2"></i>Accéder à mon compte
        </button>
      </form>

      <div class="text-center border-top mt-4 pt-3">
        <button id="show-operator-login" class="btn btn-link btn-sm text-decoration-none text-mp-muted" type="button">
          <i class="bi bi-shield-lock me-1"></i>Espace opérateur
        </button>
      </div>
    </div>

    <!-- Connexion opérateur masquée au chargement -->
    <div id="operator-login-panel" class="<?= session()->getFlashdata('login_mode') === 'operator' ? '' : 'd-none' ?>">
      <div class="text-center mb-3">
        <span class="badge rounded-pill text-bg-light border">Espace opérateur</span>
      </div>

      <form action="<?= site_url('admin/login') ?>" method="post">
        <?= csrf_field() ?>
        <div class="mb-3">
          <label class="form-label" for="password">Mot de passe opérateur</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-key"></i></span>
            <input class="form-control" id="password" name="password" type="password"
                   placeholder="Saisissez le mot de passe" autocomplete="current-password" required>
            <button class="btn btn-outline-secondary" id="toggle-password" type="button" aria-label="Afficher le mot de passe">
              <i class="bi bi-eye"></i>
            </button>
          </div>
        </div>

        <button class="btn btn-mp w-100" type="submit">
          <i class="bi bi-shield-check me-2"></i>Se connecter
        </button>
      </form>

      <div class="text-center border-top mt-4 pt-3">
        <button id="show-client-login" class="btn btn-link btn-sm text-decoration-none text-mp-muted" type="button">
          <i class="bi bi-arrow-left me-1"></i>Retour à l’espace client
        </button>
      </div>
    </div>
  </section>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const clientPanel = document.getElementById('client-login-panel');
  const operatorPanel = document.getElementById('operator-login-panel');
  const phoneInput = document.getElementById('phone');
  const passwordInput = document.getElementById('password');

  document.getElementById('show-operator-login').addEventListener('click', function () {
    clientPanel.classList.add('d-none');
    operatorPanel.classList.remove('d-none');
    passwordInput.focus();
  });

  document.getElementById('show-client-login').addEventListener('click', function () {
    operatorPanel.classList.add('d-none');
    clientPanel.classList.remove('d-none');
    phoneInput.focus();
  });

  document.getElementById('toggle-password').addEventListener('click', function () {
    const icon = this.querySelector('i');
    const visible = passwordInput.type === 'text';
    passwordInput.type = visible ? 'password' : 'text';
    icon.className = visible ? 'bi bi-eye' : 'bi bi-eye-slash';
  });
});
</script>
</body>
</html>
