<span style="background-color: red; padding: 5px; color: white;"><?php echo __('This is my last warning !') ?></span><br /><br />
  This is very serious, I will delete - <?php echo $userCount ?> user(s). Data will be erased permanently and cannot be restored automatically.<br />Do you will proceed ?
<input type="button" value="<?= __('Yes') ?>!" onclick="this.form.op.value='finally_delete'; this.form.submit();"/>&nbsp;
<input type="button" value="<?= __('No, don\'t do it, please !') ?>" onclick="this.form.submit();"/>