<?php
get_header();
global $wp_query;

$yolo_sidebar_position = function_exists( 'fw_ext_sidebars_get_current_position' ) ? fw_ext_sidebars_get_current_position() : 'right';

$ext_photography_instance = fw()->extensions->get( 'photography' );
$ext_photography_settings = $ext_photography_instance->get_settings();
$thumbnails               = fw_ext_photography_get_gallery_images();
$term_list                = get_the_term_list( get_the_ID(), $ext_photography_instance->get_taxonomy_name(), '', ', ' );


$prevPost = get_previous_post();
$nextPost = get_next_post();

?>
    <section class="bt-main-row <?php yolo_get_content_class( 'main', $yolo_sidebar_position ); ?>" role="main" itemprop="mainEntity" itemscope="itemscope" itemtype="http://schema.org/Blog">
    <div class="photography-wrap">

        <div class="photography-carousel"> 
            <div class="owl-carousel" data-bears-owl-carousel="">
                <?php
                /* gallery */
                if ( ! empty( $thumbnails ) && count( $thumbnails ) > 0 ) :
	                foreach ( $thumbnails as $thumbnail ) :
		                echo '<div class="photo-inner" style="background-image: url(' . $thumbnail['url'] . ');"></div>';
	                endforeach;
                else:
                    echo '<div class="photo-inner" style="background-image: url(' . get_the_post_thumbnail_url(get_the_ID(), 'full' ) . ');"></div>';
                endif;
                ?>
            </div>
        </div><!-- /.photography-carousel -->

        <div class="container">
            <div class="row">
                <div class="bt-content-area <?php yolo_get_content_class( 'content', $yolo_sidebar_position ); ?>">
                    <div class="bt-col-inner">
                        <?php while ( have_posts() ) : the_post();
	                        ?>
                            <article id="post-<?php the_ID(); ?>" <?php post_class( "photography photography-details" ); ?> itemscope="itemscope" itemtype="http://schema.org/photographyPosting" itemprop="photographyPost">
                            <div class="entry-content">
                                <div class="col-md-4">
                                    <h1 class="post-title"><?php the_title(); ?></h1>
                                    <div class="post-cat" title="<?php __( 'Post in category', 'yolo' ) ?>"><?php echo $term_list; ?></div>
                                    
                                    <div class="social-share-entry"><?php echo __( 'Share', 'yolo' ); ?>: 
                                        <?php echo yolo_share_post( array(
	                                        'facebook'    => true,
	                                        'twitter'     => true,
	                                        'google_plus' => true,
	                                        'linkedin'    => true,
	                                        'pinterest'   => true
                                        ) ); ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="photo-entry">
                                        <?php 
                                        the_content(); 
                                        wp_link_pages( array(
                                            'before'      => '<div class="page-links"><span class="page-links-title">' . esc_html__( 'Pages:', 'yolo' ) . '</span>',
                                            'after'       => '</div>',
                                            'link_before' => '<span>',
                                            'link_after'  => '</span>',
                                        ) );?>
                                    </div> 
                                </div>
                                <div class="col-md-4">
                                    <div class="photo-params"> 
                                        <table class="table"> 
                                            <tbody>
                                                <?php if (! empty(fw_get_db_post_option( get_the_ID(), 'post_photo_param' )['photo_make'])) {
                                                    echo '<tr>';
                                                        echo '<td>'. __( 'MAKE', 'yolo' ). '</td>';
                                                        echo '<td>'.fw_get_db_post_option( get_the_ID(), 'post_photo_param' )['photo_make'].'</td>';
                                                    echo '</tr>';
                                                }?>
                                                <?php if (! empty(fw_get_db_post_option( get_the_ID(), 'post_photo_param' )['photo_model'])) {
                                                    echo '<tr>';
                                                        echo '<td>'. __( 'MODEL', 'yolo' ). '</td>';
                                                        echo '<td>'.fw_get_db_post_option( get_the_ID(), 'post_photo_param' )['photo_model'].'</td>';
                                                    echo '</tr>';
                                                }?>
                                                <?php if (! empty(fw_get_db_post_option( get_the_ID(), 'post_photo_param' )['photo_shutter_speed'])) {
                                                    echo '<tr>';
                                                        echo '<td>'. __( 'SHUTTER SPEED', 'yolo' ). '</td>';
                                                        echo '<td>'.fw_get_db_post_option( get_the_ID(), 'post_photo_param' )['photo_shutter_speed'].'</td>';
                                                    echo '</tr>';
                                                }?>
                                                <?php if (! empty(fw_get_db_post_option( get_the_ID(), 'post_photo_param' )['photo_aperture'])) {
                                                    echo '<tr>';
                                                        echo '<td>'. __( 'APERTURE', 'yolo' ). '</td>';
                                                        echo '<td>'.fw_get_db_post_option( get_the_ID(), 'post_photo_param' )['photo_aperture'].'</td>';
                                                    echo '</tr>';
                                                }?>
                                                <?php if (! empty(fw_get_db_post_option( get_the_ID(), 'post_photo_param' )['photo_focal_length'])) {
                                                    echo '<tr>';
                                                        echo '<td>'. __( 'FOCAL LENGTH', 'yolo' ). '</td>';
                                                        echo '<td>'.fw_get_db_post_option( get_the_ID(), 'post_photo_param' )['photo_focal_length'].'</td>';
                                                    echo '</tr>';
                                                }?>
                                                <?php if (! empty(fw_get_db_post_option( get_the_ID(), 'post_photo_param' )['photo_iso'])) {
                                                    echo '<tr>';
                                                        echo '<td>'. __( 'ISO', 'yolo' ). '</td>';
                                                        echo '<td>'.fw_get_db_post_option( get_the_ID(), 'post_photo_param' )['photo_iso'].'</td>';
                                                    echo '</tr>';
                                                }?>
                                                <?php if (! empty(fw_get_db_post_option( get_the_ID(), 'post_photo_param' )['photo_dimension'])) {
                                                    echo '<tr>';
                                                        echo '<td>'. __( 'DIMENSIONS', 'yolo' ). '</td>';
                                                        echo '<td>'.fw_get_db_post_option( get_the_ID(), 'post_photo_param' )['photo_dimension'].'</td>';
                                                    echo '</tr>';
                                                }?>
                                                <?php if (! empty(fw_get_db_post_option( get_the_ID(), 'post_photo_param' )['photo_published'])) {
                                                    echo '<tr>';
                                                        echo '<td>'. __( 'PUBLISHED ON', 'yolo' ). '</td>';
                                                        echo '<td>'.fw_get_db_post_option( get_the_ID(), 'post_photo_param' )['photo_published'].'</td>';
                                                    echo '</tr>';
                                                }?> 
                                            </tbody>
                                        </table> 
                                    </div> 
                                </div> 
                            </div>
                            </article>
	                        <?php
	                        break;
                        endwhile; ?>
                        <?php if($prevPost || $nextPost) : ?>
                            <ul class="previous-next-link">
                                <?php if($prevPost) { ?>
                                <li class="previous">
                                    <?php $prevthumbnail = get_the_post_thumbnail($prevPost->ID, array(80,80) ); ?>
                                    <?php previous_post_link('%link', $prevthumbnail . '<div><div class="icon"><span class="ion-ios-arrow-thin-left"></span> '.__('Previous', 'yolo').'</div> <div class="title">%title</div></div>'); ?>
                                </li>
                                <?php } if($nextPost) { ?>
                                <li class="next">
                                    <?php $nextthumbnail = get_the_post_thumbnail($nextPost->ID, array(80,80) ); ?>
                                    <?php next_post_link('%link', $nextthumbnail . '<div><div class="icon">'.__('Next', 'yolo').' <span class="ion-ios-arrow-thin-right"></span></div> <div class="title">%title</div></div>'); ?>
                                </li>
                                <?php } ?>
                            </ul>
                        <?php endif; ?>
                    </div><!-- /.bt-col-inner -->
                </div><!-- /.bt-content-area -->
	            <?php get_sidebar(); ?>
            </div><!-- /.row -->
        </div><!-- /.container -->

    </div><!-- /.photography-wrap -->
</section>
<?php
// free memory
unset( $ext_photography_instance );
unset( $ext_photography_settings );
set_query_var( 'fw_photography_loop_data', '' );
get_footer();
