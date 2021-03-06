<?php

/**
 * Classe de gestion du thème
 */
class ThemeManager
{
    // Instance du singleton
    private static $instance = null;

    /**
     * Constructeur privée pour le singleton
     */
    private function __construct()
    {
        $this->addStyles();
        $this->addScripts();
        $this->registerMenus();
        $this->actions();
        $this->addGlobalFeatures();
        // Chargement des traductions
        load_theme_textdomain('rfwpt', get_template_directory() . '/languages');
    }

    /**
     * Obtenir l'instance de la classe
     */
    public static function getInstance(): ThemeManager
    {
        if (self::$instance === null) {
            self::$instance = new ThemeManager();
        }
        return self::$instance;
    }

    /**
     * Ajoute les feuilles de styles nécessaires au thème
     */
    private function addStyles(): void
    {
        // style.css du thème
        if (!is_admin()) {
            wp_enqueue_style('style', get_stylesheet_uri());
            wp_enqueue_style('bulma', get_template_directory_uri() . '/css/bulma.min.css');
            wp_enqueue_style('fontawesome', get_template_directory_uri() . '/css/fontawesome/css/all.min.css');
        }
    }

    /**
     * Ajoute le javascript nécessaire au thème
     */
    private function addScripts(): void
    {
        wp_enqueue_script('global', get_template_directory_uri() . '/js/global.js');
    }

    /**
     * Ajoute les fonctionnalités concernant la globalité du site
     */
    private function addGlobalFeatures(): void
    {
        add_theme_support('post-thumbnails');
    }

    /**
     * Enregistre l'ensemble des menus du thème
     */
    private function registerMenus(): void
    {
        register_nav_menu('nav-menu', __('Navigation menu', 'rfwpt'));
        register_nav_menu('featured-menu', __('Featured items', 'rfwpt'));
        register_nav_menu('footer-menu', __('Footer menu', 'rfwpt'));
        for ($sideMenuIndex = 1; $sideMenuIndex < 6; ++$sideMenuIndex) {
            register_nav_menu('side' . $sideMenuIndex . '-menu', __('Side menu', 'rfwpt') . ' ' . $sideMenuIndex);
        }
    }

    /**
     * Ajoute des actions
     */
    private function actions(): void
    {
        // Paramètres du thème
        add_action('customize_register', [$this, 'customizeRegister']);
        // En-tête dynamique
        add_action('wp_head', [$this, 'showDynamicCss']);
    }

