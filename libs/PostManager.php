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
                <p class="content"><?php echo the_excerpt(); ?></p>
              </div>
            </div>
          <?php else: ?>
            <div class="title"><?php echo $this->getHtmlPermalink(get_the_title()); ?></div>
            <div class="content"><?php echo the_excerpt(); ?></div>
          <?php endif;
        if (get_theme_mod('show_categories', true)) {
            foreach (wp_get_post_categories(get_the_ID()) as $category) {
                echo '<span class="tag">' . get_category($category)->name . '</span>';
            }
        } ?>
        </div>
        <footer class="card-footer">
          <p class="card-footer-item">
            <span><?php _e('Author') . ' : ' . get_the_author(); ?></span>
          </p>
          <p class="card-footer-item">
            <span><?php echo $this->getHtmlPermalink(__('Read more...', 'rfwpt')); ?></span>
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
        ?>
    <div class="card">
      <div class="card-content">
        <div class="title">
          <?php echo $this->getHtmlPermalink(get_the_title()); ?>
        </div>
        <div class="content">
          <?php the_content(); ?>
        </div>
      </div>
    </div>
    <?php
    }

    /**
     * Affichage d'une page
     * @see PostManager::loadCurrentPost
     */
    public function showPage(): void
    {
        ?>
    <div class="card">
      <div class="card-content">
        <div class="title">
          <?php echo $this->getHtmlPermalink(get_the_title()); ?>
        </div>
        <div class="content">
          <?php the_content(); ?>
        </div>
      </div>
    </div>
    <?php
    }
}
