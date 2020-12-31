<?php
/**
 * Pied de page
 */
if (get_theme_mod('show_footer', true)) : ?>
  <?php if (Menus::menuIsSelected('footer-menu')): ?>
    <footer>
      <nav id="footer-nav" class="navbar" role="navigation" aria-label="main navigation">
        <div class="navbar-brand">
          <a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false" data-target="footerMenu">
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
          </a>
        </div>
        <div id="footerMenu" class="navbar-menu">
        <?php Menus::showNavbarMenu('footer-menu', 'navbar-start navbar-centered', true); ?>
        </div>
      </nav>
    </footer>
  <?php else: ?>
    <footer class="footer">
      <div class="content has-text-centered">
        <p><?php bloginfo('name') ?></p>
      </div>
    </footer>
  <?php endif; ?>

<?php
  endif;
  wp_footer()
?>
</body>
</html>