    /**
     * Ajoute des éléments de personnalisation du thème
     *
     * @param WP_Customize_Manager Gestionnaire de personnalisation
     */
    public function customizeRegister(WP_Customize_Manager $wpCustomize): void
    {
        $wpCustomize->add_section('appearance', ['title' => __('Appearance', 'rfwpt'), 'priority' => 30]);
        $wpCustomize->add_section('features', ['title' => __('Features', 'rfwpt'), 'priority' => 30]);
        $wpCustomize->add_section('home', ['title' => __('Home', 'rfwpt'), 'priority' => 30]);
        $wpCustomize->add_section('lists', ['title' => __('Lists', 'rfwpt'), 'priority' => 30]);
        $wpCustomize->add_section('posts', ['title' => __('Posts', 'rfwpt'), 'priority' => 30]);
        $wpCustomize->add_section('colors', ['title' => __('Colors', 'rfwpt'), 'priority' => 30]);

        $wpCustomize->add_setting('banner_image', ['default' => 0, 'transport' => 'refresh']);
        $wpCustomize->add_setting('banner_height', ['default' => '100px', 'transport' => 'refresh']);

        $wpCustomize->add_setting('background_image_wp', ['default' => 0, 'transport' => 'refresh']);
        $wpCustomize->add_setting('background_image_gradient', ['default' => true, 'transport' => 'refresh']);
        $wpCustomize->add_setting('background_under_nav', ['default' => true, 'transport' => 'refresh']);
        $wpCustomize->add_setting('column_mode', ['default' => true, 'transport' => 'refresh']);

        $wpCustomize->add_setting('show_featured', ['default' => true, 'transport' => 'refresh']);
        $wpCustomize->add_setting('show_footer', ['default' => true, 'transport' => 'refresh']);
        $wpCustomize->add_setting('show_categories', ['default' => true, 'transport' => 'refresh']);
        $wpCustomize->add_setting('fixed_menu', ['default' => false, 'transport' => 'refresh']);
        $wpCustomize->add_setting('navbar_shadow', ['default' => true, 'transport' => 'refresh']);
        $wpCustomize->add_setting('featured_first_side_menu', ['default' => false, 'transport' => 'refresh']);

        $wpCustomize->add_setting('show_home_mode', ['default' => 'cards', 'transport' => 'refresh']);
        $wpCustomize->add_setting('home_slideshow_count', ['default' => 5, 'transport' => 'refresh']);
        $wpCustomize->add_setting('tiles_thumbnail_background', ['default' => true, 'transport' => 'refresh']);
        $wpCustomize->add_setting('promoted_category1', ['default' => '', 'transport' => 'refresh']);
        $wpCustomize->add_setting('promoted_category2', ['default' => '', 'transport' => 'refresh']);
        $wpCustomize->add_setting('promoted_category1_count', ['default' => 5, 'transport' => 'refresh']);
        $wpCustomize->add_setting('promoted_category2_count', ['default' => 5, 'transport' => 'refresh']);

        $wpCustomize->add_setting('show_lists_mode', ['default' => 'cards', 'transport' => 'refresh']);
        $wpCustomize->add_setting('posts_per_page', ['default' => 20, 'transport' => 'refresh']);
        $wpCustomize->add_setting('disable_read_more', ['default' => true, 'transport' => 'refresh']);
        $wpCustomize->add_setting('show_author', ['default' => true, 'transport' => 'refresh']);
        $wpCustomize->add_setting('use_custom_excerpt', ['default' => true, 'transport' => 'refresh']);
        $wpCustomize->add_setting('excerpt_size', ['default' => 300, 'transport' => 'refresh']);
        $wpCustomize->add_setting('show_post_data', ['default' => 'bottom', 'transport' => 'refresh']);
        $wpCustomize->add_setting('show_post_author', ['default' => true, 'transport' => 'refresh']);
        $wpCustomize->add_setting('show_post_date', ['default' => true, 'transport' => 'refresh']);

        $colors = [
          'text_color' => ['default' => '#4A4A4A', 'label' => 'Text'],
          'background_color_wp' => ['default' => '#E0E0E0', 'label' => 'Background'],
          'cards_color' => ['default' => '#FFFFFF', 'label' => 'Cards'],
          'promoted_category1_color' => ['default' => '#FFFFFF', 'label' => 'Promoted category 1', 'active_callback' => 'promotedCategory1Callback'],
          'promoted_category2_color' => ['default' => '#FFFFFF', 'label' => 'Promoted category 2', 'active_callback' => 'condensedHomeCallback'],
          'menu_card' => ['default' => '#FFFFFF', 'label' => 'Side menu'],
          'text_buttons_color' => ['default' => '#FFFFFF', 'label' => 'Text buttons'],
          'buttons_color' => ['default' => '#3D7799', 'label' => 'Buttons'],
          'link_color' => ['default' => '#4A4A4A', 'label' => 'Link'],
          'link_hover_color' => ['default' => '#3273DC', 'label' => 'Link hover'],
          'featured_color' => ['default' => '#3D7799', 'label' => 'Featured background'],
          'featured_text_color' => ['default' => '#FFFFFF', 'label' => 'Featured text'],
          'featured_side_menu_text_color' => ['default' => '#d82626', 'label' => 'Featured side menu color'],
          'navbar_background_color' => ['default' => '#FFFFFF', 'label' => 'Navbar background'],
          'navbar_text_color' => ['default' => '#4A4A4A', 'label' => 'Navbar text'],
          'navbar_hover_background_color' => ['default' => '#FAFAFA', 'label' => 'Navbar hover background'],
          'navbar_hover_text_color' => ['default' => '#3273DC', 'label' => 'Navbar hover text']
        ];
        foreach ($colors as $colorKey => $colorConfig) {
            $wpCustomize->add_setting($colorKey, ['default' => $colorConfig['default'], 'transport' => 'refresh']);
        }

        /**
         * Identité du site
         */
        $wpCustomize->add_control(new WP_Customize_Media_Control($wpCustomize, 'banner_image', [
          'label' => __('Banner image', 'rfwpt'),
          'section' => 'title_tagline',
          'settings' => 'banner_image',
          'mime_type' => 'image']));
        $wpCustomize->add_control('banner_height', [
          'label' => __('Banner height', 'rfwpt'),
          'section' => 'title_tagline',
          'settings' => 'banner_height',
          'type' => 'text',
          'active_callback' => [$this, 'activeBannerCallback']]);

        /**
         * Apparence
         */
        $wpCustomize->add_control(new WP_Customize_Media_Control($wpCustomize, 'background_image_wp', [
          'label' => __('Background image', 'rfwpt'),
          'section' => 'appearance',
          'settings' => 'background_image_wp',
          'mime_type' => 'image']));
        $wpCustomize->add_control('background_under_nav', [
          'label' => __('Background image under principal menu', 'rfwpt'),
          'section' => 'appearance',
          'settings' => 'background_under_nav',
          'type' => 'checkbox',
          'active_callback' => [$this, 'activeBackgroundCallback']]);
        $wpCustomize->add_control('background_image_gradient', [
            'label' => __('Background image gradient', 'rfwpt'),
            'section' => 'appearance',
            'settings' => 'background_image_gradient',
            'type' => 'checkbox',
            'active_callback' => [$this, 'activeBackgroundCallback']]);
        $wpCustomize->add_control('column_mode', [
            'label' => __('Show central column', 'rfwpt'),
            'section' => 'appearance',
            'settings' => 'column_mode',
            'type' => 'checkbox']);

        /**
         * Capacités
         */
        $wpCustomize->add_control('show_featured', [
          'label' => __('Show featured items', 'rfwpt'),
          'section' => 'features',
          'settings' => 'show_featured',
          'type' => 'checkbox']);
        $wpCustomize->add_control('show_footer', [
          'label' => __('Show footer', 'rfwpt'),
          'section' => 'features',
          'settings' => 'show_footer',
          'type' => 'checkbox']);
        $wpCustomize->add_control('show_categories', [
          'label' => __('Show categories', 'rfwpt'),
          'section' => 'features',
          'settings' => 'show_categories',
          'type' => 'checkbox']);
        $wpCustomize->add_control('fixed_menu', [
            'label' => __('Fixed top menu', 'rfwpt'),
            'section' => 'features',
            'settings' => 'fixed_menu',
            'type' => 'checkbox']);
        $wpCustomize->add_control('navbar_shadow', [
            'label' => __('Show navbar shadow', 'rfwpt'),
            'section' => 'features',
            'settings' => 'navbar_shadow',
            'type' => 'checkbox']);
        $wpCustomize->add_control('featured_first_side_menu', [
            'label' => __('First side menu featured', 'rfwpt'),
            'section' => 'features',
            'settings' => 'featured_first_side_menu',
            'type' => 'checkbox']);
                        
        /**
         * Home
         */
        $wpCustomize->add_control('show_home_mode', [
            'label' => __('Show home mode', 'rfwpt'),
            'section' => 'home',
            'settings' => 'show_home_mode',
            'type' => 'select',
            'choices' => [
                'cards' => __('Cards', 'rfwpt'),
                'tiles' => __('Tiles', 'rfwpt'),
                'condensed' => __('Condensed', 'rfwpt')
            ]]);
        $categoriesChoices = ['' => __('None')];
        foreach (get_categories() as $category ) {
            $categoriesChoices[$category->term_id] = $category->name;
        }
        $wpCustomize->add_control('tiles_thumbnail_background', [
            'label' => __('Show thumbnail in background', 'rfwpt'),
            'section' => 'home',
            'settings' => 'tiles_thumbnail_background',
            'type' => 'checkbox',
            'active_callback' => [$this, 'tilesModeCallback']]);
        $wpCustomize->add_control('home_slideshow_count', [
            'label' => __('Slideshow posts count', 'rfwpt'),
            'section' => 'home',
            'settings' => 'home_slideshow_count',
            'type' => 'text',
            'active_callback' => [$this, 'condensedHomeCallback']]);
        $wpCustomize->add_control('promoted_category1', [
            'label' => __('Promoted category 1', 'rfwpt'),
            'section' => 'home',
            'settings' => 'promoted_category1',
            'type' => 'select',
            'choices' => $categoriesChoices,
            'active_callback' => [$this, 'promotedCategory1Callback']]);
        $wpCustomize->add_control('promoted_category1_count', [
            'label' => __('Promoted category 1 posts count', 'rfwpt'),
            'section' => 'home',
            'settings' => 'promoted_category1_count',
            'type' => 'text',
            'active_callback' => [$this, 'promotedCategory1SelectedCallback']]);
        $wpCustomize->add_control('promoted_category2', [
            'label' => __('Promoted category 2', 'rfwpt'),
            'section' => 'home',
            'settings' => 'promoted_category2',
            'type' => 'select',
            'choices' => $categoriesChoices,
            'active_callback' => [$this, 'condensedHomeCallback']]);
        $wpCustomize->add_control('promoted_category2_count', [
            'label' => __('Promoted category 2 posts count', 'rfwpt'),
            'section' => 'home',
            'settings' => 'promoted_category2_count',
            'type' => 'text',
            'active_callback' => [$this, 'promotedCategory2SelectedCallback']]);
    
        /**
         * Listes
         */
        $wpCustomize->add_control('show_lists_mode', [
            'label' => __('Show lists mode', 'rfwpt'),
            'section' => 'lists',
            'settings' => 'show_lists_mode',
            'type' => 'select',
            'choices' => [
                'cards' => __('Cards', 'rfwpt'),
                'tiles' => __('Tiles', 'rfwpt')
            ]]);
        $wpCustomize->add_control('posts_per_page', [
            'label' => __('Posts per page', 'rfwpt'),
            'section' => 'lists',
            'settings' => 'posts_per_page',
            'type' => 'text']);
        $wpCustomize->add_control('use_custom_excerpt', [
            'label' => __('Use custom excerpt', 'rfwpt'),
            'section' => 'lists',
            'settings' => 'use_custom_excerpt',
            'type' => 'checkbox']);
        $wpCustomize->add_control('excerpt_size', [
          'label' => __('Custom excerpt characters size', 'rfwpt'),
          'section' => 'lists',
          'settings' => 'excerpt_size',
          'type' => 'text',
          'active_callback' => [$this, 'customExcerptCallback']]);
        $wpCustomize->add_control('disable_read_more', [
            'label' => __('Disable read more on small posts', 'rfwpt'),
            'section' => 'lists',
            'settings' => 'disable_read_more',
            'type' => 'checkbox']);
        $wpCustomize->add_control('show_author', [
            'label' => __('Show author', 'rfwpt'),
            'section' => 'lists',
            'settings' => 'show_author',
            'type' => 'checkbox']);

        /**
         * Articles
         */
        $wpCustomize->add_setting('show_post_data', ['default' => 'bottom', 'transport' => 'refresh']);
        $wpCustomize->add_control('show_post_data', [
            'label' => __('Show post author', 'rfwpt'),
            'section' => 'posts',
            'settings' => 'show_post_data',
            'type' => 'select',
            'choices' => [
                'bottom' => __('Bottom', 'rfwpt'),
                'top' => __('Top', 'rfwpt'),
                'none' => __('Hide', 'rfwpt')
            ]]);
        $wpCustomize->add_control('show_post_author', [
            'label' => __('Show post author', 'rfwpt'),
            'section' => 'posts',
            'settings' => 'show_post_author',
            'type' => 'checkbox']);
        $wpCustomize->add_control('show_post_date', [
            'label' => __('Show post date', 'rfwpt'),
            'section' => 'posts',
            'settings' => 'show_post_date',
            'type' => 'checkbox']);

        /**
         * Couleurs
         */
        foreach ($colors as $colorKey => $colorConfig) {
            $colorControl = [
                'label' => __($colorConfig['label'], 'rfwpt'),
                'section' => 'colors',
                'settings' => $colorKey
            ];
            if (array_key_exists('active_callback', $colorConfig)) {
                $colorControl['active_callback'] = [$this, $colorConfig['active_callback']];
            }
            $wpCustomize->add_control(new WP_Customize_Color_Control($wpCustomize, $colorKey, $colorControl));
        }
    }

