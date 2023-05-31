
  <p>
    <b>Version: <?php echo $version ?><br />
    <?php echo __('Developer')?>: <a href="http://shra.ru" rel="nofollow" target="_blank" >Korol Yuriy aka SHRA</a><br />
    <?php echo __('Plugin page')?>: <a href="https://wordpress.org/plugins/inactive-user-deleter/" rel="nofollow" target="_blank" >wordpress.org/plugins/inactive-user-deleter</a><br />
  </p>
<?php
  if ($status != 'production') {
?>
  <p>
    <form method="post" >
    <?php wp_nonce_field('generate_dummies'); ?>
    <input type="hidden" name="op" value="generate" />
    Dummies Number: <input type="text" name="N" value="500" />  <input type="submit" name="op" value="generate" />
    </form>
  </p>
<?php
  }
?>
  <p>
    <img src="<?= $assetsDir . 'yurbanator.jpg' ?>" /><br />
    <?php echo __('Enjoy, this plugin is free to use.<br /> But you can support author for futher development (actually, for some beer and nuts).')?><br />
    <iframe src="https://yoomoney.ru/quickpay/fundraise/button?billNumber=NIG3iALDkAQ.230531&" width="330" height="50" frameborder="0" allowtransparency="true" scrolling="no"></iframe>
  </p>