
  <p><b><?php echo  count($user_list) ?> <?php
    echo __('record(s) are shown.');
    if ($total > count($user_list)) {
      echo ' ' . $total . ' ' . __('are found total.');
    }
?></b></p>
  <hr><?php echo  __('Check this list') ?>.
  <input type="button" value="<?php echo  __('Mark all') ?>" onclick="IUD_markALL(this.form['f_users[]']);" />
  <input type="button" value="<?php echo  __('Unmark all') ?>" onclick="IUD_unmarkALL(this.form['f_users[]']);" />
  <?php echo  __('When everything is ready') ?> -
  <input type="button" class="button-secondary-red" value="<?php echo  __('Delete all marked users') ?>" onclick="
      if (confirm('Yes, I really want to delete all marked users.')) {
        this.form.op.value='delete';
        this.form.submit();
      }
    "/>
  <input type="button" class="button-secondary-red" value="<?php echo __('Disable users') ?>" onclick="
      if (confirm('Yes, disable all marked users.')) {
        this.form.op.value='disable';
        this.form.submit();
      }
    "/>
  <input type="button" class="button-primary" value="<?php echo __('Enable users') ?>" onclick="
      if (confirm('Yes, activate all marked users.')) {
        this.form.op.value='activate';
        this.form.submit();
      }
    "/>
  <input type="button" class="button-secondary-red" value="<?php echo __('Draft posts') ?>" onclick="
      if (confirm('Yes, unpublush all their posts.')) {
        this.form.op.value='draft';
        this.form.submit();
      }
    "/>
  <input type="button" class="button-primary" value="<?php echo __('Publush posts') ?>" onclick="
      if (confirm('(Be carefull, it will publish all posts of users from the list!) I understand all risks, publush all their posts.')) {
        this.form.op.value='publish';
        this.form.submit();
      }
    "/>


  <table cellpadding="3"><tr>
    <th>No.</th>
    <th><?php echo  __('Mark') ?></th>
    <th class="clickable" width="150" align="left" onclick="jQuery('#sort_order').val('login'); jQuery('#inactive-user-deleter-form').submit(); "><?php echo  __('Login') ?></th>
    <th class="clickable" align="left" onclick="jQuery('#sort_order').val('mail'); jQuery('#inactive-user-deleter-form').submit(); "><?php echo  __('Email') ?></th>
    <th class="clickable" align="left" onclick="jQuery('#sort_order').val('disabled'); jQuery('#inactive-user-deleter-form').submit(); "><?php echo  __('Status') ?></th>
    <th class="clickable" align="left" onclick="jQuery('#sort_order').val('name'); jQuery('#inactive-user-deleter-form').submit(); "><?php echo  __('Name') ?></th>
    <th class="clickable" onclick="jQuery('#sort_order').val('userlevel'); jQuery('#inactive-user-deleter-form').submit(); "><?php echo  __('Role') ?></th>
    <th class="clickable" width="120" onclick="jQuery('#sort_order').val('regdate'); jQuery('#inactive-user-deleter-form').submit(); "><?php echo  __('Reg date') ?></th>
    <th class="clickable" width="120" onclick="jQuery('#sort_order').val('logindate'); jQuery('#inactive-user-deleter-form').submit(); "><?php echo  __('Last log-in') ?></th>
    <th><?php echo  __('Published posts') ?></th>
    <th class="clickable" onclick="jQuery('#sort_order').val('spam'); jQuery('#inactive-user-deleter-form').submit(); "><?php echo  __('Spam comments') ?></th>
    <th class="clickable" onclick="jQuery('#sort_order').val('comments'); jQuery('#inactive-user-deleter-form').submit(); "><?php echo  __('Approved comments') ?></th></tr>
<?php
      $i = 0;
      $stroked = 0;

      foreach($user_list as $UR) {
        $i++;
        $class = $i % 2 ? 'odd' : 'even';
        echo "<tr align=\"center\" class=\"$class\" ><td>$i.</td><td>";

        $login = (empty($UR['url']) ? $UR['login'] : "<a href=\"$UR[url]\" target=\"_blank\">$UR[login]</a>");
        if (!empty($UR['removetime'])) {
          $login = '<s>' . $login . '</s> *';
          //to remove checkbox
          $UR['ID'] = 1;
          $stroked ++;
        }

        $UR['USL'] = @unserialize($UR['USL']);
        $isAdministrator = (is_array($UR['USL']) && !empty($UR['USL']['administrator']));

        if ($isAdministrator || $UR['ID'] == 1) {
          echo "-";
        } else {
          echo "<input type=\"checkbox\" name=\"f_users[]\" value=\"$UR[ID]\"/ "
          . (isset($_POST['f_users']) && in_array($UR['ID'], $_POST['f_users']) ? 'checked' : '')
          . ">";
        }

        //last_login_classipress date preferable
        $last_login = $UR['last_login_classipress'] ? strtotime($UR['last_login_classipress']) : $UR['last_login'];

        $status = $UR['disabled_time'] ? __('blocked') . date(' [d M Y]', $UR['disabled_time']) : __('active');

        echo "</td>\n<td align=\"left\">{$login}</td>"
          . "<td align=\"left\">$UR[mail]</td>"
          . "<td align=\"left\">{$status}</td>"
          . "<td align=\"left\">$UR[name]</td>"
          . "</td><td>" . (is_array($UR['USL']) && !empty($UR['USL']) ? implode(', ', array_keys($UR['USL'])) : '-') . "</td><td>"
          . date('d M Y', strtotime($UR['dt_reg'])) . "</td>"
          . '<td>' . ($last_login ? date('d M Y', $last_login) : '?') . "</td>"
          . '<td>' . ($UR['recs'] ? $UR['recs'] : '-')
          . "</td><td>"
          . ($UR['spam'] ? $UR['spam'] : '-')
          . "</td><td>"
          . ($UR['approved'] ? $UR['approved'] : '-')
          . "</td></tr>\n";
            }
?>
  </table>
<?php
    if ($stroked) {
      echo '<p>* - striked through logins - user is informed (by email) about deletion and marked to delete soon.<p>';
    }
?>
  <script>
    function IUD_markALL(f_elm) {
      if (f_elm.length > 0) {
        for(i=0; i<f_elm.length; i++)
          f_elm[i].checked = true;
      } else f_elm.checked = true;
    }
    function IUD_unmarkALL(f_elm) {
      if (f_elm.length > 0) {
        for(i=0; i<f_elm.length; i++)
          f_elm[i].checked = false;
      } else f_elm.checked = false;
    }
  </script>

  <style>
    .clickable {
      cursor: pointer;
    }
    .odd {
      background-color: #FFFFEE;
    }
    .even {
      background-color: #EEFFFF;
    }
    .button-secondary-red {
      background: #ba0000;
      border-color: #690000 #690000 #690000;
      -webkit-box-shadow: 0 1px 0 #690000;
      box-shadow: 0 1px 0 #690000;
      color: #fff;
      text-decoration: none;
      text-shadow: 0 -1px 1px #690000, 1px 0 1px #690000, 0 1px 1px #690000, -1px 0 1px #690000;
      display: inline-block;
      text-decoration: none;
      font-size: 13px;
      line-height: 26px;
      height: 28px;
      margin: 0;
      padding: 0 10px 1px;
      cursor: pointer;
      border-width: 1px;
      border-style: solid;
      -webkit-appearance: none;
      -webkit-border-radius: 3px;
      border-radius: 3px;
      white-space: nowrap;
    }
    .button-secondary-red:hover {
      background: #ca0000;
      color: #fff;
    }
  </style>