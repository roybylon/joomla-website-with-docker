<?php
/**
*
* Show the products in a category
*
* @package	VirtueMart
* @subpackage
* @author RolandD
* @author Max Milbers
* @todo add pagination
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: default.php 5007 2011-12-10 00:05:41Z electrocity $
*/
//vmdebug('$this->category '.$this->category->category_name);
//error_reporting('E_ALL');
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
JHTML::_( 'behavior.modal' );
/* javascript for list Slide
  Only here for the order list
  can be changed by the template maker
*/

$app			= JFactory::getApplication();
$doc			= JFactory::getDocument();
$templateparams	= $app->getTemplate(true)->params;
$template = $app->getTemplate();
$base = $this->baseurl;
$path2 = $base.'/templates/'.$template;

$path_url = $this->baseurl.'/index.php';
$path_url = substr($path_url,1);
$js = "
jQuery(document).ready(function () {
	jQuery('.orderlistcontainer').hover(
		function() { jQuery(this).find('.orderlist').has('div').stop().show()},
		function() { jQuery(this).find('.orderlist').has('div').stop().hide()}
	)
	jQuery('.orderlistcontainer .orderlist').each(function(){
	 	jQuery(this).parent().find('.activeOrder').addClass('block');            
	})

});
";

$document = JFactory::getDocument();
$document->addScriptDeclaration($js);

?>
<?php 
if ($this->category->category_name) 
{ ?>
	<h1 class="browse-view"><span><span><?php echo $this->category->category_name; ?></span></span></h1>
	
<?php } ?>
<?php
//var_dump($this->category->images[0]); 
if ($this->category->category_description || ($this->category->images->virtuemart_media_id > 0) ) { ?>
<div class="wrap-cat">
<img class="cat" src="<?php echo $this->baseurl.'/'.$this->category->images[0]->file_url; ?>" alt="<?php echo $this->category->category_name; ?>"/>
<div class="category_description">
	<?php echo $this->category->category_description ; ?>
</div>
</div>
<?php } ?>
<?php
/* Show child categories */

if ( (VmConfig::get('showCategory',1) && $this->search == !null && $this->category->category_name) || $this->category->category_name) {
	if ($this->category->haschildren) {

		// Category and Columns Counter
		$iCol = 1;
		$iCategory = 1;

		// Calculating Categories Per Row
		$categories_per_row = VmConfig::get ('categories_per_row', 3);
		$category_cellwidth = floor ( 100 / $categories_per_row );

		// Separator
		$verticalseparator = " vertical-separator";
		?>

		<div class="category-view pad-bot hg">

		<?php // Start the Output
		if(!empty($this->category->children)){
		foreach ( $this->category->children as $category ) {

			// Show the horizontal seperator
			if ($iCol == 1 && $iCategory > $categories_per_row) { ?>
			<div class="horizontal-separator"></div>
			<?php }

			// this is an indicator wether a row needs to be opened or not
			if ($iCol == 1) { ?>
			<div class="row">
			<?php }

			// Show the vertical seperator
			if ($iCategory == $categories_per_row or $iCategory % $categories_per_row == 0) {
				$show_vertical_separator = ' ';
			} else {
				$show_vertical_separator = $verticalseparator;
			}

			// Category Link
			$caturl = JRoute::_ ( 'index.php?option=com_virtuemart&view=category&virtuemart_category_id=' . $category->virtuemart_category_id, FALSE );

				// Show Category ?>
				<div class="category floatleft<?php echo$show_vertical_separator ?>" style="width:<?php echo $category_cellwidth ?>%">
					<div class="spacer">
						<h2>
							<a href="<?php echo $caturl ?>" title="<?php echo $category->category_name ?>">
							<div class="category-border">
							<?php // if ($category->ids) {
								echo $category->images[0]->displayMediaThumb("",false);
							//} ?>
							</div>
							<div class="category-title"><?php echo $category->category_name ?></div>
							</a>
						</h2>
					</div>
				</div>
			<?php
			$iCategory ++;

		// Do we need to close the current row now?
		if ($iCol == $categories_per_row) { ?>
		<div class="clear"></div>
		</div>
			<?php
			$iCol = 1;
		} else {
			$iCol ++;
		}
	}
	}
	// Do we need a final closing row tag?
	if ($iCol != 1) { ?>
		<div class="clear"></div>
		</div>
	<?php } ?>
	</div>

<?php }
}
?>
<?php
// Show child categories
if (!empty($this->products)) {
	if (!empty($this->keyword)) {
		?>
		<h3><?php echo $this->keyword; ?></h3>
	<?php }	?>
<?php } ?>
					
 		<?php if ($this->search !== NULL ) { ?>
		
			<?php if (empty($this->products)) { ?>
				<h2><?php echo JText::_('COM_VIRTUEMART_NO_RESULT');?></h2>
			<?php } ?>
			
		    <form action="<?php echo JRoute::_ ('index.php?option=com_virtuemart&view=category&limitstart=0&virtuemart_category_id=' . $this->category->virtuemart_category_id, FALSE); ?>" method="get">

			<!--BEGIN Search Box -->
			<div class="virtuemart_search">
				<?php echo $this->searchcustom ?>
				<br/>
				<br/>
				<div class="clear"></div>
				<?php echo $this->searchcustomvalues ?>
				<div class="clear"></div>
				<br/><input name="keyword" class="inputbox" type="text" size="20" value="<?php echo $this->keyword ?>"/>
				<input type="submit" value="<?php echo JText::_ ('COM_VIRTUEMART_SEARCH') ?>" class="button" onclick="this.form.keyword.focus();"/>
				
			</div>
			<input type="hidden" name="search" value="true"/>
			<input type="hidden" name="view" value="category"/>

			</form>
		<!-- End Search Box -->
		<?php } ?>
		
		
