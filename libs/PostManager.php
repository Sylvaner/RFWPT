<?php

/**
 * Classe d'affichage et de gestion des articles
 */
class PostManager
{
    /**
     * Affiche l'intégralité des articles chargés
     *
     * @return bool True si des articles ont été affichés
     */
    public function showAllPosts(): bool
    {
        if (have_posts()) {
            echo '<div class="posts-list">';
            while (have_posts()) {
                $this->loadCurrentPost();
                $this->showCurrentPostSummary();
            }
            echo '</div>';
            return true;
        }
        return false;
    }

    /**
     * Charge l'article de façon globale
     *
     * Wordpress fonctionne avec des variables globales
     */
    private function loadCurrentPost(): void
    {
        the_post();
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
        if (get_theme_mod('show_categories', true)) {
            echo '<span class="tags">';
            foreach (wp_get_post_categories(get_the_ID()) as $category) {
                echo '<span class="tag">' . get_category($category)->name . '</span>';
            }
            echo '</span>';
        } ?>
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
        $this->loadCurrentPost();
        $this->showContent();
    }

    /**
     * Affichage d'une page
     * @see PostManager::loadCurrentPost
     */
    public function showPage(): void
    {
        $this->loadCurrentPost();
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
                    <?php $this->showPostData('top'); ?>
                    <?php the_content(); ?>
                    <?php $this->showPostData('bottom'); ?>
                </div>
            </div>
        </div>
        <?php
    }

    private function showPostData($location): void
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
