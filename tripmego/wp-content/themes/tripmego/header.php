<?php
/**
 * The template for displaying the header
 *
 *
 * @package tripmego
 * @since tripmego theme 1.0
 */

?>

<!DOCTYPE html>
<!--  This site was created in Webflow. http://www.webflow.com -->
<!--  Last Published: Tue Sep 12 2017 03:36:57 GMT+0000 (UTC)  -->
<html <?PHP language_attributes(); ?>data-wf-page="59814eaae127e80001d7f707" data-wf-site="59814eaae127e80001d7f704">
<head>
  <meta charset="<?PHP bloginfo('charset'); ?>">
  <meta content="width=device-width, initial-scale=1" name="viewport">
  <meta content="Webflow" name="generator">
  <meta name="viewport" content="width=device-width">
    <meta name="google-site-verification" content="ya4TbEQ0NUDdme5Ifqj68qP2YDYkPWhGeYK-h3oMHjc" />
  <title><?php wp_title( '|', true, 'right' ); ?></title>
  <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
        <?php if ( is_singular() && get_option( 'thread_comments' ) ) wp_enqueue_script( 'comment-reply' ); ?>

        <?php wp_head(); ?>

  <script type="text/javascript">WebFont.load({
  google: {
    families: ["Lato:100,100italic,300,300italic,400,400italic,700,700italic,900,900italic","Inconsolata:400,700","Montserrat:100,100italic,200,200italic,300,300italic,400,400italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic","Varela Round:400","PT Serif:400,400italic,700,700italic","PT Sans:400,400italic,700,700italic","Lora:regular,italic,700","Oxygen:300,regular,700"]
  }
});
  </script>
  
  <link href="https://fonts.googleapis.com/css?family=Kanit:200,300,400,500&amp;subset=thai" rel="stylesheet">

<link href="<?php echo get_bloginfo( 'template_directory' ); ?>/images/favicon.ico" rel="shortcut icon" type="image/x-icon" />

<?php if(is_front_page()):?>
<?PHP //echo"<script src='https://code.jquery.com/jquery-1.10.2.js'></script>"; ?>  <!--For vdo -->
<script
  src="https://code.jquery.com/jquery-1.10.2.min.js"
  integrity="sha256-C6CB9UYIS9UJeqinPHWTHVqh/E1uhG5Twh+Y5qFQmYg="
  crossorigin="anonymous"></script>
<?PHP //echo"<script src=";?><?PHP// bloginfo( 'template_directory' );?><?PHP //echo"/js/jquery-1.10.2.js' type='text/javascript'></script>"; ?>
<?PHP echo"<script src=";?><?PHP bloginfo( 'template_directory' );?><?PHP echo"/js/webflow.js' type='text/javascript'></script>"; ?>
<?PHP elseif(is_page( 'test-create' )) :  ?>

<?PHP else : ?>

<?PHP endif ; ?>


</head>

<div id="wptime-plugin-preloader"></div> <!-- Plugin Preloader -->


<body <?php body_class(); ?> id="body"> <!--<body class="body">-->

  <script>
  window.fbAsyncInit = function() {
    FB.init({
      appId      : '168382000562290',
      cookie     : true,
      xfbml      : true,
      version    : 'v2.11'
    });
      
    FB.AppEvents.logPageView();   
      
  };



  (function(d, s, id){
     var js, fjs = d.getElementsByTagName(s)[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement(s); js.id = id;
     js.src = "https://connect.facebook.net/en_US/sdk.js";
     fjs.parentNode.insertBefore(js, fjs);
   }(document, 'script', 'facebook-jssdk'));
</script>
  <div class="div_fixnav w-clearfix">
    <nav class="nav_bar_main w-nav">
        <span id="brand">
          <div class="div_logo w-clearfix" id="Top_logo"><!-- div logo top -->
              <a class="brand-2 w-nav-brand" href="<?PHP echo home_url(); ?>">

                  <img class="image-2" src="<?php echo get_bloginfo( 'template_directory' ); ?>/images/icon/Group-95-Copy-12.png" width="210">
              </a>
          </div> <!-- end div_logo -->
        </span>
          <div class="container_main_menu w-container"><!-- Main Menu navbar -->
              <!--<div class="nav_main_menu w-hidden-small w-hidden-tiny w-nav-menu" role="navigation">-->
                <?PHP  
                  $menuParameters = array(
                    'theme_location' => 'primary',
                    'container'       => false,
                    'echo'            => false,
                    //'items_wrap'      => '%3$s',
                    'menu_id'         => 'menu',
                    'depth'           => 0,
                    );
                    echo wp_nav_menu($menuParameters);  
                ?>
             <!-- </div>-->
          </div> <!-- END Main Menu navbar -->
        
          <div class="resmenu">
                <span style="font-size:18px;cursor:pointer" onclick="openMenu()">
                    <button id="myBtn3" class="btn_responsive_menu ">
                      <i class="fa fa-bars" aria-hidden="true"></i>
                </button>
          </div>
          <?PHP do_action ('tripmego_resmenu'); ?>

    <!-- <div class="nav_search w-nav"> -->
          <div class="div_nav_search_icon">
                <span style="font-size:18px;cursor:pointer" onclick="openNav()">
                    <button id="myBtn2" class="btn_search ">
                      <i class="fa fa-search" aria-hidden="true"></i>
                    </button>
                </span>
          </div>
          <?PHP do_action ('tripmego_filter'); ?>

   
          <div class="menu_btn_profile">
            <span style="font-size:17px;cursor:pointer" onclick="openNav2()">
              <?php if(is_user_logged_in()) : ?>
                   <button id="myBtn2" class="btn_profile ">
                          <?PHP //dynamic_sidebar('sidebar'); ?>
                            <?php
                            $profilepic =  array( 
                              'theme_location'  => 'profileimg', 
                                  'container'       => false,
                                  'echo'            => false,
                                  'fallback_cb'     => false,
                                  'items_wrap' => '%3$s',
                            'depth'           => 0,
                                  );


                $output5 = strip_tags(wp_nav_menu($profilepic),'<a>');
                $output5 = preg_replace('/<a/', '<a class="" href="#"', $profilepic);
           
                      echo wp_nav_menu($output5);  

                          ?>
                  </button>
              <?php  else : ?>
             <button id="myBtn2" class="btn_profile ">
                      <i class="fa fa-user-o iconhuman" aria-hidden="true"></i>
             </button>
      <?PHP endif ; ?>
             
            </span>
          </div>

          <?PHP do_action ('tripmego_profile'); ?>

    </nav> <!-- end div nav_bar_main-->
  </div> <!-- end div fix nav -->

            <script>
                  // search & filter menu
                function openNav() {
                    document.getElementById("mySidenav").style.width = "350px";    
                }

                function closeNav() {
                    document.getElementById("mySidenav").style.width = "0";
                }

                  // Profile menu
                function openNav2() {
                    document.getElementById("mySidenav2").style.width = "300px";
                    document.getElementById("main").style.marginRight = "300px";
                    document.body.style.backgroundColor = "white";    
                }

                function closeNav2() {
                    document.getElementById("mySidenav2").style.width = "0";
                    document.getElementById("main").style.marginRight= "0";
                    document.body.style.backgroundColor = "white";
                }
                  //Responsive Menu
                function openMenu() {
                    document.getElementById("resize").style.width = "350px";    
                }
                function closeMenu() {
                    document.getElementById("resize").style.width = "0";
                }

               
             </script>

<?php open_div_id_main(); ?>





