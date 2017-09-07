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

namespace Isotope\Model;

/**
 * Coupon model.
 *
 * @property int    $id
 * @property int    $tstamp
 * @property int    $dateAdded
 * @property int    $product_collection_item
 * @property string $document_number
 * @property string $product_name
 * @property float  $price
 * @property string $owner
 * @property string $code
 * @proeprty string $status
 */
class Coupon extends \Model
{
    const STATUS_DRAFT = 'draft';
    const STATUS_AVAILABLE = 'available';
    const STATUS_REDEEMED = 'redeemed';
    const STATUS_CANCELLED = 'cancelled';

    protected static $strTable = 'tl_iso_coupon';

    /**
     * Returns true if the coupon is in draft status.
     *
     * @return bool
     */
    public function isDraft()
    {
        return self::STATUS_DRAFT === $this->status;
    }

    /**
     * Finds coupon codes for given product collection item. Multiple can exist depending on item quantity.
     *
     * @param ProductCollectionItem $item
     *
     * @return Coupon[]|\Model\Collection
     */
    public static function findByProductCollectionItem(ProductCollectionItem $item)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return static::findBy('product_collection_item', $item->id);
    }

    /**
     * Find coupons by product collection ID.
     *
     * @param int $id
     *
     * @return \Contao\Model\Collection|null
     */
    public static function findByProductCollectionId($id)
    {
        $itemIds = \Database::getInstance()
            ->prepare("SELECT id FROM tl_iso_product_collection_item WHERE pid=?")
            ->execute($id)
            ->fetchEach('id')
        ;

        if (empty($itemIds)) {
            return null;
        }

        return static::findBy([static::$strTable.'.product_collection_item IN ('.implode(',', $itemIds).')'], []);
    }

    /**
     * Generates a random code with given length from given characters.
     *
     * @param array $chars
     * @param int   $length
     *
     * @return string
     */
    public static function generateRandomCode(array $chars, $length)
    {
        return implode(
            '',
            array_intersect_key(
                $chars,
                array_flip(
                    array_rand(
                        $chars,
                        $length
                    )
                )
            )
        );
    }
}
