<?php

class PoweriseApiModuleFrontController extends ModuleFrontController
{
    const PRODUCTS_BATCH_SIZE = 50;

    public function postProcess()
    {
        $page = Tools::getValue('page');
        $products = \Product::getProducts(
            $this->context->language->id,
            ($page - 1) * self::PRODUCTS_BATCH_SIZE,
            $page * self::PRODUCTS_BATCH_SIZE,
            'id_product',
            'ASC',
            false,
            true
        );
        return die(json_encode($products));
    }
}
