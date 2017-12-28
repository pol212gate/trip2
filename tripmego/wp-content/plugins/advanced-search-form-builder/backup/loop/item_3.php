<article class="khanh_post-03__element">
    <header class="khanh_post-03__header">
        <a href="<?php the_permalink() ?>" title="<?php the_title() ?>">
            <?php if (get_the_post_thumbnail() != '') : ?>
                <?php the_post_thumbnail() ?>
            <?php else : ?>
                <img src="http://lorempixel.com/300/300?time=<?php time() ?>" alt="">
            <?php endif; ?>
        </a>
    </header>
    <div class="khanh_post-03__body">
        <h2 class="khanh_post-03__title">
            <a href="<?php the_permalink() ?>" title="<?php the_title() ?>">
                <?php the_title() ?>
            </a>
        </h2>
        <div class="khanh_post-03__meta">
            <?php $author_id = $item['post_author']; ?>
            <span class="khanh_post-03__author">
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

        <div class="khanh_post-03__footer">
            <span class="khanh_post-03__readmore">
                <a href="<?php the_permalink() ?>" title="<?php the_title() ?>">
                    <?php echo __('Read more', 'advanced_search_form_builder') ?>
                </a>
            </span>

            <span class="khanh_post-03__share">
                <i class="fa fa-share-alt"></i>
                <span class="khanh_post-03__group">
                    <a href="#">Facebook</a>
                    <a href="#">Google</a>
                    <a href="#">Twitter</a>
                </span>
            </span>
            <span>
                <a href="<?php the_permalink() ?>" title="<?php the_title() ?>">
                    <i class="fa fa-comments"></i> <?php echo get_comments_number() ?>
                </a>
            </span>
<!--            <span>-->
<!--                <a href="#"><i class="fa fa-heart-o"></i>09</a>-->
<!--            </span>-->
        </div>
    </div>
</article>