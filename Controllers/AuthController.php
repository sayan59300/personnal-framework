<?php

namespace Itval\Controllers;

use DateTime;
use Itval\core\DAO\Exception\QueryException;
use Slim\Http\Request;
use Slim\Http\Response;
use Itval\core\Classes\FormBuilder;
use Itval\core\Classes\Session;
use Itval\core\Classes\Validator;
use Itval\core\Factories\LoggerFactory;
use Itval\src\Models\UsersModel;

/**
 * Class AuthController Controlleur d'authentification
 *
 * @author Nicolas Buffart <concepteur-developpeur@nicolas-buffart.fr>
 */
class AuthController extends Controller
{

    /**
     * Rend la vue de connexion execute la fonction de connexion
     *
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function index(Request $request, Response $response, $args): Response
    {
        if (isAuthenticated()) {
            error('Vous êtes déja connecté');
            return redirect('/profil');
        }
        $this->setToken();
        $this->set('title', 'Connexion');
        $this->set('description', 'Page de connexion');
        return $this->render('index');
    }

    /**
     * Traite les données et execute le procédure de connexion
     *
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     * @throws QueryException
     */
    public function connexion(Request $request, Response $response, $args): Response
    {
        $posted = $this->getPost();
        if (!isValidToken()) {
            $this->emitter->emit('token.rejected');
            error('Token invalide');
            return redirect();
        }
        $username = $posted['username'];
        $password = $posted['password'];
        $user = new UsersModel;
        /** @var UsersModel $result */
        $result = current($user->find(
            ['fields' => ['id', 'username', 'nom', 'prenom', 'email', 'confirmed', 'registered_at', 'password', 'reset_password_token'],
                    'conditions' => "username = :username"],
            [':username' => $username]
        ));
        if (!$result || password_verify($password, $result->password) === false) {
            Session::delete('auth');
            error('Connexion impossible, mauvais nom d\'utilisateur ou mauvais mot de passe');
            return redirect('/auth');
        }
        if ($result->confirmed !== '1') {
            LoggerFactory::getInstance('security')->addWarning(
                'Tentative de connexion avec un compte non validé',
                ['username' => $result->username, 'email' => $result->email]
            );
            error(
                'Votre compte n\'a pas encore été validé, un email a été envoyé lors de votre inscription avec un lien de 
                confirmation, vous ne pourrez pas vous connecter avant d\'avoir terminé la procédure de validation, en cas 
                d\'impossibilité de valider votre compte veuillez contacter l\'administrateur du site'
            );
            return redirect();
        }
        if (!is_null($result->reset_password_token)) {
            LoggerFactory::getInstance('security')->addWarning(
                'Tentative de connexion à un compte qui a une procédure de réinitialisation en cours',
                ['username' => $result->username, 'email' => $result->email]
            );
            error(
                'Une procédure de réinitialisation de mot de passe est en cours, votre compte a été bloqué '
                . 'le temps que finalisiez la procédure, en cas de problème réitérez l\'opération ou contactez '
                . 'l\'administrateur du site'
            );
            return redirect();
        }
        Session::set('auth', new \stdClass());
        Session::add('auth', 'statut', 1);
        Session::add('auth', 'id', $result->id);
        Session::add('auth', 'username', $result->username);
        Session::add('auth', 'nom', $result->nom);
        Session::add('auth', 'prenom', $result->prenom);
        Session::add('auth', 'confirmed', $result->confirmed);
        Session::add('auth', 'registered_at', date_create($result->registered_at));
        LoggerFactory::getInstance('security')->addInfo('Connexion utilisateur', ['username' => $result->username]);
        Session::delete('erreur_login');
        return redirect();
    }

