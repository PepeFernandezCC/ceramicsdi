<?php

class Mail extends MailCore
{
    public static function send(
        $idLang,
        $template,
        $subject,
        $templateVars,
        $to,
        $toName = null,
        $from = null,
        $fromName = null,
        $fileAttachment = null,
        $mode_smtp = null,
        $templatePath = _PS_MAIL_DIR_,
        $die = false,
        $idShop = null,
        $bcc = null,
        $replyTo = null,
        $replyToName = null
    ) {

        $block_templates = ['order_conf', 'cheque'];
        // Bloquear solo confirmación de pedido
        if (in_array($template, $block_templates)) {
            return true; // "simula" enviado para no romper flujos
        }

        return parent::send(
            $idLang,
            $template,
            $subject,
            $templateVars,
            $to,
            $toName,
            $from,
            $fromName,
            $fileAttachment,
            $mode_smtp,
            $templatePath,
            $die,
            $idShop,
            $bcc,
            $replyTo,
            $replyToName
        );
    }
}
