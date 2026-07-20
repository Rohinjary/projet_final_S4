<?php

namespace App\Models;

use CodeIgniter\Model;
use DateTime;

class OperationModel extends Model
{
    protected $table         = 'operation';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['client_numero', 'type_operation_id', 'destinataire_numero', 'montant', 'frais', 'date_operation'];
    protected $useTimestamps = false;

    public function enregistrer(
        string $clientNumero,
        int $typeOperationId,
        float $montant,
        float $frais,
        ?string $destinataireNumero = null
    ) {
        return $this->insert([
            'client_numero'        => $clientNumero,
            'type_operation_id'    => $typeOperationId,
            'destinataire_numero'  => $destinataireNumero,
            'montant'              => $montant,
            'frais'                => $frais,
            'date_operation'       => (new DateTime())->format('Y-m-d H:i:s'),
        ]);
    }

    // Calcule le solde en repartant de zero a partir de toutes les operations.
    // depot        -> +montant (client)          / -frais (client)
    // retrait      -> -(montant + frais) (client)
    // transfert    -> -(montant + frais) (client emetteur) / +montant (destinataire)
    public function calculerSolde(string $numero): float
    {
        $db = $this->db;

        $entrees = $db->query("
            SELECT COALESCE(SUM(
                CASE
                    WHEN t.libelle = 'depot' AND o.client_numero = ? THEN o.montant
                    WHEN t.libelle = 'transfert' AND o.destinataire_numero = ? THEN o.montant
                    ELSE 0
                END
            ), 0) AS total
            FROM operation o
            JOIN type_operation t ON t.id = o.type_operation_id
            WHERE o.client_numero = ? OR o.destinataire_numero = ?
        ", [$numero, $numero, $numero, $numero])->getRow()->total;

        $sorties = $db->query("
            SELECT COALESCE(SUM(
                CASE
                    WHEN t.libelle = 'depot' THEN o.frais
                    WHEN t.libelle IN ('retrait', 'transfert') THEN o.montant + o.frais
                    ELSE 0
                END
            ), 0) AS total
            FROM operation o
            JOIN type_operation t ON t.id = o.type_operation_id
            WHERE o.client_numero = ?
        ", [$numero])->getRow()->total;

        return (float) $entrees - (float) $sorties;
    }

    public function getHistorique(string $numero, ?int $typeOperationId = null): array
    {
        $builder = $this->db->table('operation o')
            ->select('o.*, t.libelle AS type_libelle')
            ->join('type_operation t', 't.id = o.type_operation_id')
            ->groupStart()
                ->where('o.client_numero', $numero)
                ->orWhere('o.destinataire_numero', $numero)
            ->groupEnd()
            ->orderBy('o.date_operation', 'DESC');

        if ($typeOperationId !== null) {
            $builder->where('o.type_operation_id', $typeOperationId);
        }

        return $builder->get()->getResultArray();
    }
}