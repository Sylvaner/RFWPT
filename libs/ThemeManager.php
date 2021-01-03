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
        $this->init();
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
     * Initialise l'ensemble des éléments
     */
    private function init(): void
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
        $wpCustomize->add_setting('show_featured', ['default' => true, 'transport' => 'refresh']);
        $wpCustomize->add_setting('fixed_menu', ['default' => false, 'transport' => 'refresh']);
        $wpCustomize->add_setting('banner_image', ['default' => 0, 'transport' => 'refresh']);
        $wpCustomize->add_setting('banner_height', ['default' => '100px', 'transport' => 'refresh']);
        $wpCustomize->add_setting('background_image_wp', ['default' => 0, 'transport' => 'refresh']);
        $wpCustomize->add_setting('background_image_gradient', ['default' => true, 'transport' => 'refresh']);
        $wpCustomize->add_setting('background_under_nav', ['default' => true, 'transport' => 'refresh']);
        $wpCustomize->add_setting('show_footer', ['default' => true, 'transport' => 'refresh']);
        $wpCustomize->add_setting('show_categories', ['default' => true, 'transport' => 'refresh']);
        $wpCustomize->add_setting('use_custom_excerpt', ['default' => true, 'transport' => 'refresh']);
        $wpCustomize->add_setting('excerpt_size', ['default' => 300, 'transport' => 'refresh']);

        $colors = [
          'text_color' => ['default' => '#4A4A4A', 'label' => 'Text color'],
          'background_color' => ['default' => '#E0E0E0', 'label' => 'Background color'],
          'cards_color' => ['default' => '#FFFFFF', 'label' => 'Cards color'],
          'text_buttons_color' => ['default' => '#FFFFFF', 'label' => 'Text buttons color'],
          'buttons_color' => ['default' => '#3D7799', 'label' => 'Buttons color'],
          'link_color' => ['default' => '#4A4A4A', 'label' => 'Link color'],
          'link_hover_color' => ['default' => '#3273DC', 'label' => 'Link hover color'],
          'featured_color' => ['default' => '#3D7799', 'label' => 'Featured background'],
          'featured_text_color' => ['default' => '#FFFFFF', 'label' => 'Featured text'],
          'navbar_background_color' => ['default' => '#FFFFFF', 'label' => 'Navbar background'],
          'navbar_text_color' => ['default' => '#4A4A4A', 'label' => 'Navbar text'],
          'navbar_hover_background_color' => ['default' => '#FAFAFA', 'label' => 'Navbar hover background'],
          'navbar_hover_text_color' => ['default' => '#3273DC', 'label' => 'Navbar hover text']
        ];
        foreach ($colors as $colorKey => $colorConfig) {
            $wpCustomize->add_setting($colorKey, ['default' => $colorConfig['default'], 'transport' => 'refresh']);
        }

        $wpCustomize->add_section('appearance', ['title' => __('Appearance', 'rfwpt'), 'priority' => 30]);
        $wpCustomize->add_section('features', ['title' => __('Features', 'rfwpt'), 'priority' => 30]);
        $wpCustomize->add_section('colors', ['title' => __('Colors', 'rfwpt'), 'priority' => 30]);
        
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
          'type' => 'text']);

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
          'type' => 'checkbox']);
        $wpCustomize->add_control('background_image_gradient', [
          'label' => __('Background image gradient', 'rfwpt'),
          'section' => 'appearance',
          'settings' => 'background_image_gradient',
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
        $wpCustomize->add_control('use_custom_excerpt', [
          'label' => __('Use custome excerpt', 'rfwpt'),
          'section' => 'features',
          'settings' => 'use_custom_excerpt',
          'type' => 'checkbox']);
        $wpCustomize->add_control('excerpt_size', [
          'label' => __('Custom excerpt characters size', 'rfwpt'),
          'section' => 'features',
          'settings' => 'excerpt_size',
          'type' => 'text']);
        
        /**
         * Couleurs
         */
        foreach ($colors as $colorKey => $colorConfig) {
            $wpCustomize->add_control(new WP_Customize_Color_Control($wpCustomize, $colorKey, [
              'label' => __($colorConfig['label'], 'rfwpt'),
              'section' => 'colors',
              'settings' => $colorKey
            ]));
        }
    }

    /**
     * Affiche le CSS dépendant de la configuration du thème
     */
    public function showDynamicCss(): void
    {
        // Couleurs
       ?>
      <style type="text/css">
        #global-content {
          color: <?php echo get_theme_mod('text_color', '#4A4A4A'); ?>;
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
        #global-content .section .column:first-child a {
          color: <?php echo get_theme_mod('link_color', '#4A4A4A'); ?>;
          text-decoration: underline;
        }
        #global-content .section .column:first-child a:hover {
          color: <?php echo get_theme_mod('link_hover_color', '#3273DC'); ?>;
        }
        #global-content .section .card {
          background-color: <?php echo get_theme_mod('cards_color', '#FFFFFF'); ?>;
        }
        #global-content a.wp-block-file__button,
        #global-content a.wp-block-file__button:hover {
          color: <?php echo get_theme_mod('text_buttons_color', '#FFFFFF'); ?> !important;
          text-decoration: none !important;
          background-color: <?php echo get_theme_mod('buttons_color', '#3D7799'); ?> !important;
        }
        <?php
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
            echo '#global-content { background-color: #' . get_theme_mod('background_color', '#E0E0E0') . ';}';
        }
        // Affichage d'une bannière
        if (get_theme_mod('banner_image', 0) !== 0 && get_theme_mod('banner_image', 0) !== ''):?>
        #banner {
          height: <?php echo get_theme_mod('banner_height', '100px'); ?>;
          background: url('<?php echo wp_get_attachment_url(get_theme_mod('banner_image', '')); ?>') no-repeat;
        }
        <?php endif;
        // Bug d'affichage, les 2 menus se superposent
        if (is_admin_bar_showing()) {
            echo '.navbar.is-fixed-top { top: 32px !important; }';
        } ?>
      </style>
    <?php
    }
}
