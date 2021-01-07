<?php

/**
 * Classe d'affichage et de gestion des articles
 */
class PostManager
{
    /**
     * Affiche l'intégralité des articles chargés
     *
     * @return bool True si des articles ont été affiché
     */
    public function showAllPosts(): bool
    {
        global $paged;
        $postPerPage = get_theme_mod('posts_per_page', 20);
        $showTiles = get_theme_mod('show_tiles', true);
        $paged = max(1, intval(get_query_var('paged')));
        if ($showTiles) {
            return $this->showAllPostsAsTiles($postPerPage, $paged);
        } else {
            return $this->showAllPostsAsList($postPerPage, $paged);
        }
    }

    /**
     * Affiche l'intégralité des articles en liste
     *
     * @param int $postsPerPage Nom d'articles par page
     * @param int $currentPage Page courrante
     * @return bool True si des articles ont été affiché
     */
    public function showAllPostsAsList(int $postsPerPage, int $currentPage): bool
    {
        $queryArgs = ['posts_per_page' => $postsPerPage, 'paged' => $currentPage];
        $posts = new WP_Query($queryArgs);
        if ($posts->have_posts()) {
            echo '<div id="posts-list">';
            while ($posts->have_posts()) {
                $posts->the_post();
                $this->showCurrentPostSummary();
            }
            echo '</div>';
            $this->showPagination($posts, $currentPage);
            return true;
        }
        return false;
    }

    /**
     * Afficher le résumé en fonction des options
     *
     * @return bool True si l'article a été complètement affiché
     */
    private function showTheExcerpt(): bool
    {
        $smallPost = false;
        if (get_theme_mod('use_custom_excerpt', true)) {
            $excerptSize = get_theme_mod('excerpt_size', 300);
            $addHellipsis = false;
            $contentWithLinks = strip_tags(get_the_content(), '<a>');
            // Extrait l'ensemble des liens
            $linksFound = preg_match_all('/<a.*?href="(.*?)".*?>(.*?)<\/a>/', strip_tags($contentWithLinks, '<a>'), $matches);
            $excerpt = wp_strip_all_tags(get_the_content());
            if (strlen($excerpt) < 300) {
                $smallPost = true;
            }
            // Découpe si besoin le contenu de l'article
            if (strlen($excerpt) > $excerptSize) {
                $excerpt = wordwrap($excerpt, $excerptSize, '$$$');
                // Ne conserve que la première ligne
                if (strpos($excerpt, '$$$') !== false) {
                    $excerpt = explode('$$$', $excerpt)[0];
                }
                $addHellipsis = true;
            }
            // Si des liens se trouvaient dans l'article
            if ($linksFound > 0) {
                // Parcours l'ensemble des liens jusqu'à ce qu'un ne soit plus trouver (et donc hors résumé)
                for ($linkIndex = 0; $linkIndex < $linksFound; ++$linkIndex) {
                    $linkStrPos = strpos($excerpt, $matches[2][$linkIndex]);
                    if ($linkStrPos !== false) {
                        $excerpt = substr_replace($excerpt, $matches[0][$linkIndex], $linkStrPos, strlen($matches[2][$linkIndex]));
                    } else {
                        break;
                    }
                }
            }
            if ($addHellipsis) {
                $excerpt .= ' [&hellip;]';
            }
        } else {
            $excerpt = get_the_excerpt();
            if (strlen(wp_strip_all_tags($excerpt)) === strlen(wp_strip_all_tags(get_the_content()))) {
                $smallPost = true;
            }
        }
        echo '<p>' . $excerpt . '</p>';
        return $smallPost;
    }

    /**
     * Affiche l'article courant
     * @see PostManager::loadCurrentPost
     */
    private function showCurrentPostSummary(): void
    {
        $thumbnailUrl = get_the_post_thumbnail_url(get_the_ID(), '128'); ?>
      <div class="card">
        <div class="card-content">
          <?php if ($thumbnailUrl): ?>
            <div class="media">
              <div class="media-left">
                <figure class="image is-128x128">
                  <img src="<?php echo $thumbnailUrl; ?>" alt="Placeholder image">
                </figure>
              </div>
              <div class="media-content">
                <p class="title"><?php echo $this->getHtmlPermalink(get_the_title()); ?></p>
                <p class="content"><?php $smallPost = $this->showTheExcerpt(); ?></p>
              </div>
            </div>
          <?php else: ?>
            <div class="title"><?php echo $this->getHtmlPermalink(get_the_title()); ?></div>
            <div class="content"><?php $smallPost = $this->showTheExcerpt(); ?></div>
          <?php endif;
            $this->showCategories();
          ?>
        </div>
        <footer class="card-footer">
          <p class="card-footer-item">
            <?php if (get_theme_mod('show_author', true)): ?>
            <span><?php echo __('Author') . ' : ' . get_the_author_meta('display_name'); ?></span>
            <?php endif; ?>
          </p>
          <p class="card-footer-item">
            <?php if (!(get_theme_mod('disable_read_more', true) && $smallPost)): ?>
            <span><?php echo $this->getHtmlPermalink(__('Read more...')); ?></span>
            <?php endif; ?>
          </p>
          <p class="card-footer-item">
            <span><?php the_date('d/m/Y'); ?></span>
          </p>
        </footer>
      </div>
      <?php
    }

