<?php
/**
 * @version					$Id: index.php 20196 2011-01-09 02:40:25Z ian $
 * @package					Joomla.Site
 * @copyright				Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license					GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;
//error_reporting('E_ALL');
$path = $this->baseurl.'/templates/'.$this->template;

JHTML::_('behavior.framework', true);

// get params

$app				= JFactory::getApplication();
$logo				= $this->params->get('logo');
$templateparams		= $app->getTemplate(true)->params;

$showLeftColumn = ($this->countModules('left'));
$showRightColumn = ($this->countModules('right'));
$showuser3 = ($this->countModules('user3'));
$showuser4 = ($this->countModules('user4'));
$showuser5 = ($this->countModules('user5'));
$showuser6 = ($this->countModules('user6'));
$showuser8 = ($this->countModules('user8'));
$showuser9 = ($this->countModules('user9'));
$showuser10 = ($this->countModules('user10'));
$showFeatured = ($this->countModules('user2'));
$showNew = ($this->countModules('new'));
$showSpecials = ($this->countModules('specials'));

$option   = $app->input->getCmd('option', '');
$view     = $app->input->getCmd('view', '');
$layout   = $app->input->getCmd('layout', '');
$task     = $app->input->getCmd('task', '');


$menus      = &JSite::getMenu();
$menu      = $menus->getActive();
$pageclass   = "";

if (is_object( $menu )) : 
$params1 =  $menu->params;
$pageclass = $params1->get( 'pageclass_sfx' );
endif; 
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>" >
<head>
<script type="text/javascript" src="<?php echo $path ?>/javascript/jquery-1.8.3.min.js"></script>
<script type="text/javascript" src="<?php echo $path ?>/javascript/jquery.noconflict.js"></script>
<jdoc:include type="head" />
<link href='//fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
<link href='//fonts.googleapis.com/css?family=Open+Sans:600' rel='stylesheet' type='text/css'>
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/system/css/system.css" type="text/css" />
<link rel="stylesheet" href="<?php echo $path ?>/css/position.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="<?php echo $path ?>/css/layout.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="<?php echo $path ?>/css/print.css" type="text/css" media="Print" />
<link rel="stylesheet" href="<?php echo $path ?>/css/virtuemart.css" type="text/css"  />
<link rel="stylesheet" href="<?php echo $path ?>/css/products.css" type="text/css" />
<link rel="stylesheet" href="<?php echo $path ?>/css/personal.css" type="text/css" />
<!--[if IE 8]>
<link href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/css/ie8only.css" rel="stylesheet" type="text/css" />
<![endif]-->
<style>
.img-indent  {
 behavior:url(<?php echo $path ?>/PIE.php);
}
</style>
<!--[if lt IE 8]>
    <div style=' clear: both; text-align:center; position: relative; z-index:9999;'>
        <a href="http://www.microsoft.com/windows/internet-explorer/default.aspx?ocid=ie6_countdown_bannercode"><img src="http://www.theie6countdown.com/images/upgrade.jpg" border="0" &nbsp;alt="" /></a>
    </div>
<![endif]-->
<!--[if lt IE 9]>
<script type="text/javascript" src="<?php echo $path ?>/javascript/html5.js"></script>
<![endif]-->
<script type="text/javascript" src="<?php echo $path ?>/javascript/script.js"></script>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<?php
$menu = &JSite::getMenu();
if ($menu->getActive() == $menu->getDefault()) {
    $body_class = 'first';
}else{
    $body_class = 'all';
}
?>
<body class="<?php echo $body_class." ".$pageclass;?>">
		
			<div id="header">
			<div class="main">
				<div class="logoheader">
					<h5 id="logo">
		
					<?php if ($logo != null ): ?>
					<a href="<?php echo $this->baseurl ?>"><img src="<?php echo $this->baseurl ?>/<?php echo htmlspecialchars($logo); ?>" alt="<?php echo htmlspecialchars($templateparams->get('sitetitle'));?>" /></a>
					<?php else: ?>
					<?php echo htmlspecialchars($templateparams->get('sitetitle'));?>
					<?php endif; ?>
					<span class="header1">
					<?php echo htmlspecialchars($templateparams->get('sitedescription'));?>
					</span></h5>
				</div>
					<?php if ($showuser6) : ?>
					<div class="cart">
						<jdoc:include type="modules" name="user6" style="xhtml" />
					</div>
					<?php endif; ?>
					<?php if ($showuser5) : ?>
						<div class="currency">
							<jdoc:include type="modules" name="user5" style="xhtml" />
						</div>
					<?php endif; ?>
				<?php if ($showuser3) : ?>
					<div id="topmenu">
						<jdoc:include type="modules" name="user3" style="xhtml" />
					</div>
				<?php endif; ?>
				
				<?php if ($showuser4) : ?>
					<div id="search">
						<jdoc:include type="modules" name="user4" style="xhtml" />
					</div>
				<?php endif; ?>
					<jdoc:include type="modules" name="user10" style="xhtml" />
					<jdoc:include type="modules" name="user7" style="xhtml"/>
				</div>
				</div>
				<!-- END header -->
				 <div id="content">
				 <div class="main">
					<div class="wrapper2">
						<?php if ($showLeftColumn): ?>
							<div id="left">
								<div class="wrapper2">
									<div class="extra-indent">
										<jdoc:include type="modules" name="left" style="left" />
										
									</div>
								</div>
							</div>
						<?php endif; ?>
						<?php if (($showRightColumn) && (($option!="com_virtuemart") || (!$pageclass))) : ?>

						<div id="right">
							<div class="wrapper">
								<div class="extra-indent">
									<jdoc:include type="modules" name="right" style="user" />
								</div>
							</div>
						</div>
						<?php endif; ?>
						<div class="container">
						<jdoc:include type="modules" name="user8" style="xhtml"/>
						<jdoc:include type="modules" name="syndicate" style="xhtml"/>
						<?php if (($showFeatured ) && ($option!="com_search") ) { ?>
							<?php if ($this->getBuffer('message')) : ?>
								<div class="error err-space">
									<jdoc:include type="message" />
								</div>
							<?php endif; ?>
							<jdoc:include type="modules" name="user2" style="user" />
						<?php } else { ?>
							<?php if ($this->getBuffer('message')) : ?>
								<div class="error err-space">
									<jdoc:include type="message" />
								</div>
							<?php endif; ?>
							<div class="content-indent">
								<jdoc:include type="component" />
							</div>
						<?php }; ?>
						<jdoc:include type="modules" name="new" style="new" />
					</div>
					<div class="clear"></div>
				</div>
					
				</div>
			<div class="clear"></div>
			</div>
			 <div class="main">
			<div id="foot">
				<p id="back-top">
					<a href="#top"><span></span></a>
				</p>
				<?php if ($showuser9) : ?>
					<div class="wrapper">
						<jdoc:include type="modules" name="user9" style="xhtml" />
					</div>	
				<?php endif; ?>
				<div class="space">
					<div class="wrapper">
						<div class="footerText">
							<jdoc:include type="modules" name="footer" />
							<?php
							if ($menu->getActive() == $menu->getDefault())  { ?>
							<!--{%FOOTER_LINK} -->
							<?php  }
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
<jdoc:include type="modules" name="debug" />
</body>
</html>