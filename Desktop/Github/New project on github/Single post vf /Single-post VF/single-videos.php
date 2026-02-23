<?php
/**
 * Template pour afficher un contenu média unique
 * Custom Post Type: Videos
 * Version: 2.1 - Avec icônes SVG sprite
 */

get_header();

// Fil d'Ariane
if (function_exists('get_breadcrumb')) : ?>
<div class="kl-breadcrumb-wrapper kl-py-sp-3">
    <div class="container kl-container-default">
        <?php get_breadcrumb(); ?>
    </div>
</div>
<?php endif;

// Récupère les liens et textes
$link_contenus = function_exists('mansa_get_cpt_archive_link') ? mansa_get_cpt_archive_link('videos') : home_url();
$config = function_exists('mansa_get_cpt_config') ? mansa_get_cpt_config() : array();
$back_text = isset($config['videos']['back_text']) ? $config['videos']['back_text'] : __('Retour', 'mansa');

// Récupère le lien vidéo depuis ACF
$lien_video = function_exists('get_field') ? get_field('lien_video') : '';

// Convertit l'URL YouTube en URL embed
$youtube_embed = '';
if ($lien_video) {
    preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $lien_video, $matches);
    if (isset($matches[1])) {
        $video_id = $matches[1];
        $youtube_embed = "https://www.youtube.com/embed/{$video_id}";
    }
}

// Récupère les autres champs
$extrait_video = function_exists('get_field') ? get_field('extrait_video') : '';
$realisation = function_exists('get_field') ? get_field('Realisation') : '';
$montage = function_exists('get_field') ? get_field('montage') : '';

// Date de publication
$publication_date = get_the_date();
?>