    /**
     * Affiche l'intégralité des articles avec des tuiles
     *
     * @param int $postsPerPage Nom d'articles par page
     * @param int $currentPage Page courrante
     * @return bool True si des articles ont été affiché
     */
    public function showAllPostsAsTiles(int $postsPerPage, int $currentPage): bool
    {
        $postCount = 0;
        $specialCategory = get_theme_mod('special_category', '');
        $queryArgs = ['posts_per_page' => $postsPerPage, 'paged' => $currentPage];
        $showSpecialCategory = $specialCategory !== '';
        if ($showSpecialCategory) {
            $queryArgs['cat'] = '-' . $specialCategory;
        }
        $posts = new WP_Query($queryArgs);
        if ($posts->have_posts()) {
            // Créations des tuiles au format bulma
            echo '<div id="posts-tiles" class="tile is-ancestor is-vertical">';
            if ($showSpecialCategory) {
                // Créations de la zone avec uniquement 2 tuiles
                while ($posts->have_posts()) {
                    if ($postCount === 0 || $postCount === 2) {
                        if ($postCount === 0) {
                            echo '<div class="tile is-horizontal">';
                            echo '<div class="tile is-vertical is-8">';
                        }
                        echo '<div class="tile is-horizontal">';
                    } elseif ($postCount > 3 && ($postCount - 1) % 3 === 0) {
                        echo '<div class="tile is-horizontal">';
                    }
                    $posts->the_post();
                    $this->showPostTile();
                    ++$postCount;
                    if ($postCount === 2 || $postCount === 4) {
                        echo '</div>';
                        // Affichage de la catégorie spéciale
                        if ($postCount === 4) {
                            $specialPosts = new WP_Query(
                                    [
                                        'posts_per_page' => 10,
                                        'cat' => $specialCategory
                                    ]);
                            echo '</div><div id="special-category" class="tile is-parent"><article class="tile is-child box"><div class="content">';
                            echo '<p class="title">' . get_cat_name($specialCategory) . '</p>';
                            echo '<div class="content"><ul>';
                            while ($specialPosts->have_posts()) {
                                $this->showSpecialPost();
                                $specialPosts->the_post();
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
            } else {
                // Affichage simple avec 3 articles par ligne
                while ($posts->have_posts()) {
                    if ($postCount % 3 === 0) {
                        echo '<div class="tile is-horizontal is-12">';
                    }
                    $posts->the_post();
                    $this->showPostTile();
                    ++$postCount;
                    if ($postCount % 3 === 0) {
                        echo '</div>';
                    }
                }
            }
            echo '</div>';
            $this->showPagination($posts, $currentPage);
            return true;
        }
        return false;
    }

    /**
     * Affiche un articles dans la zone de la catégorie spéciale
     */
    private function showSpecialPost(): void
    {
        echo '<li><span class="tag">' . get_the_date('d/m/Y') . '</span> - ' . $this->getHtmlPermalink(get_the_title()) . '</li>';
    }

    /**
     * Affiche l'article courant sous forme de tuile
     * @see PostManager::loadCurrentPost
     */
    private function showPostTile(): void
    {
        $thumbnailUrl = get_the_post_thumbnail_url(get_the_ID(), '128'); ?>
        <div class="tile is-parent">
            <article class="tile box is-child"<?php if (!empty($thumbnailUrl)): ?> style="background: linear-gradient(#FFFFFFCC, #FFFFFFCC), url('<?php echo $thumbnailUrl; ?>');"<?php endif; ?>>
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

    /**
     * Affiche les catégories de l'article en cours
     */
    private function showCategories(): void
    {
        if (get_theme_mod('show_categories', true)) {
            echo '<span class="tags">';
            foreach (wp_get_post_categories(get_the_ID()) as $category) {
                echo '<span class="tag">' . get_category($category)->name . '</span>';
            }
            echo '</span>';
        }
    }

    /**
     * Affiche le bandeau de pagination
     *
     * @param WP_Query $query Requête en cours
     * @param int $currentPage Page courante
     */
    private function showPagination(WP_Query $query, int $currentPage): void
    {
        $maxNumPages = intval($query->max_num_pages);
        if ($maxNumPages > 1) {
            $pagination = [];
            if ($currentPage !== 1) {
                if ($currentPage < 5) {
                    for ($p = 1; $p < $currentPage; ++$p) {
                        $pagination[] = $p;
                    }
                } else {
                    $pagination[] = 1;
                    $pagination[] = '';
                    $pagination[] = $currentPage - 1;
                }
            }
            $pagination[] = $currentPage;
            if ($currentPage !== $maxNumPages) {
                if ($currentPage + 3 >= $maxNumPages) {
                    $p = $maxNumPages - $currentPage - 1;
                    do {
                        $pagination[] = $maxNumPages - $p;
                    } while($p-- > 0);
                } else {
                    $pagination[] = $currentPage + 1;
                    $pagination[] = '';
                    $pagination[] = $maxNumPages;
                }
            }
            ?>
            <div id="pagination" class="card"><div class="card-content">
                <nav class="pagination is-centered" role="navigation" aria-label="pagination">
                    <?php
                        echo '<a class="pagination-previous" ';
                        if ($currentPage > 1) {
                            echo 'href="' . previous_posts(false) . '"';
                        } else {
                            echo 'disabled';
                        }
                        echo '>' . __('Previous') . '</a>';
                        echo '<a class="pagination-next" ';
                        if ($currentPage < $maxNumPages) {
                            echo 'href="' . next_posts($maxNumPages, false) . '"';
                        } else {
                            echo 'disabled';
                        }
                        echo '>' . __('Next') . '</a>';
                    ?>
                    <ul class="pagination-list">
                        <?php foreach ($pagination as $paginationLabel) {
                            echo '<li>';
                            if ($paginationLabel === '') {
                                echo '<span class="pagination-ellipsis">&hellip;</span>';
                            } else {
                                if (intval($paginationLabel) === $currentPage) {
                                    echo '<a class="pagination-link is-current" aria-label="Goto page ' . $paginationLabel . '" aria-current="page"';
                                } else {
                                    echo '<a class="pagination-link" aria-label="Goto page ' . $paginationLabel . '"';

                                }
                                echo ' href="' . get_pagenum_link($paginationLabel) . '">' . $paginationLabel . '</a>';
                            }
                            echo '</li>';
                        }
                        ?>
                    </ul>
                </nav>
            </div></div>
            <?php
        }
    }

    /**
     * Obtenir le code HTML d'un lien vers l'article courant
     * @see PostManager::loadCurrentPost
     *
     * @param string $title Titre du lien
     *
     * @return string Code HTML du lien vers l'article courant
     */
    private function getHtmlPermalink(string $title): string
    {
        return '<a href="' . get_the_permalink() . '">' . $title . '</a>';
    }

    /**
     * Affichage de l'article courant
     * @see PostManager::loadCurrentPost
     */
    public function showSinglePost(): void
    {
        the_post();
        $this->showContent();
    }

    /**
     * Affichage d'une page
     * @see PostManager::loadCurrentPost
     */
    public function showPage(): void
    {
        the_post();
        $this->showContent();
    }

    /**
     * Affichage du contenu
     * @see PostManager::loadCurrentPost
     */
    private function showContent(): void
    {
        ?>
        <div class="card">
            <div class="card-content">
                <div class="title">
                    <?php echo $this->getHtmlPermalink(get_the_title()); ?>
                </div>
                <div class="content">
                    <?php
                        $this->showCategories();
                        $this->showPostData('top');
                        the_content();
                        $this->showPostData('bottom');
                    ?>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Affiche les données complémentaires d'un articles
     * @param string $location Position de l'affichage à tester
     */
    private function showPostData(string $location): void
    {
        $postDataLocation = get_theme_mod('show_post_data', 'bottom');
        if ($postDataLocation === $location) : ?>
            <nav class="level">
                <div class="level-left">
                <?php
                if (get_theme_mod('show_post_author', true)) {
                    echo '<div class="level-item">' . get_the_author_meta('display_name') . '</div>';
                }
                echo '</div>';
                if (get_theme_mod('show_post_date', true)) {
                    echo '<div class="level-right"><div class="level-item">' . get_the_date('d/m/Y') . '</div></div>';
                }
                ?>
            </nav>
        <?php
        endif;
    }
}
