<?php
/**
* NOTICE OF LICENSE
*
* This source file is subject to a commercial license from ScaleDEV.
* Use, copy, modification or distribution of this source file without written
* license agreement from ScaleDEV is strictly forbidden.
* In order to obtain a license, please contact us: contact@scaledev.fr
* ...........................................................................
* INFORMATION SUR LA LICENCE D'UTILISATION
*
* L'utilisation de ce fichier source est soumise à une licence commerciale
* concédée par la société ScaleDEV.
* Toute utilisation, reproduction, modification ou distribution du présent
* fichier source sans contrat de licence écrit de la part de ScaleDEV est
* expressément interdite.
* Pour obtenir une licence, veuillez nous contacter : contact@scaledev.fr
* ...........................................................................
* @author ScaleDEV <contact@scaledev.fr>
* @copyright Copyright (c) ScaleDEV - 12 RUE CHARLES MORET - 10120 SAINT-ANDRE-LES-VERGERS - FRANCE
* @license Commercial license
* @package Scaledev\Adeo
* Support: support@scaledev.fr
*/

namespace Scaledev\Adeo\Core\Component;

use Scaledev\Adeo\Core\Module;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Class Logger
 *
 * @package Scaledev\Adeo
 * @author Pascal Fischer <contact@scaledev.fr>
 */
final class Logger
{
    /**
     * The log file's path.
     *
     * @var string $filePath
     */
    private $filePath;

    /**
     * Logger constructor.
     *
     * @param string $filePath file path from the log dir of the module without extension
     */
    public function __construct($filePath)
    {
        $this->filePath = _PS_MODULE_DIR_.Module::NAME.'/logs/'.$filePath.'.log';

        if (!is_dir(dirname($this->filePath))) {
            @mkdir(dirname($this->filePath), 0777, true);
        }
    }

    /**
     * Add a message to the ".log" file.
     *
     * @param mixed $message
     * @return void
     */
    public function addLog($message)
    {
        file_put_contents(
            $this->filePath,
            sprintf(
                '[%s] %s'."\n",
                date('Y-m-d H:i:s'),
                print_r($message, true)
            ),
            FILE_APPEND
        );
    }

    /**
     * Clears the ".log" file.
     *
     * @return bool
     */
    public function clearLogFile()
    {
        if (is_file($this->filePath)) {
            return unlink($this->filePath);
        }

        return true;
    }
}