    /**
     * Rend la vue d'enregistrement d'un nouveau compte utilisateur
     *
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function register(Request $request, Response $response, $args): Response
    {
        if (isAuthenticated()) {
            error('Vous êtes déjà connecté, veuillez vous déconnecter pour faire une nouvelle inscription sur le site');
            return redirect();
        }
        $this->set('title', 'Enregistrement');
        $this->set('description', 'Page d\'enregistrement permettant de créer un compte');
        $this->set('registrationForm', $this->getRegisterForm());
        return $this->render('registration');
    }

    /**
     * Traite les données et execute la procédure d'enregistrement
     *
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     * @throws QueryException
     */
    public function registration(Request $request, Response $response, $args): Response
    {
        if (!isValidToken()) {
            $this->emitter->emit('token.rejected');
            error('Token invalide');
            return redirect();
        }
        $posted = $this->getPost();
        $values = [
            'nom' => $posted['nom'],
            'prenom' => $posted['prenom'],
            'email' => $posted['email'],
            'confirm_email' => $posted['confirm_email'],
            'username' => $posted['username'],
            'password' => $posted['password'],
            'confirm_password' => $posted['confirm_password']
        ];
        $validator = new Validator($values);
        $validator->isValidString('nom', DENOMINATION, true);
        $validator->isValidString('prenom', DENOMINATION, true);
        $validator->isValidEmail('email', true, 'confirm_email');
        $validator->isValidString('username', USERNAME, true);
        $validator->isAvailable(UsersModel::class, 'username');
        $validator->isAvailable(UsersModel::class, 'email');
        $validator->isValidString('password', PASSWORD, true, 'confirm_password', 8, 72);
        if ($validator->getErrors() === 0) {
            $this->resetValuesSession($values);
            $user = new UsersModel;
            $user->nom = $values['nom'];
            $user->prenom = $values['prenom'];
            $user->email = $values['email'];
            $user->username = $values['username'];
            $user->password = encrypted($values['password']);
            $user->confirmed = 0;
            $user->setConfirmationToken()->setRegisteredAt();
            $user->save();
            $this->emitter->emit('user.registered', [$user]);
            return redirect();
        }
        $this->setValuesSession($values);
        return redirect('/registration');
    }

    /**
     * Génère le formulaire d'inscription
     *
     * @return FormBuilder
     */
    private function getRegisterForm(): FormBuilder
    {
        $form = new FormBuilder('subscribeForm', 'post', getUrl('registration'));
        $form->setCsrfInput($this->setToken())
            ->setInput('text', 'nom', ['id' => 'nom', 'value' => printSession('nom')], 'Nom')
            ->setInput('text', 'prenom', ['id' => 'prenom', 'value' => printSession('prenom')], 'Prénom')
            ->setInput('email', 'email', ['id' => 'email', 'value' => printSession('email')], 'Email')
            ->setInput('email', 'confirm_email', ['id' => 'confirm_email', 'autocomplete' => 'off'], 'Confirmation email')
            ->setInput('text', 'username', ['id' => 'username', 'value' => printSession('username')], 'Nom d\'utilisateur')
            ->setInput('password', 'password', ['id' => 'password'], 'Mot de passe')
            ->setInput('password', 'confirm_password', ['id' => 'confirm_password'], 'Confirmation mot de passe')
            ->setButton('submit', 'inscription', 'Valider votre inscription', ['class' => 'btn btn-primary']);
        return $form;
    }

    /**
     * Fonction de confirmation de compte utilisateur
     *
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     * @throws QueryException
     */
    public function confirmation(Request $request, Response $response, $args): Response
    {
        $getValues = $this->getQuery();
        if (!isset($getValues['username']) || !isset($getValues['token'])) {
            return error404($response);
        }
        $username = htmlentities($getValues['username']);
        $token = htmlentities($getValues['token']);
        $model = new UsersModel();
        /** @var UsersModel $user */
        $user = current($model->find(['conditions' => "username = '$username' AND confirmation_token = '$token'"]));
        if (!$user) {
            return error404($response);
        }
        $user->confirmation();
        $this->set('title', 'Confirmation');
        $this->set('description', 'Confirmation de création de votre compte');
        return $this->render('confirmed');
    }

