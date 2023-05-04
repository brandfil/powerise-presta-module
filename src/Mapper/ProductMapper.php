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
