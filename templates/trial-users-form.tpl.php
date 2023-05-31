  <form method="POST" action="" style="max-width: 800px">
  <input type="hidden" name="op" value="trial-user" />
  <?php wp_nonce_field('trial_user_list'); ?>
  <table cellspacing=5 border=0 cellpadding=0>
  <tr><th align="left"></tr>
  <tr>
    <td>
      <p><?php echo __('The trail users deleter tool allows you to configure and to delete users with a specific role after given period of time since registration date.'); ?></p>
      <p><?=__('Select a trial user role from the list')?>:
      <select name="trial-role">
        <option value="">-- <?=__('Not selected')?> --</option>
    <?php
      global $wp_roles;
      $roles = $wp_roles->get_names();

      // protect some important roles
      unset($roles['administrator']);
      unset($roles['editor']);

      foreach($roles as $roleId => $roleLabel) {
        print '<option value="' . $roleId . '" ' . ($OP['trial-role'] == $roleId ? 'selected' : '') . '>' . $roleLabel . '</option>';
      }
    ?>
      </select>.
      <br /><small>
      <?=__('Administrator and Editor roles have been removed from the list for security reasons.')?></small>
    </td>
  </tr>
  <tr>
    <td>
      <?= __('Delete users with given role after')?> <input size="2" maxlength="4" type="text" name="trial-period" value="<?php echo $OP['trial-period']?>" /> <?php echo __('day(s)')?>.<br />
      <?= __('Last check was performed:') ?>
      <?php
        $lastCheck = get_option('UsrInDeleter_lastTrialCheck', 0);
        echo ($lastCheck) ? date('d/M/Y H:i', $lastCheck) : __('never');
      ?>.
    </td>
  </tr>
  <tr><td colspan="2"><input class="button-primary" name="sbm" type="submit" size="4" value="<?php echo __('Save')?>" /></td></tr>
  </table>

  </form>