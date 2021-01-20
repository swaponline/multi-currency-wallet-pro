<?php

// Add the checkbox to user profile home
function mcwallet_show_extra_profile_fields( $user ) {
	?>
    <h3 style="font-size: 22px"><?php esc_html_e( 'Wallet info', 'multi-currency-wallet' ); ?></h3>
<b style="color:red">Don't send funds to these addresses! Ask address from the user directly, be sure he has saved his 12 words seed phrase!</b>
	<?php
	$data = get_user_meta( $user->ID, '_mcwallet_data' );


	if ( isset( $data[0] ) ) {
		// var_dump($data[2]); ?>
        <ul class="mw-user-ul"> <?php
			foreach ( $data[0] as $k => $item ) {

				if ( $k == 'WPuserUid' ) {
					continue;
				}
				?>
                <li><b class="mw-user-li-b"><?php echo esc_html( $k ); ?></b>
                    <ul>
						<?php
						if ( is_array( $item ) ) {
							foreach ( $item as $j => $el ) {
								?>

                                <li class="mw-user-li" style=""><b><?php echo esc_html( $j ); ?>:</b>
									<?php echo esc_html( $el ); ?> <br></li>
								<?php
							}
						}


						?>
                    </ul>
                    <hr>

                </li> <?php
			}
			?>
        </ul> <?php
	}
  
  
  // debug info
  $backup = get_user_meta( $user->ID, '_mcwallet_backup' );
  echo '<h3>Backup</h3>';
  echo '<pre>';
  print_r($backup);
  echo '</pre>';
  
}

add_action( 'show_user_profile', 'mcwallet_show_extra_profile_fields' );
add_action( 'edit_user_profile', 'mcwallet_show_extra_profile_fields' );
