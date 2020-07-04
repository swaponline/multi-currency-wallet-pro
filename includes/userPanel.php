<?php

// Add the checkbox to user profile home
function mcwallet_show_extra_profile_fields($user)
{
    ?>
    <h3 style="font-size: 22px"><?php esc_html_e('Wallet info', 'multi-currency-wallet'); ?></h3>


    <?php
    $data = get_user_meta($user->ID, '_mcwallet_data');
    if (isset($data[0])) {
        // var_dump($data[2]); ?>
        <ul style="font-size: 18px"> <?php
            foreach ($data[0] as $k => $item) {

                if($k == 'WPuserUid') continue;
                ?>
                <li><b style="font-size: 18px; padding-bottom: 12px"><?php echo esc_html($k); ?></b>
                    <ul>
                        <?php
                        if(is_array($item)) {
                            foreach ($item as $j => $el) {
                                ?>

                                <li style="margin-top: 10px; padding-left: 22px"><b><?php echo esc_html($j); ?>:</b>
                                    <?php echo esc_html($el); ?> <br></li>
                                <?php
                            } }


                        ?>
                    </ul>
                    <hr>

                </li> <?php
            }
            ?>
        </ul> <?php
    }
}

add_action('show_user_profile', 'mcwallet_show_extra_profile_fields');
add_action('edit_user_profile', 'mcwallet_show_extra_profile_fields');
