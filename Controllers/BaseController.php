<?php

namespace Itval\Controllers;

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
     * Rend la vue index
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
            $this->set('scripts', "<script>CKEDITOR.replace( 'message_contact', {'height': '400'} );</script>");
            return $this->render('contact');
        }
        if (!isValidToken()) {
            $this->emitter->emit('token.rejected');
            error('Token invalide');
            return redirect('/contact');
        }
        $posted = $this->getPost();
        $values = [
            'categorie_contact' => htmlentities($posted['categorie_contact']),
            'nom_contact' => $posted['nom_contact'],
            'prenom_contact' => $posted['prenom_contact'],
            'email_contact' => $posted['email_contact'],
            'objet_contact' => $posted['objet_contact'],
            'message_contact' => $posted['message_contact']
        ];
        $validator = new Validator($values);
        $validator->isValidString('nom_contact', ALPHABETIC, true);
        $validator->isValidString('prenom_contact', ALPHABETIC, true);
        $validator->isValidEmail('email_contact', true);
        $validator->isValidString('objet_contact', TEXT, true);
        $validator->required('message_contact');
        if ($validator->getErrors() === 0) {
            $this->resetValuesSession($values);
            $this->emitter->emit('message.sended', [$values]);
            return true;
        }
        $this->setValuesSession($values);
        return redirect('/contact');
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
                'nom_contact',
                ['placeholder' => 'Entrez votre nom',
                'id' => 'nom_contact', 'value' => printSession('nom_contact')],
                'Votre nom'
            )
            ->setInput(
                'text',
                'prenom_contact',
                ['placeholder' => 'Entrez votre prénom',
                'id' => 'prenom_contact', 'value' => printSession('prenom_contact')],
                'Votre prénom'
            )
            ->setInput(
                'email',
                'email_contact',
                ['placeholder' => 'Entrez votre email',
                'id' => 'email_contact', 'value' => printSession('email_contact')],
                'Votre email'
            )
            ->setInput(
                'text',
                'objet_contact',
                ['placeholder' => 'Objet du message',
                'id' => 'objet_contact', 'value' => printSession('objet_contact')],
                'Objet du message'
            )
            ->setTextArea(
                '10',
                'message_contact',
                ['placeholder' => 'Tapez votre message', 'id' => 'message_contact'],
                'Votre message',
                printSession('message_contact')
            )
            ->setButton('submit', 'envoyer', 'Envoyer votre message');
        return $form;
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
