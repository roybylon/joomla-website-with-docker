<?php
/**
 *
 * Show the product details page
 *
 * @package	VirtueMart
 * @subpackage
 * @author Max Milbers, Eugen Stranz
 * @author RolandD,
 * @todo handle child products
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: default.php 5444 2012-02-15 15:31:35Z Milbo $
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
//error_reporting('E_ALL');
// addon for joomla modal Box
JHTML::_('behavior.modal');
// JHTML::_('behavior.tooltip');
$document = JFactory::getDocument();
$document->addScriptDeclaration("
	jQuery(document).ready(function($) {
		$('a.ask-a-question').click( function(){
			$.facebox({
				iframe: '" . $this->askquestion_url . "',
				rev: 'iframe|550|800'
			});
			return false ;
		});
	/*	$('.additional-images a').mouseover(function() {
			var himg = this.href ;
			var extension=himg.substring(himg.lastIndexOf('.')+1);
			if (extension =='png' || extension =='jpg' || extension =='gif') {
				$('.main-image img').attr('src',himg );
			}
			console.log(extension)
		});*/
	});
");
/* Let's see if we found the product */
if (empty($this->product)) {
    echo JText::_('COM_VIRTUEMART_PRODUCT_NOT_FOUND');
    echo '<br /><br />  ' . $this->continue_link_html;
    return;
}
?>
 <?php
	 if (!empty($this->product->customfieldsSorted['ontop'])) {
	$this->position = 'ontop';
	echo $this->loadTemplate('customfields');
    } // Product custom_fields END 
	?>
<div class="productdetails-view">
	<?php
    // PDF - Print - Email Icon
    if (VmConfig::get('show_emailfriend') || VmConfig::get('show_printicon') || VmConfig::get('pdf_button_enable')) {
	?>
        <div class="icons">
	    <?php
	    //$link = (JVM_VERSION===1) ? 'index2.php' : 'index.php';
	    $link = 'index.php?tmpl=component&option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $this->product->virtuemart_product_id;
	    $MailLink = 'index.php?option=com_virtuemart&view=productdetails&task=recommend&virtuemart_product_id=' . $this->product->virtuemart_product_id . '&virtuemart_category_id=' . $this->product->virtuemart_category_id . '&tmpl=component';

	    if (VmConfig::get('pdf_icon', 1) == '1') {
		echo $this->linkIcon($link . '&format=pdf', 'COM_VIRTUEMART_PDF', 'pdf_button', 'pdf_button_enable', false);
	    }
	    echo $this->linkIcon($link . '&print=1', 'COM_VIRTUEMART_PRINT', 'printButton', 'show_printicon');
	    echo $this->linkIcon($MailLink, 'COM_VIRTUEMART_EMAIL', 'emailButton', 'show_emailfriend');
	    ?>
    	<div class="clear"></div>
        </div>
    <?php } // PDF - Print - Email Icon END
    ?>
<div class="wrapper2">
	<div class="fright">
		 <?php // Product Title  ?>
			<h1 class="title"><?php echo $this->product->product_name ?></h1>
		<?php // Product Title END  ?>
		
		<?php 
		if ($this->product->product_s_desc) { ?>
			<div class="s_desc"><?php echo $this->product->product_s_desc; ?></div>
		<?php } ?>
		<div class="product-box2">
			<div class="rating">
			<?php
			if ($this->showRating) {
				$maxrating = VmConfig::get('vm_maximum_rating_scale',5);
	
					if (empty($this->rating)) { ?>
					<span class="vote">
						<span title=" <?php echo (JText::_("COM_VIRTUEMART_RATING_TITLE") . $this->rating->rating . '/' . $maxrating) ?>" class="vmicon ratingbox" style="display:inline-block;">
							<span class="stars-orange" style="width:<?php echo $ratingwidth;?>%">
							</span>
						</span>
					
						<span class="rating-title"><?php echo JText::_('COM_VIRTUEMART_RATING').' '.JText::_('COM_VIRTUEMART_UNRATED') ?></span>
						</span>	
					<?php } else {
						$ratingwidth = ( $this->rating->rating * 100 ) / $maxrating;//I don't use round as percetntage with works perfect, as for me
						?>
						<span class="vote">
							<span title="" class="vmicon ratingbox" style="display:inline-block;">
								<span class="stars-orange" style="width:<?php echo $ratingwidth;?>%">
								</span>
							</span>
							<span class="rating-title"><?php echo JText::_('COM_VIRTUEMART_RATING').' '.round($this->rating->rating, 2) . '/'. $maxrating; ?></span>
						</span>
				<?php	} ?>
				<?php 	} ?>
			</div>	
			<div class="spacer-buy-area">
			<div class="addtocart-area">
    		<?php
		 	   echo $this->loadTemplate('addtocart2');
			?>
    <div class="clear"></div>
