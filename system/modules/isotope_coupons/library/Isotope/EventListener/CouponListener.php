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

use Isotope\Interfaces\IsotopeOrderableCollection;
use Isotope\Interfaces\IsotopePurchasableCollection;
use Isotope\Model\Coupon;
use Isotope\Model\OrderStatus;
use Isotope\Model\Product\CouponProduct;
use Isotope\Model\ProductType;

class CouponListener
{

    /**
     * Adds a coupon code for each respective product on postCheckout hook.
     *
     * @param IsotopeOrderableCollection $order
     */
    public function onPostCheckout(IsotopeOrderableCollection $order)
    {
        foreach ($order->getItems() as $item) {
            $product = $item->getProduct();

            if ($product instanceof CouponProduct) {
                $time = time();
                $type = $product->getType();

                try {
                    \Database::getInstance()->lockTables(array('tl_iso_coupon' => 'WRITE'));

                    for ($i = 0; $i < $item->quantity; $i++) {
                        $coupon = new Coupon();
                        $coupon->tstamp = $time;
                        $coupon->dateAdded = $time;
                        $coupon->product_collection_item = $item->id;
                        $coupon->document_number = $order->getDocumentNumber();
                        $coupon->product_name = $item->name;
                        $coupon->price = $item->price;
                        $coupon->owner = strip_tags($order->getBillingAddress()->generate());
                        $coupon->code = $this->generateCode($type);
                        $coupon->status = Coupon::STATUS_DRAFT;
                        $coupon->save();
                    }

                    \Database::getInstance()->unlockTables();
                } catch (\Exception $e) {
                    // Make sure tables are always unlocked
                    \Database::getInstance()->unlockTables();

                    throw $e;
                }
            }
        }
    }

    /**
     * Updates state for all coupon codes on order status change.
     *
     * @param IsotopeOrderableCollection $order
     * @param int                        $oldStatusId
     * @param OrderStatus                $newStatus
     */
    public function onPostOrderStatusUpdate(IsotopeOrderableCollection $order, $oldStatusId, OrderStatus $newStatus)
    {
        if (!$newStatus->isPaid() && (!$order instanceof IsotopePurchasableCollection || !$order->isPaid())) {
            return;
        }

        foreach ($order->getItems() as $item) {
            if ($item->getProduct() instanceof CouponProduct) {
                $coupons = Coupon::findByProductCollectionItem($item);

                if (null !== $coupons) {
                    foreach ($coupons as $coupon) {
                        if ($coupon->isDraft()) {
                            $coupon->status = Coupon::STATUS_AVAILABLE;
                            $coupon->save();
                        }
                    }
                }
            }
        }
    }

    /**
     * Generates a coupon code based on product type configuration.
     *
     * @param ProductType $type
     *
     * @return string
     */
    private function generateCode(ProductType $type)
    {
        $code = $this->getPrefixedNumber($type->coupon_prefix, $type->coupon_numbers) . '-';

        $code .= Coupon::generateRandomCode(
            str_split($type->coupon_alphabet),
            $type->coupon_chars
        );

        return $code;
    }

    /**
     * Gets next higher prefixed number based on database records.
     *
     * @param string $prefix
     * @param string $length
     *
     * @return string
     */
    private function getPrefixedNumber($prefix, $length)
    {
        $prefix = \Controller::replaceInsertTags($prefix, false);
        $skip   = utf8_strlen($prefix) + 2;

        if ('' !== $prefix) {
            $query = "SELECT code FROM tl_iso_coupon WHERE code LIKE ? ORDER BY CAST(SUBSTRING(code, $skip) AS UNSIGNED) DESC";
        } else {
            $query = "SELECT code FROM tl_iso_coupon ORDER BY CAST(code AS UNSIGNED) DESC";
        }

        $highest = \Database::getInstance()->prepare($query)->limit(1)->execute($prefix . '%')->code;
        $highest = (int) substr($highest, $skip - 1);

        return $prefix . ($prefix ? '-' : '') . str_pad($highest + 1, $length, '0', STR_PAD_LEFT);
    }
}
