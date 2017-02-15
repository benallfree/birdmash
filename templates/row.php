<?php
    #d($row);
    $user_url = esc_url( 'https://twitter.com/' . $row->user->screen_name );
?>

<li>
    <figure class="bm-image">
        <a href="<?php echo $user_url; ?>" target="_blank">
            <img src="<?php echo esc_url( $row->user->profile_image_url_https ); ?>" alt="">
        </a>
        <div class="bm-user-data">
            <h4><a href="<?php echo $user_url; ?>" target="_blank"><?php echo esc_html( $row->user->name ); ?></a></h4>
            <span>@<?php echo esc_html( $row->user->screen_name ); ?></span>
            <time class="bm-date"><?php echo date( 'd M', strtotime( $row->created_at ) ); ?></time>
        </div>
    </figure>
    <div class="bm-content">
        <p><?php echo esc_html( $row->text ); ?></p>
    </div>
</li>
