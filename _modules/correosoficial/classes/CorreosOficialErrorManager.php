<?php

/**
 * @author: Grupo Correos A649437
 * @uses: provide methods to manage errors
 * @version: 1
 *
 */
class CorreosOficialErrorManager
{

    /**
     * Errores de usuarios: deben ser traducibles
     *
     */
    public $UserErrorLoginError;
    public $userErrorAnErrorHasOCurred;
    public $couldNotConnectToHost;

    /**
     * Errores técnicos. Directamente en español.
     */
    public $preregisterError;

    public function __construct()
    {

        $module = Module::getInstanceByName('correosoficial');
        /* Errores de usuario */
        $this->UserErrorLoginError = $module->l('Please, check your credentials in module Correos Oficial, in Settings Customer Data.', 'correosoficial');

        /* Errores técnicos */
        $this->preregisterError = 'Debe indicarse una operación (PreRegistro/MultiBulto)';

        $this->couldNotConnectToHost = $module->l('The waiting time has expired', 'correosoficialerrormanager');
    }

    public function display_timeout_error($line)
    {
        echo nl2br("<br/>" . "Ha ocurrido un error temporal. Puede que el servicio de Correos no esté disponible en estos momentos.");
        echo "Inténtelo de nuevo más tarde. ";
        echo "Error en línea: " . $line . " en Fichero: " . __FILE__;
    }

    public static function checkStateConnection($state)
    {
        $error = new self;

        switch ($state) {
            /**
                 * Errores de usuarios: deben ser traducibles
                 *
                 */
            case 0:
                return $error->couldNotConnectToHost;
                break;
            case 'Authorization Required':
            case '401':
                return $error->UserErrorLoginError;
                break;
            case 'Could not connect to host':
                return $error->couldNotConnectToHost;
                break;
            /**
                 * Errores técnicos. Directamente en español.
                 */
            case 'Not Found':
            case '404':
                return 'Servicio no encontrado. Se ha conectado correctamente al host.';
                break;
            default:
                return "Error no conocido. Código HTTP del HOST: " . $state;
        }
    }
}
