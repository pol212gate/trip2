<?php
$wt_main_color = get_option('wt_main_color');
$hex  = $wt_main_color = str_replace("#", "", $wt_main_color);

if(strlen($hex) == 3) {
  $r = hexdec(substr($hex,0,1).substr($hex,0,1));
  $g = hexdec(substr($hex,1,1).substr($hex,1,1));
  $b = hexdec(substr($hex,2,1).substr($hex,2,1));
} else {
  $r = hexdec(substr($hex,0,2));
  $g = hexdec(substr($hex,2,2));
  $b = hexdec(substr($hex,4,2));
}
$rgb = $r.','. $g.','.$b;
if($wt_main_color!=''){?>
	.widget.wt-latest-tours-widget .thumb.item-thumbnail .item-evprice,
    .btn.wt-button,
    .ex-loadmore .loadmore-grid,
    .wt-grid-shortcode figure.ex-modern-blog .date,
    .wt-departure .picker table thead tr th,
    .wt-grid-shortcode.wt-grid-column-1 figure.ex-modern-blog .ex-social-share ul li a,
    .wt-table-lisst .wt-table th,
    .widget.wt-latest-tours-widget .item .wt-big-date > div, 
    .wt-search-form button[type="submit"]:hover, .wt-search-form button[type="submit"],
    .wt-quantity > input[type=button],  
    .wt-grid-shortcode figure.ex-modern-blog .ex-social-share{ background:#<?php echo esc_html($wt_main_color);?>}
    .wt-table-lisst .wt-table td.tb-viewdetails .tb-price,
    .wt-search-form span.loc-details h3,
    
    .wt-table-lisst .wt-table td h3 a,
    .wt-table-lisst .wt-table td.tb-viewdetails .tb-price,
    figure.ex-modern-blog h3,
    
    .wt-table-lisst .wt-table td.tb-price, .wt-table-lisst .wt-table td span.amount{ color:#<?php echo esc_html($wt_main_color);?>}
    .wt-search-form button[type="submit"]:hover, .wt-search-form button[type="submit"],
    .wt-table-lisst .wt-table{ border-color:#<?php echo esc_html($wt_main_color);?>}
    .wt-location-arr{background: rgba(<?php echo esc_attr($rgb);?>, .7);}
<?php
}
$wt_fontfamily = get_option('wt_fontfamily');
$main_font_family = explode(":", $wt_fontfamily);
$main_font_family = $main_font_family[0];
if($wt_fontfamily!=''){?>
    .wt-search-form input.form-control::-webkit-input-placeholder{
        font-family: "<?php echo esc_html($main_font_family);?>", sans-serif;
    }
    .wt-search-form input.form-control:-ms-input-placeholder{
        font-family: "<?php echo esc_html($main_font_family);?>", sans-serif;
    }
    .wt-search-form input.form-control:-moz-placeholder{
        font-family: "<?php echo esc_html($main_font_family);?>", sans-serif;
    }
	.wt-search-form input.form-control {
        font-family: "<?php echo esc_html($main_font_family);?>", sans-serif;
    }
    
    
    .wt-grid-shortcode figure.ex-modern-blog .wt-more-meta span{
        font-family: "<?php echo esc_html($main_font_family);?>", sans-serif;
    }
<?php }
$wt_fontsize = get_option('wt_fontsize');
if($wt_fontsize!=''){?>
	.wt-location-arr,
    .woocommerce-wt-onsale,
    .wt-search-form .btn, .wt-search-form input[type="text"],
    .wt-search-form span.loc-details span,
    .wt-grid-shortcode figure.ex-modern-blog .grid-excerpt,
    .ex-loadmore .loadmore-grid,
    .wt-table-lisst .wt-table,
    .btn.wt-button,
    .wootour-search .btn.wt-product-search-dropdown-button,
    .wootour-search .wt-product-search-form button,
    .wt-grid-shortcode figure.ex-modern-blog .wt-more-meta span{
        font-size: <?php echo esc_html($wt_fontsize) ?>;
    }
    .wt-search-form input.form-control::-webkit-input-placeholder{ font-size: <?php echo esc_html($wt_fontsize) ?>;  }
    .wt-search-form input.form-control{ font-size: <?php echo esc_html($wt_fontsize) ?>;  }
    .wt-search-form input.form-control:-ms-input-placeholder{ font-size: <?php echo esc_html($wt_fontsize) ?>;  }
    .wt-search-form input.form-control:-moz-placeholder{ font-size: <?php echo esc_html($wt_fontsize) ?>;  }

<?php }
$wt_hfont = get_option('wt_hfont');
$h_font_family = explode(":", $wt_hfont);
$h_font_family = $h_font_family[0];
if($h_font_family!=''){?>
    .wt-table-lisst .wt-table td h3 a,
    .wt-grid-shortcode figure.ex-modern-blog h3 a,
	.wt-infotable .wemap-details h4.wemap-title a{
        font-family: "<?php echo esc_html($h_font_family);?>", sans-serif;
    }
<?php }

$wt_hfontsize = get_option('wt_hfontsize');
if($wt_hfontsize!=''){?>
	.wt-table-lisst .wt-table td h3 a, .wt-table-lisst .wt-table td.tb-viewdetails .tb-price, 
    .wt-search-form span.loc-details h3,
    .wt-table-lisst .wt-table td.tb-viewdetails .tb-price, figure.ex-modern-blog h3{
        font-size: <?php echo esc_html($wt_hfontsize); ?>;
    }
<?php }

$wt_custom_css = get_option('wt_custom_css');
if($wt_custom_css!=''){
	echo $wt_custom_css;
}