<?php
class CcProductReviewsSubmitModuleFrontController extends ModuleFrontController
{
    public function postProcess()
    {
        header('Content-Type: application/json');

        if (!$this->context->customer->isLogged()) {
            die(json_encode(['ok'=>false,'error'=>'Debes iniciar sesión.']));
        }

        $idProduct = (int)Tools::getValue('id_product');
        $rating = (int)Tools::getValue('rating');
        $comment = trim((string)Tools::getValue('comment'));

        if ($idProduct <= 0 || $rating < 1 || $rating > 5) {
            die(json_encode(['ok'=>false,'error'=>'Datos inválidos.']));
        }

        require_once _PS_MODULE_DIR_.$this->module->name.'/classes/Review.php';

        $idCustomer = (int)$this->context->customer->id;
        /* para debug quitar después
        if (!Review::customerCanReview($idCustomer, $idProduct)) {
            die(json_encode(['ok'=>false,'error'=>'Solo puedes reseñar si has recibido este producto.']));
        }
        */
        // Guardar reseña
        $name = $this->context->customer->firstname.' '.$this->context->customer->lastname;

        Db::getInstance()->insert('product_review', [
            'id_product' => $idProduct,
            'id_customer' => $idCustomer,
            'customer_name' => pSQL($name),
            'rating' => $rating,
            'comment' => pSQL($comment, true),
            'active' => 0, // pendiente de revisión
            'date_add' => date('Y-m-d H:i:s'),
        ]);
        $idReview = (int)Db::getInstance()->Insert_ID();

        // Fotos (hasta 3)
        $files = $_FILES['photos'] ?? null;
        if ($files && is_array($files['name'])) {
            $count = count($files['name']);
            if ($count > 3) {
                die(json_encode(['ok'=>false,'error'=>'Máximo 3 fotos.']));
            }

            $dir = _PS_IMG_DIR_.'ccproductreviews/uploads/'.$idReview.'/';
            if (!is_dir($dir)) { @mkdir($dir, 0755, true); }

            for ($i=0; $i<$count; $i++) {
                if (empty($files['name'][$i])) continue;

                // Validación upload PS
                $tmp = [
                    'name' => $files['name'][$i],
                    'type' => $files['type'][$i],
                    'tmp_name' => $files['tmp_name'][$i],
                    'error' => $files['error'][$i],
                    'size' => $files['size'][$i],
                ];

                $error = ImageManager::validateUpload($tmp, 2*1024*1024); // 2MB
                if ($error) continue;

                $ext = Tools::strtolower(pathinfo($tmp['name'], PATHINFO_EXTENSION));
                if (!in_array($ext, ['jpg','jpeg','png','webp'])) continue;

                $filename = 'p'.($i+1).'-'.sha1(uniqid('', true)).'.'.$ext;
                $dest = $dir.$filename;

                if (@move_uploaded_file($tmp['tmp_name'], $dest)) {

                    // ✅ Generar thumbnail
                    $thumbName = 'thumb_'.$filename;
                    $thumbPath = $dir.$thumbName;

                    // 420x420 va bien para grid; ajusta si quieres
                    $this->createThumb($dest, $thumbPath, 420, 420);

                    Db::getInstance()->insert('product_review_image', [
                        'id_review' => $idReview,
                        'file_name' => pSQL($filename), // guardamos el original
                        'date_add' => date('Y-m-d H:i:s'),
                    ]);
                }
            }
        }

        die(json_encode(['ok'=>true,'message'=>'¡Gracias! Tu reseña queda pendiente de revisión.']));
    }

    private function createThumb($sourcePath, $destPath, $maxW = 420, $maxH = 420)
    {

        return ImageManager::resize($sourcePath, $destPath, (int)$maxW, (int)$maxH);
    }
}