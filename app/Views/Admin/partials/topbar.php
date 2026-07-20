<?php $pageTitle = $pageTitle ?? ($title ?? 'Espace opérateur'); ?>
<header class="admin-topbar">
  <div class="d-flex align-items-center gap-2">
    <button class="btn btn-sm btn-outline-secondary sidebar-toggle" type="button" data-sidebar-toggle aria-label="Ouvrir le menu"><i class="bi bi-list"></i></button>
    <div class="admin-page-title"><?= esc($pageTitle) ?></div>
  </div>
  <?php $operatorName = (string) (session()->get('operator_name') ?: 'Opérateur'); ?>
  <div class="admin-user"><span><?= esc($operatorName) ?></span><div class="admin-avatar"><?= esc(mb_strtoupper(mb_substr($operatorName, 0, 2))) ?></div></div>
</header>
