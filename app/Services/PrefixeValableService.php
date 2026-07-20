<?php

namespace App\Services;

use App\Models\PrefixeValableModel;

class PrefixeValableService
{
    private PrefixeValableModel $prefixeValableModel;

    public function __construct()
    {
        $this->prefixeValableModel = new PrefixeValableModel();
    }

    public function getAllPrefixeValable(): array
    {
        return $this->prefixeValableModel
            ->orderBy('prefixe', 'ASC')
            ->findAll();
    }

    public function getAllWithOperateur(): array
    {
        return $this->prefixeValableModel
            ->select('prefixe_valable.*, operateur.nom AS operateur_nom, operateur.est_principal')
            ->join('operateur', 'operateur.id = prefixe_valable.operateur_id', 'left')
            ->orderBy('prefixe_valable.prefixe', 'ASC')
            ->findAll();
    }

    public function getPrefixeValableById(int $id): ?array
    {
        return $this->prefixeValableModel->find($id);
    }

    public function createPrefixeValable(string $prefixe, int $operateurId)
    {
        return $this->prefixeValableModel->insert([
            'operateur_id' => $operateurId,
            'prefixe'      => $prefixe,
            'date_ajout'   => date('Y-m-d H:i:s'),
        ]);
    }

    public function updatePrefixeValable(int $id, string $prefixe, int $operateurId): bool
    {
        return $this->prefixeValableModel->update($id, [
            'operateur_id' => $operateurId,
            'prefixe'      => $prefixe,
        ]);
    }

    public function prefixeExiste(string $prefixe, ?int $ignoreId = null): bool
    {
        $builder = $this->prefixeValableModel->where('prefixe', $prefixe);
        if ($ignoreId !== null) {
            $builder->where('id !=', $ignoreId);
        }
        return $builder->countAllResults() > 0;
    }

    public function getPrefixeValableByPrefixe(string $prefixe): ?array
    {
        return $this->prefixeValableModel->where('prefixe', $prefixe)->first();
    }

    public function getPrefixeValableByOperateurId(int $operateurId): array
    {
        return $this->prefixeValableModel
            ->where('operateur_id', $operateurId)
            ->orderBy('prefixe', 'ASC')
            ->findAll();
    }
}
