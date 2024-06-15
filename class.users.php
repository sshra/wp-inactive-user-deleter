<?php

namespace inactive_user_deleter;

class users {

  private static $disabled_user_login_key = '_is_disabled';

  static function isAdministrator($userID) {
    global $wpdb;
    $cap = get_user_meta( $userID, $wpdb->get_blog_prefix() . 'capabilities', true );
    return (is_array( $cap ) && !empty( $cap['administrator'] ));
  }

  static function isVIPUser($userID) {
    global $user_ID;
    if ($userID == $user_ID) {
      //i never will delete current-user
      return __('I can\'t to delete your profile !');
    }

    if ($userID == 1) {
      return __('I will never delete the super-user !');
    }

    if (self::isAdministrator($userID)) {
      return __('I will never delete an user with admin privileges !');
    }

    return false;
  }

  static function publishPostsOfGivenUsers($users) {
    global $wpdb;

    // filter users
    $verifiedUserList = array();
    if (is_array($users)) {
      foreach ($users as $userID) {
        if (self::isVIPUser($userID) === false) {
          $verifiedUserList[] = $userID + 0;
        }
      }
    }

    if (!empty($verifiedUserList)) {
      // get post list
      $query = "
        SELECT WP.ID
        FROM $wpdb->posts WP
        INNER JOIN $wpdb->users WU ON WP.post_author = WU.ID
        WHERE NOT WP.post_type in ('attachment', 'revision') AND post_status = 'draft'
          AND WU.ID IN (" . implode(',', $verifiedUserList) . ")";
      $posts = $wpdb->get_results($query);
      $count = count($posts);

      // update posts status
      foreach ($posts as $post) {
        $my_post = array(
          'ID'           => $post->ID,
          'post_status'  => 'publish',
        );
        wp_update_post( $my_post );
      }
    } else {
      $count = 0;
    }

    return $count;
  }

  static function unpublishPostsOfGivenUsers($users) {
    global $wpdb;

    // filter users
    $verifiedUserList = array();
    if (is_array($users)) {
      foreach ($users as $userID) {
        if (self::isVIPUser($userID) === false) {
          $verifiedUserList[] = $userID + 0;
        }
      }
    }

    if (!empty($verifiedUserList)) {
      // get post list
      $query = "
        SELECT WP.ID
        FROM $wpdb->posts WP
        INNER JOIN $wpdb->users WU ON WP.post_author = WU.ID
        WHERE NOT WP.post_type in ('attachment', 'revision') AND post_status = 'publish'
          AND WU.ID IN (" . implode(',', $verifiedUserList) . ")";
      $posts = $wpdb->get_results($query);
      $count = count($posts);

      // update posts status
      foreach ($posts as $post) {
        $my_post = array(
          'ID'           => $post->ID,
          'post_status'  => 'draft',
        );
        wp_update_post( $my_post );
      }
    } else {
      $count = 0;
    }

    return $count;
  }

  // activate / enable user
  static function enable($user_id) {
    $tm = get_user_meta($user_id, '_IUD_userBlockedTime', true);
    $result = false;
    if ($tm) {
      delete_user_meta($user_id, '_IUD_userBlockedTime');
      $result = true;
    }
    if (is_plugin_active('disable-user-login/disable-user-login.php')) {
      $disabled = get_user_meta( $user_id, self::$disabled_user_login_key, true );
      if ( $disabled == '1' ) {
        update_user_meta( $user_id, self::$disabled_user_login_key, 0);
        $result = true;
      }
    }
    if ($result) {
      do_action( 'disable_user_login.user_enabled', $user_id );
    }
    return $result;
  }

  // disable user
  static function disable($user_id) {
    $tm = get_user_meta($user_id, '_IUD_userBlockedTime', true);
    $result = false;
    if (!$tm) {
      update_user_meta($user_id, '_IUD_userBlockedTime', time());
      $result = true;
    }
    if (is_plugin_active('disable-user-login/disable-user-login.php')) {
      $disabled = get_user_meta( $user_id, self::$disabled_user_login_key, true );
      if ( $disabled !== '1' ) {
        update_user_meta( $user_id, self::$disabled_user_login_key, 1);
        $result = true;
      }
    }
    if ($result) {
      do_action( 'disable_user_login.user_disabled', $user_id );
    }
    return $result;
  }

