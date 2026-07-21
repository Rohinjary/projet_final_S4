<?php

namespace App\Services;

use App\Models\OperateurModel;

class OperateurService
{
    private OperateurModel $operateurModel;

    public function __construct()
    {
        $this->operateurModel = new OperateurModel();
    }

    public function getAllOperateurs(): array
    {
        return $this->operateurModel
            ->orderBy('est_principal', 'DESC')
            ->orderBy('nom', 'ASC')
            ->findAll();
    }

    public function getAllWithConfiguration(): array
    {
        $db = db_connect();
        $rows = $db->table('operateur o')
            ->select('o.*, COUNT(DISTINCT p.id) AS nombre_prefixes')
            ->join('prefixe_valable p', 'p.operateur_id = o.id', 'left')
            ->groupBy('o.id')
            ->orderBy('o.est_principal', 'DESC')
            ->orderBy('o.nom', 'ASC')
            ->get()->getResultArray();

        $commissionService = new CommissionOperateurService();
        foreach ($rows as &$row) {
            $commission = $commissionService->getCommissionByOperateurId((int) $row['id']);
            // Les taux de commission concernent uniquement les partenaires.
            // Les frais conservés par MobiPay sont gérés séparément.
            $row['pourcentage'] = (int) ($row['est_principal'] ?? 0) === 1
                ? 0.0
                : (float) ($commission['pourcentage'] ?? 0);
        }
        unset($row);

        return $rows;
    }

    public function getOperateurById(int $id): ?array
    {
        return $this->operateurModel->find($id);
    }

    public function getPrincipal(): ?array
    {
        return $this->operateurModel
            ->where('est_principal', 1)
            ->orderBy('id', 'ASC')
            ->first();
    }

    public function nomExiste(string $nom, ?int $ignoreId = null): bool
    {
        $builder = $this->operateurModel
            ->where('LOWER(nom)', mb_strtolower(trim($nom)));
        if ($ignoreId !== null) {
            $builder->where('id !=', $ignoreId);
        }
        return $builder->countAllResults() > 0;
    }

    public function createOperateur(string $nom, int $estPrincipal = 0)
    {
        return $this->operateurModel->insert([
            'nom'           => trim($nom),
            'est_principal' => $estPrincipal === 1 ? 1 : 0,
            'date_ajout'    => date('Y-m-d H:i:s'),
        ]);
    }

    public function updateOperateur(int $id, string $nom): bool
    {
        return $this->operateurModel->update($id, ['nom' => trim($nom)]);
    }
}
