<?php

namespace Powerise\PrestaShop\Mapper;

class ProductMapper
{
    private $context;

    public function __construct($context)
    {
        $this->context = $context;
    }

    public function mapArray(array $products)
    {
        return array_map([$this, 'map'], $products);
    }

    public function map($product)
    {
        $image = \Image::getCover($product['id_product']);
        return [
            'id' => $product['id_product'],
            'name' => $product['name'],
            'sku' => $product['reference'],
            'url' => $this->context->link->getProductLink($product['id_product']),
            'image' => $this->context->link->getImageLink(
                $product['link_rewrite'],
                $image['id_image'],
                \ImageType::getFormattedName('home')
            ),
            'description' => $product['description'],
            'price' => $product['price'],
        ];
    }
}