  //get inactive user by number posts - no comments, no posts
  static function countInactiveUsers() {
    global $wpdb;

    $query = "SELECT COUNT(WC.comment_ID) as approved, WU.ID
      FROM {$wpdb->prefix}users WU
      LEFT JOIN {$wpdb->prefix}comments WC ON WC.user_id = WU.ID AND WC.comment_approved = 1
      WHERE
        (SELECT COUNT(*) FROM {$wpdb->prefix}posts PST
         WHERE PST.post_author = WU.ID AND PST.post_status = 'publish'
         AND NOT PST.post_type IN ('attachment', 'revision')) = 0
      GROUP BY WU.ID
      HAVING COUNT(WC.comment_ID) = 0 ";

    return count($wpdb->get_results($query));
  }

  static function getUsersList($ARGS = array(), $environment) {
      global $wpdb;

      $conditions = array();
      $conditions_sec2 = array(1);

      $joins = array(
        "FROM {$wpdb->prefix}users WU",
        "LEFT JOIN {$wpdb->prefix}comments WC ON WC.user_id = WU.ID",
        "LEFT JOIN {$wpdb->prefix}usermeta WUCAP ON WUCAP.user_id = WU.ID AND WUCAP.meta_key = 'wp_capabilities'",
        "LEFT JOIN {$wpdb->prefix}usermeta WUMD ON WUMD.user_id = WU.ID AND WUMD.meta_key = '_IUD_deltime'",
        "LEFT JOIN {$wpdb->prefix}usermeta WUFN ON WUFN.user_id = WU.ID AND WUFN.meta_key = 'first_name'",
        "LEFT JOIN {$wpdb->prefix}usermeta WULN ON WULN.user_id = WU.ID AND WULN.meta_key = 'last_name'",
        "LEFT JOIN {$wpdb->prefix}usermeta WUMDIS ON WUMDIS.user_id = WU.ID AND WUMDIS.meta_key = '_IUD_userBlockedTime'",
        "LEFT JOIN {$wpdb->prefix}usermeta WUMDUL ON WUMDUL.user_id = WU.ID AND WUMDUL.meta_key = '" . self::$disabled_user_login_key . "'"
      );

      $havings = array();
      $groupBy = array('WU.ID, WU.user_login, WU.user_email, WU.user_url, WU.user_registered, WU.user_activation_key, WU.display_name, WUCAP.meta_value, WUM21.meta_value, WUMD.meta_value, WUMDIS.meta_value, WUMDUL.meta_value');

      if (!empty($ARGS['f_approve'])) {
        //user with approved comments
        if ($ARGS['f_approve'] == 'yes') {
          $conditions[] = "EXISTS (SELECT * FROM {$wpdb->prefix}comments WCAPP WHERE WCAPP.user_id = WU.ID AND WCAPP.comment_approved = 1)";
        } else {
          $conditions[] = "NOT EXISTS (SELECT * FROM {$wpdb->prefix}comments WCAPP WHERE WCAPP.user_id = WU.ID AND WCAPP.comment_approved = 1)";
        }
      }

      if (!empty($ARGS['has_spam'])) {
        if ($ARGS['has_spam'] === 'yes') {
          $conditions[] = "EXISTS (SELECT * FROM {$wpdb->prefix}comments WCSPM WHERE WCSPM.user_id = WU.ID AND WCSPM.comment_approved = 'spam')";
        } else {
          $conditions[] = "NOT EXISTS (SELECT * FROM {$wpdb->prefix}comments WCSPM WHERE WCSPM.user_id = WU.ID AND WCSPM.comment_approved = 'spam')";
        }
      }

      if (!empty($ARGS['f_userdisabled'])) {
        if (is_plugin_active('disable-user-login/disable-user-login.php')) {
          if ($ARGS['f_userdisabled'] === 'yes') {
            $conditions[] = "WUMDIS.meta_value > 0 OR WUMDUL.meta_value > 0";
          } else {
            $conditions[] = "((WUMDIS.meta_value is NULL OR WUMDIS.meta_value = 0)
              AND (WUMDUL.meta_value is NULL OR WUMDUL.meta_value = 0))";
          }

        } else {
          if ($ARGS['f_userdisabled'] === 'yes') {
            $conditions[] = "WUMDIS.meta_value > 0";
          } else {
            $conditions[] = "(WUMDIS.meta_value is NULL OR WUMDIS.meta_value = 0)";
          }
        }
      }

      if (!empty($ARGS['f_lastlogin'])) {
        $days = (int) $ARGS['f_lastlogin'] + 0;
        $time = time() - $days * 86400;
        $timeStr = date('Y-m-d H:i:s', $time);
        $conditions[] = "(WUM2.meta_value < $time OR WUM21.meta_value < '$timeStr')";
      }

