<article class="khanh_post-01__element">
    <header class="khanh_post-01__header">
        <a href="<?php the_permalink() ?>" title="<?php the_title() ?>">
            <?php if (get_the_post_thumbnail() != '') : ?>
                <?php the_post_thumbnail() ?>
            <?php else : ?>
                <img src="http://lorempixel.com/300/300?time=<?php time() ?>" alt="">
            <?php endif; ?>
        </a>
    </header>
    <div class="khanh_post-01__body">
<!--        <div class="khanh_post-01__meta"><a href="#"><i class="fa fa-mail-forward"></i>387 Shares</a><a href="#"><i class="fa fa-eye"></i>849 View</a></div>-->
        <h2 class="khanh_post-01__title">
            <a href="<?php the_permalink() ?>" title="<?php the_title() ?>">
                <?php the_title() ?>
            </a>
        </h2>
        <div class="divder"></div>
        <div class="khanh_post-01__infobox">
            <?php $author_id = $item['post_author']; ?>

            <div class="khanh_post-01__avatar">
                <img src="<?php echo get_avatar_url($author_id); ?>" alt="">
            </div>
            <div class="khanh_post-01__info">
                <span>
                    By
                    <a href="<?php echo get_author_posts_url($author_id) ?>">
                        <?php echo the_author_meta( 'display_name' , $author_id ); ?>
                    </a>
                </span>
                <span> <?php echo get_the_date() ?></span>
            </div>
        </div>
    </div>
</article>