<?php 
$ratingModel = VmModel::getModel('ratings');

// 			$this->assignRef('manufacturerProductsURL', $manufacturerProductsURL);

if (!empty($this->products)) { ?>

			<div class="orderby-displaynumber">
			<div id="navigation">
				<a class="active Clist" href="#" title"List view"></a>
				<a class="Cgrid" href="#" title"Grid view" ></a>
			</div>
				<div class="width90">
					<?php echo $this->orderByList['orderby']; ?>
					<?php 
					if (!empty($this->orderByList['manufacturer'])) {
						echo $this->orderByList['manufacturer'];
					} ?>
					<div class="Results">
						<div class="floatleft display-number"><span><?php echo $this->vmPagination->getResultsCounter();?></span><?php echo $this->vmPagination->getLimitBox ($this->category->limit_list_step); ?></div>
					</div>
				</div>
			<div class="clear"></div>
			</div>
			<div id="bottom-pagination" class="pag-bot"><?php echo $this->vmPagination->getPagesLinks(); ?></div>
			 <!-- end of orderby-displaynumber -->
			<div id="product_list" class="list">
					<?php // Category and Columns Counter
					$counter = 0;
					$iBrowseCol = 1;
					$iBrowseProduct = 1;
					
					// Calculating Products Per Row
					$BrowseProducts_per_row = $this->perRow;
					$Browsecellwidth = floor ( 100 / $BrowseProducts_per_row );
					
					// Separator
					$verticalseparator = " vertical-separator";
					?>
					<div  class="browse-view">
					
					<?php // Start the Output
					foreach ( $this->products as $product ) {
						if (isset($product->step_order_level))
							$step=$product->step_order_level;
						else
							$step=1;
						if($step==0)
							$step=1;
						$alert=JText::sprintf ('COM_VIRTUEMART_WRONG_AMOUNT_ADDED', $step);
						if ($iBrowseCol == 1 && $iBrowseProduct > $BrowseProducts_per_row) {
									?>
								<div class="horizontal-separator"></div>
									<?php
								}
					
						// this is an indicator wether a row needs to be opened or not
						
						if ($counter%2) $cls = "even"; else $cls = "odd";
						if ($iBrowseCol == 1) { ?>
						
						<div class="row element">
						<?php }
					
						// Show the vertical seperator
						if ($iBrowseProduct == $BrowseProducts_per_row or $iBrowseProduct % $BrowseProducts_per_row == 0) {
							$show_vertical_separator = ' ';
						} else {
							$show_vertical_separator = $verticalseparator;
						}
					
							// Show Products ?>
							
							<div class="product1 floatleft" style="width:<?php echo $Browsecellwidth ?>%">
								<div class="spacer <?php if ($product->prices[discountAmount] > 0) {echo 'disc';} ?> ">
									
									<div class="floatleft col-1">
										<div class="browseProductImageContainer">
										
										<?php /** @todo make image popup */
										$imgsmall = JURI::base(true).'/'.$product->images[0]->file_url;
			echo JHTML::link($product->link, $product->images[0]->displayMediaThumb('class="browseProductImage" border="0" title="'.$product->product_name.'" ',false ), 'rel="'.$imgsmall.'" class="img-scr"');
											?>
										</div>
										<?php
											if ( VmConfig::get ('display_stock', 1)) { ?>
											<div class="padding-stock">
												<span class="stock-level"><?php echo JText::_('COM_VIRTUEMART_STOCK_LEVEL_DISPLAY_TITLE_TIP') ?></span>
												<span class="vmicon vm2-<?php echo $product->stock->stock_level ?>" title="<?php echo $product->stock->stock_tip ?>"></span>

											</div>
										<?php }?>
											
									</div>
					
									<div class="floatleft col-3">
										<div class="title-indent">
											<h2><?php echo JHTML::link($product->link, shopFunctionsF::limitStringByWord($product->product_name,50, '...')); ?></h2>
												<?php // Product Short Description
												if(!empty($product->product_s_desc)) { ?>
												<?php echo shopFunctionsF::limitStringByWord($product->product_s_desc, 100, '...') ?>
												<?php } ?>
												
											<div class="rating">
											
													<?php
																										$rating = $ratingModel->getRatingByProduct($product->virtuemart_product_id);
													
													if( !empty($rating)) {
													$r = $rating->rating;
													} else {
													$r = 0;
													}
													$maxrating = VmConfig::get('vm_maximum_rating_scale',5);
													$ratingwidth = ( $r * 100 ) / $maxrating;//I don't use round as percetntage with works perfect, as for me
													?>
													
													<span class="vote">
													<span title="" class="vmicon ratingbox" style="display:inline-block;">
													<span class="stars-orange" style="width:<?php echo $ratingwidth;?>%">
													</span>
													</span>
													</span>


											</div>	
											<div class="detal">
											<?php // Product Details Button
											echo JHTML::link($product->link, JText::_('COM_VIRTUEMART_PRODUCT_DETAILS'), array('title' => $product->product_name,'class' => 'product-details'));
											?>
											</div>
										</div>
									</div>
									<div class="floatright col-2">
									<?php if ((!VmConfig::get('use_as_catalog', 0) and !empty($product->prices['salesPrice'])) && !$product->images[0]->file_is_downloadable) { ?>
										<div class="product-price marginbottom12" id="productPrice<?php echo $product->virtuemart_product_id ?>">
											<?php
											if ($this->show_prices == '1') {
												echo $this->currency->createPriceDiv('salesPrice','',$product->prices);
												//print_r($product->prices);
												if (abs($product->prices[discountAmount]) > 0) {
													echo $this->currency->createPriceDiv('basePriceWithTax','',$product->prices);
												}
												//echo $this->currency->createPriceDiv('discountAmount','COM_VIRTUEMART_PRODUCT_DISCOUNT_AMOUNT',$product->prices);
												
												//echo $this->currency->createPriceDiv('salesPrice','COM_VIRTUEMART_PRODUCT_SALESPRICE',$product->prices);
												//echo $this->currency->createPriceDiv('priceWithoutTax','COM_VIRTUEMART_PRODUCT_SALESPRICE_WITHOUT_TAX',$product->prices);
												
												//echo $this->currency->createPriceDiv('variantModification','COM_VIRTUEMART_PRODUCT_VARIANT_MOD',$product->prices);
												//echo $this->currency->createPriceDiv('basePriceWithTax','COM_VIRTUEMART_PRODUCT_BASEPRICE_WITHTAX',$product->prices);
												//echo $this->currency->createPriceDiv('discountedPriceWithoutTax','COM_VIRTUEMART_PRODUCT_DISCOUNTED_PRICE',$product->prices);
												//echo $this->currency->createPriceDiv('salesPriceWithDiscount','COM_VIRTUEMART_PRODUCT_SALESPRICE_WITH_DISCOUNT',$product->prices);
												//echo $this->currency->createPriceDiv('taxAmount','COM_VIRTUEMART_PRODUCT_TAX_AMOUNT',$product->prices);
											} ?>
										</div>
										<?php  } ?>
									<?php if ((!VmConfig::get('use_as_catalog', 0) and !empty($product->prices['salesPrice'])) && !$product->images[0]->file_is_downloadable) { ?>
									<div class="addtocart-area">
										<form method="post" class="product js-recalculate" action="index.php" id="addtocartproduct<?php echo $product->virtuemart_product_id ?>">
										                <input name="quantity" type="hidden" value="<?php echo $step ?>" />

										<div class="addtocart-bar2">
												<script type="text/javascript">
													function check(obj) {
													// use the modulus operator '%' to see if there is a remainder
													remainder=obj.value % <?php echo $step?>;
													quantity=obj.value;
													if (remainder  != 0) {
														alert('<?php echo $alert?>!');
														obj.value = quantity-remainder;
														return false;
														}
													return true;
													}
											</script> 

											<?php // Display the quantity box 
											//print_r($product);

											$stockhandle = VmConfig::get ('stockhandle', 'none');
			if (
				($stockhandle == 'disableit' or $stockhandle == 'disableadd') and (($product->product_in_stock - $product->product_ordered) < 1) || 
			(
			 ($product->product_in_stock - $product->product_ordered) < $product->min_order_level ))  {
				?>
												
												
										<span class="addtocart-button">
											<i>&gt;</i>
												<a class="addtocart-button not" href="<?php echo JRoute::_('index.php?option=com_virtuemart&view=productdetails&layout=notify&virtuemart_product_id='.$product->virtuemart_product_id); ?>"><?php echo JText::_('COM_VIRTUEMART_CART_NOTIFY') ?></a>
												</span>
												
										<?php } else { ?>	
										<div class="not_notyfi">
										<?php // Display the quantity box 
											if ((!empty($product->customsChilds)) || (!empty($product->customfieldsCart))) { ?>
											<span class="attributes"><b>*</b> Product has attributes</span>
											<span class="addtocart-button">
											<i>&gt;</i>
										<?php echo JHTML::link($product->link, JText::_('COM_VIRTUEMART_CART_ADD_TO'), array('title' => $product->product_name,'class' => 'addtocart-button attribut')); ?>
										</span>
										<?php } else { ?>
										<?php if ($BrowseProducts_per_row <5) { ?>
											<label for="quantity<?php echo $product->virtuemart_product_id;?>" class="quantity_box"><?php echo JText::_('COM_VIRTUEMART_CART_QUANTITY'); ?>: </label>
										<?php } ?>	
											<span class="quantity-box">
											<input type="text" class="quantity-input js-recalculate" name="quantity[]" onblur="check(this);" value="<?php if (isset($product->step_order_level) && (int)$product->step_order_level > 0) {
			echo $product->step_order_level;
		} else if(!empty($product->min_order_level)){
			echo $product->min_order_level;
		}else {
			echo '1';
		} ?>"/>
											</span>
											<span class="quantity-controls">
											<input type="button" class="quantity-controls quantity-plus" />
											<input type="button" class="quantity-controls quantity-minus" />
											</span>
											<?php // Add the button
											$button_lbl = JText::_('COM_VIRTUEMART_CART_ADD_TO');
											$button_cls = 'addtocart-button cart-click'; //$button_cls = 'addtocart_button';
											?>
											<?php // Display the add to cart button ?>
											<div class="clear"></div>
											<span class="addtocart-button">
											<i>&gt;</i>
												<input type="submit" name="<?php echo $button_name ?>"  class="<?php echo $button_cls ?>" value="<?php echo $button_lbl ?>" title="<?php echo $button_lbl ?>" />
											</span>
											<?php if ($BrowseProducts_per_row <= 3) { ?>
											<div class="det">
												<?php // Product Details Button
												echo JHTML::link($product->link, JText::_('TM_DETAILS').'<span>&gt;</span>', array('title' => $product->product_name,'class' => 'button'));
												?>
											</div>
											<?php } ?>
											<div class="clear"></div>
											<?php }?>
											</div>
											<?php }?>
										
										</div>
										<input type="hidden" class="pname" value="<?php echo $product->product_name ?>"/>
										<input type="hidden" name="option" value="com_virtuemart" />
										<input type="hidden" name="view" value="cart" />
										<noscript><input type="hidden" name="task" value="add" /></noscript>
										<input type="hidden" name="virtuemart_product_id[]" value="<?php echo $product->virtuemart_product_id ?>" />
										<input type="hidden" name="virtuemart_category_id[]" value="<?php echo $product->virtuemart_category_id ?>" />
										
									</form>
									</div>
								<?php }?>
									</div>
								<div class="clear"></div>
								</div>
							</div>
						<?php
						$iBrowseProduct ++;
						$counterr++;
					
						// Do we need to close the current row now?
						if ($iBrowseCol == $BrowseProducts_per_row) { ?>
						<div class="clear"></div>
						</div>
							<?php
							$iBrowseCol = 1;
						} else {
							$iBrowseCol ++;
							$counter++;
						}
					}
					// Do we need a final closing row tag?
					if ($iBrowseCol != 1) { ?>
						<div class="clear"></div>
						</div>
					<?php
					}
					?>
					
				<div id="page-nav" name="page-nav" style="display:none">
						<?php echo $this->vmPagination->getPagesLinks(); ?>
					</div>

