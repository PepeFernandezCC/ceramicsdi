<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licensed under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the license agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 * @author    MyPrestaModules
 * @copyright 2013-2020 MyPrestaModules
 * @license LICENSE.txt
 */

require_once _PS_MODULE_DIR_ . 'exportproducts/src/product/PEProductDataProvider.php';
require_once _PS_MODULE_DIR_ . 'exportproducts/src/tools/PESerializationChecker.php';

class PEExtraField
{
    private $conditions;
    private $condition_field;
    private $product_data_provider;

    const CONDITION_LESS_THAN = 1;
    const CONDITION_MORE_THAN = 2;
    const CONDITION_EQUAL = 3;
    const CONDITION_NOT_EQUAL = 4;
    const CONDITION_IN_ARRAY = 5;
    const CONDITION_NOT_IN_ARRAY = 6;
    const CONDITION_EMPTY = 7;
    const CONDITION_NOT_EMPTY = 8;
    const CONDITION_REGEX = 9;
    const CONDITION_ANY = 10;

    const FORMULA_TYPE_STRING = 1;
    const FORMULA_TYPE_MATH = 2;
    const FORMULA_TYPE_FIND_AND_REPLACE = 3;

    const FORMULA_TYPE_TRUNCATE = 4;
    public function __construct($encoded_conditions_data, PEProductDataProvider $product_data_provider)
    {
        $this->conditions = [];
        $this->product_data_provider = $product_data_provider;

        $conditions_data = $this->decodeConditionsData($encoded_conditions_data);
        $this->condition_field = $conditions_data['condition_field'];

        foreach ($conditions_data['condition'] as $key => $condition) {
            $this->conditions[$key]['condition'] = $condition;
            $this->conditions[$key]['condition_value'] = $conditions_data['condition_value'][$key];
            $this->conditions[$key]['formula_type'] = $conditions_data['formula_type'][$key];
            $this->conditions[$key]['formula'] = $conditions_data['formula'][$key];
            $this->conditions[$key]['format_as_price'] = $conditions_data['format_as_price'][$key];
        }
    }

    private function decodeConditionsData($encoded_conditions_data)
    {
        if (is_string($encoded_conditions_data)) {
            if (PESerializationChecker::isStringSerialized($encoded_conditions_data)) {
                $conditions_data = Tools::unSerialize($encoded_conditions_data);
            } else {
                $conditions_data = json_decode($encoded_conditions_data, true);
            }
        } else {
            $conditions_data = $encoded_conditions_data;
        }

        $conditions_data['condition'] = json_decode($conditions_data['condition'], true);
        $conditions_data['condition_value'] = json_decode($conditions_data['condition_value'], true);
        $conditions_data['formula_type'] = json_decode($conditions_data['formula_type'], true);
        $conditions_data['formula'] = json_decode($conditions_data['formula'], true);
        $conditions_data['format_as_price'] = json_decode($conditions_data['format_as_price'], true);

        return $conditions_data;
    }

    public function getValue()
    {
        $result = '';
        $condition_field_value = $this->product_data_provider->getExportFieldValue($this->condition_field, []);

        foreach ($this->conditions as $condition) {
            if (!$this->isFieldConditionTrue($condition['condition'], $condition['condition_value'], $condition_field_value)) {
                continue;
            }

            switch ($condition['formula_type']) {
                case self::FORMULA_TYPE_MATH:
                    $this->product_data_provider->apply_price_decoration = false;
                    $formula = $this->constructFormulaWithRealValues($condition['formula']);

                    if (!$this->isValidMathFormula($formula)) {
                        break;
                    }

                    require_once _PS_MODULE_DIR_ . 'exportproducts/libraries/calculate/calculateString.php';

                    $math_formula_calculator = new \calculateString();
                    $result = $math_formula_calculator->execute($formula);

                    if ($condition['format_as_price']) {
                        $this->product_data_provider->apply_price_decoration = true;
                        $result = $this->product_data_provider->getFormattedPrice($result);
                    }

                    break;
                case self::FORMULA_TYPE_FIND_AND_REPLACE:
                    $exploded_formula = explode('=>', $condition['formula'], 3);

                    $search = $exploded_formula[1];
                    $replace = $exploded_formula[2];
                    $string = $this->constructFormulaWithRealValues($exploded_formula[0]);

                    $result = str_replace($search, $replace, $string);

                    break;
                case self::FORMULA_TYPE_TRUNCATE:
                    $result = $this->_truncateValue( $condition['formula'] );
                    break;
                default:
                    $result = $this->constructFormulaWithRealValues($condition['formula']);
            }
        }

        return $result;
    }

