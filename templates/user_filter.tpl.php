
<input type="hidden" name="op" value="search_users" />
<table >
<tr><td colspan="2">
  <div class="section-title"><?=__('Flags')?></div>
  <hr width=50% align="left"/>
  <?=_('Flags behavior. Add to list if ')?>
  <select name="flagsCND">
    <option value="intersept" <?php echo !empty($_POST['flagsCND']) && $_POST['flagsCND'] == 'intersept' ? 'selected' : '' ?> ><?=__('... all conditions are true : AND CASE')?></option>
    <option value="add" <?php echo !empty($_POST['flagsCND']) && $_POST['flagsCND'] == 'add' ? 'selected' : '' ?> ><?=__('... any conditions are true : OR CASE')?></option>
  </select>
  </td>
</tr>
<tr><td colspan="2">
  <?=_('User has ...')?>
  </td>
</tr>
<tr>
  <td align="center" width=250>
    <label for="flag_approve_yes">
      <input id="flag_approve_yes" type="radio" name="f_approve" value="yes" <?php echo $_POST['f_approve'] == 'yes' ? 'checked' : '' ?> />
      <?php echo __('Yes')?></label>
    <label for="flag_approve_no">
      <input id="flag_approve_no" type="radio" name="f_approve" value="no" <?php echo $_POST['f_approve'] === 'no' ? 'checked' : '' ?> />
      <?php echo __('No')?></label>
    <label for="flag_approve_nomatter">
      <input id="flag_approve_nomatter" type="radio" name="f_approve" value="0" <?php echo empty($_POST['f_approve']) ? 'checked' : '' ?> />
      <?php echo __('Ignore')?></label>
  </td>
  <td>
    <?php echo __('... approved comments.')?>
  </td>
</tr>
<tr>
<?php
  if (!isset($_POST['has_spam'])) $_POST['has_spam'] = 'yes';
?>

  <td align="center">
    <label for="flag_has_spam_yes">
      <input id="flag_has_spam_yes" type="radio" name="has_spam" value="yes" <?php echo $_POST['has_spam'] === 'yes' ? 'checked' : '' ?> />
    <?php echo __('Yes')?></label>
    <label for="flag_has_spam_no">
      <input id="flag_has_spam_no" type="radio" name="has_spam" value="no" <?php echo $_POST['has_spam'] === 'no' ? 'checked' : '' ?> />
    <?php echo __('No')?></label>
    <label for="flag_has_spam_nomatter">
      <input id="flag_has_spam_nomatter" type="radio" name="has_spam" value="0" <?php echo empty($_POST['has_spam']) ? 'checked' : '' ?> />
    <?php echo __('Ignore')?></label>
  </td>
  <td>
    <?php echo __('... spam comments.')?>
  </td>
</tr>
<tr>
  <td align="center" valign="top">
    <label for="flag_has_name_yes">
      <input id="flag_has_name_yes" type="radio" name="has_name" value="yes" <?php echo $_POST['has_name'] === 'yes' ? 'checked' : '' ?> />
    <?php echo __('Yes')?></label>
    <label for="flag_has_name_no">
      <input id="flag_has_name_no" type="radio" name="has_name" value="no" <?php echo $_POST['has_name'] === 'no' ? 'checked' : '' ?> />
    <?php echo __('No')?></label>
    <label for="flag_has_name_nomatter">
      <input id="flag_has_name_nomatter" type="radio" name="has_name" value="0" <?php echo empty($_POST['has_name']) ? 'checked' : '' ?> />
    <?php echo __('Ignore')?></label>
  </td>
  <td>
    <?php echo __('... name.')?><br />
    <small><?php echo __('First or last name were provided by user.'); ?></small>
  </td>
</tr>
<tr>
  <td align="center" valign="top">
    <label for="flag_has_recs_yes">
      <input id="flag_has_recs_yes" type="radio" name="has_recs" value="yes" <?php echo $_POST['has_recs'] === 'yes' ? 'checked' : '' ?> />
    <?php echo __('Yes')?></label>
    <label for="flag_has_recs_no">
      <input id="flag_has_recs_no" type="radio" name="has_recs" value="no" <?php echo $_POST['has_recs'] === 'no' ? 'checked' : '' ?> />
    <?php echo __('No')?></label>
    <label for="flag_has_recs_nomatter">
      <input id="flag_has_recs_nomatter" type="radio" name="has_recs" value="0" <?php echo empty($_POST['has_recs']) ? 'checked' : '' ?> />
    <?php echo __('Ignore')?></label>
  </td>
  <td>
    <?php echo __('... (!Achtung) records or posts.')?><br />
    <small><?php echo __('I don`t care about attachments or revisions, and take in account only published articles.'); ?></small>
  </td>
</tr>

