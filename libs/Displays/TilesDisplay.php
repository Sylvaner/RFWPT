<?php

require_once('BaseDisplay.php');

/**
 * Modèle d'affichage en tuile
 */
class TilesDisplay extends BaseDisplay
{
    /**
     * Affiche la page d'accueil avec des tuiles
     *
     * @param int $specialCategory Catégorie spéciale sélectionnée
     *
     * @return bool True si des articles ont été affiché
     */
    public function showHome(int $specialCategory): bool
    {
        $postCount = 0;
        if ($this->posts->have_posts()) {
            // Créations des tuiles au format bulma
            echo '<div id="posts-tiles" class="tile is-ancestor is-vertical">';
            // Créations de la zone avec uniquement 2 tuiles
            while ($this->posts->have_posts()) {
                if ($postCount === 0 || $postCount === 2) {
                    if ($postCount === 0) {
                        echo '<div class="tile is-horizontal">';
                        echo '<div class="tile is-vertical is-8">';
                    }
                    echo '<div class="tile is-horizontal">';
                } elseif ($postCount > 3 && ($postCount - 1) % 3 === 0) {
                    echo '<div class="tile is-horizontal">';
                }
                $this->posts->the_post();
                $this->showPostTile();
                ++$postCount;
                if ($postCount === 2 || $postCount === 4) {
                    echo '</div>';
                    // Affichage de la catégorie spéciale
                    if ($postCount === 4) {
                        $promotedPosts = new WP_Query(
                            [
                                'posts_per_page' => 10,
                                'cat' => $specialCategory
                            ]);
                        echo '</div><div class="promoted-category tile is-parent"><article class="tile is-child box"><div class="content">';
                        echo '<p class="title">' . get_cat_name($specialCategory) . '</p>';
                        echo '<div class="content"><ul>';
                        while ($promotedPosts->have_posts()) {
                            $this->showListPost();
                            $promotedPosts->the_post();
                        }
                        echo '</ul></div></div></article></div></div>';
                    }
                } elseif ($postCount > 3 && ($postCount - 1) % 3 === 0) {
                    echo '</div>';
                }
            }
            // Fermeture des lignes, colonnes, etc quand il y a peu d'articles
            // Cas particuliers liés à la structure
            if ($postCount < 3) {
                echo '</div></div>';
            } elseif ($postCount === 3) {
                echo '</div></div></div>';
            } elseif (($postCount - 1) % 3 !== 0 && $postCount > 4) {
                echo '</div>';
            }
            echo '</div>';
            $this->showPagination();
            return true;
        }
        return false;
    }

    /**
     * Affiche l'ensemble des articles demandés
     *
     * @return bool True si au moins un article a été affiché
     */
    public function showAllPosts(): bool
    {
        $postCount = 0;
        // Affichage simple avec 3 articles par ligne
        echo '<div id="posts-tiles" class="tile is-ancestor is-vertical">';
        while ($this->posts->have_posts()) {
            if ($postCount % 3 === 0) {
                echo '<div class="tile is-horizontal is-12">';
            }
            $this->posts->the_post();
            $this->showPostTile();
            ++$postCount;
            if ($postCount % 3 === 0) {
                echo '</div>';
            }
        }
        if ($postCount % 3 !== 0) {
            echo '</div>';
        }
        echo '</div>';
        return $postCount !== 0;
    }

    /**
     * Affiche l'article courant sous forme de tuile
     */
    private function showPostTile(): void
    {
        $thumbnailUrl = '';
        if (get_theme_mod('tiles_thumbnail_background', true)) {
            $thumbnailUrl = get_the_post_thumbnail_url(get_the_ID(), '128'); 
        }
        ?>
        <div class="tile is-parent">
            <article class="tile box is-child"<?php if (!empty($thumbnailUrl)): ?> style="background: linear-gradient(#FFFFFFDD, #FFFFFFDD), url('<?php echo $thumbnailUrl; ?>');"<?php endif; ?>>
                <div class="title">
                    <?php echo $this->getHtmlPermalink(get_the_title()); ?>
                </div>
                <div class="content">
                    <?php $this->showCategories();
                    $this->showTheExcerpt();
                    echo '<p class="tile-footer">';
                    if (get_theme_mod('show_author', true)) {
                        echo get_the_author_meta('display_name') . ' - ';
                    }
                    the_date('d/m/Y');
                    echo '</p>';
                    ?>
                </div>
            </article>
        </div>
        <?php
    }
}