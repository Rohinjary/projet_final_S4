<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Epargne | MobiPay</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
</head>

<body data-page="client-depot">

    <main class="client-page">
        <section class="client-shell">
            <header class="client-header">
                <div class="d-flex align-items-center justify-content-between gap-3">
                    <div>
                        <div class="client-brand"><i class="bi bi-phone me-1"></i>MobiPay</div>
                    </div>
                    <a class="client-logout" href="<?= base_url('client/deconnexion') ?>"><i class="bi bi-box-arrow-right me-1"></i>Deconnexion</a>
                </div>
            </header>
            <div class="client-content">
                <div class="client-content-container">

                    <div class="page-heading">
                        <h1><i class="bi bi-arrow-down-circle me-1"></i>Definir le pourcentage d'epargne</h1>
                    </div>

                    <?php if (session()->getFlashdata('erreur')) : ?>
                        <div class="alert alert-danger"><?= esc(session()->getFlashdata('erreur')) ?></div>
                    <?php endif; ?>


                    <form action="<?= base_url('client/traiter-epargne') ?>" method="post">
<?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label" for="pourcentage"></label>
                            <input class="form-control" id="pourcentahe" name="pourcentage" type="number" min="0" max="100" placeholder="Ex. 50" required>
                        </div>
                        <button class="btn btn-success w-100 fw-bold py-2" type="submit"><i class="bi bi-check-circle me-1"></i>Confirmer le pourcentage</button>
                    </form>

                </div>
            </div>
            <nav class="client-nav" aria-label="Navigation client">
                <a href="<?= base_url('client/accueil') ?>"><i class="bi bi-house-door"></i><span>Accueil</span></a>
                <a href="<?= base_url('client/depot') ?>" class="active"><i class="bi bi-arrow-down-circle"></i><span>Depot</span></a>
                <a href="<?= base_url('client/retrait') ?>"><i class="bi bi-arrow-up-circle"></i><span>Retrait</span></a>
                <a href="<?= base_url('client/transfert') ?>"><i class="bi bi-arrow-left-right"></i><span>Transfert</span></a>
                <a href="<?= base_url('client/historique') ?>"><i class="bi bi-clock-history"></i><span>Historique</span></a>
            </nav>
        </section>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>