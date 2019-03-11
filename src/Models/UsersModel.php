<?php

namespace Itval\src\Models;

use Itval\core\DAO\Tables;

/**
 * Class UsersModel
 *
 * @package Itval\src\Models
 * @author  Nicolas Buffart <concepteur-developpeur@nicolas-buffart.fr>
 */
class UsersModel extends Tables
{

    /**
     * Identifiant de l'utilisateur
     *
     * @var int
     */
    public $id;

    /**
     * Nom d'utilisateur
     *
     * @var string
     */
    public $username;

    /**
     * Nom de famille
     *
     * @var string
     */
    public $nom;

    /**
     * Prénom
     *
     * @var string
     */
    public $prenom;

    /**
     * Adresse email
     *
     * @var string
     */
    public $email;

    /**
     * Mot de passe
     *
     * @var string
     */
    public $password;

    /**
     * Token de confirmation envoyé par email pour confirmation d'inscription
     *
     * @var string|null
     */
    public $confirmation_token;

    /**
     * Statut de l'utilisateur après inscription
     *
     * @var int
     */
    public $confirmed;

    /**
     * Date d'enregistrement de l'utilisateur
     *
     * @var string
     */
    public $registered_at;

    /**
     * Token de réinitalisation de mot de passe
     *
     * @var string|null
     */
    public $reset_password_token;

    /**
     * Date de demande de réinitalisation de mot de passe
     *
     * @var string|null
     */
    public $reseted_at;

    public function __construct(\PDO $pdo = null)
    {
        parent::__construct($pdo);
    }

    /**
     * Setter confirmation token
     *
     * @param  string|null $value
     * @return UsersModel
     */
    public function setConfirmationToken(string $value = null): self
    {
        $this->confirmation_token = $value ?? random_token();
        return $this;
    }

    /**
     * Setter registered_at
     *
     * @return UsersModel
     */
    public function setRegisteredAt(): self
    {
        $this->registered_at = date("Y-m-d H:i:s");
        return $this;
    }

    /**
     * Setter $reseted_at
     *
     * @return UsersModel
     */
    public function setResetedAt(): self
    {
        $this->reseted_at = date("Y-m-d H:i:s");
        return $this;
    }

    /**
     * Sauvegarde un utilisateur dans la base de données
     *
     * @return int
     */
    public function save(): int
    {
        return $this->add(
            [
                    'fields' =>
                    [
                        'nom',
                        'prenom',
                        'email',
                        'username',
                        'password',
                        'confirmation_token',
                        'confirmed',
                        'registered_at',
                        'reset_password_token',
                        'reseted_at'
                    ]
                ],
            [
                    'nom' => $this->nom,
                    'prenom' => $this->prenom,
                    'email' => $this->email,
                    'username' => $this->username,
                    'password' => $this->password,
                    'confirmation_token' => $this->confirmation_token,
                    'confirmed' => $this->confirmed,
                    'registered_at' => $this->registered_at,
                    'reset_password_token' => null,
                    'reseted_at' => null
                ]
        );
    }

    /**
     * Update un utilisateur dans la base de données
     *
     * @return int
     */
    public function update(): int
    {
        return $this->amend(
            [
                    'fields' =>
                    [
                        "nom = :nom",
                        "prenom = :prenom",
                        "email = :email",
                        "username = :username",
                        "confirmation_token = :confirmation_token",
                        "confirmed = :confirmed"
                    ],
                    'conditions' => 'id = ' . $this->id
                ],
            [
                    'nom' => $this->nom,
                    'prenom' => $this->prenom,
                    'email' => $this->email,
                    'username' => $this->username,
                    'confirmation_token' => $this->confirmation_token,
                    'confirmed' => $this->confirmed,
                ]
        );
    }

    /**
     * Update le profil de l'utilisateur dans la base de données
     *
     * @return int
     */
    public function profilUpdate(): int
    {
        $args = [
            'fields' =>
            [
                "nom = :nom",
                "prenom = :prenom",
                "email = :email",
                "username = :username"
            ],
            'conditions' => 'id = ' . $this->id
        ];
        $values = [
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'email' => $this->email,
            'username' => $this->username,
        ];
        return $this->amend($args, $values);
    }

    /**
     * Update le mot de passe d'un utilisateur
     *
     * @return int
     */
    public function updatePassword(): int
    {
        $args = [
            'fields' =>
            [
                "password = :password"
            ],
            'conditions' => 'id = ' . $this->id
        ];
        $values = [
            'password' => $this->password
        ];
        return $this->amend($args, $values);
    }

    /**
     * Fonction de confirmation de compte
     *
     * @return int
     */
    public function confirmation(): int
    {
        $values = [
            'fields' =>
            [
                "confirmation_token = NULL",
                "confirmed = '1'"
            ],
            'conditions' => 'id = ' . $this->id
        ];
        return $this->amend($values);
    }

    /**
     * Fonction qui ajoute le token et la date de demande de réinitialisation de mot de passe
     *
     * @return int
     */
    public function updateResetPassword(): int
    {
        $values = [
            'fields' =>
            [
                "reset_password_token = '{$this->reset_password_token}'",
                "reseted_at = '{$this->reseted_at}'"
            ],
            'conditions' => 'id = ' . $this->id
        ];
        return $this->amend($values);
    }

    /**
     * Fonction qui permet de mettre à jour le mot de passe et de supprimer le reset_password_token pour débloquer le compte
     *
     * @return int
     */
    public function resetPassword(): int
    {
        $values = [
            'fields' =>
            [
                "password = '{$this->password}'",
                "reset_password_token = NULL",
            ],
            'conditions' => 'id = ' . $this->id
        ];
        return $this->amend($values);
    }
}
