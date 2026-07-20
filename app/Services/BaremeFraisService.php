<?php

namespace App\Services;

use App\Models\BaremeFraisModel;
use DateTime;
use Throwable;

class BaremeFraisService
{
    protected BaremeFraisModel $baremeFraisModel;

    public function __construct()
    {
        $this->baremeFraisModel = new BaremeFraisModel();
    }

    public function getAllBaremeFrais(): array
    {
        return $this->baremeFraisModel->orderBy('date_ajout', 'DESC')->findAll();
    }

    public function getBaremeFraisById(int $id): ?array
    {
        return $this->baremeFraisModel->find($id);
    }

    public function getBaremeFraisByTypeOperation(int $typeOperationId): array
    {
        return $this->baremeFraisModel
            ->where('type_operation_id', $typeOperationId)
            ->orderBy('date_ajout', 'DESC')
            ->findAll();
    }

    public function getActiveBaremesByTypeOperation(int $typeOperationId): array
    {
        return $this->baremeFraisModel
            ->where('type_operation_id', $typeOperationId)
            ->where('date_fin', null)
            ->orderBy('montant_min', 'ASC')
            ->findAll();
    }

    /** Retourne uniquement la version active (la dernière) de chaque tranche, groupée par type. */
    public function getLatestBaremesGroupedByType(): array
    {
        $rows = $this->baremeFraisModel
            ->select('bareme_frais.*, type_operation.libelle AS type_libelle')
            ->join('type_operation', 'type_operation.id = bareme_frais.type_operation_id')
            ->where('bareme_frais.date_fin', null)
            ->orderBy('type_operation.id', 'ASC')
            ->orderBy('bareme_frais.montant_min', 'ASC')
            ->findAll();

        $grouped = [];
        foreach ($rows as $row) {
            $key = (int) $row['type_operation_id'];
            if (! isset($grouped[$key])) {
                $grouped[$key] = [
                    'id'       => $key,
                    'libelle'  => $row['type_libelle'],
                    'baremes'  => [],
                ];
            }
            $grouped[$key]['baremes'][] = $row;
        }

        return array_values($grouped);
    }

    public function getBaremeFraisMontant(int $typeOperationId, float $montant, string $datetime): ?array
    {
        return $this->baremeFraisModel
            ->where('type_operation_id', $typeOperationId)
            ->where('montant_min <=', $montant)
            ->where('montant_max >=', $montant)
            ->where('date_ajout <=', $datetime)
            ->groupStart()
                ->where('date_fin', null)
                ->orWhere('date_fin >', $datetime)
            ->groupEnd()
            ->orderBy('date_ajout', 'DESC')
            ->first();
    }

    public function createBaremeFrais(int $typeOperationId, float $montantMin, float $montantMax, float $montantFrais)
    {
        return $this->baremeFraisModel->insert([
            'type_operation_id' => $typeOperationId,
            'montant_min'       => $montantMin,
            'montant_max'       => $montantMax,
            'montant_frais'     => $montantFrais,
            'date_ajout'        => (new DateTime())->format('Y-m-d H:i:s'),
            'date_fin'          => null,
        ]);
    }

    public function hasActiveOverlap(int $typeOperationId, float $min, float $max, ?int $ignoreId = null): bool
    {
        $builder = $this->baremeFraisModel
            ->where('type_operation_id', $typeOperationId)
            ->where('date_fin', null)
            ->where('montant_min <=', $max)
            ->where('montant_max >=', $min);

        if ($ignoreId !== null) {
            $builder->where('id !=', $ignoreId);
        }

        return $builder->countAllResults() > 0;
    }

    /**
     * Historisation : clôture l'enregistrement actuel via date_fin,
     * puis insère une nouvelle ligne active avec les nouvelles valeurs.
     */
    public function replaceBareme(int $id, int $typeOperationId, float $min, float $max, float $frais): bool
    {
        $db = db_connect();
        $db->transBegin();

        try {
            $now = (new DateTime())->format('Y-m-d H:i:s');

            $updated = $this->baremeFraisModel->update($id, ['date_fin' => $now]);
            if (! $updated) {
                throw new \RuntimeException('Impossible de clôturer le barème.');
            }

            $inserted = $this->baremeFraisModel->insert([
                'type_operation_id' => $typeOperationId,
                'montant_min'       => $min,
                'montant_max'       => $max,
                'montant_frais'     => $frais,
                'date_ajout'        => $now,
                'date_fin'          => null,
            ]);

            if ($inserted === false) {
                throw new \RuntimeException('Impossible de créer le nouveau barème.');
            }

            $db->transCommit();
            return true;
        } catch (Throwable $e) {
            $db->transRollback();
            log_message('error', 'Erreur de remplacement du barème : {message}', ['message' => $e->getMessage()]);
            return false;
        }
    }
}