</div>
					
			</div>
		</div>
		<?php $thumb = $this->product->images;
				$thumb_url =  $thumb[0]->file_url_thumb;
		 ?>
			<div class="share1">
			<!-- AddThis Button BEGIN -->
		   <span class='st_facebook_hcount' displayText='Facebook'></span>
		   <span class='st_twitter_hcount' displayText='Tweet'></span>
		   <span class='st_googleplus_hcount' displayText='Google +'></span>
		   <span class='st_pinterest_hcount' displayText='Pinterest'></span>
		   <script type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script>
		   <script type="text/javascript">stLight.options({publisher: "", doNotHash: false, doNotCopy: false, hashAddressBar: false});</script>
		   <!-- AddThis Button END -->
		</div>
		
	</div> 
	<div class="fleft">
		<?php
			echo $this->loadTemplate('images');
		?>
		 
			<?php
		// Availability Image
		$stockhandle = VmConfig::get('stockhandle', 'none');
		if (($this->product->product_in_stock - $this->product->product_ordered) < 1) {
			if ($stockhandle == 'risetime' and VmConfig::get('rised_availability') and empty($this->product->product_availability)) {
			?>	<div class="availability">
			    <?php echo (file_exists(JPATH_BASE . DS . VmConfig::get('assets_general_path') . 'images/availability/' . VmConfig::get('rised_availability'))) ? JHTML::image(JURI::root() . VmConfig::get('assets_general_path') . 'images/availability/' . VmConfig::get('rised_availability', '7d.gif'), VmConfig::get('rised_availability', '7d.gif'), array('class' => 'availability')) : VmConfig::get('rised_availability'); ?>
			</div>
		    <?php
			} else if (!empty($this->product->product_availability)) {
			?>
			<div class="availability">
			<?php echo (file_exists(JPATH_BASE . DS . VmConfig::get('assets_general_path') . 'images/availability/' . $this->product->product_availability)) ? JHTML::image(JURI::root() . VmConfig::get('assets_general_path') . 'images/availability/' . $this->product->product_availability, $this->product->product_availability, array('class' => 'availability')) : $this->product->product_availability; ?>
			</div>
			<?php
			}
		}
		?>
	</div>
  </div> 
  	<div class="clear"></div>	
	  
    <?php
	 if (!empty($this->product->customfieldsSorted['normal'])) {
	$this->position = 'normal';
	echo $this->loadTemplate('customfields');
    } // Product custom_fields END
    // Product Packaging
    $product_packaging = '';
    if ($this->product->product_box) {
	?>
        <div class="product-box">
	    <?php
	        echo JText::_('COM_VIRTUEMART_PRODUCT_UNITS_IN_BOX') .$this->product->product_box;
	    ?>
        </div>
    <?php } // Product Packaging END
    ?>

    <?php
    // Product Files
    // foreach ($this->product->images as $fkey => $file) {
    // Todo add downloadable files again
    // if( $file->filesize > 0.5) $filesize_display = ' ('. number_format($file->filesize, 2,',','.')." MB)";
    // else $filesize_display = ' ('. number_format($file->filesize*1024, 2,',','.')." KB)";

    /* Show pdf in a new Window, other file types will be offered as download */
    // $target = stristr($file->file_mimetype, "pdf") ? "_blank" : "_self";
    // $link = JRoute::_('index.php?view=productdetails&task=getfile&virtuemart_media_id='.$file->virtuemart_media_id.'&virtuemart_product_id='.$this->product->virtuemart_product_id);
    // echo JHTMl::_('link', $link, $file->file_title.$filesize_display, array('target' => $target));
    // }
   
    
    ?>
	<div class="responsive-tabs">
			<?php if ($this->product->product_desc) { ?>

			<h2><?php echo JText::_('COM_VIRTUEMART_PRODUCT_DESC_TITLE') ?></h2>
			<div>
				<div class="desc"> <?php echo $this->product->product_desc ?></div>
			</div>
			<?php } ?>
			<?php if ($this->product->customfieldsSorted['specification']) { ?>
			<h2>Specification</h2>
			<div>
			<?php  if (!empty($this->product->customfieldsSorted['specification'])) {
				$this->position = 'specification';
				echo $this->loadTemplate('specification');
				} // Product custom_fields END	?>
			</div>
			<?php } ?>
			<?php if ($this->product->product_desc) { ?>
			<h2><?php echo JText::_('COM_VIRTUEMART_REVIEWS') ?></h2>
			<div>
				<?php echo $this->loadTemplate('reviews') ?>
			</div>
			<?php } ?>
			<?php if ($this->product->customfieldsSorted['custom']) { ?>
			<h2>Custom</h2>
			<div>
			<?php  if (!empty($this->product->customfieldsSorted['custom'])) {
				$this->position = 'custom';
				echo $this->loadTemplate('custom');
				} // Product custom_fields END	?>
			</div>
			<?php } ?>
			<?php if ($this->product->customfieldsSorted['video']) { ?>
			<h2>Video</h2>
			<div>
			<?php  if (!empty($this->product->customfieldsSorted['video'])) {
				$this->position = 'video';
				echo $this->loadTemplate('customvideo');
				} // Product custom_fields END	?>
			</div>
			<?php } ?>
		</div>
        
 <?php
  if (!empty($this->product->customfieldsRelatedProducts)) {
	echo $this->loadTemplate('relatedcategories');
    } // Product customfieldsRelatedProducts END

 if (!empty($this->product->customfieldsRelatedProducts)) {
	echo $this->loadTemplate('relatedproducts');
    } // Product customfieldsRelatedProducts END
	
		echo $this->loadTemplate('recent');

    // Product Navigation
    if (VmConfig::get('product_navigation', 1)) { ?>
	 <div class="product-neighbours">
	    <?php
		$next = JText::_('TM_VIRTUEMART_NEXTPRODUCT');
		$prev = JText::_('TM_VIRTUEMART_PREVPRODUCT');

	    if (!empty($this->product->neighbours ['previous'][0])) {
		$prev_link = JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $this->product->neighbours ['previous'][0] ['virtuemart_product_id'] . '&virtuemart_category_id=' . $this->product->virtuemart_category_id);
		echo JHTML::_('link', $prev_link, $prev, 'class="previous-page"');
	    }
	    if (!empty($this->product->neighbours ['next'][0])) {
		$next_link = JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $this->product->neighbours ['next'][0] ['virtuemart_product_id'] . '&virtuemart_category_id=' . $this->product->virtuemart_category_id);
		echo JHTML::_('link', $next_link, $next , 'class="next-page"');
	    }
	    ?>
    	<div class="clear"></div>
        </div>
    <?php } // Product Navigation END
    ?>
	
 </div>
	<?php
		$app			= JFactory::getApplication();
		$doc			= JFactory::getDocument();
		$templateparams	= $app->getTemplate(true)->params;
		$template = $app->getTemplate();
		$base = $this->baseurl;
		$path2 = $base.'/templates/'.$template;
	?>
	
