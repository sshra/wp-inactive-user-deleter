
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
    <iframe src="https://money.yandex.ru/quickpay/shop-widget?writer=seller&targets=WP%20Inactive%20User%20Deleter%20plugin%20thanks&targets-hint=&default-sum=&button-text=14&payment-type-choice=on&comment=on&hint=&successURL=&lang=en&quickpay=shop&account=410011969010464" width="423" height="301" frameborder="0" allowtransparency="true" scrolling="no"></iframe>
  </p>