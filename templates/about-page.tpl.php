
  <p>
    <strong>Version: <?php echo $version ?><br />
    <?php echo __('Developer')?>: <a href="https://shra.ru" rel="nofollow" target="_blank" >Korol Yuriy aka SHRA</a><br />
    <?php echo __('Plugin page')?>: <a href="https://wordpress.org/plugins/inactive-user-deleter/" rel="nofollow" target="_blank" >wordpress.org/plugins/inactive-user-deleter</a><br />
    </strong>
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
    <img src="<?php echo $assetsDir . 'yurbanator.jpg' ?>" width="150"/>
    <p>
    <?php
      echo __('Enjoy, this plugin is always free for any your purpose.');
      echo '<br />';
      echo __('But you may say `thank you, author` through donations for futher development (honestly, for some beer and nuts).');
      echo '<br />';
      echo __('Support forum page to resolve an issues:')
        .  ' <a href="https://wordpress.org/support/plugin/inactive-user-deleter/" target="_blank">' . __('Get Support') . '</a>.'
    ?>
    </p>
    <p><a target="_blank" title="Thank you, Shra" href="https://pay.cryptocloud.plus/pos/Oc9ieI6Eb5HWPptn">Donations via crypticloud</a></p>
    <h3>Bitcoin Wallet Address (for donations)</h3>
    <pre>
bc1q75h2apyfk9vwr30849pdr33cq8pje04ypkcse5
    </pre>
    <p>
      <img src="<?php echo $assetsDir . 'btc-wallet.jpg' ?>" />
    </p>
  </p>