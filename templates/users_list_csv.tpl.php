<?php

  $outputHandler = fopen('php://output', 'w');
  fputcsv($outputHandler,
    array(
      '#',
      __('Login'),
      __('Email'),
      __('Status'),
      __('Name'),
      __('Role'),
      __('Reg date'),
      __('Last log-in'),
      __('Published posts'),
      __('Spam comments'),
      __('Approved comments')
    )
  );

  $i = 0;

  foreach($user_list as $UR) {
    $i++;

    $login = $UR['login'];
    $UR['USL'] = @unserialize($UR['USL']);
    //last_login_classipress date preferable
    $last_login = $UR['last_login_classipress'] ? strtotime($UR['last_login_classipress']) : $UR['last_login'];

    $status = $UR['disabled_time'] || $UR['disabled']
    ? ( __('blocked') . ($UR['disabled_time'] ? date(' [d M Y]', $UR['disabled_time']) : '' ))
    : __('active');
    if (!empty($UR['removetime'])) {
      $status .= ', to be deleted';
    }

    $row = array(
      $i,
      $login,
      $UR['mail'],
      $status . ($UR['act_key_len'] ? ' [pending]' : ''),
      $UR['name'],
      is_array($UR['USL']) && !empty($UR['USL']) ? implode(', ', array_keys($UR['USL'])) : '-',
      date('Y-m-d', strtotime($UR['dt_reg'])),
      $last_login ? date('Y-m-d', $last_login) : '?',
      $UR['recs'] ? $UR['recs'] : '-',
      $UR['spam'] ? $UR['spam'] : '-',
      $UR['approved'] ? $UR['approved'] : '-'
    );

    fputcsv($outputHandler, $row);
  }

  fclose($outputHandler);
