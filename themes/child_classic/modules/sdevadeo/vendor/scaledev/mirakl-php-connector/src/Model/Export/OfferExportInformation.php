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
 * @package Scaledev\MiraklPhpConnector
 * Support: support@scaledev.fr
 */

namespace Scaledev\MiraklPhpConnector\Model\Export;

use Scaledev\MiraklPhpConnector\Core\Model\AbstractModel;

/**
 * Class OfferExportInformation
 *
 * @package Scaledev\MiraklPhpConnector
 * @author Louis Pavoine <contact@scaledev.fr>
 */
final class OfferExportInformation extends AbstractModel
{
    /**
     * @var string
     */
    private $date_created;

    /**
     * @var boolean
     */
    private $has_error_report;

    /**
     * @var integer
     */
    private $import_id;

    /**
     * @var integer
     */
    private $lines_in_error;

    /**
     * @var integer
     */
    private $lines_in_pending;

    /**
     * @var integer
     */
    private $lines_in_success;

    /**
     * @var integer
     */
    private $lines_read;

    /**
     * @var string
     */
    private $mode;

    /**
     * @var integer
     */
    private $offer_deleted;

    /**
     * @var integer
     */
    private $offer_inserted;

    /**
     * @var integer
     */
    private $offer_updated;

    /**
     * @var string
     */
    private $reason_status;

    /**
     * @var string
     */
    private $status;

    /**
     * @return string
     */
    public function getDateCreated()
    {
        return $this->date_created;
    }

    /**
     * @param string $date_created
     * @return $this
     */
    public function setDateCreated($date_created)
    {
        $this->date_created = $date_created;
        return $this;
    }

    /**
     * @return bool
     */
    public function isHasErrorReport()
    {
        return $this->has_error_report;
    }

    /**
     * @param bool $has_error_report
     * @return $this
     */
    public function setHasErrorReport($has_error_report)
    {
        $this->has_error_report = $has_error_report;
        return $this;
    }

    /**
     * @return int
     */
    public function getImportId()
    {
        return $this->import_id;
    }

    /**
     * @param int $import_id
     * @return $this
     */
    public function setImportId($import_id)
    {
        $this->import_id = $import_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getLinesInError()
    {
        return $this->lines_in_error;
    }

    /**
     * @param int $lines_in_error
     * @return $this
     */
    public function setLinesInError($lines_in_error)
    {
        $this->lines_in_error = $lines_in_error;
        return $this;
    }

    /**
     * @return int
     */
    public function getLinesInPending()
    {
        return $this->lines_in_pending;
    }

    /**
     * @param int $lines_in_pending
     * @return $this
     */
    public function setLinesInPending($lines_in_pending)
    {
        $this->lines_in_pending = $lines_in_pending;
        return $this;
    }

    /**
     * @return int
     */
    public function getLinesInSuccess()
    {
        return $this->lines_in_success;
    }

    /**
     * @param int $lines_in_success
     * @return $this
     */
    public function setLinesInSuccess($lines_in_success)
    {
        $this->lines_in_success = $lines_in_success;
        return $this;
    }

    /**
     * @return int
     */
    public function getLinesRead()
    {
        return $this->lines_read;
    }

    /**
     * @param int $lines_read
     * @return $this
     */
    public function setLinesRead($lines_read)
    {
        $this->lines_read = $lines_read;
        return $this;
    }

    /**
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @param string $mode
     * @return $this
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
        return $this;
    }

    /**
     * @return int
     */
    public function getOfferDeleted()
    {
        return $this->offer_deleted;
    }

    /**
     * @param int $offer_deleted
     * @return $this
     */
    public function setOfferDeleted($offer_deleted)
    {
        $this->offer_deleted = $offer_deleted;
        return $this;
    }

    /**
     * @return int
     */
    public function getOfferInserted()
    {
        return $this->offer_inserted;
    }

    /**
     * @param int $offer_inserted
     * @return $this
     */
    public function setOfferInserted($offer_inserted)
    {
        $this->offer_inserted = $offer_inserted;
        return $this;
    }

    /**
     * @return int
     */
    public function getOfferUpdated()
    {
        return $this->offer_updated;
    }

    /**
     * @param int $offer_updated
     * @return $this
     */
    public function setOfferUpdated($offer_updated)
    {
        $this->offer_updated = $offer_updated;
        return $this;
    }

    /**
     * @return string
     */
    public function getReasonStatus()
    {
        return $this->reason_status;
    }

    /**
     * @param string $reason_status
     * @return $this
     */
    public function setReasonStatus($reason_status)
    {
        $this->reason_status = $reason_status;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }
}
