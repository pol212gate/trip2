<article class="khanh_post-03__element khanh_post-03__style-02">
    <header class="khanh_post-03__header">
        <a href="<?php the_permalink() ?>" title="<?php the_title() ?>">
            <?php if (get_the_post_thumbnail() != '') : ?>
                <?php the_post_thumbnail() ?>
            <?php else : ?>
                <img src="http://lorempixel.com/300/300?time=<?php time() ?>" alt="">
            <?php endif; ?>
        </a>
        <div class="khanh_post-03__iconheader"><i class="fa fa-image"></i></div>
    </header>
    <div class="khanh_post-03__body">
<!--        <ul class="khanh_post-03__cat">-->
<!--            <li><a href="#">Design</a></li>-->
<!--        </ul>-->
        <h2 class="khanh_post-03__title">
            <a href="<?php the_permalink() ?>" title="<?php the_title() ?>">
                <?php the_title() ?>
            </a>
        </h2>
        <div class="khanh_post-03__meta">
            <span class="khanh_post-03__author">
                <?php $author_id = $item['post_author']; ?>
                <i class="fa fa-user"></i>
                <?php echo __('Posted by', 'advanced_search_form_builder') ?>
                <a href="<?php echo get_author_posts_url($author_id) ?>">
                    <?php the_author_meta( 'display_name' , $author_id ); ?>
                </a>
            </span>
            <span>
                <i class="fa fa-calendar"></i><?php echo get_the_date() ?>
            </span>
        </div>
    </div>
</article>