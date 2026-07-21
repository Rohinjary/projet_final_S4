<?php

namespace App\Models;

use CodeIgniter\Model;
use DateTime;

class OperationModel extends Model
{
    protected $table         = 'operation';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'client_numero',
        'type_operation_id',
        'destinataire_numero',
        'montant',
        'frais',
        'frais_retrait',
        'commission_operateur',
        'reference_transfert',
        'nb_destinataires',
        'date_operation',
    ];
    protected $useTimestamps = false;

    public function enregistrer(
        string $clientNumero,
        int $typeOperationId,
        float $montant,
        float $frais,
        ?string $destinataireNumero = null,
        float $fraisRetrait = 0.0,
        ?string $referenceTransfert = null,
        int $nbDestinataires = 1,
        float $commissionOperateur = 0.0
    ) {
        return $this->insert([
            'client_numero'        => $clientNumero,
            'type_operation_id'    => $typeOperationId,
            'destinataire_numero'  => $destinataireNumero,
            'montant'              => $montant,
            'frais'                => $frais,
            'frais_retrait'        => $fraisRetrait,
            'commission_operateur'  => $commissionOperateur,
            'reference_transfert'  => $referenceTransfert,
            'nb_destinataires'     => $nbDestinataires,
            'date_operation'       => (new DateTime())->format('Y-m-d H:i:s'),
        ]);
    }

    // Calcule le solde en repartant de zero a partir de toutes les operations.
    // depot        -> +montant (client)          / -frais (client)
    // retrait      -> -(montant + frais) (client)
    // transfert    -> -(montant + frais + frais_retrait + commission_operateur) (client emetteur)
    //                / +(montant + frais_retrait) (destinataire si le frais de retrait est inclus)
    public function calculerSolde(string $numero): float
    {
        $db = $this->db;

        $entrees = $db->query("
            SELECT COALESCE(SUM(
                CASE
                    WHEN t.libelle = 'depot' AND o.client_numero = ? THEN o.montant
                    WHEN t.libelle = 'transfert' AND o.destinataire_numero = ? THEN o.montant + COALESCE(o.frais_retrait, 0)
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
                    WHEN t.libelle = 'retrait' THEN o.montant + o.frais
                    WHEN t.libelle = 'transfert' THEN o.montant + o.frais + COALESCE(o.frais_retrait, 0) + COALESCE(o.commission_operateur, 0)
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
            ->orderBy('o.date_operation', 'DESC')
            ->orderBy('o.id', 'DESC');

        if ($typeOperationId !== null) {
            $builder->where('o.type_operation_id', $typeOperationId);
        }

        $rows = $builder->get()->getResultArray();

        $historique = [];
        $groupesTransfert = [];

        foreach ($rows as $row) {
            $row['montant']          = (float) $row['montant'];
            $row['frais']            = (float) $row['frais'];
            $row['frais_retrait']    = (float) ($row['frais_retrait'] ?? 0);
            $row['commission_operateur'] = (float) ($row['commission_operateur'] ?? 0);
            $row['nb_destinataires'] = (int) ($row['nb_destinataires'] ?? 1);
            $row['reference_transfert'] = $row['reference_transfert'] ?? null;
            $row['destinataires_affiches'] = '';

            $row['est_entree'] = $row['type_libelle'] === 'depot'
                || ($row['type_libelle'] === 'transfert' && $row['destinataire_numero'] === $numero);
            $row['est_sortie'] = ! $row['est_entree'];

            if ($row['type_libelle'] !== 'transfert' || empty($row['reference_transfert'])) {
                if (! empty($row['destinataire_numero'])) {
                    $row['destinataires_affiches'] = (string) $row['destinataire_numero'];
                }

                $historique[] = $row;
                continue;
            }

            $reference = (string) $row['reference_transfert'];

            if (! isset($groupesTransfert[$reference])) {
                $historique[] = $row;
                $index = array_key_last($historique);

                $historique[$index]['montant'] = 0.0;
                $historique[$index]['frais'] = 0.0;
                $historique[$index]['frais_retrait'] = 0.0;
                $historique[$index]['commission_operateur'] = 0.0;
                $historique[$index]['nb_destinataires'] = 0;
                $historique[$index]['destinataires_liste'] = [];
                $historique[$index]['destinataires_affiches'] = '';

                $groupesTransfert[$reference] = $index;
            }

            $index = $groupesTransfert[$reference];
            $historique[$index]['montant'] += (float) $row['montant'];
            $historique[$index]['frais'] += (float) $row['frais'];
            $historique[$index]['frais_retrait'] += (float) ($row['frais_retrait'] ?? 0);
            $historique[$index]['commission_operateur'] += (float) ($row['commission_operateur'] ?? 0);
            $historique[$index]['nb_destinataires'] = max(
                (int) $historique[$index]['nb_destinataires'],
                (int) ($row['nb_destinataires'] ?? 1)
            );
            $historique[$index]['est_entree'] = ! empty($historique[$index]['est_entree']) || ! empty($row['est_entree']);
            $historique[$index]['est_sortie'] = ! empty($historique[$index]['est_sortie']) || ! empty($row['est_sortie']);

            if (! empty($row['destinataire_numero']) && ! in_array($row['destinataire_numero'], $historique[$index]['destinataires_liste'], true)) {
                $historique[$index]['destinataires_liste'][] = $row['destinataire_numero'];
            }

            $historique[$index]['destinataires_affiches'] = implode(', ', $historique[$index]['destinataires_liste']);
        }

        foreach ($historique as &$item) {
            if (! isset($item['destinataires_liste'])) {
                $item['destinataires_liste'] = [];
            }
            if (! isset($item['destinataires_affiches'])) {
                $item['destinataires_affiches'] = '';
            }
        }
        unset($item);

        return $historique;
    }
}