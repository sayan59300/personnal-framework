<?php

namespace Itval\src\events;

use Itval\core\Classes\Email;
use Itval\core\Factories\EventFactory;
use Itval\core\Factories\LoggerFactory;
use Itval\src\Models\UsersModel;

$emitter = EventFactory::getInstance();

/**
 * Ecouteur de l'évènement émit si un token csrf est invalide
 */
$emitter->on(
    'token.rejected',
    function (array $args = []) {
        LoggerFactory::getInstance('security')->addWarning('Tentative d\'accès avec un token invalide', $args);
    }
);

/**
 * Ecouteur de l'évènement émit si un utilisateur s'enregistre
 */
$emitter->on(
    'user.registered',
    function (UsersModel $user) {
        $mail = new Email('registration', ['username' => $user->username, 'confirmation_token' => $user->confirmation_token]);
        $message = new \Swift_Message('Email de confirmation suite à votre inscription sur le site ' . SITE_NAME);
        $message->setFrom(SITE_CONTACT_MAIL)
            ->setTo($user->email)
            ->setBody($mail->getView(), 'text/html');
        if ($mail->send($message) === 0) {
            LoggerFactory::getInstance()->addWarning(
                'Erreur lors d\'une tentative d\'envoie de message après inscription d\'un nouvel utilisateur',
                ['username' => $user->username, 'email' => $user->email]
            );
            warning(
                'Votre inscription a bien été enregistrée, il y a eu un problème lors de l\'envoie de l\'email de confirmation, '
                . 'veuillez contacter l\'administrateur du site à l\'aide du formulaire de contact'
            );
            return redirect();
        }
        LoggerFactory::getInstance('users')->addInfo(
            'Un nouvel utilisateur s\'est enregistré',
            ['username' => $user->username, 'email' => $user->email]
        );
        success('Votre inscription a bien été enregistrée, un email de confirmation a été envoyé à votre adresse email');
        return redirect();
    }
);

/**
 * Ecouteur de l'évènement émit si un message est envoyé
 */
$emitter->on(
    'message.sended',
    function (array $values) {
        $mail = new Email('contact', $values);
        $message = new \Swift_Message('Contact - ' . SITE_NAME);
        $message->setFrom($values['email_contact'])
            ->setTo(SITE_CONTACT_MAIL)
            ->setBody($mail->getView(), 'text/html');
        if ($mail->send($message) === 0) {
            LoggerFactory::getInstance('contact')->addAlert(
                'Erreur lors de l\'envoie d\'un message depuis la page de contact',
                ['Email' => $values['email_contact']]
            );
            error('Problème lors de l\'envoi du message');
            return redirect('/contact');
        }
        LoggerFactory::getInstance('contact')->addAlert(
            'Nouveau message envoyé depuis la page de contact',
            ['Email' => $values['email_contact']]
        );
        success('Votre message a bien été envoyé');
        return redirect();
    }
);
