<?php
/**
 * Template pour afficher un événement unique
 */

get_header();

// --- Lien retour vers la page des événements ---
$id_events = get_option('page_for_posts');
$link_events = get_the_permalink($id_events);

// --- Récupération des catégories ---
$categories = get_the_category();
$cat_name = !empty($categories) ? esc_html($categories[0]->name) : '';

// --- Récupération des champs ACF ---
$date_debut   = get_field('date_evenement');
$date_fin     = get_field('date_fin');
$heure_debut  = get_field('heure_debut');
$heure_fin    = get_field('heure_fin');
$lieu         = get_field('lieu_evenement');
$tarifs       = get_field('tarifs');
$tarifs_2     = get_field('tarifs_2'); // Nouveau champ tarif 2
$lien_resa    = get_field('je_reserve');
$image_ev     = get_field('image_evenement');
$transports   = get_field('transport');
$parking      = get_field('parking');
$en_savoir_plus = get_field('en_savoir_plus'); // Nouveau champ

// --- Formatage des dates ---
function format_date_fr($date_string) {
    if (!$date_string) return '';
    
    if (strpos($date_string, '/') !== false) {
        $date_obj = DateTime::createFromFormat('d/m/Y', $date_string);
    } 
    elseif (strpos($date_string, '-') !== false) {
        $date_obj = DateTime::createFromFormat('Y-m-d', $date_string);
    }
    else {
        $date_obj = date_create($date_string);
    }
    
    if (!$date_obj) return $date_string;
    
    $mois = ['', 'janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];
    
    $jour = $date_obj->format('j');
    $mois_nom = $mois[(int)$date_obj->format('n')];
    $annee = $date_obj->format('Y');
    
    return "$jour $mois_nom $annee";
}

// --- Formatage des heures ---
function format_heure_fr($heure_string) {
    if (!$heure_string) return '';
    
    $heure_string = substr($heure_string, 0, 5);
    list($heures, $minutes) = explode(':', $heure_string);
    
    if ($minutes === '00') {
        return $heures . 'h';
    }
    
    return $heures . 'h' . $minutes;
}

// --- Application du formatage ---
$affichage_date = '';
if ($date_debut) {
    $date_debut_format = format_date_fr($date_debut);
    if ($date_fin && $date_fin !== $date_debut) {
        $date_fin_format = format_date_fr($date_fin);
        $affichage_date = "Du $date_debut_format au $date_fin_format";
    } else {
        $affichage_date = $date_debut_format;
    }
}

$affichage_heure = '';
if ($heure_debut) {
    $affichage_heure = format_heure_fr($heure_debut);
    if ($heure_fin) {
        $affichage_heure .= ' à ' . format_heure_fr($heure_fin);
    }
}

// --- Date badge (format "13 janvier 2026" pour le badge) ---
$date_badge = '';
if ($date_debut) {
    $date_badge = format_date_fr($date_debut);
}

// --- Génération de l'URL Google Maps ---
$google_maps_url = '';
if ($lieu) {
    $lieu_encoded = urlencode($lieu);
    $google_maps_url = "https://maps.google.com/maps?width=100%25&amp;height=400&amp;hl=fr&amp;q=" . $lieu_encoded . "&amp;t=&amp;z=14&amp;ie=UTF8&amp;iwloc=B&amp;output=embed";
}

// --- Récupération de la légende de l'image ---
$image_caption = '';
if ($image_ev && is_array($image_ev) && !empty($image_ev['caption'])) {
    $image_caption = $image_ev['caption'];
}
?>

<div class="kl-content-wrapper kl-mb-sp-6">
    <div class="container kl-container-default">

        <!-- Badges du haut -->
        <?php if ($cat_name || $date_badge): ?>
        <div class="event-header-badges kl-mb-sp-3">
            <?php if ($cat_name): ?>
            <span
                class="kl-single-title-post--title kl-headline-single-title kl-fw-bold mb-0 d-inline-block kl-mb-sp-4">
                <?php echo $cat_name; ?>
            </span>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Titre principal -->
        <h1 class="event-main-title kl-font-secondary kl-fw-bold kl-mb-sp-4">
            <?php the_title(); ?>
        </h1>

        <!-- Disposition en 3 colonnes : 25% Image | 50% Texte | 25% Infos -->
        <div class="row event-three-columns">

            <!-- Colonne 1 : Image (25%) -->
            <?php if ($image_ev): ?>
            <div class="col-event-image">
                <div class="event-image-wrapper">
                    <img src="<?php echo esc_url($image_ev['url']); ?>"
                        alt="<?php echo esc_attr($image_ev['alt'] ?: get_the_title()); ?>" class="img-fluid">
                    <?php if ($image_caption): ?>
                    <p class="event-image-caption"><?php echo esc_html($image_caption); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Colonne 2 : Contenu texte (50%) -->
            <div class="col-event-content">
                <article class="event-article-content kl-bloc-the-content">
                    <?php the_content(); ?>
                </article>
            </div>

            <!-- Colonne 3 : Infos pratiques (25%) -->
            <div class="col-event-sidebar">
                <div class="event-sidebar-container">

                    <!-- Tarif -->
                    <div class="info-row" id="event-tarif">
                        <div class="info-header">
                            <!-- Icône masquée -->
                        </div>
                        <div class="info-value">
                            <?php 
                            if ($tarifs && $tarifs_2) {
                                // Si les deux tarifs sont renseignés : "tarif / tarif2 €"
                                echo esc_html($tarifs) . ' / ' . esc_html($tarifs_2) . '&nbsp;€';
                            } elseif ($tarifs) {
                                // Si seul tarifs est renseigné
                                echo esc_html($tarifs) . '&nbsp;€';
                            } elseif ($tarifs_2) {
                                // Si seul tarifs_2 est renseigné
                                echo esc_html($tarifs_2) . '&nbsp;€';
                            } else {
                                // Si aucun tarif n'est renseigné
                                echo __('Entrée libre sur réservation', 'mansa');
                            }
                            ?>
                        </div>
                    </div>

                    <!-- Bouton réservation (en tête) -->
                    <?php if ($lien_resa): ?>
                    <div class="sidebar-cta-top">
                        <a href="<?php echo esc_url($lien_resa); ?>" class="kl-btn kl-btn-w-auto btn-reserve-square"
                            target="_blank" rel="noopener noreferrer">
                            <span class="kl-text-btn"><?php echo __('Je réserve', 'mansa'); ?></span>
                        </a>
                    </div>
                    <?php endif; ?>

                    <!-- Informations pratiques -->
                    <div class="sidebar-info-list">

                        <!-- 3. Quand (Date & Horaire) - AVEC ICÔNE HORAIRE -->
                        <?php if ($affichage_date || $affichage_heure): ?>
                        <div class="info-row info-row-quand">
                            <div class="info-header">
                                <?php mansa_icon('horaire', 20, true); ?>
                                <span class="info-label"><?php echo __('Quand', 'mansa'); ?></span>
                            </div>
                            <div class="info-value">
                                <?php 
                                echo $affichage_date;
                                if ($affichage_date && $affichage_heure) echo ', ';
                                echo $affichage_heure;
                                ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- 4. Où (Lieu) - AVEC ICÔNE LOCALISATION -->
                        <?php if ($lieu): ?>
                        <div class="info-row info-row-lieu">
                            <div class="info-header">
                                <?php mansa_icon('localisation', 20, true); ?>
                                <span class="info-label"><?php echo __('Où', 'mansa'); ?></span>
                            </div>
                            <div class="info-value"><?php echo nl2br(esc_html($lieu)); ?></div>
                        </div>
                        <?php endif; ?>

                    </div>

                    <!-- 5. Carte Google Maps (en dernier, après la sidebar-info-list) -->
                    <?php if ($google_maps_url): ?>
                    <div class="sidebar-map-wrapper">
                        <iframe scrolling="no" src="<?php echo esc_url($google_maps_url); ?>"
                            style="border: 0; width: 100%; height: 150px; border-radius: 8px;" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Section En savoir plus (trait complet en bas des colonnes) -->
        <?php if ($en_savoir_plus): ?>
        <div class="event-en-savoir-plus-section">
            <div class="en-savoir-plus-divider"></div>
            <div class="en-savoir-plus-content">
                <h3 class="en-savoir-plus-title"><?php echo __('En savoir plus', 'mansa'); ?></h3>
                <div class="en-savoir-plus-text">
                    <?php echo $en_savoir_plus; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Bouton retour -->
        <div class="kl-back-parent kl-mt-sp-5">
            <a href="<?php echo esc_url($link_events); ?>" class="kl-btn kl-btn-w-auto">
                <span class="kl-text-btn kl-text-btn-icon kl-text-btn-icon--left">
                    <?php echo __('Retour à l\'agenda', 'mansa'); ?>
                    <?php mansa_icon('lien', 16, true, true); // true pour stroke ?>
                </span>
            </a>
        </div>

    </div>
</div>

<?php get_footer(); ?>