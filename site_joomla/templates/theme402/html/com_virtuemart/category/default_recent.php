<?php
defined ( '_JEXEC' ) or die ( 'Restricted access' );

if(!isset($_SESSION['recent']))
	$_SESSION['recent'] = array();
if(!in_array($this->product->virtuemart_product_id,$_SESSION['recent'])){
	$_SESSION['recent'][] = $this->product->virtuemart_product_id;
	if(count($_SESSION['recent']) > 5){
		array_shift($_SESSION['recent']);
	}
}

$product_model = VmModel::getModel('product');
$products = $product_model->getProducts($_SESSION['recent']);
$product_model->addImages($products,1);
if(count($products) > 1){
		echo '<ul class="recentproducts">';
		$i=1;
		foreach($products as $v){
			if($v->virtuemart_product_id == $this->product->virtuemart_product_id)
				continue;
				if ($v->virtuemart_product_id >0){
					echo '<h2 class="item'.$i.'">'.JText::_('RECENTLY').'</h2>'; 		
					echo '<li>';
					$thumb = !empty($v->images[0]) ? $v->images[0]->displayMediaThumb('class="image"', false, 'class="modal"', true, true) : '';
					echo $thumb;
					echo JHTML::link ( JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id='.$v->virtuemart_product_id.'&virtuemart_category_id='.$v->virtuemart_category_id), $v->product_name , array ('title' => $v->product_name ) );
					echo '( Category: '.JHTML::link ( JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id='.$v->virtuemart_category_id), $v->category_name).');';
					echo '</li>';
					$i++;
				}
			
		}
		echo '</ul>';

}