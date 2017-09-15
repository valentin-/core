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
    'label'                 => &$GLOBALS['TL_LANG']['tl_iso_product_collection']['coupon_data'],
    'inputType'             => 'dcaWizard',
    'foreignTable'          => 'tl_iso_coupon',
    'foreignField'          => 'product_collection_id',
    'eval'                  => array
    (
        'fields'            => ['product_name', 'code', 'status'],
        'showOperations'    => true,
        'hideButton'        => true,
        'emptyLabel'        => &$GLOBALS['TL_LANG']['tl_iso_product_collection']['coupon_empty'],
        'tl_class'          => 'clr',
        'doNotShow'         => true,
    ),
);
