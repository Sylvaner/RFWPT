<?php
/**
 * En-tête
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php bloginfo('name'); ?></title>
  <?php wp_head() ?>
</head>
<body <?php body_class(); ?>>
<nav id="global-nav" class="navbar" role="navigation" aria-label="main navigation">
  <div class="navbar-brand">
    <a class="navbar-item" href="<?php echo home_url(); ?>">
      <?php bloginfo('name') ?>
    </a>
    <a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false" data-target="headerMenu">
      <span aria-hidden="true"></span>
      <span aria-hidden="true"></span>
      <span aria-hidden="true"></span>
    </a>
  </div>
  <div id="headerMenu" class="navbar-menu">
  <?php Menus::showNavbarMenu('nav-menu', 'navbar-end'); ?>
  </div>
</nav>
