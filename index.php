<?php
/**
 * Point d'entrée du thème
 */
get_header();
if (is_front_page()) {
    if (get_theme_mod('banner_image', 0) !== 0 && get_theme_mod('banner_image', 0) !== '') {
        echo '<section id="banner" class="hero"></section>';
    }
}
?>
<div id="global-content">
  <?php
  // Affichage des éléments mis en avant uniquement sur la page de garde
  if (is_front_page() && get_theme_mod('show_featured', true)) {
      Menus::showFeaturedItems('featured-menu');
  }
  ?>
  <div class="<?php if (get_theme_mod('column_mode', true)) { echo 'container column-page'; } else { echo 'section'; } ?>">
    <div class="columns">
      <div class="column <?php if (get_theme_mod('column_mode', true)) { echo 'is-three-quarters'; } else { echo 'is-four-fifths-desktop'; } ?>">
        <?php
        $postManager = new PostManager();
        if (is_page() || is_single()) {
            $postManager->showSingle();
        } else {
            if (is_home()) {
                $postManager->showHome();
            } elseif (!$postManager->showAllPosts()) {
            ?>
                <div class="card">
                    <div class="card-content">
                        <div class="title"><?php _e('No post', 'rfwpt'); ?></div>
                    </div>
                </div>
            <?php
            }
        }
        ?>
      </div>
      <div id="side-menu" class="column">
        <?php if (Menus::hasSideMenu()): ?>
        <div class="card">
          <div class="card-content">
            <aside class="menu">
              <?php
              for ($sideMenuIndex = 1; $sideMenuIndex < 7; ++$sideMenuIndex) {
                  Menus::showSideMenu('side' . $sideMenuIndex . '-menu');
              }
              ?>
            </aside>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<?php
get_footer();
