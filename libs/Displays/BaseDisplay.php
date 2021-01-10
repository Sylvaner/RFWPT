<?php

/**
 * Classe de base des modèles d'affiche
 */
class BaseDisplay
{
    /** @var WP_Query Articles */
    protected $posts;

    /** @var int */
    protected $currentPage;

    /** @var int */
    protected $selectedCategory;

    /**
     * Constructeur réalisant la requête sur les articles
     *
     * @param array $args Arguments de la requête
     */
    public function __construct(array $args = []) {
        $this->initialQuery($args);
    }

    /**
     * Exécute une reuqête
     *
     * @param array $args Arguments initiaux
     */
    protected function initialQuery(array $args = []): void
    {
        $postsPerPage = get_theme_mod('posts_per_page', 20);
        $this->currentPage = max(1, intval(get_query_var('paged')));
        $this->selectedCategory = get_query_var('cat');

        $args['posts_per_page'] = $postsPerPage;
        $args['paged'] = $this->currentPage;
        if ($this->selectedCategory !== '') {
            $args['cat'] = $this->selectedCategory;
        }

        $this->posts = new WP_Query($args);

    }

    /**
     * Affiche le bandeau de pagination
     */
    protected function showPagination(): void
    {
        $maxNumPages = intval($this->posts->max_num_pages);
        if ($maxNumPages > 1) {
            $pagination = [];
            if ($this->currentPage !== 1) {
                if ($this->currentPage < 5) {
                    for ($p = 1; $p < $this->currentPage; ++$p) {
                        $pagination[] = $p;
                    }
                } else {
                    $pagination[] = 1;
                    $pagination[] = '';
                    $pagination[] = $this->currentPage - 1;
                }
            }
            $pagination[] = $this->currentPage;
            if ($this->currentPage !== $maxNumPages) {
                if ($this->currentPage + 3 >= $maxNumPages) {
                    $p = $maxNumPages - $this->currentPage - 1;
                    do {
                        $pagination[] = $maxNumPages - $p;
                    } while($p-- > 0);
                } else {
                    $pagination[] = $this->currentPage + 1;
                    $pagination[] = '';
                    $pagination[] = $maxNumPages;
                }
            }
            ?>
            <div id="pagination" class="card"><div class="card-content">
                    <nav class="pagination is-centered" role="navigation" aria-label="pagination">
                        <?php
                        echo '<a class="pagination-previous" ';
                        if ($this->currentPage > 1) {
                            echo 'href="' . previous_posts(false) . '"';
                        } else {
                            echo 'disabled';
                        }
                        echo '>' . __('Previous') . '</a>';
                        echo '<a class="pagination-next" ';
                        if ($this->currentPage < $maxNumPages) {
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
                                    if (intval($paginationLabel) === $this->currentPage) {
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
     *
     * @param string $title Titre du lien
     *
     * @return string Code HTML du lien vers l'article courant
     */
    protected function getHtmlPermalink(string $title): string
    {
        return '<a href="' . get_the_permalink() . '">' . $title . '</a>';
    }

    /**
     * Afficher le résumé en fonction des options
     *
     * @return bool True si l'article a été complètement affiché
     */
    protected function showTheExcerpt(): bool
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
     * Affiche les catégories de l'article en cours
     */
    protected function showCategories(): void
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
     * Obtenir l'url de la miniature de l'image mise en avant
     *
     * @param int $size Taille de la miniature
     *
     * @return string URL
     */
    protected function getThumbnailUrl(int $size = 128): string
    {
        return get_the_post_thumbnail_url(get_the_ID(), $size);
    }

    /**
     * Affiche un articles dans la zone de la catégorie spéciale
     */
    protected function showListPost(): void
    {
        echo '<li><span class="tag">' . get_the_date('d/m/Y') . '</span> - ' . $this->getHtmlPermalink(get_the_title()) . '</li>';
    }
}