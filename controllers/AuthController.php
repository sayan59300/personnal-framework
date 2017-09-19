<?php
require_once 'Controller.php';

use GuzzleHttp\Psr7\Response;
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

    use Itval\src\Traits\UsersTreatments;

    /**
     * Rend la vue de connexion execute la fonction de connexion
     *
     * @return Response|bool
     */
    public function index()
    {
        $posted = $this->getPost();
        if (!AUTH) {
            return error404();
        }
        if (!isset($posted['connexion']) && !isAuthenticated()) {
            $this->setToken();
            return $this->render('index');
        }
        if (isAuthenticated()) {
            error('Vous êtes déja connecté');
            return redirect('/profil');
        }
        $this->connexion($posted);
    }

    /**
     * Fonction de connexion de l'utilisateur
     *
     * @param  array $posted
     * @return Response|bool
     */
    private function connexion(array $posted)
    {
        if (!AUTH) {
            return error404();
        }
        if (!isValidToken()) {
            $this->emitter->emit('token.rejected');
            error('Token invalide');
            return redirect();
        }
        $username = htmlentities($posted['username']);
        $password = htmlentities(encrypted($posted['password']));
        $user = new UsersModel;
        $result = $user->find(
            ['fields' => ['id', 'username', 'nom', 'prenom', 'email', 'confirmed', 'registered_at'],
                'conditions' => "username = :username AND password = :password"],
            [':username' => $username, ':password' => $password]
        );
        $this->responseValidation(current($result));
    }

    /**
     * Fonction de contrôle de validation du compte en base de données, qui crée la session de l'utilisateur ou qui
     * redirige vers l'accueil avec un message d'erreur si l'utilisateur n'a pas été confirmé
     *
     * @param  $result
     * @return bool
     */
    private function responseValidation($result): bool
    {
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
        Session::set('auth', new stdClass());
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

    /**
     * Rend la vue d'enregistrement d'un nouveau compte utilisateur execute la fonction d'inscription
     *
     * @return Response|bool
     */
    public function register()
    {
        if (!AUTH) {
            return error404();
        }
        if (isAuthenticated()) {
            error('Vous êtes déjà connecté, veuillez vous déconnecter pour faire une nouvelle inscription sur le site');
            return redirect();
        }
        if (!isset($this->getPost()['inscription'])) {
            $this->set('registrationForm', $this->getRegisterForm());
            return $this->render('registration');
        }
        $this->registration();
    }

    /**
     * Fonction de confirmation de compte utilisateur
     *
     * @return Response
     */
    public function confirmation(): Response
    {
        $getValues = $this->getQuery();
        if (!isset($getValues['username']) && !isset($getValues['token'])) {
            return error404();
        }
        $username = htmlentities($getValues['username']);
        $token = htmlentities($getValues['token']);
        $model = new Itval\src\Models\UsersModel;
        $user = current($model->find(['conditions' => "username = '$username' AND confirmation_token = '$token'"]));
        if (!$user) {
            return error404();
        }
        $user->confirmation();
        return $this->render('confirmed');
    }

    /**
     * Retourne la vue de confirmation de compte
     *
     * @return Response
     */
    public function confirmed(): Response
    {
        return $this->render('confirmed');
    }

    /**
     * Rend le vue profil de l'utilisateur connecté, execute la fonction update du profil
     *
     * @return Response|bool
     */
    public function profil()
    {
        if (!isset($this->getPost()['update'])) {
            if (!AUTH) {
                return error404();
            }
            if (!isAuthenticated()) {
                LoggerFactory::getInstance('security')->addWarning(
                    'Tentative d\'accès à la page '
                    . 'profil'
                );
                error('Vous devez être connecté pour accéder à votre profil');
                return redirect();
            }
            $model = new UsersModel;
            $user = current(
                $model->find(
                    ['conditions' => 'id = ' . currentUser()->id,
                        'fields' => ['id', 'email', 'nom', 'prenom', 'username', 'registered_at', 'confirmed']]
                )
            );
            $this->set('profilForm', $this->getProfilForm($user));
            $this->set('registerAt', new DateTime($user->registered_at));
            $this->set('title', 'Profil');
            return $this->render('profil');
        }
        $this->update();
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
            ->setInput('hidden', 'id', ['value' => $user->id])
            ->setInput('text', 'nom', ['id' => 'nom', 'value' => $user->nom], 'Votre nom')
            ->setInput('text', 'prenom', ['id' => 'prenom', 'value' => $user->prenom], 'Votre prénom')
            ->setInput('email', 'email', ['id' => 'email', 'value' => $user->email], 'Votre email')
            ->setInput('text', 'username', ['id' => 'username', 'value' => $user->username], 'Votre nom d\'utilisateur')
            ->setInput('password', 'password', ['id' => 'password'], 'Nouveau mot de passe')
            ->setInput('password', 'confirm_password', ['id' => 'confirm_password'], 'Confirmation mot de passe')
            ->setButton('submit', 'update', 'Mettre à jour votre profil', ['class' => 'btn btn-primary']);
        return $form;
    }

    /**
     * Met à jour le profil de l'utilisateur connecté
     *
     * @return Response|bool
     */
    public function update()
    {
        if (!AUTH) {
            return error404();
        }
        if (!isValidToken()) {
            $this->emitter->emit('token.rejected', [['username' => Session::read('auth')->username ?? 'Anonymous']]);
            error('Token invalide');
            return redirect('/profil');
        }
        $posted = $this->getPost();
        $values = [
            'profil' => true,
            'id' => intval($posted['id']),
            'nom' => $posted['nom'],
            'prenom' => $posted['prenom'],
            'email' => $posted['email'],
            'username' => $posted['username'],
            'password' => $posted['password'] ?? '',
            'confirm_password' => $posted['confirm_password'] ?? ''
        ];
        $validator = new Validator($values);
        $validator->isValidInt('id');
        $validator->isValidString('nom', ALPHABETIC, true);
        $validator->isValidString('prenom', ALPHABETIC, true);
        $validator->isValidEmail('email', true);
        $validator->isValidString('username', USERNAME, true);
        $validator->isAvailable(UsersModel::class, 'username');
        if ($posted['password'] !== '') {
            $validator->isValidString('password', PASSWORD, false, 'confirm_password');
        }
        if (count($validator->getErrors()) === 0) {
            $user = new UsersModel;
            $user->id = $values['id'];
            $user->nom = $values['nom'];
            $user->prenom = $values['prenom'];
            $user->email = $values['email'];
            $user->username = $values['username'];
            $user->password = $values['password'];
            $user->profilUpdate();
            LoggerFactory::getInstance('users')->addInfo('Modification de profil', ['username' => $values['username']]);
            success('Votre profil a été mis à jour avec succès');
            return redirect('/profil');
        }
        error($this->formattedErrors($validator->getErrors()));
        return redirect('/profil');
    }

    /**
     * Fonction de déconnexion
     *
     * @return Response|bool
     */
    public function logout()
    {
        if (!AUTH) {
            return error404();
        }
        session_unset();
        session_destroy();
        return redirect();
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
     * Fonction de contrôle des données et d'enregistrement de l'inscription dans la base de données
     *
     * @return Response|bool
     */
    private function registration()
    {
        if (!AUTH) {
            return error404();
        }
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
        if (count($validator->getErrors()) === 0) {
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
            return true;
        }
        $this->setValuesSession($values);
        error($this->formattedErrors($validator->getErrors()));
        return redirect('/registration');
    }
}