</div>
</div>
	<?php // Show child categories
	}
	?>
	<script type="text/javascript" src="<?php echo $path2 ?>/html/com_virtuemart/category/cookie.js"></script>
<script type="text/javascript">
 jQuery(document).ready(function($) {
	var cc = $.cookie('list_grid');
	if (cc == 'g') {
		$('#product_list').addClass('grid');
		$('#product_list').removeClass('list');
		$('.Clist').removeClass('active');
		$('.Cgrid').addClass('active');
	} else  {
		$('#product_list').removeClass('grid');
		$('#product_list').addClass('list');
		$('.Clist').addClass('active');
		$('.Cgrid').removeClass('active');
	}
	
	$('.Cgrid').click(function() {
		$('#product_list').fadeOut(300, function() {
			$(this).addClass('grid').removeClass('list').fadeIn(300);
		});
		$('.Clist').removeClass('active');
		$('.Cgrid').addClass('active');
		$.cookie('list_grid', 'g' , {path: '/<?php echo $path_url ?>'});
		return false;
	});
	$('.Clist').click(function() {
		$('#product_list').fadeOut(300, function() {
			$(this).removeClass('grid').addClass('list').fadeIn(300);
		});
		$('.Clist').addClass('active');
		$('.Cgrid').removeClass('active');
		$.cookie('list_grid', 'l' , {path: '/<?php echo $path_url ?>'});
		
		return false;
	});
});

</script>

	
	<div class="clear"></div>
	<div id="bottom-pagination"><?php echo $this->vmPagination->getPagesLinks(); ?></div>
		<?php echo $this->loadTemplate('recent'); ?>