<div class="kl-content-wrapper kl-mb-sp-6">
    <div class="container kl-container-default">

        <div class="row gy-5">

            <!-- 📄 COLONNE GAUCHE : Informations (TEXTE) -->
            <div class="col-lg-5 order-lg-1 order-2">
                <div class="media-info-column">

                    <!-- Titre -->
                    <h1 class="kl-font-secondary kl-fw-bold kl-color-black kl-mb-sp-3">
                        <?php the_title(); ?>
                    </h1>

                    <!-- Extrait vidéo (si rempli) -->
                    <?php if ($extrait_video) : ?>
                    <div class="media-excerpt kl-mb-sp-3">
                        <?php echo wp_kses_post($extrait_video); ?>
                    </div>
                    <?php endif; ?>

                    <!-- Description (contenu) -->
                    <div class="media-description kl-mb-sp-4">
                        <?php the_content(); ?>
                    </div>

                    <!-- Métadonnées : Réalisation & Montage -->
                    <?php if ($realisation || $montage) : ?>
                    <div class="media-credits">

                        <?php if ($realisation) : ?>
                        <div class="credit-item kl-mb-sp-2">
                            <?php if ($montage) : ?>
                            <!-- Affichage séparé si montage existe -->
                            <strong class="kl-fw-bold"><?php echo __('Réalisation :', 'mansa'); ?></strong>
                            <?php else : ?>
                            <!-- Affichage combiné si pas de montage -->
                            <strong class="kl-fw-bold"><?php echo __('Réalisation / Montage :', 'mansa'); ?></strong>
                            <?php endif; ?>
                            <span><?php echo esc_html($realisation); ?></span>
                        </div>
                        <?php endif; ?>

                        <?php if ($montage) : ?>
                        <div class="credit-item kl-mb-sp-2">
                            <strong class="kl-fw-bold"><?php echo __('Montage :', 'mansa'); ?></strong>
                            <span><?php echo esc_html($montage); ?></span>
                        </div>
                        <?php endif; ?>

                    </div>
                    <?php endif; ?>

                    <!-- Bouton retour avec NOUVELLE icône -->
                    <div class="kl-back-parent kl-mt-sp-5">
                        <a href="<?php echo esc_url($link_contenus); ?>" class="kl-btn kl-btn-w-auto">
                            <span class="kl-text-btn kl-text-btn-icon kl-text-btn-icon--left">
                                <!-- Nouvelle icône (flèche avec lien) -->
                                <?php echo esc_html(' Toutes les publications'); ?>
                                <?php mansa_icon('lien', 16, true, true); // true = is_stroke ?>

                            </span>
                        </a>
                    </div>

                </div>
            </div>

            <!-- 🎬 COLONNE DROITE : Vidéo -->
            <div class="col-lg-7 order-lg-2 order-1">
                <div class="media-video-column">

                    <?php if ($youtube_embed) : ?>
                    <!-- Vidéo YouTube (style inchangé) -->
                    <div class="video-container"
                        style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
                        <iframe style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"
                            src="<?php echo esc_url($youtube_embed); ?>" frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            allowfullscreen>
                        </iframe>
                    </div>
                    <?php elseif (has_post_thumbnail()) : ?>
                    <!-- Fallback : image à la une (style inchangé) -->
                    <div class="video-placeholder">
                        <?php the_post_thumbnail('large', array('class' => 'img-fluid', 'style' => 'border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.15);')); ?>
                    </div>
                    <?php else : ?>
                    <!-- Aucune vidéo avec icône du sprite (style inchangé) -->
                    <div class="no-video-placeholder"
                        style="background: #f8f9fa; padding: 3rem; text-align: center; border-radius: 12px;">
                        <svg width="64" height="64" viewBox="0 0 24 24" fill="currentColor"
                            style="margin-bottom: 1rem; opacity: 0.3;">
                            <use href="#icon-videos"></use>
                        </svg>
                        <p style="margin: 0; color: #999;"><?php echo __('Aucune vidéo disponible', 'mansa'); ?></p>
                    </div>
                    <?php endif; ?>

                    <!-- Date de publication - Légende sous la vidéo (style inchangé) -->
                    <div class="video-caption kl-mt-sp-2">
                        <span class="kl-color-gray-600 kl-fs-small"><?php echo __('Publié le', 'mansa'); ?>
                            <?php echo esc_html($publication_date); ?></span>
                    </div>

                </div>
            </div>

        </div>

        <!-- 🎬 VIDÉOS RÉCENTES (sans icône dans le titre) -->
        <div class="row mt-5 pt-5">
            <div class="col-12">
                <h2 class="kl-font-secondary kl-fw-bold kl-color-black kl-mb-sp-4">
                    <?php echo __('Vidéos récentes', 'mansa'); ?>
                </h2>
            </div>

            <?php
            // Fonction helper pour récupérer la miniature YouTube
            function get_youtube_thumbnail($post_id) {
                $lien_video = get_field('lien_video', $post_id);
                if ($lien_video && preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $lien_video, $matches)) {
                    $video_id = $matches[1];
                    // Retourne l'URL de la miniature haute qualité
                    return "https://img.youtube.com/vi/{$video_id}/maxresdefault.jpg";
                }
                return false;
            }
            
            // Récupère les 6 derniers contenus média (sauf le current)
            $recent_videos = new WP_Query(array(
                'post_type' => 'videos',
                'posts_per_page' => 6,
                'post__not_in' => array(get_the_ID()),
                'orderby' => 'date',
                'order' => 'DESC'
            ));
            
            if ($recent_videos->have_posts()) :
                while ($recent_videos->have_posts()) : $recent_videos->the_post();
                    $current_id = get_the_ID();
                    
                    // Récupère la miniature YouTube si pas d'image à la une
                    $youtube_thumb = '';
                    if (!has_post_thumbnail($current_id)) {
                        $youtube_thumb = get_youtube_thumbnail($current_id);
                    }
                    
                    // Affiche le post avec la miniature YouTube si disponible
                    ?>
            <div class="col-lg-4 col-md-6 mb-4">
                <article class="post-blog-item">
                    <a href="<?php the_permalink(); ?>" class="post-thumbnail-link">
                        <div class="post-thumbnail-wrapper">
                            <?php if (has_post_thumbnail($current_id)) : ?>
                            <?php the_post_thumbnail('medium_large', array('class' => 'img-fluid kl-img-cover')); ?>
                            <?php elseif ($youtube_thumb) : ?>
                            <img src="<?php echo esc_url($youtube_thumb); ?>"
                                alt="<?php echo esc_attr(get_the_title()); ?>" class="img-fluid kl-img-cover">
                            <?php else : ?>
                            <div class="video-placeholder-icon">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="currentColor"
                                    style="opacity: 0.3;">
                                    <use href="#icon-videos"></use>
                                </svg>
                            </div>
                            <?php endif; ?>
                        </div>
                    </a>
                    <div class="post-content" style="padding-top: 1rem;">
                        <h3 class="post-title" style="font-size: 1.25rem; margin-bottom: 0.5rem;">
                            <a href="<?php the_permalink(); ?>" style="color: #000; text-decoration: none;">
                                <?php the_title(); ?>
                            </a>
                        </h3>
                        <?php if (has_excerpt()) : ?>
                        <div class="post-excerpt" style="font-size: 0.95rem; color: #666;">
                            <?php the_excerpt(); ?>
                        </div>
                        <?php endif; ?>

                        <!-- Date avec icône du sprite -->
                        <div class="post-meta" style="font-size: 0.85rem; color: #999; margin-top: 0.5rem;">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"
                                style="vertical-align: middle; margin-right: 4px;">
                                <use href="#icon-horaire"></use>
                            </svg>
                            <span style="vertical-align: middle;"><?php echo get_the_date('j F Y'); ?></span>
                        </div>
                    </div>
                </article>
            </div>
            <?php
                endwhile;
                wp_reset_postdata();
            else :
            ?>
            <div class="col-12">
                <p><?php echo __('Aucune autre vidéo disponible.', 'mansa'); ?></p>
            </div>
            <?php endif; ?>

        </div>

    </div>
</div>

<?php
get_footer();
?>