    /**
     * Rend le vue profil de l'utilisateur connecté
     *
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     * @throws QueryException
     */
    public function profil(Request $request, Response $response, $args): Response
    {
        if (!isAuthenticated()) {
            LoggerFactory::getInstance('security')->addWarning(
                'Tentative d\'accès à la page profil'
            );
            error('Vous devez être connecté pour accéder à votre profil');
            return redirect();
        }
        $model = new UsersModel;
        /** @var UsersModel $user */
        $user = current(
            $model->find(
                ['conditions' => 'id = :id', 'fields' => ['id', 'email', 'nom', 'prenom', 'username', 'registered_at', 'confirmed']],
                [':id' => currentUser()->id]
            )
        );
        $this->set('profilForm', $this->getProfilForm($user));
        $this->set('registerAt', new DateTime($user->registered_at));
        $this->set('title', 'Profil');
        $this->set('description', 'Profil de l\'utilisateur connecté');
        return $this->render('profil');
    }

    /**
     * Traite les données et met à jour le profil
     *
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     * @throws QueryException
     */
    public function updateProfil(Request $request, Response $response, $args): Response
    {
        if (!isValidToken()) {
            $this->emitter->emit('token.rejected', [['username' => Session::read('auth')->username ?? 'Anonymous']]);
            error('Token invalide');
            return redirect('/profil');
        }
        $posted = $this->getPost();
        $values = [
            'id' => currentUser()->id,
            'nom' => $posted['nom'],
            'prenom' => $posted['prenom'],
            'email' => $posted['email'],
            'username' => $posted['username']
        ];
        $validator = new Validator($values);
        $validator->isValidString('nom', DENOMINATION, true);
        $validator->isValidString('prenom', DENOMINATION, true);
        $validator->isValidEmail('email', true);
        $validator->isValidString('username', USERNAME, true);
        $validator->isAvailable(UsersModel::class, 'username');
        $validator->isAvailable(UsersModel::class, 'email');
        if ($validator->getErrors() === 0) {
            $user = new UsersModel;
            $user->id = $values['id'];
            $user->nom = $values['nom'];
            $user->prenom = $values['prenom'];
            $user->email = $values['email'];
            $user->username = $values['username'];
            $user->profilUpdate();
            LoggerFactory::getInstance('users')->addInfo('Modification de profil', ['username' => $values['username']]);
            success('Votre profil a été mis à jour avec succès');
            return redirect('/profil');
        }
        return redirect('/profil');
    }

