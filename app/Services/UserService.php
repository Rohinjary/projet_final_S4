<?php

namespace App\Services;

use App\Models\OperateurModel;
use App\Models\UserModel;

class UserService
{
    private UserModel $userModel;
    private OperateurModel $operateurModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->operateurModel = new OperateurModel();
    }

    /**
     * Authentifie un compte opérateur et retourne l'utilisateur avec son opérateur.
     * Une ancienne base sans operateur_id est réparée automatiquement au premier login.
     */
    public function authenticate(string $password): ?array
    {
        foreach ($this->userModel->orderBy('id', 'ASC')->findAll() as $user) {
            if (! isset($user['password']) || ! password_verify($password, (string) $user['password'])) {
                continue;
            }

            $operateur = null;
            if (! empty($user['operateur_id'])) {
                $operateur = $this->operateurModel->find((int) $user['operateur_id']);
            }

            if ($operateur === null) {
                $operateur = $this->operateurModel
                    ->where('est_principal', 1)
                    ->orderBy('id', 'ASC')
                    ->first();
            }

            if ($operateur === null) {
                $id = $this->operateurModel->insert([
                    'nom'           => 'MobiPay',
                    'est_principal' => 1,
                    'date_ajout'    => date('Y-m-d H:i:s'),
                ], true);
                $operateur = $this->operateurModel->find((int) $id);
            }

            if ($operateur !== null && (int) ($user['operateur_id'] ?? 0) !== (int) $operateur['id']) {
                $this->userModel->update((int) $user['id'], ['operateur_id' => (int) $operateur['id']]);
                $user['operateur_id'] = (int) $operateur['id'];
            }

            return [
                'user'      => $user,
                'operateur' => $operateur,
            ];
        }

        return null;
    }

    public function verifyUser(string $password): bool
    {
        return $this->authenticate($password) !== null;
    }
}