<?php if ($woocommerce_active): ?>
<tr>
  <td align="center" valign="top">
    <label for="flag_has_orders_yes">
      <input id="flag_has_orders_yes" type="radio" name="has_orders" value="yes" <?php echo $_POST['has_orders'] === 'yes' ? 'checked' : '' ?> />
    <?php echo __('Yes')?></label>
    <label for="flag_has_orders_no">
      <input id="flag_has_orders_no" type="radio" name="has_orders" value="no" <?php echo $_POST['has_orders'] === 'no' ? 'checked' : '' ?> />
    <?php echo __('No')?></label>
    <label for="flag_has_orders_nomatter">
      <input id="flag_has_orders_nomatter" type="radio" name="has_orders" value="0" <?php echo empty($_POST['has_orders']) ? 'checked' : '' ?> />
    <?php echo __('Ignore')?></label>
  </td>
  <td>
    <?php echo __('... <a href="https://wordpress.org/plugins/woocommerce/" target="_blank">woocommerce</a> anonymous orders.')?><br />
    <small><?php echo __('Sometimes users are too lazy to log-in to make order. But they provide a correct contact email in the order\'s details. We can conntect the profile and an anonymous orders by the email.'); ?></small>
  </td>
</tr>
<?php endif; ?>

<tr valign="top">
  <td align="center">
    <label for="flag_userlog_in_yes">
      <input id="flag_userlog_in_yes" type="radio" name="f_usereverlogin" value="yes" <?php echo $_POST['f_usereverlogin'] === 'yes' ? 'checked' : '' ?> />
      <?php echo __('Yes')?></label>
    <label for="flag_userlog_in_no">
      <input id="flag_userlog_in_no" type="radio" name="f_usereverlogin" value="no" <?php echo $_POST['f_usereverlogin'] === 'no' ? 'checked' : '' ?> />
      <?php echo __('No')?></label>
    <label for="flag_userlog_in_nomatter">
      <input id="flag_userlog_in_nomatter" type="radio" name="f_usereverlogin" value="0" <?php echo empty($_POST['f_usereverlogin']) ? 'checked' : '' ?> />
      <?php echo __('Ignore')?></label>
  </td>
  <td>
    <?php echo __('... known date log-in.')?> <br />
    <small><?php echo __('This flag adds users with known log-in date to list. Log-in date isn`t a native WP variable. So it will be collected while this module is activated or can be used data provided by one of the next plugins:
      <a href="https://wordpress.org/plugins/wp-last-login/" target="_blank">WP Last Login</a>,
      <a href="https://wordpress.org/plugins/user-login-history/" target="_blank">User Login History</a>,
      <a href="https://wordpress.org/plugins/when-last-login/" target="_blank">When Last Login</a>,
      or Classipress usermeta data.')?></small>
  </td>
</tr>

<tr valign="top">
  <td align="center">
    <label for="flag_userdisabled_yes">
      <input id="flag_userdisabled_yes" type="radio" name="f_userdisabled" value="yes" <?php echo $_POST['f_userdisabled'] === 'yes' ? 'checked' : '' ?> />
      <?php echo __('Yes')?></label>
    <label for="flag_userdisabled_no">
      <input id="flag_userdisabled_no" type="radio" name="f_userdisabled" value="no" <?php echo $_POST['f_userdisabled'] === 'no' ? 'checked' : '' ?> />
      <?php echo __('No')?></label>
    <label for="flag_userdisabled_nomatter">
      <input id="flag_userdisabled_nomatter" type="radio" name="f_userdisabled" value="0" <?php echo empty($_POST['f_userdisabled']) ? 'checked' : '' ?> />
      <?php echo __('Ignore')?></label>
  </td>
  <td>
    <?php echo __('... been disabled.')?> <br />
    <small><?php echo __('This is not a native WP feature. Plugin can disable user account instead of deletion. It is also compatible with plugin <a href="https://wordpress.org/plugins/disable-user-login/" target="_blank">Disable User Login</a>. Disabling feature works until you enable user or turn the plugin off.')?></small>
  </td>
</tr>

<tr>
  <td align="center">
    <label>
      <input type="radio" name="is_pending" value="yes" <?php echo $_POST['is_pending'] === 'yes' ? 'checked' : '' ?> />
    <?php echo __('Yes')?></label>
    <label>
      <input type="radio" name="is_pending" value="no" <?php echo $_POST['is_pending'] === 'no' ? 'checked' : '' ?> />
    <?php echo __('No')?></label>
    <label>
      <input type="radio" name="is_pending" value="0" <?php echo empty($_POST['is_pending']) ? 'checked' : '' ?> />
    <?php echo __('Ignore')?></label>
  </td>
  <td>
    <?php echo __('... pending status. It means that user created an account and special email has been sent to reset password, but user still hasn\'t reset password.')?>
  </td>
</tr>

<tr valign="top">
  <td colspan="2">
    <div class="section-title"><?=__('Filters')?></div>
    <hr width=50% align="left"/>
    <label for="usernameFilter"><?php echo __('User name') ?></label>
    <input type="text" size="15" name="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>" id="usernameFilter" />
    <br />
    <small><?php echo __('To refine user\'s list by user login.')?></small>

    <hr width=50% align="left"/>
    <label for="flag_daysleft"><?php echo __('User was created') ?>
    <select name="f_daysleft">
<?php
  if (!isset($_POST['f_daysleft'])) $_POST['f_daysleft'] = 1;
?>
      <option value="1" <?=!empty($_POST['f_daysleft'])  ? 'selected' : ''?>><?=__('more')?></option>
      <option value="0" <?=empty($_POST['f_daysleft']) ? 'selected' : ''?>><?=__('less')?></option>
    </select> <?=__('then')?>
    <input type="text" size="4" name="daysleft" value="<?php echo isset($_POST['daysleft']) ? intval($_POST['daysleft']) : 7 ?>" />
    <?php echo __('days ago. Set number of days.')?></label><br />
    <small><?php echo __('User need a time to begin commenting, subscribing, posting... Check to show recently registered user.')?></small>
  </td>
</tr>

<tr valign="top">
  <td colspan="2">
    <hr width=50% align="left"/>
    <label for="f_lastlogin"><?php echo __('Last time when user was logged-in is more then') ?>
    <select name="f_lastlogin">
      <option value="0">-- no filter --</option>
<?php
  $columns = array(15, 30, 60, 90, 180, 360, 720);

  foreach($columns as $v) {
    print '<option value="' . $v . '" ' . ($_POST['f_lastlogin'] == $v ? 'selected' : '') . '>' . $v . '</option>';
  }
?>
    </select> <?php echo __('days ago.') ?></label><br />
    <small><?php echo __('User was active, but long time ago. Probably it\'s time to remove him from database?')?></small>
  </td>
</tr>

<?php
  if ($ss2_active):
?>
<tr><td colspan="2">
  <hr width=50% align="left"/>
  </td>
</tr>
<tr valign="top">
  <td align="center">
    <label for="flag_s2_scriber_no">
      <input id="flag_s2_scriber_no" type="radio" name="has_SS2" value="0" <?php echo empty($_POST['has_SS2']) ? 'checked' : '' ?> /> No</label>
    <label for="flag_s2_scriber_yes">
      <input id="flag_s2_scriber_yes" type="radio" name="has_SS2" value="1" <?php echo empty($_POST['has_SS2']) ? '' : 'checked' ?> /> Yes</label>
  </td>
  <td>
    <?php echo __('User is SS2 subscriber.')?><br />
    <small><?=('Detect those, who has active subscriptions made by <a href="https://wordpress.org/plugins/subscribe2/" target="_blank">Subscribe2</a> plugin.');?></small>
  </td>
</tr>
<?php endif; ?>

<tr><td colspan="2">
  <hr width=50% align="left"/>
  <label for="user_role"><?php echo __('User role ')?></label>
  <select name="user_role">
<?php
  global $wp_roles;
  $roles = array('' => '-- Any --') + $wp_roles->get_names();
  foreach($roles as $roleId => $roleName) {
    print '<option value="' . $roleId . '" ' . ($_POST['user_role'] == $roleId ? 'selected' : '') . '>' . $roleName . '</option>';
  }
?>
  </select>
  <br /><small><?php echo  __('Filter by user role.')?></small></td>
</tr>

<tr>
  <td align="left" colspan="2">
    <div class="section-title"><?=__('Formatting')?></div>
    <hr width=50% align="left"/>
    <label for="sort_order"><?php echo  __('Max size output')?></label>
    <select id="max_size_output" name="max_size_output" />
<?php
  $columns = array('150', '300', '500', '1000', '5000', 'all');

  foreach($columns as $v) {
    print '<option value="' . $v . '" ' . ($_POST['max_size_output'] == $v ? 'selected' : '') . '>' . $v . '</option>';
  }
?>
    </select> <?php echo __('recs.')?><br />
  </td>
</tr>
<tr>
  <td align="left" colspan="2">
    <label for="sort_order"><?php echo  __('Sort by column')?></label>
    <select id="sort_order" name="sort_order" />
<?php
  $columns = array(
    'login' => 'user login',
    'mail' => 'user email',
    'disabled' => 'status',
    'logindate' => 'last log-in',
    'regdate' => 'register date',
    'name' => 'name',
    'userlevel' => 'role',
    'spam' => 'spam',
    'comments' => 'comments');
  foreach($columns as $k => $v) {
    print '<option value="' . $k . '" ' . ($_POST['sort_order'] == $k ? 'selected' : '') . '>' . $v . '</option>';
  }
?>
    </select>
  </td>
</tr>
<tr><td colspan="2">
  <input class="button-primary" type="submit" value="<?php echo __('Search')?>" />
  <button class="button-primary" onclick="
    window.open('<?php echo admin_url("admin-ajax.php")?>'
      + '?action=iud_getCsvUserList&'
      + jQuery('#inactive-user-deleter-form').serialize());
    return false;"><?php echo __('Export to CSV')?></button>
</td></tr>
</table>
<textarea type="hidden" name="f_users" style="display: none"></textarea>
<a name="outputs"></a>

