<?php
/**
 * En-tête
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?><?php
  if (get_theme_mod('fixed_menu', false)) {
      echo ' class="has-navbar-fixed-top"';
  }
  ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php if (is_single()) : ?><meta name="description" content="<?php echo wp_strip_all_tags(get_the_excerpt(), true); ?>" /><?php endif ?>
  <title><?php bloginfo('name'); ?></title>
  <?php wp_head() ?>
</head>
<body <?php body_class(); ?>>
<nav id="global-nav" class="navbar<?php if (get_theme_mod('fixed_menu')) {
      echo ' is-fixed-top';
  } ?>" role="navigation" aria-label="main navigation">
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
