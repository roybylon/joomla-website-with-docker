<?php
/**
 *
 * Show the product details page
 *
 * @package	VirtueMart
 * @subpackage
 * @author Max Milbers, Valerie Isaksen

 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: default_relatedproducts.php 5406 2012-02-09 12:22:33Z alatak $
 */

// Check to ensure this file is included in Joomla!
defined ( '_JEXEC' ) or die ( 'Restricted access' );
$featured_per_row = 4;
$iCol = 1;
//Set the cell width
$cellwidth = intval( (100 / $featured_per_row) );
$counter = 0;
?>
<h2><?php echo JText::_('COM_VIRTUEMART_RELATED_PRODUCTS'); ?></h2>
      <div class="product-related-products">
    	

    <?php
    foreach ($this->product->customfieldsRelatedProducts as $field) {
		
	?><div class="product-field<?php if ($counter % 2) echo 'null'?> product-field-type-<?php echo $field->field_type ?>" style="width:<?php echo $cellwidth ?>%;">
		    <span class="product-field-display"><div class="releted"><?php echo $field->display ?></div></span>
		</div>
	<?php 
		// Do we need to close the current row now?
	if ($iCol == $featured_per_row) { // If the number of products per row has been reached
		echo "<div class=\"product-field_h\" /></div>\n";
		$iCol = 1;
	}
	else {
		$iCol++;
	}

 }?>
	
</div>
