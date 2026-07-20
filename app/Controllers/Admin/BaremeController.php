<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\BaremeFraisService;
use App\Services\TypeOperationService;

class BaremeController extends BaseController
{
    private BaremeFraisService $baremeService;
    private TypeOperationService $typeService;

    public function __construct()
    {
        $this->baremeService = new BaremeFraisService();
        $this->typeService   = new TypeOperationService();
    }

    public function index()
    {
        if ($redirect = $this->requireOperator()) {
            return $redirect;
        }

        $types = $this->typeService->getAllTypeOperation();
        $selectedTypeId = (int) ($this->request->getGet('type') ?: ($types[0]['id'] ?? 0));

        return view('Admin/baremes', [
            'title'          => 'Types et barèmes',
            'types'          => $types,
            'selectedTypeId' => $selectedTypeId,
            'baremes'        => $selectedTypeId > 0
                ? $this->baremeService->getActiveBaremesByTypeOperation($selectedTypeId)
                : [],
        ]);
    }

    public function storeType()
    {
        if ($redirect = $this->requireOperator()) {
            return $redirect;
        }

        $libelle = trim((string) $this->request->getPost('libelle'));

        if ($libelle === '' || mb_strlen($libelle) > 50) {
            return redirect()->to(site_url('admin/baremes'))
                ->withInput()->with('error', 'Le libellé est obligatoire et ne doit pas dépasser 50 caractères.');
        }

        if ($this->typeService->libelleExists($libelle)) {
            return redirect()->to(site_url('admin/baremes'))
                ->withInput()->with('error', 'Ce type d’opération existe déjà.');
        }

        $id = $this->typeService->createTypeOperation(mb_strtolower($libelle));

        return redirect()->to(site_url('admin/baremes?type=' . $id))
            ->with('success', 'Le type d’opération a été ajouté.');
    }

    public function store()
    {
        if ($redirect = $this->requireOperator()) {
            return $redirect;
        }

        $typeId = (int) $this->request->getPost('type_operation_id');
        $min     = $this->normalizeAmount($this->request->getPost('montant_min'));
        $max     = $this->normalizeAmount($this->request->getPost('montant_max'));
        $frais   = $this->normalizeAmount($this->request->getPost('montant_frais'));

        $error = $this->validateBareme($typeId, $min, $max, $frais);
        if ($error !== null) {
            return redirect()->to(site_url('admin/baremes?type=' . $typeId))
                ->withInput()->with('error', $error);
        }

        if ($this->baremeService->hasActiveOverlap($typeId, $min, $max)) {
            return redirect()->to(site_url('admin/baremes?type=' . $typeId))
                ->withInput()->with('error', 'Cette tranche chevauche déjà un barème actif.');
        }

        $this->baremeService->createBaremeFrais($typeId, $min, $max, $frais);

        return redirect()->to(site_url('admin/baremes?type=' . $typeId))
            ->with('success', 'La nouvelle tranche a été ajoutée.');
    }

    public function update(int $id)
    {
        if ($redirect = $this->requireOperator()) {
            return $redirect;
        }

        $ancien = $this->baremeService->getBaremeFraisById($id);
        if ($ancien === null || $ancien['date_fin'] !== null) {
            return redirect()->to(site_url('admin/baremes'))
                ->with('error', 'Le barème demandé est introuvable ou déjà clôturé.');
        }

        $typeId = (int) $this->request->getPost('type_operation_id');
        $min     = $this->normalizeAmount($this->request->getPost('montant_min'));
        $max     = $this->normalizeAmount($this->request->getPost('montant_max'));
        $frais   = $this->normalizeAmount($this->request->getPost('montant_frais'));

        $error = $this->validateBareme($typeId, $min, $max, $frais);
        if ($error !== null) {
            return redirect()->to(site_url('admin/baremes?type=' . $typeId))
                ->withInput()->with('error', $error);
        }

        if ($this->baremeService->hasActiveOverlap($typeId, $min, $max, $id)) {
            return redirect()->to(site_url('admin/baremes?type=' . $typeId))
                ->withInput()->with('error', 'Cette tranche chevauche déjà un autre barème actif.');
        }

        if (! $this->baremeService->replaceBareme($id, $typeId, $min, $max, $frais)) {
            return redirect()->to(site_url('admin/baremes?type=' . $typeId))
                ->with('error', 'La modification du barème a échoué.');
        }

        return redirect()->to(site_url('admin/baremes?type=' . $typeId))
            ->with('success', 'L’ancien barème a été clôturé et le nouveau barème a été créé.');
    }

    private function requireOperator()
    {
        if (session()->get('operator_logged_in') !== true) {
            return redirect()->to(site_url('admin/login'))
                ->with('error', 'Connectez-vous à l’espace opérateur.');
        }

        return null;
    }

    private function normalizeAmount($value): float
    {
        return (float) str_replace([' ', ','], ['', '.'], trim((string) $value));
    }

    private function validateBareme(int $typeId, float $min, float $max, float $frais): ?string
    {
        if ($this->typeService->getTypeOperationById($typeId) === null) {
            return 'Le type d’opération sélectionné est invalide.';
        }
        if ($min < 0 || $max < 0 || $frais < 0) {
            return 'Les montants et les frais doivent être positifs.';
        }
        if ($min > $max) {
            return 'Le montant minimum doit être inférieur ou égal au montant maximum.';
        }

        return null;
    }
}
