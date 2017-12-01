<?php

namespace Itval\Controllers;

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
        if ($this->getRequest()->isGet() && !isAuthenticated()) {
            $this->setToken();
            $this->set('title', 'Connexion');
            $this->set('description', 'Page de connexion');
            return $this->render('index');
        }
    }

    /**
     * Traite les données et execute le procédure de connexion
     *
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function connexion(Request $request, Response $response, $args)
    {
        $posted = $this->getPost();
        if (!isValidToken()) {
            $this->emitter->emit('token.rejected');
            error('Token invalide');
            return redirect();
        } else {
            $username = htmlentities($posted['username']);
            $password = htmlentities(encrypted($posted['password']));
            $user = new UsersModel;
            $result = current($user->find(
                ['fields' => ['id', 'username', 'nom', 'prenom', 'email', 'confirmed', 'registered_at'],
                    'conditions' => "username = :username AND password = :password"],
                [':username' => $username, ':password' => $password]
            ));
            if (!$result) {
                Session::delete('auth');
                error('Connexion impossible, mauvais login ou mauvais mot de passe');
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
            Session::set('auth', new \stdClass());
            Session::add('auth', 'statut', 1);
            Session::add('auth', 'id', $result->id);
            Session::add('auth', 'username', $result->username);
            Session::add('auth', 'nom', $result->nom, 'nom');
            Session::add('auth', 'prenom', $result->prenom);
            Session::add('auth', 'confirmed', $result->confirmed);
            Session::add('auth', 'registered_at', date_create($result->registered_at));
            LoggerFactory::getInstance('security')->addInfo('Connexion utilisateur', ['username' => $result->username]);
            Session::delete('erreur_login');
            return redirect();
        }
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
        if (!isset($this->getPost()['inscription'])) {
            $this->set('title', 'Enregistrement');
            $this->set('description', 'Page d\'enregistrement permettant de créer un compte');
            $this->set('registrationForm', $this->getRegisterForm());
            return $this->render('registration');
        }
    }

    /**
     * Traite les données et execute la procédure d'enregistrement
     *
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
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
        $validator->isValidString('nom', ALPHABETIC, true);
        $validator->isValidString('prenom', ALPHABETIC, true);
        $validator->isValidEmail('email', true, 'confirm_email');
        $validator->isValidString('username', USERNAME, true);
        $validator->isAvailable(UsersModel::class, 'username');
        $validator->isValidString('password', PASSWORD, true, 'confirm_password');
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
     */
    public function confirmation(Request $request, Response $response, $args): Response
    {
        $getValues = $this->getQuery();
        if (!isset($getValues['username']) && !isset($getValues['token'])) {
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
     */
    public function profil(Request $request, Response $response, $args): Response
    {
        if (!isAuthenticated()) {
            LoggerFactory::getInstance('security')->addWarning(
                'Tentative d\'accès à la page '
                . 'profil'
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
        $this->set('registerAt', new \DateTime($user->registered_at));
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
        $validator->isValidString('nom', ALPHABETIC, true);
        $validator->isValidString('prenom', ALPHABETIC, true);
        $validator->isValidEmail('email', true);
        $validator->isValidString('username', USERNAME, true);
        $validator->isAvailable(UsersModel::class, 'username');
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
     * Génère le formulaire du profil
     *
     * @param  $user
     * @return FormBuilder
     */
    public function getProfilForm($user): FormBuilder
    {
        $form = new FormBuilder('profilForm', 'post', getUrl('profil'));
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
        $form = new FormBuilder('updatePasswordForm', 'post', '');
        $form->setCsrfInput($this->setToken())
            ->setInput('password', 'old_password', ['id' => 'old_password'], 'Ancien mot de passe')
            ->setInput('password', 'password', ['id' => 'password'], 'Nouveau mot de passe')
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
     */
    public function passwordUpdate(Request $request, Response $response, $args): Response
    {
        if (!isValidToken()) {
            $this->emitter->emit('token.rejected', [['username' => Session::read('auth')->username ?? 'Anonymous']]);
            error('Token invalide');
            return redirect('/update_password');
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
        $validator->isValidString('password', PASSWORD, true, 'confirm_password');
        if (!empty($posted['old_password']) && encrypted($posted['old_password']) !== $user->password) {
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
        return redirect('/update_password');
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
