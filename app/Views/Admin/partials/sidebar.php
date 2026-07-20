<?php $activePage = $activePage ?? ''; ?>
<aside class="admin-sidebar">
  <div class="admin-logo">
    <strong><i class="bi bi-phone me-1"></i>MobiPay</strong>
    <small><?= esc((string) (session()->get('operator_name') ?: 'Espace Opérateur')) ?></small>
  </div>
  <nav class="admin-nav">
    <div class="admin-nav-label">Tableau de bord</div>
    <a class="<?= $activePage === 'dashboard' ? 'active' : '' ?>" href="<?= site_url('admin/dashboard') ?>"><i class="bi bi-grid-1x2"></i>Dashboard</a>

    <div class="admin-nav-label">Configuration</div>
    <a class="<?= $activePage === 'operateurs' ? 'active' : '' ?>" href="<?= site_url('admin/operateurs') ?>"><i class="bi bi-building"></i>Opérateurs</a>
    <a class="<?= $activePage === 'prefixes' ? 'active' : '' ?>" href="<?= site_url('admin/prefixes') ?>"><i class="bi bi-hash"></i>Préfixes</a>
    <a class="<?= $activePage === 'commissions' ? 'active' : '' ?>" href="<?= site_url('admin/commissions') ?>"><i class="bi bi-percent"></i>Commissions</a>
    <a class="<?= $activePage === 'baremes' ? 'active' : '' ?>" href="<?= site_url('admin/baremes') ?>"><i class="bi bi-table"></i>Types & barèmes</a>

    <div class="admin-nav-label">Rapports</div>
    <a class="<?= $activePage === 'gains' ? 'active' : '' ?>" href="<?= site_url('admin/gains') ?>"><i class="bi bi-graph-up-arrow"></i>Situation gains</a>
    <a class="<?= $activePage === 'reversements' ? 'active' : '' ?>" href="<?= site_url('admin/reversements') ?>"><i class="bi bi-send-check"></i>Montants à envoyer</a>
    <a class="<?= $activePage === 'comptes' ? 'active' : '' ?>" href="<?= site_url('admin/comptes') ?>"><i class="bi bi-people"></i>Comptes clients</a>
  </nav>
  <div class="admin-sidebar-footer"><a href="<?= site_url('logout') ?>"><i class="bi bi-box-arrow-left me-2"></i>Déconnexion</a></div>
</aside>
