<?php

/**
 * Classe d'affichage et de gestion des menus
 */
class Menus
{
    /**
     * Obtenir les données d'un menu sélectionné
     *
     * @param string $menuName Code du menu dans le thème
     *
     * @return WP_Term|null Données du menu
     */
    private static function getMenuData(string $menuName): ?WP_Term
    {
        $menuLocations = get_nav_menu_locations();
        $menuId = $menuLocations[$menuName];
        if ($menuId > 0) {
            return get_term($menuId, 'nav_menu');
        }
        return null;
    }

    /**
     * Obtenir les informations d'un élément de menu
     *
     * @param WP_Post $menuItem Objet de l'élément du menu
     *
     * @return array Données du menu sous forme de tableau associatif
     */
    private static function extractDataFromItem(WP_Post $menuItem): array
    {
        return [
          'id' => $menuItem->ID,
          'title' => $menuItem->title,
          'target' => $menuItem->target,
          'url' => $menuItem->url,
          'children' => []
        ];
    }

    /**
     * Obtenir la structure d'un menu
     * Nécessaire pour obtenir la hiérarchie
     *
     * @param WP_Post[] Tableau des éléments du menu
     *
     * @return array Tableau avec la structure du menu
     */
    private static function getMenuStruct(array $menuItems): array
    {
        $menuStruct = [];
        foreach ($menuItems as $menuItem) {
            $menuItemParent = $menuItem->menu_item_parent;
            $menuItemData = self::extractDataFromItem($menuItem);
            if ($menuItemParent !== '0') {
                if (array_key_exists($menuItemParent, $menuStruct)) {
                    $menuStruct[$menuItemParent]['children'][$menuItem->ID] = $menuItemData;
                } else {
                    $menuStruct[$menuItem->ID] = $menuItemData;
                }
            } else {
                $menuStruct[$menuItem->ID] = $menuItemData;
            }
        }
        return $menuStruct;
    }

    /**
     * Afficher un lien avec une classe CSS à partir des données extraites.
     *
     * @param array $menuItem Données extraites du WP_Post
     * @param string $cssClass Classe CSS à intégrer
     */
    private static function showLinkWithClass(array $menuItem, string $cssClass): void
    {
        echo '<a class="' . $cssClass . '" href="' . $menuItem['url'] . '"';
        if ($menuItem['target'] !== '') {
            echo ' target="' . $menuItem['target'] . '"';
        }
        echo '>' . $menuItem['title'] . '</a>';
    }

    /**
     * Afficher la barre de navigation
     *
     * @param string $menuName Nom de la barre de navigation
     * @param string $containerClass Classe CSS du container globale
     * @param bool $dropUp Afficher les menus secondaires vers le haut
     */
    public static function showNavbarMenu(string $menuName, string $containerClass, bool $dropUp = false): void
    {
        $menuTerm = self::getMenuData($menuName);
        $dropDownBulmaClass = 'has-dropdown';
        if ($dropUp) {
            $dropDownBulmaClass .= '-up';
        }
        if (!is_null($menuTerm)) {
            echo '<div class="' . $containerClass . '">';
            $menuStruct = self::getMenuStruct(wp_get_nav_menu_items($menuTerm));
            foreach ($menuStruct as $menuItem) {
                if (count($menuItem['children']) > 0) {
                    echo '<div class="navbar-item ' . $dropDownBulmaClass . ' is-hoverable">';
                    echo '<a class="navbar-link" href="' . $menuItem['url'] . '">' . $menuItem['title'] . '</a>';
                    echo '<div class="navbar-dropdown">';
                    foreach ($menuItem['children'] as $child) {
                        self::showLinkWithClass($child, 'navbar-item');
                    }
                    echo '</div></div>';
                } else {
                    self::showLinkWithClass($menuItem, 'navbar-item');
                }
            }
            echo '</div>';
        }
    }

    /**
     * Affichage des éléments mis en avant
     *
     * @param string $menuName Nom du menu
     */
    public static function showFeaturedItems(string $menuName): void
    {
        $menuTerm = self::getMenuData($menuName);
        if (!is_null($menuTerm)) {
            echo '<div class="columns featured-menu">';
            $menuStruct = self::getMenuStruct(wp_get_nav_menu_items($menuTerm));
            foreach ($menuStruct as $menuItem) {
                $title = $menuItem['title'];
                $icon = '';
                if (strpos($menuItem['title'], '|') !== false) {
                    $titleData = explode('|', $menuItem['title']);
                    $title = $titleData[1];
                    $icon = '<i class="fas fa-' . $titleData[0] . '"></i>';
                }
                $link = 'href="' . $menuItem['url'] . '"';
                if ($menuItem['target'] !== '') {
                    $link .= ' target="' . $menuItem['target'] . '"';
                } ?>
                <div class="column">
                  <a <?php echo $link; ?>>
                    <div class="card">
                      <div class="card-content">
                        <?php if ($icon !== '') {
                                echo $icon;
                            } ?>
                        <p class="title"><?php echo $title; ?></p>
                      </div>
                    </div>
                  </a>
                </div>
                <?php
            }
            echo '</div>';
        }
    }

    /**
     * Affichage d'un menu latéral
     *
     * @param string $menuName Nom du menu
     */
    public static function showSideMenu(string $menuName): void
    {
        $menuTerm = self::getMenuData($menuName);
        if (!is_null($menuTerm)) {
            $menuStruct = self::getMenuStruct(wp_get_nav_menu_items($menuTerm));
            echo '<p class="menu-label">' . $menuTerm->name . '</p>';
            echo '<ul class="menu-list">';
            foreach ($menuStruct as $menuItem) {
                echo '<li>';
                self::showLinkWithClass($menuItem, 'menu-item');
                echo '</li>';
            }
            echo '</ul>';
        }
    }

    /**
     * Test si un menu a été sélectionné pour un emplacement.
     *
     * @param string $menuName Nom du menu
     *
     * @return bool True si un menu a été sélectionné pour l'emplacement
     */
    public static function menuIsSelected(string $menuName): bool
    {
        return self::getMenuData($menuName) !== null;
    }

    public static function hasSideMenu(): bool
    {
        $hasSideMenu = false;
        $menuLocations = get_nav_menu_locations();
        for ($sideMenuIndex = 1; $sideMenuIndex < 7; ++$sideMenuIndex) {
            $menuId = $menuLocations['side' . $sideMenuIndex . '-menu'];
            if ($menuId > 0) {
                $hasSideMenu = true;
                break;
            }
        }
        return $hasSideMenu;
    }
}
