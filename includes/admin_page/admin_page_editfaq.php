<div class="welcome-panel-column-container mcwallet-panel-tab mcwallet-form-options" id="mcwallet-tab-editfaq">
  <div class="mcwallet-shortcode-panel-row">
    <?php
      $own_before_faqs = get_option( 'mcwallet_own_before_faqs' , array() );
      $own_after_faqs = get_option( 'mcwallet_own_after_faqs', array() );

      function render_faq_rows($rows, $type) {
        if (count($rows)) {
          foreach ($rows as $k=>$own_faq) {
            ?>
        <tr class="mcwallet-own-faq-row">
          <td>
            <input type="text" data-mcwallet-target="mcwallet-faq-title" value="<?php esc_attr_e($own_faq['title'])?>" />
          </td>
          <td>
            <textarea data-mcwallet-target="mcwallet-faq-content"><?php esc_attr_e($own_faq['content']);?></textarea>
          </td>
          <td>
            <a href="#" data-mcwallet-action="mcwallet_faq_move_up">[Up]</a>
            <a href="#" data-mcwallet-action="mcwallet_faq_move_down">[Down]</a>
            <a href="#" data-mcwallet-action="mcwallet_faq_remove">[Delete]</a>
          </td>
        </tr>
            <?php
          }
        }
        ?>
        <tr class="<?php echo (count($rows)) ? '-mc-hidden' : ''?>" data-mcwallet-role="empty-row">
          <td colspan="3" align="center"><?php esc_html_e('Empty') ?></td>
        </tr>
        <?php
      }
    ?>
    <h3><?php esc_html_e( 'Edit FAQ section', 'multi-currency-wallet' );?></h3>
    <style type="text/css">
      .mcwallet-faq-list STRONG {
        font-weight: bold;
      }
      .mcwallet-own-faq-row INPUT,
      .mcwallet-own-faq-row TEXTAREA {
        width: 100%;
      }
      .mcwallet-faq-list .-mc-hidden {
        display: none;
      }
    </style>
    <table class="mcwallet-faq-list wp-list-table widefat striped">
      <thead>
        <tr>
          <td width="25%"><strong><?php esc_html_e('Caption', 'multi-currency-wallet'); ?></strong></td>
          <td><strong><?php esc_html_e('Content', 'multi-currency-wallet'); ?></strong></td>
          <td width="250px"><strong><?php esc_html_e('Actions', 'multi-currency-wallet'); ?></strong></td>
        </tr>
      </thead>
      <tbody id="mcwallet-faq-before" data-mcwallet-role="faq-before-holder">
        <?php render_faq_rows($own_before_faqs, 'before'); ?>
      </tbody>
      <tbody>
        <tr>
          <td colspan="3" align="center" style="background: #e9e9e9">
            <strong>
              <?php esc_html_e('Default FAQ block (&quot;How are my private keys stored&quot;, &quot;Waht are the fees involved&quot;, &quot;Why minning fee is to high&quot;)'); ?>
            </strong>
          </td>
        </tr>
      </tbody>
      <tbody id="mcwallet-faq-after" data-mcwallet-role="faq-after-holder">
        <?php render_faq_rows($own_after_faqs, 'after'); ?>
      </tbody>
      <thead>
        <tr>
          <td colspan="3">
            <h3><?php esc_html_e( 'Add new FAQ section', 'multi-currency-wallet' );?></h3>
          </td>
        </tr>
      </thead>
      <tbody>
        <tr class="mcwallet-own-faq-row">
          <td>
            <input type="text" data-mcwallet-role="mcwallet-addfaq-title" value="" />
          </td>
          <td>
            <textarea data-mcwallet-role="mcwallet-addfaq-content"></textarea>
          </td>
          <td>
            <a href="#" data-mcwallet-action="mcwallet_faq_add">[Add FAQ]</a>
          </td>
        </tr>
      </tbody>
      <tbody style="display: none" data-mcwallet-role="faq_template">
        <tr class="mcwallet-own-faq-row">
          <td>
            <input type="text" data-mcwallet-target="mcwallet-faq-title" value="" />
          </td>
          <td>
            <textarea data-mcwallet-target="mcwallet-faq-content"></textarea>
          </td>
          <td>
            <a href="#" data-mcwallet-action="mcwallet_faq_move_up">[Up]</a>
            <a href="#" data-mcwallet-action="mcwallet_faq_move_down">[Down]</a>
            <a href="#" data-mcwallet-action="mcwallet_faq_remove">[Delete]</a>
          </td>
        </tr>
      </tbody>
    </table>
    
    <table class="form-table">
      <tr>
        <th scope="row"></th>
        <td>
          <input type="submit" name="mcwallet-update-faq" id="mcwallet-update-faq" class="button button-primary" value="Update FAQ" >
          <span class="spinner"></span>
        </td>
      </tr>
    </table>
    <script type="text/javascript">
      (($) => {
        const $beforeHolder = $('#mcwallet-faq-before')
        const $afterHolder = $('#mcwallet-faq-after')

        $('#mcwallet-update-faq').on('click', function (e) {
          e.preventDefault()

          const $beforeFaqsRows = $beforeHolder.find('TR.mcwallet-own-faq-row')
          const $afterFaqsRows = $afterHolder.find('TR.mcwallet-own-faq-row')
          
          const ajaxData = {
            action: 'mcwallet_update_faqs',
            nonce: mcwallet.nonce,
            faqsBefore: [],
            faqsAfter: []
          }
          $beforeFaqsRows.each((i, rowholder) => {
            const title = $($(rowholder).find('INPUT[data-mcwallet-target="mcwallet-faq-title"]')[0]).val()
            const content = $($(rowholder).find('TEXTAREA[data-mcwallet-target="mcwallet-faq-content"]')[0]).val()
            ajaxData.faqsBefore.push({
              title,
              content
            })
          })
          $afterFaqsRows.each((i, rowholder) => {
            const title = $($(rowholder).find('INPUT[data-mcwallet-target="mcwallet-faq-title"]')[0]).val()
            const content = $($(rowholder).find('TEXTAREA[data-mcwallet-target="mcwallet-faq-content"]')[0]).val()
            ajaxData.faqsAfter.push({
              title,
              content
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
        $('A[data-mcwallet-action="mcwallet_faq_add"]').on('click', function (e) {
          e.preventDefault()
          const title = $('INPUT[data-mcwallet-role="mcwallet-addfaq-title"]').val()
          const content = $('TEXTAREA[data-mcwallet-role="mcwallet-addfaq-content"]').val()
          const $newRow = $('TBODY[data-mcwallet-role="faq_template"]>TR').clone()
          $('INPUT[data-mcwallet-role="mcwallet-addfaq-title"]').val('')
          $('TEXTAREA[data-mcwallet-role="mcwallet-addfaq-content"]').val('')
          $newRow.css({ opacity: 0 })
          $($newRow.find('INPUT[data-mcwallet-target="mcwallet-faq-title"]')[0]).val(title)
          $($newRow.find('TEXTAREA[data-mcwallet-target="mcwallet-faq-content"]')[0]).val(content)
          $afterHolder.append($newRow)
          $afterHolder.find('TR[data-mcwallet-role="empty-row"]').addClass('-mc-hidden')
          $newRow.animate( { opacity: 1 }, 500)
        })
        $('TABLE.mcwallet-faq-list').on('click', 'A[data-mcwallet-action="mcwallet_faq_move_up"]', function (e) {
          e.preventDefault();
          const faqHolder = $($(e.target).parents('TR')[0])
          const topHolder = $($(e.target).parents('TBODY')[0])
          let faqPrev = $(faqHolder.prev('TR')[0])
          if (faqPrev.length && faqPrev.data('mcwallet-role') === 'empty-row') {
            faqPrev = $(faqPrev.prev('TR')[0])
          }
          if (faqPrev.length) {
            faqHolder
              .animate({
                opacity: 0
              },
              500,
              () => {
                faqHolder
                  .insertBefore(faqPrev)
                  .animate({ opacity: 1, duration: 500 })
              })
          } else {
            if (topHolder.data('mcwallet-role') === 'faq-after-holder') {
              faqHolder
                .animate({
                  opacity: 0
                },
                500,
                () => {
                  $beforeHolder.find('TR[data-mcwallet-role="empty-row"]').addClass('-mc-hidden')
                  $beforeHolder.append(faqHolder)
                  faqHolder.animate({ opacity: 1, duration: 500 })
                  const afterFaqs = $afterHolder.find('TR.mcwallet-own-faq-row')
                  if (!afterFaqs.length) {
                    $afterHolder.find('TR[data-mcwallet-role="empty-row"]').removeClass('-mc-hidden')
                  }
                })
            }
          }
        })
        $('TABLE.mcwallet-faq-list').on('click', 'A[data-mcwallet-action="mcwallet_faq_move_down"]', function (e) {
          e.preventDefault();
          const faqHolder = $($(e.target).parents('TR')[0])
          const topHolder = $($(e.target).parents('TBODY')[0])
          let faqNext = $(faqHolder.next('TR')[0])
          if (faqNext.length && faqNext.data('mcwallet-role') === 'empty-row') {
            faqNext = $(faqNext.next('TR')[0])
          }
          if (faqNext.length) {
            faqHolder
              .animate({
                opacity: 0
              },
              500,
              () => {
                faqHolder
                  .insertAfter(faqNext)
                  .animate({ opacity: 1, duration: 500 })
              })
          } else {
            if (topHolder.data('mcwallet-role') === 'faq-before-holder') {
              faqHolder
                .animate({
                  opacity: 0
                },
                500,
                () => {
                  $afterHolder.find('TR[data-mcwallet-role="empty-row"]').addClass('-mc-hidden')
                  $afterHolder.prepend(faqHolder)
                  faqHolder.animate({ opacity: 1, duration: 500 })
                  const beforeFaqs = $beforeHolder.find('TR.mcwallet-own-faq-row')
                  if (!beforeFaqs.length) {
                    $beforeHolder.find('TR[data-mcwallet-role="empty-row"]').removeClass('-mc-hidden')
                  }
                })
            }
          }
        })
        $('TABLE.mcwallet-faq-list').on('click', 'A[data-mcwallet-action="mcwallet_faq_remove"]', function (e) {
          e.preventDefault();
          if (confirm('Confirm delete')) {
            const faqHolder = $($(e.target).parents('TR')[0])
            const topHolder = $($(e.target).parents('TBODY')[0])
            faqHolder.animate({
              opacity: 0
            },
            500,
            () => {
              faqHolder.remove()
              if(!topHolder.find('TR.mcwallet-own-faq-row').length) {
                topHolder.find('TR[data-mcwallet-role="empty-row"]').removeClass('-mc-hidden')
              }
            })
          }
        })
      })(jQuery)
    </script>
  </div>
</div>