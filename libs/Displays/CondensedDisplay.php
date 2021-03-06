<?php

require_once('BaseDisplay.php');

/**
 * Modèle d'affichage condensé
 */
class CondensedDisplay extends BaseDisplay
{
    private static $sideFilesAdded = false;

    /**
     * Affiche la page d'accueil condensée
     * 
     * @param int $promotedCategory1 Catégorie spéciale sélectionnée
     * @param int $promotedCategory2 Catégorie spéciale sélectionnée
     */
    public function showHome(int $promotedCategory1, int $promotedCategory2): void
    {
        $postsCount = 0;
        $maxPosts = get_theme_mod('home_slideshow_count', 5);;
        if (!self::$sideFilesAdded) {
            echo '<script type="text/javascript" src="' . get_template_directory_uri() . '/js/slideshow.js"></script>';
            echo '<style>' . file_get_contents(get_template_directory() . '/css/slideshow.css') . '</style>';
            self::$sideFilesAdded = true;
        }
        if ($this->posts->have_posts()) {
            ?>
            <div id="condensed">
                <section class="hero slideshow">
                    <?php while ($this->posts->have_posts() && $postsCount++ < $maxPosts) :
                        $this->posts->the_post();
                        $thumbnailUrl = $this->getThumbnailUrl();
                        ?>
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
                                        <p class="content"><?php $this->showTheExcerpt(); ?></p>
                                    </div>
                                </div>
                                <?php else: ?>
                                <div class="title"><?php echo $this->getHtmlPermalink(get_the_title()); ?></div>
                                <div class="content"><?php $this->showTheExcerpt(); ?></div>
                                <?php endif;
                                $this->showCategories();
                                ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </section>
                <?php if ($promotedCategory1 !== 0): ?>
                <div class="columns">
                    <div class="column"><?php $this->showPromotedCategory(1, $promotedCategory1); ?></div>
                    <?php if ($promotedCategory2 !== 0) : ?>
                    <div class="column"><?php $this->showPromotedCategory(2, $promotedCategory2); ?></div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
                <?php
        }
    }

    /**
     * Affich une des catégories promues
     *
     * @param int $promotedCategoryIndex Index de la catégorie promue
     * @param int $promotedCategory Identifiant de la catégorie promue
     */
    private function showPromotedCategory(string $promotedCategoryIndex, int $promotedCategory): void
    {
        $postsLimit = max(1, get_theme_mod('promoted_category' . $promotedCategoryIndex . '_count', 5));
        $promotedPosts = new WP_Query(
            [
                'posts_per_page' => $postsLimit,
                'cat' => $promotedCategory
            ]);
        ?>
        <div id="promoted-category<?php echo $promotedCategoryIndex; ?>" class="promoted-category card">
            <div class="card-content">
                <p class="title"><?php echo get_cat_name($promotedCategory); ?></p>
                <div class="content">
                    <ul>
                    <?php
                    while ($promotedPosts->have_posts()) {
                        $promotedPosts->the_post();
                        $this->showListPost();
                    }
                    ?>
                    </ul>
                </div>
            </div>
        </div>
        <?php
    }
}