      if (!empty($ARGS['has_recs'])) {
        if ($ARGS['has_recs'] === 'yes') {
          $conditions[] = "EXISTS (SELECT * FROM {$wpdb->prefix}posts WP WHERE WP.post_author = WU.ID
            AND NOT WP.post_type in ('attachment', 'revision') AND WP.post_status = 'publish')";
        } else {
          //ignore user with posts
          $conditions[] = "NOT EXISTS (SELECT * FROM {$wpdb->prefix}posts WP WHERE WP.post_author = WU.ID
            AND NOT WP.post_type in ('attachment', 'revision') AND WP.post_status = 'publish')";
        }
      }

      if (!empty($ARGS['is_pending'])) {

        if ($ARGS['is_pending'] === 'yes') {
          $conditions[] = 'LENGTH(WU.user_activation_key) > 0';
        } else {
          $conditions[] = 'LENGTH(WU.user_activation_key) = 0';
        }
      }

      if (!empty($ARGS['has_name'])) {
        if ($ARGS['has_name'] === 'yes') {
          $conditions[] = 'LENGTH(WUFN.meta_value) + LENGTH(WULN.meta_value) > 0';
        } else {
          $conditions[] = 'LENGTH(WUFN.meta_value) + LENGTH(WULN.meta_value) = 0';
        }
      }

      if ($environment->woocommerce_active && !empty($ARGS['has_orders'])) {
        if ($ARGS['has_orders'] === 'yes') {
          $conditions[] = "EXISTS (SELECT * FROM {$wpdb->prefix}posts WP
          INNER JOIN {$wpdb->prefix}postmeta WPM ON WPM.meta_key = '_billing_email' AND WPM.post_id = WP.ID
          WHERE WPM.meta_value like WU.user_email AND WP.post_type = 'shop_order')";
        } else {
          //ignore user with orders
          $conditions[] = "NOT EXISTS (SELECT * FROM {$wpdb->prefix}posts WP
          INNER JOIN {$wpdb->prefix}postmeta WPM ON WPM.meta_key = '_billing_email' AND WPM.post_id = WP.ID
          WHERE WPM.meta_value like WU.user_email AND WP.post_type = 'shop_order')";
        }
      }

      //section two
      if (!empty($ARGS['username'])) {
        $like = '%' . $wpdb->esc_like( $ARGS['username'] ) . '%';
        $conditions_sec2[] = $wpdb->prepare("WU.user_login like %s", $like);
      }

      $days = empty($ARGS['daysleft']) ? 0 : $ARGS['daysleft'] + 0;
      if ($days >= 0) {
        $tmStr = date('Y-m-d H:i:s', time() - $days * 86400);
        if (empty($ARGS['f_daysleft'])) {
          $conditions_sec2[] = "WU.user_registered >= '$tmStr'";
        } else {
          $conditions_sec2[] = "WU.user_registered < '$tmStr'";
        }
      }

      if (!empty($ARGS['user_role'])) {
        $conditions[] = 'LOCATE(\'' . esc_sql($ARGS['user_role']) . '\', WUCAP.meta_value) > 0';
      }

      if ($environment->ss2_active) {
        if (!empty($ARGS['has_SS2'])) {
          $conditions_sec2[] = "EXISTS (SELECT * FROM {$wpdb->prefix}subscribe2 WSS2 WHERE WSS2.email = WU.user_email)";
        } else {
          $conditions_sec2[] = "NOT EXISTS (SELECT * FROM {$wpdb->prefix}subscribe2 WSS2 WHERE WSS2.email = WU.user_email)";
        }
      }

      if (is_plugin_active('user-login-history/user-login-history.php') && false) {
        //user-login-history plugin case
        $PLUGIN_LAST_LOGIN_FIELD = 'MAX(UNIX_TIMESTAMP(WUM2.time_login))';
        $joins[] = "LEFT JOIN {$wpdb->prefix}fa_user_logins WUM2 ON WUM2.user_id = WU.ID";
      } else if (is_plugin_active('when-last-login/when-last-login.php')) {
        //when-last-login plugin case
        $PLUGIN_LAST_LOGIN_FIELD = 'WUM2.meta_value';
        $groupBy[] = $PLUGIN_LAST_LOGIN_FIELD;
        $joins[] = "LEFT JOIN {$wpdb->prefix}usermeta WUM2 ON WUM2.user_id = WU.ID AND WUM2.meta_key = 'when_last_login'";
      } else if (is_plugin_active('wp-last-login/wp-last-login.php')) {
        //wp-last-login plugin case
        $PLUGIN_LAST_LOGIN_FIELD = 'WUM2.meta_value';
        $groupBy[] = $PLUGIN_LAST_LOGIN_FIELD;
        $joins[] = "LEFT JOIN {$wpdb->prefix}usermeta WUM2 ON WUM2.user_id = WU.ID AND WUM2.meta_key = 'wp-last-login'";
      } else {
        //use own data
        $PLUGIN_LAST_LOGIN_FIELD = 'WUM2.meta_value';
        $groupBy[] = $PLUGIN_LAST_LOGIN_FIELD;
        $joins[] = "LEFT JOIN {$wpdb->prefix}usermeta WUM2 ON WUM2.user_id = WU.ID AND WUM2.meta_key = 'last_login_gtm'";
      }

      if (!empty($ARGS['f_usereverlogin'])) {
        if ($ARGS['f_usereverlogin'] === 'yes') {
          $havings[] = "(last_login > 0 OR WUM21.meta_value > '1970-01-02 00:00:01')";
        } else {
          $havings[] = "((last_login = 0 OR last_login IS NULL) AND (WUM21.meta_value is NULL OR WUM21.meta_value <= '1970-01-02 00:00:01'))";
        }
      }

      //Classipress case last-login
      $joins[] = "LEFT JOIN {$wpdb->prefix}usermeta WUM21 ON WUM21.user_id = WU.ID AND WUM21.meta_key = 'last_login'";

      if (!empty($conditions)) {
        $conditions_sec2[] = implode( $ARGS['flagsCND'] == 'add' ? ' OR ' : ' AND ', $conditions);
      }

      //first action - comments published
      $query = "
        SELECT SQL_CALC_FOUND_ROWS SUM(WC.comment_approved = 1) as approved, SUM(WC.comment_approved = 'spam') as spam,
          WU.ID, WU.user_login as login, WU.user_email as mail, WU.user_url as url, WU.user_registered as dt_reg, LENGTH(WU.user_activation_key) as act_key_len, WU.display_name as name,
          WUMDIS.meta_value as disabled_time,
          WUMDUL.meta_value as disabled,
          WUCAP.meta_value as USL, {$PLUGIN_LAST_LOGIN_FIELD} as last_login, WUM21.meta_value as last_login_classipress, WUMD.meta_value as removetime
      " . implode(" ", $joins) . "
          WHERE (" .  implode( ') AND (', $conditions_sec2) . ")
          GROUP BY " . implode(', ', $groupBy)
      . (!empty($havings) ? ' HAVING ' . implode(' AND ', $havings) : '');

      switch ($ARGS['sort_order']) {
      case 'logindate':
        $sort_order = 'WUM21.meta_value DESC, WUM2.meta_value DESC';
        break;
      case 'name':
        $sort_order = 'WU.display_name';
        break;
      case 'mail':
        $sort_order = 'WU.user_email';
        break;
      case 'regdate':
        $sort_order = 'WU.user_registered';
        break;
      case 'spam':
        $sort_order = 'SUM(WC.comment_approved = \'spam\') DESC, WU.user_login';
        break;
      case 'userlevel':
        $sort_order = 'WUCAP.meta_value DESC, WU.user_login';
        break;
      case 'comments':
        $sort_order = 'SUM(WC.comment_approved = 1) DESC, WU.user_login';
        break;
      case 'disabled':
        $sort_order = 'WUMDIS.meta_value, WUMDUL.meta_value DESC';
        break;
      case 'posts':
      default:
        $sort_order = 'WU.user_login';
      }
      $query .= " ORDER BY $sort_order";

      //limit section
      $query .= $ARGS['max_size_output'] == 'all' ? ' ' : ' LIMIT ' . ($ARGS['max_size_output'] + 0);

      $rows = $wpdb->get_results($query, ARRAY_A);
      $total = $wpdb->get_var("SELECT FOUND_ROWS();");

      $user_list = array();
      if (!empty($rows)) {
        foreach($rows as $k => $UR) {
          $UR['recs'] = 0;
          $user_list[$UR['ID']] = $UR;
        }
      }

      //clean up with registration lifetime ctiteria + check user norecs criteria + count publish posts
      $query = "
        SELECT COUNT(WP.ID) as recs, WU.ID
        FROM $wpdb->posts WP
        LEFT JOIN $wpdb->users WU ON WP.post_author = WU.ID
        WHERE 1 " . (empty($ARGS['f_daysleft']) ? '' : "AND WU.user_registered < '$tmStr' ") . "
          AND NOT WP.post_type in ('attachment', 'revision') AND post_status = 'publish'
        GROUP BY WU.ID
        HAVING COUNT(WP.ID) > 0";

      $rows = $wpdb->get_results($query, ARRAY_A);

      if (!empty($rows)) {
        foreach($rows as $k => $UR) {
          $id = $UR['ID'];
          if (isset($user_list[$id])) $user_list[$id]['recs'] = $UR['recs'];
        }
      }

      $result = new \stdClass();
      $result->rows  = $user_list;
      $result->total = $total;
      return $result;

  }
}