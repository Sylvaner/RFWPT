<?php

/**
 * Classe d'affichage et de gestion des articles
 */
class PostManager
{
    /**
     * Affiche la page d'accueil
     */
    public function showHome(): void
    {
        $homeMode = get_theme_mod('show_home_mode', 'cards');
        switch ($homeMode) {
            default:
            case 'cards':
                require 'Displays/CardsDisplay.php';
                (new CardsDisplay())->showAllPosts();
                break;
            case 'tiles':
                require 'Displays/TilesDisplay.php';
                $specialCategory = get_theme_mod('promoted_category1', '');
                if ($specialCategory === '') {
                    (new TilesDisplay())->showAllPosts();
                } else {
                    $display = new TilesDisplay(['cat' => '-' . $specialCategory]);
                    $display->showHome(intval($specialCategory));
                }
                break;
            case 'condensed':
                require 'Displays/CondensedDisplay.php';
                $promotedCategory1 = get_theme_mod('promoted_category1', '');
                $promotedCategory2 = get_theme_mod('promoted_category2', '');
                $filter = '';
                if ($promotedCategory1 !== '') {
                    $filter .= '-' . $promotedCategory1;
                    if ($promotedCategory2 !== '') {
                        $filter .= ',-' . $promotedCategory2;
                    }
                }
                $display = new CondensedDisplay(['cat' => $filter]);
                $display->showHome(intval($promotedCategory1), intval($promotedCategory2));
                break;
        }
    }

    /**
     * Affiche l'intégralité des articles chargés
     *
     * @return bool True si des articles ont été affiché
     */
    public function showAllPosts(): bool
    {
        $listsMode = get_theme_mod('show_lists_mode', 'cards');
        switch ($listsMode) {
            default:
            case 'cards':
                require 'Displays/CardsDisplay.php';
                return (new CardsDisplay())->showAllPosts();
            case 'tiles':
                require 'Displays/TilesDisplay.php';
                return (new TilesDisplay())->showAllPosts();
        }
    }

    /**
     * Affichage de l'article courant
     */
    public function showSingle(): void
    {
        the_post();
        require 'Displays/CardsDisplay.php';
        (new CardsDisplay())->showSingle();
    }
}
