<?php

require_once('BaseDisplay.php');

/**
 * Modèle d'affichage en cartes
 */
class CardsDisplay extends BaseDisplay
{
    /**
     * Affiche tous les posts demandés
     *
     * @return bool True si au moins un article a été affiché
     */
    public function showAllPosts(): bool
    {
        if ($this->posts->have_posts()) {
            echo '<div id="posts-list">';
            while ($this->posts->have_posts()) {
                $this->posts->the_post();
                $this->showCurrentPostSummary();
            }
            echo '</div>';
            $this->showPagination();
            return true;
        }
        return false;
    }

    /**
     * Affiche l'article courant
     */
    private function showCurrentPostSummary(): void
    {
        $thumbnailUrl = $this->getThumbnailUrl(); ?>
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
                        <span><?php echo get_the_author_meta('display_name'); ?></span>
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
     * Affichage du contenu
     */
    public function showSingle(): void
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
     *
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