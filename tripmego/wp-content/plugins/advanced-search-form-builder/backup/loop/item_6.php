<article class="khanh_post-02__element">
    <header class="khanh_post-02__header">
        <a href="<?php the_permalink() ?>" title="<?php the_title() ?>">
            <?php if (get_the_post_thumbnail() != '') : ?>
                <?php the_post_thumbnail() ?>
            <?php else : ?>
                <img src="http://lorempixel.com/300/300?time=<?php time() ?>" alt="">
            <?php endif; ?>
        </a>

<!--        <div class="khanh_post-02__label"><span>typogaphy</span></div>-->
    </header>
    <div class="khanh_post-02__body">
        <h2 class="khanh_post-02__title">
            <a href="<?php the_permalink() ?>" title="<?php the_title() ?>">
                <?php the_title() ?>
            </a>
        </h2>
        <div class="khanh_post-02__meta">
            <span class="khanh_post-02__date">
                <span class="khanh_post-02__day"><?php echo get_the_date('d') ?></span>
                <span class="khanh_post-02__month"><?php echo get_the_date('M') ?></span>
            </span>
            <span class="khanh_post-02__author"><i class="fa fa-user"></i><?php echo __('Posted by', 'advanced_search_form_builder') ?>
                <?php $author_id = $item['post_author']; ?>
                <a href="<?php echo get_author_posts_url($author_id) ?>">
                    <?php the_author_meta( 'display_name' , $author_id ); ?>
                </a>
            </span><span class="khanh_post-02__share">
                <i class="fa fa-share-alt"></i>
                <a href="#">Facebook</a>
                <a href="#">Google</a>
                <a href="#">Twitter</a>
            </span>
        </div>
        <a class="khanh_post-02__readmore" title="<?php the_title() ?>" href="<?php the_permalink() ?>"><i class="fa fa-plus"></i></a>
    </div>
</article>