    /**
     * Rend la vue de réinitialisation de mot de passe
     *
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function vueResetPassword(Request $request, Response $response, $args): Response
    {
        if (isAuthenticated()) {
            return redirect('/profil');
        }
        $this->setToken();
        $this->set('title', 'Réinitialisation de votre mot de passe');
        $this->set('description', 'Page de demande de réinitialisation de votre mot de passe');
        return $this->render('reset_password');
    }

    /**
     * Permet de réinitialiser le mot de passe
     *
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     * @throws QueryException
     */
    public function sendResetPasswordEmail(Request $request, Response $response, $args): Response
    {
        if (!isValidToken()) {
            $this->emitter->emit('token.rejected');
            error('Token invalide');
            return redirect();
        }
        $email = $this->getPost()['email'];
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            error("Veuillez entrer un email valide");
            return redirect('/reset-password');
        }
        $model = new UsersModel();
        /** @var UsersModel $user */
        $user = current($model->find(['conditions' => 'email = :email'], ['email' => $email]));
        if (!$user) {
            error("Aucun compte ne correspont à cette email");
            return redirect('/reset-password');
        }
        $user->reset_password_token = random_token();
        $user->setResetedAt();
        $user->updateResetPassword();
        $this->emitter->emit('user.resetPassword', [$user]);
        return redirect();
    }

    /**
     * Rend la vue de confirmation de réinitialisation du mot de passe
     *
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     * @throws QueryException
     */
    public function resetPasswordConfirmation(Request $request, Response $response, $args): Response
    {
        $params = $this->getQuery();
        if (isAuthenticated()) {
            LoggerFactory::getInstance('security')->addWarning(
                'Tentative d\'accès à la page de réinitialisation de mot de passe avec un utilisateur connecté',
                ['username' => currentUser()->username]
            );
            error('Vous ne pouvez pas accéder à cette page si vous êtes connecté');
            return redirect();
        }
        if ($request->isPut()) {
            $posted = $this->getPost();
            $values = [
                'password' => $posted['password'],
                'password_confirmation' => $posted['password_confirmation']
            ];
            $validator = new Validator($values);
            $validator->isValidString('password', PASSWORD, true, 'password_confirmation', 8, 72);
            if ($validator->getErrors() === 0) {
                $model = new UsersModel();
                /** @var UsersModel $user */
                $user = current($model->find(
                    ['conditions' => 'id = :id AND reset_password_token = :reset_token'],
                    ['id' => $params['id'], 'reset_token' => $params['reset_token']]
                ));
                if (!$user) {
                    LoggerFactory::getInstance('database')->addWarning(
                        'Erreur lors de la recherche d\'un utilisateur lors d\'une procédure de réinitialisation de mot de passe',
                        ['id' => $params['id'], 'reset_token' => $params['reset_token']]
                    );
                    error('impossible de trouver l\'utilisateur correspondant');
                    return redirect();
                }
                $user->password = encrypted($values['password']);
                $user->resetPassword();
                success("Votre mot de passe à bien été mis à jour");
                return redirect();
            }
            return redirect('/reset-password-confirmation?id=' . $params['id'] . '&reset_token=' . $params['reset_token']);
        }
        if (!isset($params['id']) || !isset($params['reset_token'])) {
            return error404($response);
        }
        $model = new UsersModel();
        /** @var UsersModel $user */
        $user = current($model->find(
            ['conditions' => 'id = :id AND reset_password_token = :reset_token'],
            ['id' => $params['id'], 'reset_token' => $params['reset_token']]
        ));
        if (!$user) {
            LoggerFactory::getInstance('security')->addWarning(
                'Tentative d\'accès à la page de réinitialisation de mot de passe avec un lien invalide',
                ['id' => $params['id']]
            );
            error('Le lien de réinitialisation est invalide');
            return redirect();
        }
        $form = new FormBuilder(
            'reset_password_form',
            'put',
            getUrl('reset-password-confirmation?id=' . $params['id'] . '&reset_token=' . $params['reset_token'])
        );
        $form->setCsrfInput($this->setToken())
            ->setInput('password', 'password', [], 'Nouveau mot de passe (entre 8 et 72 caractères)')
            ->setInput('password', 'password_confirmation', [], 'Confirmer le nouveau mot de passe')
            ->setButton('submit', 'reset_password', 'Modifier votre mot de passe', ['class' => 'btn btn-primary']);
        $this->set('title', 'Réinitialisation de votre mot de passe');
        $this->set('description', 'Page de confirmation de la réinitialisation de votre mot de passe');
        $this->set('reset_password_form', $form);
        return $this->render('reset_password_confirmation');
    }

    /**
     * Génère le formulaire du profil
     *
     * @param  UsersModel $user
     * @return FormBuilder
     */
    public function getProfilForm(UsersModel $user): FormBuilder
    {
        $form = new FormBuilder('profilForm', 'put', getUrl('profil'));
        $form->setCsrfInput($this->setToken())
            ->setInput('text', 'nom', ['id' => 'nom', 'value' => $user->nom], 'Votre nom')
            ->setInput('text', 'prenom', ['id' => 'prenom', 'value' => $user->prenom], 'Votre prénom')
            ->setInput('email', 'email', ['id' => 'email', 'value' => $user->email], 'Votre email')
            ->setInput('text', 'username', ['id' => 'username', 'value' => $user->username], 'Votre nom d\'utilisateur')
            ->setButton('submit', 'update', 'Mettre à jour votre profil', ['class' => 'btn btn-primary']);
        return $form;
    }

    /**
     * Rend le vue de modification du mot de passe de l'utilisateur connecté
     *
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function updatePassword(Request $request, Response $response, $args): Response
    {
        if (!isAuthenticated()) {
            LoggerFactory::getInstance('security')->addWarning(
                'Tentative d\'accès à la page de modification de mot de passe'
            );
            error('Vous devez être connecté pour accéder à la page de modification de mot de passe');
            return redirect();
        }
        $form = new FormBuilder('updatePasswordForm', 'put', '');
        $form->setCsrfInput($this->setToken())
            ->setInput('password', 'old_password', ['id' => 'old_password'], 'Ancien mot de passe')
            ->setInput('password', 'password', ['id' => 'password'], 'Nouveau mot de passe (entre 8 et 72 caractères)')
            ->setInput('password', 'confirm_password', ['id' => 'confirm_password'], 'Confirmation nouveau mot de passe')
            ->setButton('submit', 'update_password', 'Mettre à jour votre mot de passe', ['class' => 'btn btn-primary']);
        $this->set('updatePasswordForm', $form);
        $this->set('title', 'Modification du mot de passe');
        $this->set('description', 'Page de modification de votre mot de passe');
        return $this->render('update_password');
    }

    /**
     * Traite les données et met à jours le mot de passe
     *
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     * @throws QueryException
     */
    public function passwordUpdate(Request $request, Response $response, $args): Response
    {
        if (!isValidToken()) {
            $this->emitter->emit('token.rejected', [['username' => Session::read('auth')->username ?? 'Anonymous']]);
            error('Token invalide');
            return redirect('/update-password');
        }
        $posted = $this->getPost();
        $values = [
            "id" => currentUser()->id,
            "old_password" => $posted['old_password'],
            "password" => $posted['password'],
            "confirm_password" => $posted['confirm_password']
        ];
        $model = new UsersModel;
        /** @var UsersModel $user */
        $user = current(
            $model->find(
                ['conditions' => 'id = :id', 'fields' => ['id', 'password', 'username', 'email']],
                [':id' => $values['id']]
            )
        );
        $validator = new Validator($values);
        $validator->required('old_password');
        $validator->isValidString('password', PASSWORD, true, 'confirm_password', 8, 72);
        if (!empty($posted['old_password']) && !password_verify($posted['old_password'], $user->password)) {
            $validator->setError('old_password', "Le mot de passe actuel est invalide");
        }
        if ($validator->getErrors() === 0) {
            $user->password = encrypted($posted['password']);
            $user->updatePassword();
            LoggerFactory::getInstance('users')->addInfo(
                'Modification du mot de passe',
                ['username' => $user->username]
            );
            $this->emitter->emit('userPassword.updated', [$user->username, $user->email]);
            return redirect();
        }
        return redirect('/update-password');
    }

    /**
     * Fonction de déconnexion
     *
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function deconnexion(Request $request, Response $response, $args): Response
    {
        if (!isAuthenticated()) {
            return redirect();
        }
        $this->setToken();
        $this->set('title', 'Déconnexion');
        $this->set('description', 'Page de déconnexion');
        return $this->render('logout');
    }

    /**
     * Déconnecte l'utilisateur connecté
     *
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function logout(Request $request, Response $response, $args): Response
    {
        if (isValidToken()) {
            session_unset();
            session_destroy();
            return redirect();
        }
        $this->emitter->emit('token.rejected');
        error('Token invalide');
        return redirect();
    }
}
