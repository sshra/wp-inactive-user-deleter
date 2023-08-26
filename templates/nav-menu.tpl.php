
<nav id="IUD_pager">
  <div data-page="IUD_page_1"><?php echo  __('Find & Wipe')?></div>
  <div data-page="IUD_page_4" id="trial-button"><?php echo  __('Trial users')?></div>
  <div data-page="IUD_page_2" id="misc-button"><?php echo  __('Misc')?></div>
  <div data-page="IUD_page_3"><?php echo  __('About')?></div>
</nav>

<script>
(function ($) {
  $('nav#IUD_pager div').click(function (){
    $('nav#IUD_pager div').removeClass('active');
    $(this).addClass('active');
    var pageID = $(this).attr('data-page');
    $('.sub-page').hide();
    $('#' + pageID).show();
  });

  <?php
    $option = 'div:first-child';
    if (!empty($_POST['op'])) {
      if ($_POST['op'] == 'misc') {
        $option = '#misc-button';
      }
      if ($_POST['op'] == 'trial-user') {
        $option = '#trial-button';
      }
    }
  ?>

  // document ready handler
  $( function () {
    $('nav#IUD_pager <?=$option?>').trigger('click')
  });

})(jQuery)

</script>