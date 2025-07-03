<?php
/**
 * Google Merchant Center Pro
 *
 * @author    businesstech.fr <modules@businesstech.fr> - https://www.businesstech.fr/
 * @copyright Business Tech - https://www.businesstech.fr/
 * @license   see file: LICENSE.txt
 *
 *           ____    _______
 *          |  _ \  |__   __|
 *          | |_) |    | |
 *          |  _ <     | |
 *          | |_) |    | |
 *          |____/     |_|
 */

namespace Gmerchantcenterpro\ModuleLib;

if (!defined('_PS_VERSION_')) {
    exit;
}
class moduleReporting
{
    /**
     * @var obj
     */
    public static $oReporting;

    /**
     * @var array : stock msg reported
     */
    public $aReport = [];

    /**
     * @var string : store file name
     */
    public $sFileName = '';

    /**
     * @var bool : activate or not reporting
     */
    public $bActivate;

    /**
     * @param bool $bActivate
     */
    public function __construct($bActivate = true)
    {
        $this->bActivate = $bActivate;
    }

    /**
     * stock reporting
     *
     * @param string $Key
     * @param array $aParams
     *
     * @return array
     */
    public function set($Key, $aParams)
    {
        if ($this->bActivate) {
            $this->aReport[$Key][] = $aParams;
        }
    }

    /**
     * set file name
     *
     * @param string $sFileName
     */
    public function setFileName($sFileName)
    {
        $this->sFileName = $sFileName;
    }

    /**
     * return available serialized content
     *
     * @return array
     */
    public function get()
    {
        $aData = [];

        if ($this->bActivate && file_exists($this->sFileName) && filesize($this->sFileName)) {
            $sContent = method_exists('Tools', 'file_get_contents') ? \Tools::file_get_contents($this->sFileName) : file_get_contents($this->sFileName);

            if (!empty($sContent)) {
                $aData = moduleTools::handleGetConfigurationData($sContent, ['allowed_classes' => false]);
            }
        }

        return $aData;
    }

    /**
     * delete reporting file
     *
     * @return bool
     */
    public function delete()
    {
        return is_file($this->sFileName) && unlink($this->sFileName) ? true : false;
    }

    /**
     * merge data between current data and stored data in reporting file
     *
     * @return array
     */
    public function mergeData()
    {
        $aReport = [];

        if ($this->bActivate && !empty($this->aReport)) {
            // get unserialized reporting
            $aReport = $this->get();

            if (!empty($aReport) && is_array($aReport)) {
                foreach ($this->aReport as $sKeyName => $aProducts) {
                    foreach ($this->aReport[$sKeyName] as $iKey => $mValue) {
                        $aReport[$sKeyName][] = $mValue;
                    }
                }
            } else {
                $aReport = $this->aReport;
            }
            $this->aReport = [];
        }

        return $aReport;
    }

    /**
     * write Reporting file
     *
     * @param string $sContent
     * @param string $sMode
     * @param bool $bDebug
     *
     * @return int
     */
    public function writeFile($sContent, $sMode = 'w', $bDebug = false)
    {
        $bWritten = 0;

        if (!empty($this->sFileName)) {
            $rFile = @fopen($this->sFileName, $sMode);
        }

        if (!empty($rFile)) {
            $bWritten = @fwrite($rFile, moduleTools::handleSetConfigurationData($sContent));

            if (!empty($bWritten)) {
                @fclose($rFile);
            }
        }

        return $bWritten;
    }

    /**
     * creates singleton
     *
     * @param bool $bActivate
     *
     * @return obj
     */
    public static function create($bActivate = true)
    {
        if (null === self::$oReporting) {
            self::$oReporting = new moduleReporting($bActivate);
        }

        return self::$oReporting;
    }
}
