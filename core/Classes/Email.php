<?php

namespace Itval\core\Classes;

use Swift_SmtpTransport;

/**
 * Class Email Classe qui permet de gérer les emails envoyés par le framework
 *
 * @package Itval\core\Classes
 * @author  Nicolas Buffart <concepteur-developpeur@nicolas-buffart.fr>
 */
class Email extends \Swift_Mailer
{

    /**
     * Nom de la vue de l'email
     *
     * @var string
     */
    private $view;

    /**
     * Valeurs passées à l'email
     *
     * @var array
     */
    private $values;

    /**
     * Email constructor.
     *
     * @param string $view
     * @param null   $values
     */
    public function __construct(string $view, $values = null)
    {
        $transport = new Swift_SmtpTransport(HOST_MAIL, HOST_MAIL_PORT, HOST_MAIL_SECURITY);
        $transport->setUsername(HOST_MAIL_USERNAME);
        $transport->setPassword(HOST_MAIL_PASSWORD);
        parent::__construct($transport);
        $this->view = DS . 'views' . DS . 'email' . DS . $view . '.phtml';
        if (isset($values)) {
            $this->values = $values;
        }
    }

    /**
     * Retourne la vue de l'email
     *
     * @return   string
     * @internal param string $name nom de la vue
     * @internal param mixed $values valeurs requises dans la vue
     */
    public function getView()
    {
        $values = $this->values ?? null;
        ob_start();
        include ROOT . $this->view;
        $view = ob_get_clean();
        return $view;
    }
}