    /**
     * Affiche le CSS dépendant de la configuration du thème
     */
    public function showDynamicCss(): void
    {
        // Couleurs?>
      <style type="text/css">
        #global-content,
        #posts-tiles .title {
          color: <?php echo get_theme_mod('text_color', '#4A4A4A'); ?> !important;
        }
        .featured-menu .card { 
          background-color: <?php echo get_theme_mod('featured_color', '#3D7799'); ?>;
        }
        .featured-menu .card-content,  
        .featured-menu .card-content .title {
          color: <?php echo get_theme_mod('featured_text_color', '#FFFFFF'); ?>;
        }
        #global-nav,
        #global-nav .navbar-dropdown {
          background-color: <?php echo get_theme_mod('navbar_background_color', '#FFFFFF'); ?>;
          border-top: 2px solid <?php echo get_theme_mod('navbar_background_color', '#FFFFFF'); ?>;
        }
        #global-nav .navbar-item,
        #global-nav .navbar-link {
          color: <?php echo get_theme_mod('navbar_text_color', '#4A4A4A'); ?>;
        }
        #global-nav .navbar-item:hover,
        #global-nav .navbar-link:hover {
          background-color: <?php echo get_theme_mod('navbar_hover_background_color', '#FAFAFA'); ?>;
          color: <?php echo get_theme_mod('navbar_hover_text_color', '#3273DC'); ?>;
        }
        #global-content .column:first-child a {
          color: <?php echo get_theme_mod('link_color', '#4A4A4A'); ?>;
        }
        #global-content .column:first-child a:hover {
          color: <?php echo get_theme_mod('link_hover_color', '#3273DC'); ?>;
        }
        #global-content .section .card,
        #posts-tiles article,
        #pagination,
        #condensed .slideshow .card {
          background-color: <?php echo get_theme_mod('cards_color', '#FFFFFF'); ?>;
        }
        #global-content a.wp-block-file__button,
        #global-content a.wp-block-file__button:hover,
        #pagination .is-current {
          color: <?php echo get_theme_mod('text_buttons_color', '#FFFFFF'); ?> !important;
          text-decoration: none !important;
          background-color: <?php echo get_theme_mod('buttons_color', '#3D7799'); ?> !important;
        }
        #promoted-category1,
        #special-category>article {
            background-color: <?php echo get_theme_mod('promoted_category1_color', '#FFFFFF'); ?> !important;
        }
        #promoted-category2 {
            background-color: <?php echo get_theme_mod('promoted_category2_color', '#FFFFFF'); ?> !important;
        }
        #side-menu .card {
            background-color: <?php echo get_theme_mod('menu_card', '#FFFFFF'); ?> !important;
        }
        <?php if (get_theme_mod('column_mode', true)): ?>
        @media only screen and (max-width: 1400px) {
          #global-content > div {
            width: 95% !important;
            max-width: 95% !important;
            justify-content: center;
          }
        }

        @media only screen and (min-width: 1400px) {
          #global-content > div {
            width: 80% !important;
            max-width: 80% !important;
          }
        }

        .container.column-page {
          padding-left: 0.75rem;
          padding-right: 0.75rem;
        }
        <?php else: ?>
        .featured-menu {
            padding-left: 1rem;
            padding-right: 1rem;
        }
        <?php endif; ?>
        <?php if (get_theme_mod('navbar_shadow', true)): ?>
        #global-nav {
          box-shadow: 0 5px 5px 0 rgba(0, 0, 0, 0.3);
        }
        <?php endif;
        // Image de fond
        $backgroundImageId = get_theme_mod('background_image_wp', 0);
        if ($backgroundImageId !== 0 && $backgroundImageId !== '') {
            // Changement du point de départ
            if (get_theme_mod('background_under_nav', true)) {
                echo '#global-nav { background: transparent; }';
                echo 'html {';
            } else {
                echo '#global-content {';
            }
            if (get_theme_mod('background_image_gradient', true)) {
                echo "background: linear-gradient(#FFFFFF00, #FFFFFFFF), url('" . wp_get_attachment_url(get_theme_mod('background_image_wp', 0)) . "') no-repeat fixed;";
            } else {
                echo "background: url('" . wp_get_attachment_url(get_theme_mod('background_image_wp', 0)) . "') no-repeat fixed;";
            }
            echo "background-size: cover;}";
        } else {
            echo '#global-content { background-color: ' . get_theme_mod('background_color_wp', '#E0E0E0') . ';}';
        }
        // Affichage d'une bannière
        if (get_theme_mod('banner_image', 0) !== 0 && get_theme_mod('banner_image', 0) !== ''):?>
        #banner {
          height: <?php echo get_theme_mod('banner_height', '100px'); ?>;
          background: url('<?php echo wp_get_attachment_url(get_theme_mod('banner_image', '')); ?>') no-repeat;
        }
        <?php endif;
        if (get_theme_mod('featured_first_side_menu', false)) : ?>
        #side-menu aside ul:first-of-type .menu-item {
            color: <?php echo get_theme_mod('featured_side_menu_text_color', '#d82626'); ?> !important;
        }
        <?php endif;
        // Bug d'affichage, les 2 menus se superposent
        if (is_admin_bar_showing()) {
            echo '.navbar.is-fixed-top { top: 32px !important; }';
        } ?>
      </style>
    <?php
    }

    public function activeBannerCallback($control): bool
    {
        return $control->manager->get_setting('banner_image')->value() !== '';
    }

    public function activeBackgroundCallback($control): bool
    {
        return $control->manager->get_setting('background_image_wp')->value() !== '';
    }

    public function tilesModeCallback($control): bool
    {
        return $control->manager->get_setting('show_home_mode')->value() === 'tiles';
    }

    public function promotedCategory1Callback($control): bool
    {
        return $control->manager->get_setting('show_home_mode')->value() === 'tiles' ||
            $control->manager->get_setting('show_home_mode')->value() === 'condensed';
    }

    public function condensedHomeCallback($control): bool
    {
        return $control->manager->get_setting('show_home_mode')->value() === 'condensed';
    }

    public function promotedCategory1SelectedCallback($control): bool
    {
        return $control->manager->get_setting('promoted_category1')->value() !== '';
    }

    public function promotedCategory2SelectedCallback($control): bool
    {
        return $control->manager->get_setting('show_home_mode')->value() === 'condensed' &&
               $control->manager->get_setting('promoted_category2')->value() !== '';
    }

    public function customExcerptCallback($control): bool
    {
        return $control->manager->get_setting('use_custom_excerpt')->value();
    }
}
