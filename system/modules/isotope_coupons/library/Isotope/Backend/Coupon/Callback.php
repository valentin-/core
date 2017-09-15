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
use Isotope\Model\Coupon;
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
     * @param array $row
     *
     * @return string
     */
    public function checkIcon($row)
    {
        switch ($row['status']) {
            case 'available':
                $icon       = 'system/modules/isotope_coupons/assets/check.png';
                $label      = $GLOBALS['TL_LANG']['tl_iso_coupon']['check'][0];
                $title      = sprintf($GLOBALS['TL_LANG']['tl_iso_coupon']['check'][1], $row['id']);
                $attributes = 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['tl_iso_coupon']['checkConfirm'] . '\'))return false;Backend.getScrollOffset()"';
                break;

            case 'redeemed':
                $icon       = 'system/modules/isotope_coupons/assets/uncheck.png';
                $label      = $GLOBALS['TL_LANG']['tl_iso_coupon']['uncheck'][0];
                $title      = sprintf($GLOBALS['TL_LANG']['tl_iso_coupon']['uncheck'][1], $row['id']);
                $attributes = 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['tl_iso_coupon']['uncheckConfirm'] . '\'))return false;Backend.getScrollOffset()"';
                break;

            default:
                return '';
        }

        $href = sprintf(
            '%s?do=iso_coupons&amp;act=check&amp;key=check&amp;id=%s&amp;return=%s.%s&amp;rt=%s',
            TL_SCRIPT,
            $row['id'],
            \Input::get('do'),
            \Input::get('id'),
            REQUEST_TOKEN
        );

        return '<a href="'.$href.'" title="'.specialchars($title).'"'.$attributes.'>'.\Image::getHtml($icon, $label).'</a> ';
    }

    /**
     * Update code status and redirect back to list.
     *
     * @param $dc
     */
    public function toggleStatus($dc)
    {
        if (!isset($_GET['rt']) || !\RequestToken::validate(\Input::get('rt')))
        {
            \Session::getInstance()->set('INVALID_TOKEN_URL', \Environment::get('request'));
            \Controller::redirect('contao/confirm.php');
        }

        $coupon = \Database::getInstance()->prepare('SELECT * FROM tl_iso_coupon WHERE id=?')->execute($dc->id);

        switch ($coupon->status) {
            case 'available':
                \Database::getInstance()
                    ->prepare('UPDATE tl_iso_coupon SET status=? WHERE id=?')
                    ->execute('redeemed', $coupon->id)
                ;
                break;

            case 'redeemed':
                \Database::getInstance()
                     ->prepare('UPDATE tl_iso_coupon SET status=? WHERE id=?')
                     ->execute('available', $coupon->id)
                ;
                break;

            case 'draft':
            case 'cancelled':
            default:
                $response = new HtmlResponse('', 400);
                $response->send();
        }

        list($returnTo, $returnId) = explode('.', \Input::get('return'), 2);

        if ($returnTo === 'iso_orders') {
            \Controller::redirect(TL_SCRIPT.'?do=iso_orders&amp;act=edit&amp;id='.$returnId.'&rt='.REQUEST_TOKEN);
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

    /**
     * Generate backend view for coupons inside order.
     *
     * @param \DataContainer $dc
     */
    public function generateOrderData($dc)
    {
        \System::loadLanguageFile('tl_iso_coupon');

        $coupons = Coupon::findByProductCollectionId($dc->id);

        if (null === $coupons) {
            return '<p class="tl_info" style="margin-top: 10px;">'.$GLOBALS['TL_LANG']['tl_iso_product_collection']['coupon_empty'].'</p>';
        }

        $buffer = '
<div>
<table cellpadding="0" cellspacing="0" class="tl_show">
  <thead>
  <tr>
      <td class="tl_bg"><span class="tl_label">'.$GLOBALS['TL_LANG']['tl_iso_coupon']['product_name'][0].'</span></td>  
      <td class="tl_bg"><span class="tl_label">'.$GLOBALS['TL_LANG']['tl_iso_coupon']['code'][0].'</span></td>  
      <td class="tl_bg"><span class="tl_label">'.$GLOBALS['TL_LANG']['tl_iso_coupon']['status'][0].'</span></td>
      <td class="tl_bg">&nbsp;</td>
    </tr>
</thead>
  <tbody>
  ';

        $i = 0;
        $plabel = $GLOBALS['TL_LANG']['tl_iso_coupon']['show'];

        /** @var Coupon $coupon */
        foreach ($coupons as $coupon) {
            $class = (++$i % 2) ? '' : ' class="tl_bg"';
            $status = $GLOBALS['TL_LANG']['tl_iso_coupon']['status'][$coupon->status] ?: $coupon->status;

            $buffer .= '
  <tr>
    <td' . $class . '>' . $coupon->product_name . ': </td>
    <td' . $class . '>' . $coupon->code . '</td>
    <td' . $class . '>' . $status . '</td>
    <td' . $class . '>
      <a href="'.\Backend::addToUrl('do=iso_coupons&act=show&id='.$coupon->id.'&popup=1').'" onclick="Backend.openModalIframe({\'width\':768,\'title\':\''.sprintf($plabel[1], $coupon->id).'\',\'url\':this.href});return false" class="show">'.\Image::getHtml('show.gif', $plabel[0]).'</a>    
    </td>
  </tr>';
        }

        $buffer .= '
</tbody></table>
</div>';

        return $buffer;
    }
}