    private function _truncateValue($formula)
    {
        $result = "";
        $limit = $suffix = false;
        if( strpos($formula, 'LIMIT_') !== false ){
            $exploded_formula = explode('=>', $formula, 3);
            if (isset($exploded_formula[1]) && $exploded_formula[1]) {
                $limit = str_replace('LIMIT_', '', $exploded_formula[1]);
            }
            if (isset($exploded_formula[2]) && $exploded_formula[2]) {
                $suffix = str_replace('SUFFIX_', '', $exploded_formula[2]);
            }
            if (isset($exploded_formula[0]) && $exploded_formula[0] ) {
                $result = $this->constructFormulaWithRealValues($exploded_formula[0]);
                $result = strip_tags($result);
            }
        }
        if ( $limit ) {
            $result = Tools::truncate($result, $limit, $suffix);
        }
        return $result;
    }

    private function isFieldConditionTrue($condition_operator, $condition_value, $condition_field_value)
    {
        $condition_is_true = false;
        switch ($condition_operator) {
            case self::CONDITION_LESS_THAN:
                $condition_field_value = str_replace(',', '.', $condition_field_value);
                $condition_value = str_replace(',', '.', $condition_value);
                if (is_numeric($condition_value) && is_numeric($condition_field_value) && $condition_field_value < $condition_value) {
                    $condition_is_true = true;
                }

                break;
            case self::CONDITION_MORE_THAN:
                $condition_field_value = str_replace(',', '.', $condition_field_value);
                $condition_value = str_replace(',', '.', $condition_value);
                if (is_numeric($condition_value) && is_numeric($condition_field_value) && $condition_field_value > $condition_value) {
                    $condition_is_true = true;
                }

                break;
            case self::CONDITION_EQUAL:
                if ($condition_field_value == $condition_value) {
                    $condition_is_true = true;
                }

                break;
            case self::CONDITION_NOT_EQUAL:
                if ($condition_field_value != $condition_value) {
                    $condition_is_true = true;
                }

                break;
            case self::CONDITION_IN_ARRAY:
                $conditionValues = explode(',', $condition_value);
                if (in_array($condition_field_value, $conditionValues)) {
                    $condition_is_true = true;
                }

                break;
            case self::CONDITION_NOT_IN_ARRAY:
                $conditionValues = explode(',', $condition_value);
                if (!in_array($condition_field_value, $conditionValues)) {
                    $condition_is_true = true;
                }

                break;
            case self::CONDITION_EMPTY:
                if ($condition_field_value == '') {
                    $condition_is_true = true;
                }

                break;
            case self::CONDITION_NOT_EMPTY:
                if ($condition_field_value != '') {
                    $condition_is_true = true;
                }

                break;
            case self::CONDITION_REGEX:
                @preg_match($condition_value, $condition_field_value, $matches);
                if ($matches) {
                    $condition_is_true = true;
                }

                break;
            case self::CONDITION_ANY:
                $condition_is_true = true;
                break;
            default:
                $condition_is_true = false;
        }

        return $condition_is_true;
    }

    private function constructFormulaWithRealValues($formula)
    {
        if (empty($formula)) {
            return $formula;
        }

        preg_match_all('/\[(.*?)\]/', $formula, $matches);

        if (empty($matches)) {
            return $formula;
        }

        $matched_export_fields_ids = $matches[0];

        foreach ($matched_export_fields_ids as $export_field_id) {
            $export_field_value = $this->product_data_provider->getExportFieldValue(trim($export_field_id, '[]'), []);
            $formula = str_replace($export_field_id, $export_field_value, $formula);
        }

        return $formula;
    }

    private function isValidMathFormula($formula)
    {
        if ((strpos($formula, "+") === false) &&
            strpos($formula, "-") === false &&
            strpos($formula, "*") === false &&
            strpos($formula, "/") === false
        ) {
            return false;
        }

        return true;
    }
}