<script type="text/javascript" src="<?php echo $path2 ?>/html/com_virtuemart/productdetails/slides.min.js"></script>
<script type="text/javascript">
jQuery(document).ready(function() {
	RESPONSIVEUI.responsiveTabs();
})

jQuery(document).ready(function() {
	jQuery('#products').slides({
		preload: true,
		preloadImage: false,
		effect: 'fade',
		crossfade: true,
		slideSpeed: 350,
		fadeSpeed: 500,
		generateNextPrev: false,
		generatePagination: false
	});	
	jQuery('.jqzoom').jqzoom({
		zoomType: 'reverse',
		lens:true,
		zoomWidth:300,
		zoomHeight:230,
		preloadImages: false,
		title:false,
		alwaysOn:false
	});
	jQuery("#carousel").jcarousel({
		scroll:1					  
							  });

});	
</script>
<?php // Back To Category Button
	if ($this->product->virtuemart_category_id) {
		$catURL =  JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id='.$this->product->virtuemart_category_id);
		$categoryName = $this->product->category_name ;
	} else {
		$catURL =  JRoute::_('index.php?option=com_virtuemart');
		$categoryName = jText::_('COM_VIRTUEMART_SHOP_HOME');
	}
	?>
	
	<div class="back-to-category" style="padding-top:10px;">
    	<a style="padding-bottom:0;" href="<?php echo $catURL ?>" class="button" title="<?php echo $categoryName ?>"><?php echo JText::sprintf('COM_VIRTUEMART_CATEGORY_BACK_TO',$categoryName) ?></a>
	</div>
		

