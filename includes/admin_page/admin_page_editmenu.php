<div class="welcome-panel-column-container mcwallet-panel-tab mcwallet-form-options" id="mcwallet-tab-editmenu">
  <div class="mcwallet-shortcode-panel-row">
    <?php
      $own_before_menus = get_option( 'mcwallet_own_before_menus' , array() );
      $own_after_menus = get_option( 'mcwallet_own_after_menus', array() );

      function render_menu_rows($rows, $type) {
        if (count($rows)) {
          foreach ($rows as $k=>$own_menu) {
            ?>
        <tr class="mcwallet-own-menu-row">
          <td>
            <input type="text" data-mcwallet-target="mcwallet-menu-title" value="<?php esc_attr_e($own_menu['title'])?>" />
          </td>
          <td>
            <input type="text" data-mcwallet-target="mcwallet-menu-link" value="<?php esc_attr_e($own_menu['link']);?>" />
          </td>
          <td>
            <input type="checkbox" data-mcwallet-target="mcwallet-menu-newwindow" <?php echo (isset($own_menu['newwindow']) and $own_menu['newwindow']) ? 'checked' : ''?> />
          </td>
          <td>
            <a href="#" data-mcwallet-action="mcwallet_menu_move_up">[Up]</a>
            <a href="#" data-mcwallet-action="mcwallet_menu_move_down">[Down]</a>
            <a href="#" data-mcwallet-action="mcwallet_menu_remove">[Delete]</a>
          </td>
        </tr>
            <?php
          }
        }
        ?>
        <tr class="<?php echo (count($rows)) ? '-mc-hidden' : ''?>" data-mcwallet-role="empty-row">
          <td colspan="4" align="center"><?php esc_html_e('Empty') ?></td>
        </tr>
        <?php
      }
    ?>
    <h3><?php esc_html_e( 'Edit menu items', 'multi-currency-wallet' );?></h3>
    <style type="text/css">
      .mcwallet-menu-list STRONG {
        font-weight: bold;
      }
      .mcwallet-own-menu-row INPUT[type="text"] {
        width: 100%;
      }
      .mcwallet-own-menu-row INPUT[type="checkbox"] {
        display: block;
        margin: 0 auto;
      }
      .mcwallet-menu-list .-mc-hidden {
        display: none;
      }
    </style>
    <table class="mcwallet-menu-list wp-list-table widefat striped">
      <thead>
        <tr>
          <td width="25%"><strong><?php esc_html_e('Title', 'multi-currency-wallet'); ?></strong></td>
          <td><strong><?php esc_html_e('Link', 'multi-currency-wallet'); ?></strong></td>
          <td width="200px"><strong><?php esc_html_e('New window?', 'multi-currency-wallet'); ?></strong></td>
          <td width="250px"><strong><?php esc_html_e('Actions', 'multi-currency-wallet'); ?></strong></td>
        </tr>
      </thead>
      <tbody id="mcwallet-menu-before" data-mcwallet-role="menu-before-holder">
        <?php render_menu_rows($own_before_menus, 'before'); ?>
      </tbody>
      <tbody>
        <tr>
          <td colspan="4" align="center" style="background: #e9e9e9">
            <strong>
              <?php esc_html_e('Default menu items (&quot;Wallet&quot;, &quot;Transactions&quot;, &quot;Exchange&quot;)'); ?>
            </strong>
          </td>
        </tr>
      </tbody>
      <tbody id="mcwallet-menu-after" data-mcwallet-role="menu-after-holder">
        <?php render_menu_rows($own_after_menus, 'after'); ?>
      </tbody>
      <thead>
        <tr>
          <td colspan="4">
            <h3><?php esc_html_e( 'Add new menu item', 'multi-currency-wallet' );?></h3>
          </td>
        </tr>
      </thead>
      <tbody>
        <tr class="mcwallet-own-menu-row">
          <td>
            <input type="text" data-mcwallet-role="mcwallet-addmenu-title" value="" />
          </td>
          <td>
            <input type="text" data-mcwallet-role="mcwallet-addmenu-link" value="" />
          </td>
          <td>
            <input type="checkbox" data-mcwallet-role="mcwallet-addmenu-newwindow" />
          </td>
          <td>
            <a href="#" data-mcwallet-action="mcwallet_menu_add">[Add menu]</a>
          </td>
        </tr>
      </tbody>
      <tbody style="display: none" data-mcwallet-role="menu_template">
        <tr class="mcwallet-own-menu-row">
          <td>
            <input type="text" data-mcwallet-target="mcwallet-menu-title" value="" />
          </td>
          <td>
            <input type="text" data-mcwallet-target="mcwallet-menu-link" value="" />
          </td>
          <td>
            <input type="checkbox" data-mcwallet-target="mcwallet-menu-newwindow" />
          </td>
          <td>
            <a href="#" data-mcwallet-action="mcwallet_menu_move_up">[Up]</a>
            <a href="#" data-mcwallet-action="mcwallet_menu_move_down">[Down]</a>
            <a href="#" data-mcwallet-action="mcwallet_menu_remove">[Delete]</a>
          </td>
        </tr>
      </tbody>
    </table>
    
    <table class="form-table">
      <tr>
        <th scope="row"></th>
        <td>
          <input type="submit" name="mcwallet-update-menu" id="mcwallet-update-menu" class="button button-primary" value="Update menu items" >
          <span class="spinner"></span>
        </td>
      </tr>
    </table>
    <script type="text/javascript">
      (($) => {
        const $beforeHolder = $('#mcwallet-menu-before')
        const $afterHolder = $('#mcwallet-menu-after')

        $('#mcwallet-update-menu').on('click', function (e) {
          e.preventDefault()

          const $beforemenusRows = $beforeHolder.find('TR.mcwallet-own-menu-row')
          const $aftermenusRows = $afterHolder.find('TR.mcwallet-own-menu-row')
          
          const ajaxData = {
            action: 'mcwallet_update_menus',
            nonce: mcwallet.nonce,
            menusBefore: [],
            menusAfter: []
          }
          $beforemenusRows.each((i, rowholder) => {
            const title = $($(rowholder).find('INPUT[data-mcwallet-target="mcwallet-menu-title"]')[0]).val()
            const link = $($(rowholder).find('INPUT[data-mcwallet-target="mcwallet-menu-link"]')[0]).val()
            const newwindow = $(rowholder).find('INPUT[data-mcwallet-target="mcwallet-menu-newwindow"]')[0].checked
            ajaxData.menusBefore.push({
              title,
              link,
              newwindow
            })
          })
          $aftermenusRows.each((i, rowholder) => {
            const title = $($(rowholder).find('INPUT[data-mcwallet-target="mcwallet-menu-title"]')[0]).val()
            const link = $($(rowholder).find('INPUT[data-mcwallet-target="mcwallet-menu-link"]')[0]).val()
            const newwindow = $(rowholder).find('INPUT[data-mcwallet-target="mcwallet-menu-newwindow"]')[0].checked
            ajaxData.menusAfter.push({
              title,
              link,
              newwindow
            })
          })
          const thisBtn = $(this)
          mcwallet.showSpinner(thisBtn)
          $.post( mcwallet.ajaxurl, ajaxData, function(response) {
            if( response.status == 'success' ) {
              mcwallet.showNotice( mcwallet.notices.updated, 'success')
            }
            if ( response.status == 'false' ) {
              mcwallet.showNotice( mcwallet.notices.wrong, 'error')
            }
            mcwallet.showSpinner(thisBtn)
          });
        })
        $('A[data-mcwallet-action="mcwallet_menu_add"]').on('click', function (e) {
          e.preventDefault()
          const title = $('INPUT[data-mcwallet-role="mcwallet-addmenu-title"]').val()
          const link = $('INPUT[data-mcwallet-role="mcwallet-addmenu-link"]').val()
          const newwindow = $('INPUT[data-mcwallet-role="mcwallet-addmenu-newwindow"]')[0].checked
          const $newRow = $('TBODY[data-mcwallet-role="menu_template"]>TR').clone()
          $('INPUT[data-mcwallet-role="mcwallet-addmenu-title"]').val('')
          $('INPUT[data-mcwallet-role="mcwallet-addmenu-link"]').val('')
          $newRow.css({ opacity: 0 })
          $($newRow.find('INPUT[data-mcwallet-target="mcwallet-menu-title"]')[0]).val(title)
          $($newRow.find('INPUT[data-mcwallet-target="mcwallet-menu-link"]')[0]).val(link)
          $newRow.find('INPUT[data-mcwallet-target="mcwallet-menu-newwindow"]')[0].checked = newwindow
          $afterHolder.append($newRow)
          $afterHolder.find('TR[data-mcwallet-role="empty-row"]').addClass('-mc-hidden')
          $newRow.animate( { opacity: 1 }, 500)
        })
        $('TABLE.mcwallet-menu-list').on('click', 'A[data-mcwallet-action="mcwallet_menu_move_up"]', function (e) {
          e.preventDefault();
          const menuHolder = $($(e.target).parents('TR')[0])
          const topHolder = $($(e.target).parents('TBODY')[0])
          let menuPrev = $(menuHolder.prev('TR')[0])
          if (menuPrev.length && menuPrev.data('mcwallet-role') === 'empty-row') {
            menuPrev = $(menuPrev.prev('TR')[0])
          }
          if (menuPrev.length) {
            menuHolder
              .animate({
                opacity: 0
              },
              500,
              () => {
                menuHolder
                  .insertBefore(menuPrev)
                  .animate({ opacity: 1, duration: 500 })
              })
          } else {
            if (topHolder.data('mcwallet-role') === 'menu-after-holder') {
              menuHolder
                .animate({
                  opacity: 0
                },
                500,
                () => {
                  $beforeHolder.find('TR[data-mcwallet-role="empty-row"]').addClass('-mc-hidden')
                  $beforeHolder.append(menuHolder)
                  menuHolder.animate({ opacity: 1, duration: 500 })
                  const aftermenus = $afterHolder.find('TR.mcwallet-own-menu-row')
                  if (!aftermenus.length) {
                    $afterHolder.find('TR[data-mcwallet-role="empty-row"]').removeClass('-mc-hidden')
                  }
                })
            }
          }
        })
        $('TABLE.mcwallet-menu-list').on('click', 'A[data-mcwallet-action="mcwallet_menu_move_down"]', function (e) {
          e.preventDefault();
          const menuHolder = $($(e.target).parents('TR')[0])
          const topHolder = $($(e.target).parents('TBODY')[0])
          let menuNext = $(menuHolder.next('TR')[0])
          if (menuNext.length && menuNext.data('mcwallet-role') === 'empty-row') {
            menuNext = $(menuNext.next('TR')[0])
          }
          if (menuNext.length) {
            menuHolder
              .animate({
                opacity: 0
              },
              500,
              () => {
                menuHolder
                  .insertAfter(menuNext)
                  .animate({ opacity: 1, duration: 500 })
              })
          } else {
            if (topHolder.data('mcwallet-role') === 'menu-before-holder') {
              menuHolder
                .animate({
                  opacity: 0
                },
                500,
                () => {
                  $afterHolder.find('TR[data-mcwallet-role="empty-row"]').addClass('-mc-hidden')
                  $afterHolder.prepend(menuHolder)
                  menuHolder.animate({ opacity: 1, duration: 500 })
                  const beforemenus = $beforeHolder.find('TR.mcwallet-own-menu-row')
                  if (!beforemenus.length) {
                    $beforeHolder.find('TR[data-mcwallet-role="empty-row"]').removeClass('-mc-hidden')
                  }
                })
            }
          }
        })
        $('TABLE.mcwallet-menu-list').on('click', 'A[data-mcwallet-action="mcwallet_menu_remove"]', function (e) {
          e.preventDefault();
          if (confirm('Confirm delete')) {
            const menuHolder = $($(e.target).parents('TR')[0])
            const topHolder = $($(e.target).parents('TBODY')[0])
            menuHolder.animate({
              opacity: 0
            },
            500,
            () => {
              menuHolder.remove()
              if(!topHolder.find('TR.mcwallet-own-menu-row').length) {
                topHolder.find('TR[data-mcwallet-role="empty-row"]').removeClass('-mc-hidden')
              }
            })
          }
        })
      })(jQuery)
    </script>
  </div>
</div>