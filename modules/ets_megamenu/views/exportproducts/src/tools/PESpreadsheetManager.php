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

class PESpreadsheetManager
{
    private $spreadsheet;
    private $spreadsheet_grid;

    private $file_format;
    private $file_delimiter;
    private $file_separator;
    private $configuration;
    private $encoding;

    public function __construct($configuration)
    {
        if (!class_exists('PHPExcel')) {
            require_once _PS_MODULE_DIR_ . 'exportproducts/libraries/PHPExcel_1.7.9/Classes/PHPExcel.php';
            require_once _PS_MODULE_DIR_ . 'exportproducts/libraries/PHPExcel_1.7.9/Classes/PHPExcel/IOFactory.php';
            require_once _PS_MODULE_DIR_ . 'exportproducts/libraries/PHPExcel_1.7.9/Classes/PHPExcel/Style/Alignment.php';
        }

        $this->spreadsheet = new \PHPExcel();
        $this->configuration = $configuration;
        $this->file_format = $this->configuration['format_file'];
        $this->file_delimiter = $this->getFileDelimiter();
        $this->file_separator = $this->getFileSeparator();
        $this->encoding = $this->configuration['encoding'];

        self::generateGrid($this->spreadsheet_grid);
    }

    public function save($file_path)
    {
        if ($this->file_format != 'xlsx' && $this->file_format != 'csv') {
            throw new \Exception(Module::getInstanceByName('exportproducts')->l('Unsupported file format',__CLASS__));
        }

        if ($this->file_format == 'xlsx') {
            $writer = \PHPExcel_IOFactory::createWriter($this->spreadsheet, 'Excel2007');
            return $writer->save($file_path);
        }

        if ($this->file_format == 'csv') {
            $writer = \PHPExcel_IOFactory::createWriter($this->spreadsheet, 'CSV');
            $writer->setDelimiter($this->file_delimiter);
            $writer->setEnclosure($this->file_separator);

            if ($this->encoding == 'UTF-8-BOM') {
                $writer->setUseBOM(true);
            }

            return $writer->save($file_path);
        }

        return true;
    }

