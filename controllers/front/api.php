<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */
use Powerise\PrestaShop\Mapper\ProductMapper;

class PoweriseApiModuleFrontController extends ModuleFrontController
{
    const PRODUCTS_BATCH_SIZE = 50;

    const ACTION_PRODUCTS = 'products';

    const ACTION_PRODUCT_UPDATE = 'product-update';

    public function postProcess()
    {
        $apiKey = !empty($_SERVER['HTTP_X_API_KEY']) ? $_SERVER['HTTP_X_API_KEY'] : null;
        if ($apiKey !== Configuration::get('POWERISE_API_KEY')) {
            http_response_code(401);

            return exit(json_encode(['error' => 'Invalid API key.']));
        }

        $page = Tools::getValue('page');
        $action = Tools::getValue('action');

        header('Content-Type: application/json');
        switch ($action) {
            case self::ACTION_PRODUCTS:
                http_response_code(200);

                return exit(json_encode($this->getProducts($page)));
            case self::ACTION_PRODUCT_UPDATE:
                $json = \Tools::file_get_contents('php://input');
                $data = json_decode($json, true);
                $product = new \Product($data['id']);
                $product->description = $data['description'];
                $product->save();
                http_response_code(200);

                return exit(json_encode($product)); // TODO: MAP PRODUCT
            default:
                http_response_code(400);

                return exit(json_encode(['error' => 'Invalid action.']));
        }
    }

    private function getProducts($page = 1)
    {
        $productMapper = new ProductMapper($this->context);
        $products = \Product::getProducts(
            $this->context->language->id,
            ($page - 1) * self::PRODUCTS_BATCH_SIZE,
            $page * self::PRODUCTS_BATCH_SIZE,
            'id_product',
            'ASC',
            false,
            true
        );

        return $productMapper->mapArray($products);
    }
}
