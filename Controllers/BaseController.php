<?php

namespace Itval\Controllers;

use Itval\core\Classes\FormBuilder;
use Itval\core\Classes\Validator;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class BaseController Controlleur de base contenant l'index et les vues basiques du framework
 *
 * @author Nicolas Buffart <concepteur-developpeur@nicolas-buffart.fr>
 */
class BaseController extends Controller
{

    /**
     * Rend la vue index
     *
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function index(Request $request, Response $response, $args): Response
    {
        ob_start();
        require ROOT . DS . 'composer.json';
        $encode = ob_get_clean();
        $json = json_decode($encode, true);
        $this->set('data', $json);
        return $this->render('index');
    }

    /**
     * Rend la vue contact, effectue le traitement des données et l'envoie du message
     *
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function contact(Request $request, Response $response, $args): Response
    {
        $this->set('title', 'Contact');
        $this->set('description', 'Ce formulaire de contact permet de communiquer avec l\'équipe du site ' . SITE_NAME);
        $this->set('contactForm', $this->getContactForm());
        $this->set('scripts', "<script>CKEDITOR.replace( 'message_contact', {'height': '400'} );</script>");
        return $this->render('contact');
    }

    /**
     * Traite les données du formulaire et envoie le message de la page de contact
     *
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function sendMessage(Request $request, Response $response, $args): Response
    {
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
        $validator->isValidString('nom_contact', DENOMINATION, true);
        $validator->isValidString('prenom_contact', DENOMINATION, true);
        $validator->isValidEmail('email_contact', true);
        $validator->required('objet_contact');
        $validator->required('message_contact');
        if ($validator->getErrors() === 0) {
            $this->resetValuesSession($values);
            $this->emitter->emit('message.sended', [$values]);
            return redirect('/contact');
        }
        $this->setValuesSession($values);
        return redirect('/contact');
    }

    /**
     * Rend la vue mentions
     *
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function mentions(Request $request, Response $response, $args): Response
    {
        $this->set('title', 'Mentions légales');
        $this->set('description', 'Mentions légales ' . SITE_NAME);
        return $this->render('mentions');
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
}