    public function writeHeaderData()
    {
        $this->spreadsheet->getProperties()->setCreator("PHP")
            ->setLastModifiedBy("Admin")
            ->setTitle("Office 2007 XLSX")
            ->setSubject("Office 2007 XLSX")
            ->setDescription(" Office 2007 XLSX, PHPExcel.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("File");

        $this->spreadsheet->getActiveSheet()->setTitle(Module::getInstanceByName('exportproducts')->l('Export',__CLASS__));

        $column_index = 0;

        foreach ($this->configuration['fields'] as $field_id => $head_row_column_title) {
            if ($this->configuration['display_header']) {
                $this->spreadsheet->setActiveSheetIndex(0)->setCellValue($this->spreadsheet_grid[$column_index] . '1', $head_row_column_title['name']);
                $this->spreadsheet->setActiveSheetIndex(0)->getColumnDimension($this->spreadsheet_grid[$column_index])->getAutoSize(true);
                $this->spreadsheet->setActiveSheetIndex(0)->getRowDimension('1')->setRowHeight(25);
            }

            if ($field_id == 'product_link' ||
                $field_id == 'images' ||
                $field_id == 'name' ||
                $field_id == 'description' ||
                $field_id == 'description_short'
            ) {
                $this->spreadsheet->getActiveSheet()->getColumnDimension($this->spreadsheet_grid[$column_index])->setWidth(80);
            } else {
                $this->spreadsheet->getActiveSheet()->getColumnDimension($this->spreadsheet_grid[$column_index])->setWidth(30);
            }

            $column_index++;
        }

        return true;
    }

    public function writeProductDataToFile($product, $line)
    {
        $i = 0;

        foreach ($this->configuration['fields'] as $field_id => $field_data) {
            if ($field_id == 'image_cover' && $this->file_format == 'xlsx' && $product[$field_id] ) {
                if (($mime = @getimagesize($product[$field_id]))) {
                    $image_resource = $this->getImageObject($mime, $product[$field_id]);
                    $drawing_object = new \PHPExcel_Worksheet_MemoryDrawing();
                    $drawing_object->setImageResource($image_resource);
                    $drawing_object->setRenderingFunction(\PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
                    $drawing_object->setMimeType(\PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
                    $drawing_object->setHeight(150);
                    $drawing_object->setOffsetX(6);
                    $drawing_object->setOffsetY(6);
                    $drawing_object->setCoordinates($this->spreadsheet_grid[$i] . $line);
                    $drawing_object->setWorksheet($this->spreadsheet->getActiveSheet());
                    $this->spreadsheet->getActiveSheet()->getRowDimension($line)->setRowHeight(121);
                    $this->spreadsheet->getActiveSheet()->getColumnDimension($this->spreadsheet_grid[$i])->setWidth(23);
                }
            } else {
                $this->spreadsheet->setActiveSheetIndex(0)->setCellValueExplicit($this->spreadsheet_grid[$i] . $line, isset($product[$field_id]) ? $product[$field_id] : '', \PHPExcel_Cell_DataType::TYPE_STRING);
            }

            $i++;
        }
    }

    private function getImageObject($mime, $image_path)
    {
        switch (\Tools::strtolower($mime['mime'])) {
            case 'image/png':
                return imagecreatefrompng($image_path);
            case 'image/jpeg':
                return imagecreatefromjpeg($image_path);
            case 'image/gif':
                return imagecreatefromgif($image_path);
            default:
                return false;
        }
    }

    public function setStyle($row_index)
    {
        $num_of_selected_export_fields = count($this->configuration['fields']);

        if (!$num_of_selected_export_fields) {
            return false;
        }

        $row_index = $row_index - 1;
        $last_column_index = $num_of_selected_export_fields - 1;

        if ($this->configuration['display_header']) {
            $style_hprice = [
                'alignment' => [
                    'horizontal' => \PHPExcel_STYLE_ALIGNMENT::HORIZONTAL_CENTER,
                ],
                'fill'      => [
                    'type'  => \PHPExcel_STYLE_FILL::FILL_SOLID,
                    'color' => ['rgb' => 'CFCFCF']
                ],
                'font'      => [
                    'bold'   => true,
                    'italic' => true,
                    'name'   => 'Times New Roman',
                    'size'   => 13
                ],
            ];
        } else {
            $style_hprice = [
                'alignment' => [
                    'horizontal' => \PHPExcel_STYLE_ALIGNMENT::HORIZONTAL_LEFT,
                    'vertical'   => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ],
                'fill'      => [
                    'type'  => \PHPExcel_STYLE_FILL::FILL_SOLID,
                    'color' => ['rgb' => 'F2F2F5']
                ],
            ];
        }

        $style_wrap = [
            'borders' => [
                'outline' => [
                    'style' => \PHPExcel_Style_Border::BORDER_THICK
                ],
                'allborders' => [
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => [
                        'rgb' => '696969'
                    ]
                ]
            ]
        ];

        $style_price = [
            'alignment' => [
                'horizontal' => \PHPExcel_STYLE_ALIGNMENT::HORIZONTAL_LEFT,
                'vertical'   => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ]
        ];

        $style_background1 = [
            'fill' => [
                'type'  => \PHPExcel_STYLE_FILL::FILL_SOLID,
                'color' => [
                    'rgb' => 'F2F2F5'
                ]
            ]
        ];

        
        $this->spreadsheet->setActiveSheetIndex(0);
        $this->spreadsheet->getActiveSheet()->getStyle('A1:' . $this->spreadsheet_grid[$last_column_index] . '1')->applyFromArray($style_hprice);
        $this->spreadsheet->getActiveSheet()->getStyle('A1:' . $this->spreadsheet_grid[$last_column_index] . ($row_index))->applyFromArray($style_wrap);
        $this->spreadsheet->getActiveSheet()->getStyle('A2:' . $this->spreadsheet_grid[$last_column_index] . ($row_index))->applyFromArray($style_price);
        $this->spreadsheet->getActiveSheet()->getStyle('A2:' . $this->spreadsheet_grid[$last_column_index] . ($row_index))->applyFromArray($style_background1);
    }

    public static function generateGrid(&$grid)
    {
        for ($i = 0; $i < 2000; $i++) {
            $grid[$i] = self::getColumnLetterByNumber($i + 1);
        }

        return true;
    }

    public static function getColumnLetterByNumber($column_number)
    {
        $column_number = (int)($column_number);
        if ($column_number <= 0) {
            return '';
        }

        $column_letter = '';

        while ($column_number != 0) {
            $p = ($column_number - 1) % 26;
            $column_number = (int)(($column_number - $p) / 26);
            $column_letter = chr(65 + $p) . $column_letter;
        }

        return $column_letter;
    }

    private function getFileDelimiter()
    {
        $delimiter_from_configuration = $this->configuration['delimiter_csv'];

        if ($delimiter_from_configuration == 'space') {
            return ' ';
        }

        if ($delimiter_from_configuration == 'tab') {
            return "\t";
        }

        return $delimiter_from_configuration;
    }

    private function getFileSeparator()
    {
        $separator_from_configuration = $this->configuration['separator_csv'];

        switch ($separator_from_configuration) {
            case 3:
                return '';
            case 2:
                return "'";
            default:
                return '"';
        }
    }

    private function getImageResource($mime, $image)
    {
        switch (\Tools::strtolower($mime['mime'])) {
            case 'image/png':
                $img_r = imagecreatefrompng($image);
                break;
            case 'image/jpeg':
                $img_r = imagecreatefromjpeg($image);
                break;
            case 'image/gif':
                $img_r = imagecreatefromgif($image);
                break;
            default:
                $img_r = imagecreatefrompng($image);
        }

        return $img_r;
    }
}