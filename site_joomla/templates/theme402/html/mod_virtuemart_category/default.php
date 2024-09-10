<?php // no direct access
defined('_JEXEC') or die('Restricted access');
//JHTML::stylesheet ( 'menucss.css', 'modules/mod_virtuemart_category/css/', false );


$cache 				= JFactory::getCache('com_virtuemart','callback');
$vendorId			= !isset($vendorId) || empty($vendorId) ? '1' : abs((int)$vendorId);
/* ID for jQuery dropdown */
$ID 				= isset($module) && is_object($module) && isset($module->id) ? $module->id : str_replace('.', '_', substr(microtime(true), -8, 8));
$js="jQuery(window).load(function() {
		jQuery('li.level0  ul').each(function(index) {jQuery(this).prev().addClass('idCatSubcat')});
		jQuery('#accordion li.level0 ul').css('display','none');
		jQuery('#accordion li.level0.active ul').css('display','block');
		jQuery('li.level0 ul').each(function(index) {
		  jQuery(this).prev().addClass('close').click(function() {
			if (jQuery(this).next().css('display') == 'none') {
			 jQuery(this).next().slideDown(200, function () {
				jQuery(this).prev().removeClass('collapsed').addClass('expanded');
			  });
			}else {
			  jQuery(this).next().slideUp(200, function () {
				jQuery(this).prev().removeClass('expanded').addClass('collapsed');
				jQuery(this).find('ul').each(function() {
				  jQuery(this).hide().prev().removeClass('expanded').addClass('collapsed');
				});
			  });
			}
			return false;
		  });
	});
	});" ;

$document 			= JFactory::getDocument();
$document->addScriptDeclaration($js);
$document->addStyleDeclaration("#VMmenu".$ID." li.vmOpen>ul.menu".$class_sfx."{display: block;} #VMmenu".$ID." li.VmClose>ul.menu".$class_sfx."{display: none;}");

if(!function_exists('vm_template_get_tree_recurse')){
	function vm_template_get_tree_recurse($category,$childs,$parentCategories,$vendorId,$class_sfx,$ID,$level = 0){
		
		$cache 		= JFactory::getCache('com_virtuemart','callback');
		$content 	= '';
		
		if(is_array($childs) && sizeof($childs)):
			++$level;
			ob_start(); ?>
			
			<ul class="menu<?php echo $class_sfx; ?> level<?php echo $level; ?>">
					<?php
					foreach ($childs as $child) {
						
						$active_menu = 'VmClose';
						if (in_array( $child->virtuemart_category_id, $parentCategories)) $active_menu = 'active';
			
						
						$caturl = JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id='.$child->virtuemart_category_id);
						$cattext = $child->category_name;
						$child->childs = $cache->call( array( 'VirtueMartModelCategory', 'getChildCategoryList' ),$vendorId, $child->virtuemart_category_id );
							
						?>
					
						<li id="vm-category-<?php echo $child->virtuemart_category_id; ?>" class="category-<?php echo $child->virtuemart_category_id; ?><?php if (is_array($child->childs) && sizeof($child->childs)):?> has-children<?php endif; ?> <?php echo $active_menu ?>">
								<?php echo JHTML::link($caturl, $cattext); ?>
								<?php 
								if (is_array($child->childs) && sizeof($child->childs)) {
									?>
									<span class="VmArrowdown"></span>
									<?php
								}
								?>
					<?php if (is_array($child->childs) && sizeof($child->childs)) { ?>					
								<?php echo vm_template_get_tree_recurse($child,$child->childs,$parentCategories,$vendorId,$class_sfx,$ID,$level); ?>
							<?php } ?>
						</li>
			<?php 	} ?>
			</ul>
			<?php 
		$content 	= ob_get_contents();
		ob_end_clean();
		endif;
		
		return $content;
	}
} 

?>
<ul class="VMmenu<?php echo $class_sfx ?> level0 list" id="accordion" >
<?php foreach ($categories as $category) {
		$active_menu = 'VmClose';
		$caturl = JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id='.$category->virtuemart_category_id);
		$cattext = $category->category_name;
		//if ($active_category_id == $category->virtuemart_category_id) $active_menu = 'class="active"';
		if (in_array( $category->virtuemart_category_id, $parentCategories)) $active_menu = 'active';

		?>
	<li id="vm-category-<?php echo $category->virtuemart_category_id; ?>" class="level0<?php if (is_array($category->childs) && sizeof($category->childs)):?> has-children<?php endif; ?> <?php echo $active_menu ?>">
			<?php echo JHTML::link($caturl, $cattext);
			if (is_array($category->childs) && sizeof($category->childs)) {
				?>
				<span class="VmArrowdown"></span>
				<?php
			}
			?>
		<?php if(is_array($category->childs) && sizeof($category->childs)){ ?>
					<?php echo vm_template_get_tree_recurse($category,$category->childs,$parentCategories,$vendorId,$class_sfx,$ID); ?>
		<?php };?>
	</li>
<?php
	} ?>
</ul>
