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
use Scaledev\MiraklPhpConnector\Model\Export\ProductExportStatus\IntegrationDetails;

/**
 * Class ProductExportInformation
 *
 * @package Scaledev\MiraklPhpConnector
 * @author Louis Pavoine <contact@scaledev.fr>
 */
final class ProductExportInformation extends AbstractModel
{
    /**
     * Import creation date
     *
     * @var string
     */
    private $date_created;

    /**
     * Returns true if error report is available. Value is filled when the import is completed
     *
     * @var boolean
     */
    private $has_error_report;

    /**
     * Returns true if new product report is available. Value is filled when the import is completed
     *
     * @var boolean
     */
    private $has_new_product_report;

    /**
     * Returns true if transformation error report is available. Value is filled when the import is completed
     *
     * @var boolean
     */
    private $has_transformation_error_report;

    /**
     * Returns true if transformed file is available. Value is filled when the import is completed
     *
     * @var boolean
     */
    private $has_transformed_file;

    /**
     * Import identifier
     *
     * @var integer
     */
    private $import_id;

    /**
     * Import status
     *
     * @var string
     */
    private $import_status;

    /**
     * Integration details
     *
     * @var IntegrationDetails
     */
    private $integration_details;

    /**
     * A message explaining the reason of the import status, if relevant
     *
     * @var string
     */
    private $reason_status;

    /**
     * Shop identifier
     *
     * @var integer
     */
    private $shop_id;

    /**
     * Total count of transformed lines in error
     *
     * @var integer
     */
    private $transform_lines_in_error;

    /**
     * Total count of transformed lines in success
     *
     * @var integer
     */
    private $transform_lines_in_success;

    /**
     * Total count of transformed lines in read
     *
     * @var integer
     */
    private $transform_lines_read;

    /**
     * Total count of transformed lines with warning
     *
     * @var integer
     */
    private $transform_lines_with_warning;

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
     * @return bool
     */
    public function isHasNewProductReport()
    {
        return $this->has_new_product_report;
    }

    /**
     * @param bool $has_new_product_report
     * @return $this
     */
    public function setHasNewProductReport($has_new_product_report)
    {
        $this->has_new_product_report = $has_new_product_report;
        return $this;
    }

    /**
     * @return bool
     */
    public function isHasTransformationErrorReport()
    {
        return $this->has_transformation_error_report;
    }

    /**
     * @param bool $has_transformation_error_report
     * @return $this
     */
    public function setHasTransformationErrorReport($has_transformation_error_report)
    {
        $this->has_transformation_error_report = $has_transformation_error_report;
        return $this;
    }

    /**
     * @return bool
     */
    public function isHasTransformedFile()
    {
        return $this->has_transformed_file;
    }

    /**
     * @param bool $has_transformed_file
     * @return $this
     */
    public function setHasTransformedFile($has_transformed_file)
    {
        $this->has_transformed_file = $has_transformed_file;
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
     * @return string
     */
    public function getImportStatus()
    {
        return $this->import_status;
    }

    /**
     * @param string $import_status
     * @return $this
     */
    public function setImportStatus($import_status)
    {
        $this->import_status = $import_status;
        return $this;
    }

    /**
     * @return IntegrationDetails
     */
    public function getIntegrationDetails()
    {
        return $this->integration_details;
    }

    /**
     * @param IntegrationDetails $integration_details
     * @return $this
     */
    public function setIntegrationDetails($integration_details)
    {
        $this->integration_details = $integration_details;
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
     * @param int $shop_id
     * @return $this
     */
    public function setShopId($shop_id)
    {
        $this->shop_id = $shop_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getTransformLinesInError()
    {
        return $this->transform_lines_in_error;
    }

    /**
     * @param int $transform_lines_in_error
     * @return $this
     */
    public function setTransformLinesInError($transform_lines_in_error)
    {
        $this->transform_lines_in_error = $transform_lines_in_error;
        return $this;
    }

    /**
     * @return int
     */
    public function getTransformLinesInSuccess()
    {
        return $this->transform_lines_in_success;
    }

    /**
     * @param int $transform_lines_in_success
     * @return $this
     */
    public function setTransformLinesInSuccess($transform_lines_in_success)
    {
        $this->transform_lines_in_success = $transform_lines_in_success;
        return $this;
    }

    /**
     * @return int
     */
    public function getTransformLinesRead()
    {
        return $this->transform_lines_read;
    }

    /**
     * @param int $transform_lines_read
     * @return $this
     */
    public function setTransformLinesRead($transform_lines_read)
    {
        $this->transform_lines_read = $transform_lines_read;
        return $this;
    }

    /**
     * @return int
     */
    public function getTransformLinesWithWarning()
    {
        return $this->transform_lines_with_warning;
    }

    /**
     * @param int $transform_lines_with_warning
     * @return $this
     */
    public function setTransformLinesWithWarning($transform_lines_with_warning)
    {
        $this->transform_lines_with_warning = $transform_lines_with_warning;
        return $this;
    }
}
