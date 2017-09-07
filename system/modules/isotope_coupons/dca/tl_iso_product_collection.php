<?php

/**
 * Config
 */
$GLOBALS['TL_DCA']['tl_iso_product_collection']['config']['ondelete_callback'] = ['Isotope\Backend\Coupon\Callback', 'onDeleteProductCollection'];

/**
 * Palettes
 */
\Haste\Dca\PaletteManipulator::create()
    ->addLegend('coupon_legend', 'email_legend', \Haste\Dca\PaletteManipulator::POSITION_BEFORE)
    ->addField('coupon_data', 'coupon_legend', \Haste\Dca\PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('default', 'tl_iso_product_collection')
;

/**
 * Fields
 */

$GLOBALS['TL_DCA']['tl_iso_product_collection']['fields']['coupon_data'] = array
(
    'input_field_callback'  => array('Isotope\Backend\Coupon\Callback', 'generateOrderData'),
    'eval'                  => array('doNotShow' => true),
);
