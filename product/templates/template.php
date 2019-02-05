<?php

$el = $this->el('div', [

    'class' => [
        'product-element'
    ]

]);

/**
 * Var
 */

$id = $props['field_product_id'];
$category = $props['field_product_category'];
$tags = $props['field_product_tag'];

//Grid
$grid_default = 'uk-child-width-1-'.$props['field_grid_default'];
$grid_small = 'uk-child-width-1-'.$props['field_grid_small'].'@s';
$grid_medium = 'uk-child-width-1-'.$props['field_grid_medium'].'@m';
$grid_large = 'uk-child-width-1-'.$props['field_grid_large'].'@l';;
$grid_xlarge = 'uk-child-width-1-'.$props['field_grid_xlarge'].'@xl';

$theme_grid = 'uk-grid-small uk-grid-match '.$grid_default.' '.$grid_small.' '.$grid_medium.' '.$grid_large.' '.$grid_xlarge;

//General
$theme_cart = $props['field_product_cart'];
$theme_rating = $props['field_product_ratting'];
$theme_slider = $props['field_product_slider'];

//Slider
if ($theme_slider == 1 ){
  $class_padding = 'uk-padding-small';
  $class_slider = 'uk-slider-items';
  $class_card = '';
  $start_slider = '<div uk-slider="sets: true">';
  $end_slider = '</div>';
  $start_nav = '<div class="uk-position-relative uk-padding uk-padding-remove-left uk-padding-remove-right">';
  $end_nav = '<a class="uk-position-center-left" href="#" uk-slidenav-previous uk-slider-item="previous"></a><a class="uk-position-center-right" href="#" uk-slidenav-next uk-slider-item="next"></a></div>';
 } else {
  $class_padding = 'uk-padding-small';
  $class_card = 'uk-card-hover';
  $class_slider = '';
  $start_slider = '';
  $end_slider = '';
  $start_nav = '';
  $end_nav = '';
}

//Query Data
$args = array(
  'post_type' => 'product',
  'post_status' => 'publish',
  'posts_per_page' => $props['field_product_number'],
  'product_type' => $props['field_product_type'],
  'orderby' => $props['field_product_orderby'],
  'order' => $props['field_product_order'],
  'meta_query' => array(
       array(
         'key' => '_stock_status',
         'value' => 'outofstock',
         'compare' => '!='
       )
   ),
);

// From selected product ids
if( $props['field_product_type'] == 'id' ){
  $args['post__in'] = ( $id ? explode( ',', $id ) : null );
}

// From selected product featured
if($props['field_product_type'] == 'featured'){
  $args['tax_query'][] = array(
    'taxonomy' => 'product_visibility',
            'field'    => 'name',
            'terms'    => 'featured',
  );
}

// From selected product categories
if( $props['field_product_type'] == 'category' && $category != '' ){
  $category = explode(',', $category);
  $args['tax_query'][] = array(
    'taxonomy' 	=> 'product_cat',
        'field'    	=> 'term_id',
    'terms'    	=> $category,
  );
}

// From selected product tags
if( $props['field_product_type'] == 'tags' && $tags != '' ){
  $tags = explode(',', $tags);
  $args['tax_query'][] = array(
    'taxonomy' 	=> 'product_tag',
        'field'    	=> 'id',
    'terms'    	=> $tags,
        'operator' 	=> 'IN'
  );
}

$loop = new WP_Query( $args );

?>

<?= $el($props, $attrs) ?>
  <?= $start_slider;?>
    <?= $start_nav;?>

      <div class="<?php echo $theme_grid; ?> <?= $class_slider;?> uk-grid woocommerce" uk-grid>

        <?php
          while ( $loop->have_posts() ) : $loop->the_post();
          global $product;
        ?>
          <div class="item-product products uk-product-item-<?=$product->get_id();?>">
            <div class="uk-card <?= $class_card; ?>">
              <div class="uk-card-media-top uk-position-relative uk-visible-toggle">
                <?= $product->get_image('woocommerce_thumbnail', array(
                  'class' => 'uk-product-element-photo',
                  'alt' => $product->get_name(),
                ));?>
                <div class="uk-overlay-default uk-position-cover uk-invisible-hover">
                  <a href="<?= $product->get_permalink();?>" class="uk-position-cover uk-product-element-link-overlay"></a>
                  <div class="uk-position-center uk-text-center uk-position-small">
                    <div class="uk-action-btn">
                      <?= $product->get_sku(); ?>
                      <div class="uk-product-element-button uk-margin-small-top">
                        <?php if ($theme_cart == 1) :?>
                          <?= woocommerce_template_loop_add_to_cart(array(
                            'class' => implode( ' ', array_filter( array(
                              'uk-button uk-button-primary uk-button-small',
                              'product_type_' . $product->get_type(),
                              $product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
                              $product->supports( 'ajax_add_to_cart' ) ? 'ajax_add_to_cart' : '',
                          ) ) ),
                          ));?>
                        <?php else :?>
                          <a href="<?= $product->get_permalink();?>" class="uk-button uk-button-primary uk-button-small">
                            <?php echo __( 'View', 'woocommerce' );?>
                          </a>
                        <?php endif;?>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="uk-card-body <?= $class_padding;?>">
                <h5 class="uk-margin-remove-bottom uk-text-uppercase">
                  <a href="<?= $product->get_permalink();?>">
                   <?= $product->get_name(); ?>
                  </a>
                </h5>
                <div class="uk-product-element-price uk-text-small">
                  <?= $product->get_price_html();?>
                </div>
                <?php if($theme_rating == 1) :?>
                  <div class="uk-product-element-rating uk-text-small">
                    <div class="star-rating uk-margin-remove">
                      <?= wc_get_star_rating_html( $product->get_average_rating() );?>
                    </div>
                  </div>
                <?php endif;?>
              </div>
            </div>
          </div>
        <?php
          endwhile;
          wp_reset_query();
        ?>

      </div>

    <?= $end_nav;?>
  <?= $end_slider;?>
</div>
