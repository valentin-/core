<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * TYPOlight Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  Winans Creative 2009, Intelligent Spark 2010, iserv.ch GmbH 2010
 * @author     Fred Bliss <fred.bliss@intelligentspark.com>
 * @author     Andreas Schempp <andreas@schempp.ch>
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */


/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_iso_rules']['type']					= array('Type','Please choose the type of rule.');
$GLOBALS['TL_LANG']['tl_iso_rules']['name']          		= array('Name', 'Please enter a name for this rule.');
$GLOBALS['TL_LANG']['tl_iso_rules']['label']          		= array('Label', 'The label will be show in cart. If you do not enter a label, the name will be used.');
$GLOBALS['TL_LANG']['tl_iso_rules']['discount']				= array('Discount','Valid values are decimals or whole numbers, minus a numerical value or minus a percentage.');
$GLOBALS['TL_LANG']['tl_iso_rules']['enableCode']			= array('Enable coupon code','Require a code to be entered to invoke this rule, as a coupon.');
$GLOBALS['TL_LANG']['tl_iso_rules']['code']					= array('Rule (coupon) code','Please enter a code by which a customer will invoke this rule, as a coupon.');
$GLOBALS['TL_LANG']['tl_iso_rules']['numUses']       		= array('Number of uses', 'This will be used to see if the rule has already been redeemed.  If this is set to 0, it can be used unlimited times for each customer.');
$GLOBALS['TL_LANG']['tl_iso_rules']['minSubTotal']			= array('Minimum Subtotal','Specify a minimum subtotal for items in cart that this rule can be applied to.');
$GLOBALS['TL_LANG']['tl_iso_rules']['minCartQuantity']		= array('Minimum Cart Quantity','Specify a minimum quantity of items in cart that this rule can be applied to.');
$GLOBALS['TL_LANG']['tl_iso_rules']['maxCartQuantity']		= array('Maximum Cart Quantity','Specify a maximum quantity of items in cart that this rule can be applied to.');
$GLOBALS['TL_LANG']['tl_iso_rules']['minItemQuantity']		= array('Minimum item quantity','Please specify a minimum quantity of a an item this rule applies to.');
$GLOBALS['TL_LANG']['tl_iso_rules']['maxItemQuantity']		= array('Maximum item quantity','Please specify a maximum quantity of a single item this rule applies to.');

$GLOBALS['TL_LANG']['tl_iso_rules']['ruleRestrictions']  	= array('Rule restrictions', 'Restrict a rule to be only usable alone or with certain rule.');
$GLOBALS['TL_LANG']['tl_iso_rules']['rules']     			= array('Rules', 'Select other rule this rule is usable with, or you may indicate that this rule is usable with no other rule.');
$GLOBALS['TL_LANG']['tl_iso_rules']['dateRestrictions']		= array('Date restrictions','If desired, please specify a start and end date this rule is eligible for.');
$GLOBALS['TL_LANG']['tl_iso_rules']['timeRestrictions']		= array('Time restrictions','If desired, please specify a start and end time this rule is eligible for.');
$GLOBALS['TL_LANG']['tl_iso_rules']['startDate']      		= array('Start date', 'If desired, please specify the date this rule will become eligible on.');
$GLOBALS['TL_LANG']['tl_iso_rules']['endDate']        		= array('End date', 'If desired, please specify the date this rule will no longer be eligible on.');
$GLOBALS['TL_LANG']['tl_iso_rules']['protected']			= array('Protected','Further restrict the eligibility properties of this rule.');

$GLOBALS['TL_LANG']['tl_iso_rules']['memberRestrictions']	= array('Member restrictions','Restrict a rule to certain groups or members');
$GLOBALS['TL_LANG']['tl_iso_rules']['productRestrictions']	= array('Product restrictions','Restrict this rule to certain product types, categories, or to individual products.');
$GLOBALS['TL_LANG']['tl_iso_rules']['members']        		= array('Members', 'Select members this rule is restricted to.');
$GLOBALS['TL_LANG']['tl_iso_rules']['groups']         		= array('Groups', 'Select groups this rule is restricted to.');
$GLOBALS['TL_LANG']['tl_iso_rules']['pages']     			= array('Categories', 'Select categories this rule is restricted to.  If none, all are eligible.');
$GLOBALS['TL_LANG']['tl_iso_rules']['products']				= array('Products','Select products this rule is restricted to. If none, all are eligible.');
$GLOBALS['TL_LANG']['tl_iso_rules']['producttypes']			= array('Product Types','Select the product types this rule is restricted to. If none, all are eligible.');
$GLOBALS['TL_LANG']['tl_iso_rules']['enabled']				= array('Enabled','Please select whether this rule is currently enabled or not.');


/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_iso_rules']['general_legend']			= 'General Information';
$GLOBALS['TL_LANG']['tl_iso_rules']['type_legend']				= 'Rule Type';
$GLOBALS['TL_LANG']['tl_iso_rules']['restriction_legend']		= 'Rule Restrictions'; 
$GLOBALS['TL_LANG']['tl_iso_rules']['enabled_legend']			= 'Module Enabling Details';


/**
 * Reference
 */
$GLOBALS['TL_LANG']['tl_iso_rules']['type']['product']						= 'Product';
$GLOBALS['TL_LANG']['tl_iso_rules']['type']['cart']							= 'Cart';

$GLOBALS['TL_LANG']['tl_iso_rules']['memberRestrictions']['none']			= 'No restrictions';
$GLOBALS['TL_LANG']['tl_iso_rules']['memberRestrictions']['groups']			= 'Specific groups';
$GLOBALS['TL_LANG']['tl_iso_rules']['memberRestrictions']['members']		= 'Specific members';

$GLOBALS['TL_LANG']['tl_iso_rules']['productRestrictions']['none']			= 'No restrictions';
$GLOBALS['TL_LANG']['tl_iso_rules']['productRestrictions']['producttypes']	= 'Product types';
$GLOBALS['TL_LANG']['tl_iso_rules']['productRestrictions']['pages']			= 'Specific categories';
$GLOBALS['TL_LANG']['tl_iso_rules']['productRestrictions']['products']		= 'Specific products';

$GLOBALS['TL_LANG']['tl_iso_rules']['ruleRestrictions']['all']				= 'Exclude all other rules';
$GLOBALS['TL_LANG']['tl_iso_rules']['ruleRestrictions']['none']				= 'No rule exclusions';
$GLOBALS['TL_LANG']['tl_iso_rules']['ruleRestrictions']['rules']			= 'Exclude certain rules';

$GLOBALS['TL_LANG']['tl_iso_rules']['numUses']['customer']					= 'Per customer';
$GLOBALS['TL_LANG']['tl_iso_rules']['numUses']['store']						= 'Per store';


/**
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_iso_rules']['new']        = array('New rule', 'Create a new rule');
$GLOBALS['TL_LANG']['tl_iso_rules']['edit']       = array('Edit rule', 'Edit rule ID %s');
$GLOBALS['TL_LANG']['tl_iso_rules']['copy']       = array('Duplicate rule', 'Duplicate rule ID %s');
$GLOBALS['TL_LANG']['tl_iso_rules']['delete']     = array('Delete rule', 'Delete rule ID %s');
$GLOBALS['TL_LANG']['tl_iso_rules']['show']       = array('Rule details', 'Show the details of rule ID %s');

