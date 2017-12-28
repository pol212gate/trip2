<div class="khanh_hotel-01__module">
    <header class="khanh_hotel-01__header">
        <a href="<?php the_permalink() ?>" title="<?php the_title() ?>">
            <?php if (get_the_post_thumbnail() != '') : ?>
                <?php the_post_thumbnail() ?>
            <?php else : ?>
                <img src="http://lorempixel.com/300/300?time=<?php time() ?>" alt="">
            <?php endif; ?>
        </a>
        <div class="khanh_hotel-01__active">
            <a class="khanh_hotel-01__booking">for rent</a><span class="khanh_hotel-01__price">$22.000</span>
        </div>
    </header>
    <div class="khanh_hotel-01__body">
        <ul class="khanh_hotel-01__navtab">
            <li>ruxuly</li>
            <li>Bern</li>
            <li>Switzerland</li>
        </ul>
        <div>
            <div class="khanh_hotel-01__tab">
                <ul>
                    <li><i class="fa fa-bed"></i>4</li>
                    <li><i class="fa fa-bath"></i>2</li>
                    <li><i class="fa fa-car"></i>1</li>
                    <li><i class="fa fa-bed"></i>400 sqft</li>
                </ul>
            </div>
        </div>
        <a class="khanh_hotel-01__viewdetail" href="<?php the_permalink() ?>" title="<?php the_title() ?>">
            <?php echo __('Vỉew detail', 'advanced_search_form_builder') ?><i class="fa fa-angle-double-right"></i>
        </a>
    </div>
</div>