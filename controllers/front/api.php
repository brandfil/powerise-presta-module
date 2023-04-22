<?php

use Powerise\PrestaShop\Mapper\ProductMapper;

class PoweriseApiModuleFrontController extends ModuleFrontController
{
    const PRODUCTS_BATCH_SIZE = 50;

    const ACTION_PRODUCTS = 'products';

    public function postProcess()
    {
        $page = Tools::getValue('page');
        $action = Tools::getValue('action');

        switch($action) {
            case self::ACTION_PRODUCTS:
                return die(json_encode($this->getProducts($page)));
            default:
                http_response_code(400);
                return die(json_encode(['error' => 'Invalid action.']));
        }
    }

    private function getProducts($page = 1)
    {
        $productMapper = new ProductMapper();
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