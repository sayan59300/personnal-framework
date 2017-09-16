<?php
require_once 'Controller.php';

use GuzzleHttp\Psr7\Response;
use Itval\core\Classes\FormBuilder;
use Itval\core\Classes\Validator;

/**
 * Class BaseController Controlleur de base contenant l'index et les vues basiques du framework
 *
 * @author Nicolas Buffart <concepteur-developpeur@nicolas-buffart.fr>
 */
class BaseController extends Controller
{

    /**
     * rRend la vue index
     */
    public function index()
    {
        return $this->render('index');
    }

    /**
     * Rend la vue contact
     *
     * @return bool|Response
     */
    public function contact()
    {
        if (!isset($_POST['envoyer'])) {
            $this->set('title', 'Contact');
            $this->set('contactForm', $this->getContactForm());
            $this->set('scripts', "<script>CKEDITOR.replace( 'message', {'height': '400'} );</script>");
            return $this->render('contact');
        }
        if (!Validator::isValidToken()) {
            $this->emitter->emit('token.rejected');
            error('Token invalide');
            return redirect('/contact');
        }
        $posted = $this->getPost();
        $values = [
            'categorie' => htmlentities($posted['categorie']),
            'nom' => Validator::isValidString(ALPHABETIC, $posted['nom']),
            'prenom' => Validator::isValidString(ALPHABETIC, $posted['prenom']),
            'email' => Validator::isValidEmail($posted['email']),
            'objet' => Validator::isValidString(ALPHABETIC, $posted['objet']),
            'message' => $posted['message']
        ];
        $control = $this->formValidation($values);
        if (!is_array($control)) {
            $this->resetValuesSession($values);
            $this->emitter->emit('message.sended', [$values]);
        }
        $this->setValuesSession($values);
        error($this->formattedErrors($control));
        redirect('/contact');
    }

    /**
     * Génère le formulaire de contact
     *
     * @return FormBuilder
     */
    private function getContactForm(): FormBuilder
    {
        $form = new FormBuilder('contactForm', 'post', getUrl('contact'));
        $form->setCsrfInput($this->setToken())
            ->setInput(
                'text',
                'nom',
                ['placeholder' => 'Entrez votre nom',
                'id' => 'nom', 'value' => printSession('nom')],
                'Votre nom'
            )
            ->setInput(
                'text',
                'prenom',
                ['placeholder' => 'Entrez votre prénom',
                'id' => 'prenom', 'value' => printSession('prenom')],
                'Votre prénom'
            )
            ->setInput(
                'email',
                'email',
                ['placeholder' => 'Entrez votre email',
                'id' => 'email', 'value' => printSession('email')],
                'Votre email'
            )
            ->setInput(
                'text',
                'objet',
                ['placeholder' => 'Objet du message',
                'id' => 'objet', 'value' => printSession('objet')],
                'Objet du message'
            )
            ->setTextArea(
                '10',
                'message',
                ['placeholder' => 'Tapez votre message',
                'id' => 'message'],
                'Votre message',
                printSession('message')
            )
            ->setButton('submit', 'envoyer', 'Envoyer votre message');
        return $form;
    }

    /**
     * Fonction de validation du formulaire de contact
     *
     * @param  array $values
     * @return array|bool
     */
    private function formValidation(array $values)
    {
        $wrongValues = [];
        if (isset($values['nom']) && !$values['nom']) {
            array_push($wrongValues, 'Le nom n\'est pas valide');
        }
        if (isset($values['prenom']) && !$values['prenom']) {
            array_push($wrongValues, 'Le prenom n\'est pas valide');
        }
        if (isset($values['objet']) && !$values['objet']) {
            array_push($wrongValues, 'L\'objet du message n\'est pas valide');
        }
        if (isset($values['email']) && !$values['email']) {
            array_push($wrongValues, 'L\'email n\'est pas valide');
        }
        if (empty($values['message'])) {
            array_push($wrongValues, 'Le message n\'est pas valide');
        }
        if ($wrongValues !== []) {
            return $wrongValues;
        }
        return true;
    }

    /**
     * Rend la vue mentions
     *
     * @return Response
     */
    public function mentions(): Response
    {
        $this->set('title', 'Mentions Légales');
        return $this->render('mentions');
    }
}
