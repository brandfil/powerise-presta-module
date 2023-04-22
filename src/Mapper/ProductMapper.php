<?php

namespace Powerise\PrestaShop\Mapper;

class ProductMapper
{
    public function mapArray(array $products)
    {
        return array_map([$this, 'map'], $products);
    }

    public function map($product)
    {
        $link = new \Link();
        $image = \Image::getCover($product['id_product']);
        return [
            'id' => $product['id_product'],
            'name' => $product['name'],
            'sku' => $product['reference'],
            'image' => $link->getImageLink($product['link_rewrite'], $image['id_image'], 'home_default'),
            'description' => $product['description'],
            'price' => $product['price'],
        ];
    }
}
