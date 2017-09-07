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

namespace Isotope\Backend\Coupon;

use Haste\Http\Response\HtmlResponse;
use Isotope\Model\ProductCollectionItem;

class Callback extends \Backend
{
    /** @noinspection MoreThanThreeArgumentsInspection */
    public function generateLabel($row, $label, \DataContainer $dc, $args)
    {
        $fields = array_flip(array_values($GLOBALS['TL_DCA']['tl_iso_coupon']['list']['label']['fields']));

        if (isset($fields['document_number'])) {
            $item = ProductCollectionItem::findByPk($row['product_collection_item']);

            if ($item instanceof ProductCollectionItem) {
                $args[$fields['document_number']] = sprintf(
                    '<a href="%s">%s</a>',
                    \Backend::addToUrl('do=iso_orders&act=edit&id='.$item->pid),
                    $args[$fields['document_number']]
                );
            }
        }

        if (isset($fields['code'])) {
            if (empty($args[$fields['code']])) {
                $code = sprintf(
                    '<span style="color:#ccc">%s</span>',
                    $GLOBALS['TL_LANG']['tl_iso_coupon']['codeUnavailable']
                );
            } else {
                switch ($row['status']) {
                    case 'draft':
                        $color = '#ccc';
                        break;

                    case 'available':
                        $color = 'green';
                        break;

                    case 'redeemed':
                    default:
                        $color = 'inherit';
                        break;

                    case 'cancelled':
                        $color = 'red';
                        break;
                }

                $code = sprintf('<span style="color:%s">%s</span>', $color, $row['code']);
            }

            $args[$fields['code']] = $code;
        }

        return $args;
    }

    /**
     * Return the "check" operation
     *
     * @param array  $row
     * @param string $href
     * @param string $label
     * @param string $title
     * @param string $icon
     * @param string $attributes
     *
     * @return string
     */
    public function checkIcon($row, $href, $label, $title, $icon, $attributes)
    {
        if ('redeemed' !== $row['status'] && 'available' !== $row['status']) {
            return '';
        }

        if ('redeemed' === $row['status']) {
            $icon       = 'system/modules/isotope_coupons/assets/uncheck.png';
            $label      = $GLOBALS['TL_LANG']['tl_iso_coupon']['uncheck'][0];
            $title      = sprintf($GLOBALS['TL_LANG']['tl_iso_coupon']['uncheck'][1], $row['id']);
            $attributes = 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['tl_iso_coupon']['uncheckConfirm'] . '\'))return false;Backend.getScrollOffset()"';
        }

        return '<a href="'.\Backend::addToUrl($href . '&amp;id=' . $row['id']).'" title="'.specialchars($title).'"'.$attributes.'>'.\Image::getHtml($icon, $label).'</a> ';
    }

    /**
     * Update code status and redirect back to list.
     *
     * @param $dc
     */
    public function toggleStatus($dc)
    {
        $coupon = \Database::getInstance()->prepare("SELECT * FROM tl_iso_coupon WHERE id=?")->execute($dc->id);

        switch ($coupon->status) {
            case 'available':
                \Database::getInstance()
                    ->prepare("UPDATE tl_iso_coupon SET status=? WHERE id=?")
                    ->execute('redeemed', $coupon->id)
                ;
                break;

            case 'redeemed':
                \Database::getInstance()
                     ->prepare("UPDATE tl_iso_coupon SET status=? WHERE id=?")
                     ->execute('available', $coupon->id)
                ;
                break;

            case 'draft':
            case 'cancelled':
            default:
                $response = new HtmlResponse('', 400);
                $response->send();
        }

        \Controller::redirect(\System::getReferer());
    }

    /**
     * Mark coupons as cancelled if the order is deleted.
     *
     * @param \DataContainer $dc
     */
    public function onDeleteProductCollection(\DataContainer $dc)
    {
        $coupons = \Database::getInstance()->prepare("
            SELECT id 
            FROM tl_iso_coupon 
            WHERE product_collection_item IN (
                SELECT id FROM tl_iso_product_collection_item WHERE pid=?
            )
        ")->execute($dc->id);

        if ($coupons->numRows > 0) {
            \Database::getInstance()->prepare("
                UPDATE tl_iso_coupon SET status=? WHERE id IN (".implode(',', $coupons->fetchEach('id')).")
            ")->execute(\Isotope\Model\Coupon::STATUS_CANCELLED);
        }
    }
}
