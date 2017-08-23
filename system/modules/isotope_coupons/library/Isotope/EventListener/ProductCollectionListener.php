<?php

/**
 * Isotope eCommerce for Contao Open Source CMS
 *
 * Copyright (C) 2009-2014 terminal42 gmbh & Isotope eCommerce Workgroup
 *
 * @package    Isotope
 * @link       http://isotopeecommerce.org
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */

namespace Isotope\EventListener;

use Isotope\Interfaces\IsotopeProductCollection;
use Isotope\Model\Coupon;
use Isotope\Model\Product\CouponProduct;
use Isotope\Model\ProductCollection\Order;
use Isotope\Model\ProductCollectionItem;
use Isotope\Template;

class ProductCollectionListener
{
    /**
     * @param Template                 $template
     * @param array                    $items
     * @param IsotopeProductCollection $collection
     */
    public function onAddCollectionToTemplate(Template $template, array &$items, IsotopeProductCollection $collection)
    {
        if (!$collection instanceof Order) {
            return;
        }

        foreach ($items as $k => $row) {
            $item = $row['item'];
            $product = $row['product'];

            if (!$item instanceof ProductCollectionItem || !$product instanceof CouponProduct) {
                continue;
            }

            $templateItems = $template->items;
            $templateItems[$k]['coupons'] = Coupon::findByProductCollectionItem($item);
            $template->items = $templateItems;
        }
    }
}
