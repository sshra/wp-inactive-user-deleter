
  <form method="POST" action="">
  <input type="hidden" name="op" value="misc" />
  <input type="hidden" name="last-inform" value="<?php echo intval($OP['last-inform']);?>" />
  <?php wp_nonce_field('misc_settings'); ?>
  <p>
    There are miscellaneous options to drive plugin. No risks to delete users here.
  </p>
  <table cellspacing=5 border=0 cellpadding=0>
  <tr><th width=40></th><th align="left"></tr>
  <tr><td align="center">
      <input id="flag_inform_me" type="checkbox" name="informME" value="1" <?php echo empty($OP['informME']) ? '' : 'checked' ?> />
    </td>
    <td>
      <label for="flag_inform_me"><?php echo  __('Inform me about number of inactive users by email.') ?></label>
    </td>
  </tr>
  <tr style="display: <?php echo  empty($OP['informME']) ? 'none' : 'table-row' ?>">
    <td>&nbsp;</td>
    <td>
    <?php echo __('Send report every')?> <input id="informPeriod" size="2" maxlength="4" type="text" name="informPeriod" value="<?php echo intval($OP['informPeriod'])?>" /> <?php echo __('day(s)')?>
    <?php echo __('when inactive user number will be at least')?> <input id="informUsersNumber" size="2" maxlength="4" type="text" name="informUsersNumber" value="<?php echo intval($OP['informUsersNumber'])?>" />.<br/>
    <?php echo __('I will use this email'). ' : <b>'
    . get_option('admin_email', '<a href="' . admin_url('options-general.php') . '">SETUP email value here</a>')
    . '</b>.<br />';?>
    <?php echo __('Last report was sent')?> : <?php echo empty($OP['last-inform']) ? __('never') : date('d/M/Y H:i', $OP['last-inform']) . ' GMT' ?>.
    </td>
  </tr>
  <tr><td align="center">
      <input id="flag_inform_users" type="checkbox" name="informUsers" value="1" <?php echo empty($OP['informUsers']) ? '' : 'checked' ?> />
    </td>
    <td>
      <label for="flag_inform_users"><?php echo  __('Email before delete.') ?></label>
    </td>
  </tr>
  <tr style="display: <?php echo empty($OP['informUsers']) ? 'none' : 'table-row' ?>">
    <td>&nbsp;</td>
    <td><p>
    <?php echo __('I will send emails to users instead of instant account deleting. Mails will contain link to confirm user activity. ')
    . __('Users with non-confirmed accounts will be deleted after specified period of time.');
    ?></p>
    <p>
    <?=__('Confirmation period')?>:
    <input id="confirmPeriod" size="2" maxlength="4" type="text" name="confirmPeriod" value="<?php echo intval($OP['confirmPeriod'])?>" /> <?php echo __('day(s)')?>.
    </p>
    <p>
    <?=__('Letter of confirmation template')?>:<br />
    <textarea style="width: 80%" rows="10" name="confirmLetter"><?=htmlspecialchars($OP['confirmLetter'])?></textarea><br />
    <?=__('Template variables:')?>
    <pre>
:sitename - current domain sitename,
:confirmPeriod - confirmation period value,
:link - user-dependent link to prevent account deletion.</pre>
    </p>

    </td>
  </tr>
  <tr><td colspan="2"><input class="button-primary" name="sbm" type="submit" size="4" value="<?php echo __('Save')?>" /></td></tr>
  </table>

  </form>