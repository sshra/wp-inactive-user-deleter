<?php
/*
Plugin Name: Inactive User Deleter
Plugin URI: https://wordpress.org/plugins/inactive-user-deleter/
Version: 1.65
Requires at least: 3.1.0
Description: When your project lives so long, and website got a lot of fake user's registrations (usually made by spammers, bots, etc). This tool will help you to clean this mess up. You can filter, select and delete users.
Author: Korol Yuriy aka Shra <to@shra.ru>
Author URI: https://shra.ru
Donate link: https://pay.cryptocloud.plus/pos/Oc9ieI6Eb5HWPptn
Tags: user, managment, users managment, delete, multy removal, pack deletion, user cleaner
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

*/

namespace inactive_user_deleter;

if (!class_exists('InactiveUserDeleter')) {
class InactiveUserDeleter
{
	const actual_version = 1.65;
	const status = 'production';
	var $ss2_active = null;
  var $woocommerce_active = null;

  public function __construct()
  {
		//Actions
		if (is_admin()) {
			mb_internal_encoding("UTF-8");
			mb_language('uni');

			add_action('admin_menu', array($this, 'menu'));
			add_filter('plugin_action_links', array($this, 'add_action_links'), 10, 2 );

      add_action('wp_ajax_iud_getCsvUserList', array($this, 'hook_wp_ajax_getCsvUserList'));
		}

		//check for alarm message!
		add_action('init', array($this, 'init_hook'), 10);

		//store last login DTM
		add_action('wp_login', array($this, 'last_successful_authorization'));

		//to intercept confirmation codes
		add_action('login_form', array($this, 'login_form_hook'));
		add_action('wp_login', array($this, 'login_hook'), 10, 2);

    add_filter( 'authenticate', array($this, 'authenticate_hook'), 30, 3 );
	}

  /*
  * Blocks disabled accounts
  */
  public function authenticate_hook($user, $username, $password) {
    if ($user instanceof \WP_User) {
      $tm = get_user_meta($user->ID, '_IUD_userBlockedTime', true);
      if ($tm) {
        return new \WP_Error( 'account_is_blocked', __( '<strong>ERROR</strong>: Given account is blocked.' ) );
      }
    }
    return $user;
  }

	/*
	* Last visit data
	*/
	public function last_successful_authorization($login) {
		$user = get_user_by('login', $login);
		update_user_meta($user->ID, 'last_login_gtm', time());
	}

	public function login_hook($user_login, $user ) {
		//remove confirmation timeout on log-in
		$time = get_user_meta($user->ID, '_IUD_deltime', true);
		if (!empty($time)) {
			delete_user_meta( $user->ID, '_IUD_deltime');
			update_user_meta( $user->ID, '_IUD_cancelcode', 'confirmed');
		}
	}

	public function login_form_hook() {
		if (!empty($_GET['iud-confirm'])) {
			$UID = $_GET['uid'] + 0;
			$code = get_user_meta($UID, '_IUD_cancelcode', true);

			if (empty($code)) return;
			if ($code == 'confirmed') {
				echo '<div style="padding: 20px; margin: 10px 0; font-weight: bold; background-color: #F0FFF0">'
				. __('Your confirmation code was already accepted.') . '</div>';
			} else
			if ($_GET['iud-confirm'] == $code) {
				delete_user_meta( $UID, '_IUD_deltime');
				update_user_meta( $UID, '_IUD_cancelcode', 'confirmed');
				echo '<div style="padding: 20px; margin: 10px 0; font-weight: bold; background-color: #F0FFF0">'
				. __('Your confirmation code is accepted. Thank you for your confirmation!') . '</div>';
			} else {
				echo '<div style="padding: 20px; margin: 10px 0; font-weight: bold; background-color: #FFF0F0">'
				. __('Incorrect confirmation code!') . '</div>';

			}
		}
	}

	public function init_hook() {
		$OP = self::read_settings();

    //remove trial users
    if (!empty($OP['trial-role']) && $OP['trial-period'] > 0) {
      //it's time
      $lastCheck = get_option('UsrInDeleter_lastTrialCheck', 0);
      if (time() - $lastCheck >= 21600) {
        update_option('UsrInDeleter_lastTrialCheck', time());
        global $wpdb;
        // build query
        $days = intval($OP['trial-period']);
        $tmStr = date('Y-m-d H:i:s', time() - $days * 86400);
        $conditions[] = "WU.user_registered < '$tmStr'"; // expired
        $conditions[] = "WU.ID <> 1"; // admin user protection
        $conditions[] = 'LOCATE(\'"administrator"\', WUM.meta_value) = 0'; // administrators protection
        $conditions[] = 'LOCATE(\'"editor"\', WUM.meta_value) = 0'; // editors protection
        $conditions[] = 'LOCATE(\'' . esc_sql($OP['trial-role']) . '\', WUM.meta_value) > 0'; // trial role match

        $joins = array(
          "FROM {$wpdb->prefix}users WU",
          "LEFT JOIN {$wpdb->prefix}usermeta WUM ON WUM.user_id = WU.ID AND WUM.meta_key = 'wp_capabilities'",
        );

        $query = "SELECT WU.ID, WU.user_login as login, WUM.meta_value as capabilities "
        . implode(" ", $joins) . "
          WHERE (" .  implode( ') AND (', $conditions) . ")"
        . ' ORDER BY WU.ID';

        $rows = $wpdb->get_results($query, ARRAY_A);

        if (count($rows)) {
          require_once( ABSPATH . 'wp-admin/includes/user.php' );
          foreach ($rows as $user) {
            wp_delete_user($user['ID']);
          }
        }
      }
    }

		//inform about IU
		if (!empty($OP['informME']) && $OP['informPeriod'] > 0) {
			//it's time
			if (time() - $OP['last-inform'] >= $OP['informPeriod'] * 86400) {

        require_once dirname(__FILE__) . '/class.users.php';
        $number = \inactive_user_deleter\users::countInactiveUsers();

				if ($number && $number >= $OP['informUsersNumber']) {
					wp_mail(
						get_option('admin_email'),
						$_SERVER['HTTP_HOST'] . ': ' . __('Inactive User Deleter Notification'),
						'<html>
<head profile="http://www.w3.org/1999/xhtml/vocab">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<p>Alarm, alarm!</p>
<p>It`s time to clean-up inactive users list on your website ' . $_SERVER['HTTP_HOST'] . '.</p>
<p>We have ' . $number. ' inactive user(s), who has no approved comments or published posts.</p>
<p>--<br />
Forever yours, Inactive User Deleter.</p>
</body>
</html>',
						array('Content-Type: text/html; charset=UTF-8')
					);
					$OP['last-inform'] = time();
					self::save_settings($OP);
				}

			}
		}

		//check deletion timeout
		$U = get_users(
			array(
				'meta_key' => '_IUD_deltime',
				'meta_value' => time(),
				'meta_compare' => '<',
			)
		);
		//delete users
		if (!empty($U)) {
			require_once(ABSPATH . 'wp-admin/includes/user.php');
			foreach ($U as $user) {
				if ($user->ID != 1)	wp_delete_user($user->ID);
      }
		}
	}

  /***
   * admin_menu action implementation
   **/
	public function menu() {
		add_users_page(__('Inactive users'), __('Inactive users'), 'delete_users', __FILE__, array($this, 'toolpage'));
	}

  private function view($ZNTD_name, $variables = array()) {
    ob_start();
    extract($variables, EXTR_SKIP);
    include dirname(__FILE__) . '/templates/' . $ZNTD_name . '.tpl.php';
    return ob_get_clean();
  }

  /* ENDPOINT: CSV user list export */
  public function hook_wp_ajax_getCsvUserList() {
    $user = wp_get_current_user();

    if (!in_array('administrator', $user->roles)) {
      echo __('Only users with the role - `administrator` have access to this report.');
    } else {
      require_once dirname(__FILE__) . '/class.users.php';

      $environment = new \stdClass();
      $environment->ss2_active = $this->ss2_active;
      $environment->woocommerce_active = $this->woocommerce_active;

      $filterData = $_GET;
      $filterData['max_size_output'] = 'all';
      $userListObject = \inactive_user_deleter\users::getUsersList($filterData, $environment);

      header('Content-Type: application/CSV');
      header('Content-Disposition: attachment;filename="userlist.csv"');
      header('Cache-Control: max-age=0');

      print $this->view('users_list_csv', array(
        'user_list' => $userListObject->rows,
        'total'     => $userListObject->total
      ));
    }
    die;
  }

	public function toolpage() {
		global $wpdb, $user_ID;
		$this->ss2_active = is_plugin_active('subscribe2/subscribe2.php');
    $this->woocommerce_active = is_plugin_active('woocommerce/woocommerce.php');

		readfile(dirname(__FILE__) . '/styles.css');
?>
<div class="wrap" id="IUD_area">

<?php
  print $this->view('header');
  print $this->view('nav-menu');
?>

<div class="sub-page" id="IUD_page_1" >
<p><?php echo  __('Choose the criterias, then check the user\'s list that is will displayed. If this list will correct - hit the button &laquo;Kill them all&raquo;')?>.</p>

<form method="POST" action="#outputs" id="inactive-user-deleter-form">
<?php
    wp_nonce_field('user-filter');
	  //output_filter_part
    print $this->view('user_filter', array(
      'ss2_active' => $this->ss2_active,
      'woocommerce_active' => $this->woocommerce_active
    ));
?>
</form>
<?php
    if (!isset($_POST['op'])) $_POST['op'] = 'stand_by';
    else {
      if (in_array($_POST['op'], ['disable', 'activate', 'draft', 'publish', 'finally_delete', 'delete', 'search_users']))
        check_admin_referer( 'user-filter' );
    }
    require_once dirname(__FILE__) . '/class.users.php';

    if (isset($_POST['f_users'])) {
      $_POST['f_users'] = json_decode($_POST['f_users'], true);
    }

    switch ($_POST['op']) {
    case 'stand_by':
      // i like it
      break;
    case 'disable':
      // disable accounts
      echo __('Disabling...') . '<br />';

      $cnt_disabled = 0;
      foreach($_POST['f_users'] as $user_id_to_disable) {

        $result = \inactive_user_deleter\users::isVIPUser($user_id_to_disable);

        if ($result === false) {
          $cnt_disabled += \inactive_user_deleter\users::disable($user_id_to_disable) ? 1 : 0;
        } else {
          echo $result . '<br />';
        }
      }

      //output actions status
      if ($cnt_disabled == 1) {
        echo $cnt_disabled . ' ' . __('user was disabled.');
      } else {
        echo $cnt_disabled . ' ' . __('users were disabled.');
      }

      break;
    case 'activate':
      // enable accounts
      echo __('Enabling accounts...') . '<br />';

      $cnt_enabled = 0;
      foreach($_POST['f_users'] as $user_id_to_enable) {
        $cnt_enabled += \inactive_user_deleter\users::enable($user_id_to_enable) ? 1 : 0;
      }

      //output actions status
      if ($cnt_enabled == 1) {
        echo $cnt_enabled . ' ' . __('user was enabled.');
      } else {
        echo $cnt_enabled . ' ' . __('users were enabled.');
      }

      break;
    case 'draft':
      // turn posts to draft
      echo __('Turning posts into drafts...') . '<br />';

      $cnt_draft = \inactive_user_deleter\users::unpublishPostsOfGivenUsers($_POST['f_users']);

      //output actions status
      if ($cnt_draft == 1) {
        echo $cnt_draft . ' ' . __('post was turned into draft.');
      } else {
        echo $cnt_draft . ' ' . __('posts were turned into draft');
      }
      break;

    case 'publish':
      // publish posts
      echo __('Publishing posts...') . '<br />';

      $cnt_draft = \inactive_user_deleter\users::publishPostsOfGivenUsers($_POST['f_users']);

      //output actions status
      if ($cnt_draft == 1) {
        echo $cnt_draft . ' ' . __('post has been published.');
      } else {
        echo $cnt_draft . ' ' . __('posts have been published');
      }

      break;
    case 'finally_delete':
    case 'delete':
      //delete all selected users
      echo '<hr />';
      if (empty($_POST['f_users'])) {
        echo __('I have done all work. Actually there is nothing to do. So I did nothing. :) Because you didn\'t select any user.');
      } else {
        if ( !current_user_can('delete_users') ) {
          __('You can&#8217;t delete users (no permissions). Sorry.... :)');
        } else if ($_POST['op'] == 'finally_delete') {
          $OP = self::read_settings();

          $confirmPeriod = (!isset($_POST['confirmPeriod']) || $_POST['confirmPeriod'] <= 0)
          ? 1
          : intval($_POST['confirmPeriod']);

          echo __('Deleting...') . '<br />';
          $cnt_deleted = 0;
          $cnt_sendmail = 0;
          foreach($_POST['f_users'] as $user_id_to_delete) {
            //real delete

            $result = \inactive_user_deleter\users::isVIPUser($user_id_to_delete);

            if ($result !== false) {
              echo $result . '<br />';
              continue;
            }

				  	if (!empty($OP['informUsers'])) {
  						//check if user already has notification
  						$deltime = get_user_meta($user_id_to_delete, '_IUD_deltime', true);
  						if (!empty($deltime)) continue;
  						//check if user once confirmed before
  						$code = get_user_meta($user_id_to_delete, '_IUD_cancelcode', true);
  						if ($code == 'confirmed') continue;

  						//mark account for delete
  						update_user_meta( $user_id_to_delete, '_IUD_deltime', time() + $confirmPeriod * 86400);
  						$code = wp_generate_password();
  						update_user_meta( $user_id_to_delete, '_IUD_cancelcode', $code);

  						//send confirmation letter
  						$confirmURL = 'http://' . $_SERVER['HTTP_HOST'] . '/wp-login.php?iud-confirm=' . urlencode($code) . '&uid=' . $user_id_to_delete;
  						$mail = $wpdb->get_var("SELECT user_email FROM $wpdb->users WHERE ID = {$user_id_to_delete}");
  	 				  $mailbody = str_replace(
  							array(
  								':sitename',
  								':confirmPeriod',
  								':link'),
  							array(
  								$_SERVER['HTTP_HOST'],
  								$confirmPeriod,
  								'<a href="' . $confirmURL . '">' . $confirmURL . '</a>'),
  							$OP['confirmLetter']);

  						wp_mail($mail,
	 						  $_SERVER['HTTP_HOST'] . ': ' . __('confirm your account.'),
							 '<html><head profile="http://www.w3.org/1999/xhtml/vocab">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head>
<body>' . $mailbody . '</body></html>',
							 array('Content-Type: text/html; charset=UTF-8')
						  );
						  $cnt_sendmail ++;
  					} else {
	 					  wp_delete_user($user_id_to_delete);
						  $cnt_deleted ++;
					  }
          }

  				//output actions status
  				if ($cnt_deleted > 0) {
  					if ($cnt_deleted == 1)
  						echo $cnt_deleted . ' ' . __('user was deleted.');
  					else
  						echo $cnt_deleted . ' ' . __('users were deleted.');
  				}
  				else if ($cnt_sendmail > 0) {
  					if ($cnt_sendmail == 1)
  						echo $cnt_sendmail . ' ' . __('user was notified.');
  					else
  						echo $cnt_sendmail . ' ' . __('users were notified.');
  				} else {
  					echo __('No real action were undertaken.');
  				}

        } else {
          if (!is_array($_POST['f_users'])) {
            $_POST['f_users'] = array($_POST['f_users']);
          }
          print $this->view('final_warning_before_delete', array(
            'userCount' => count($_POST['f_users']))
          );
        }
      }
    case 'search_users':
      //Ooohh damn, i hate to work!
      $environment = new \stdClass();
      $environment->ss2_active = $this->ss2_active;
      $environment->woocommerce_active = $this->woocommerce_active;
      $userListObject = \inactive_user_deleter\users::getUsersList($_POST, $environment);

	  	//user's list output
      echo '<div class="section-title">' . __('Users list') . '</div>';

      if (empty($userListObject->rows)) {
        echo __('<p><b>No users are found.</b></p>');
      } else {
        print $this->view('users_list_html', array(
          'user_list' => $userListObject->rows,
          'total'     => $userListObject->total
        ));
      }

      break;
    }

?>
</form>
</div>

<div class="sub-page" id="IUD_page_4">
<?php
  if (!empty($_POST['op']) && $_POST['op'] == 'trial-user') {
    check_admin_referer( 'trial_user_list' );
    //save and store options
    $trialPeriod = intval($_POST['trial-period']);
    if ($trialPeriod < 0) $trialPeriod = 0;

    global $wp_roles;
    $trialRole = $_POST['trial-role'];
    $roles = $wp_roles->get_names();
    if (!isset($roles[$trialRole])) $trialRole = '';

    $OP = self::read_settings();
    $OP['trial-role'] = $trialRole;
    $OP['trial-period'] = $trialPeriod;
    self::save_settings($OP);
  }

  $OP = self::read_settings();
  print self::view('trial-users-form', array('OP' => $OP));
?>
</div>

<div class="sub-page" id="IUD_page_2">
<?php
	if (!empty($_POST['op']) && $_POST['op'] == 'misc') {
    check_admin_referer( 'misc_settings' );

		//save and store options
		if ($_POST['informPeriod'] < 0) $_POST['informPeriod'] = 0;
		$_POST['informPeriod'] = intval($_POST['informPeriod'] + 0);

		if ($_POST['confirmPeriod'] <= 0) $_POST['confirmPeriod'] = 1;

		if ($_POST['informUsersNumber'] < 0) $_POST['informUsersNumber'] = 0;
		$_POST['informUsersNumber'] = intval($_POST['informUsersNumber'] + 0);

		$flags = array('informME', 'informUsers');

		foreach ($flags as $flag) {
			if (!isset($_POST[$flag]))
				$_POST[$flag] = 0;
		}
		//save $OP['last-inform']
		$OP = $_POST;
		unset($OP['sbm'], $OP['op']);

		self::save_settings($OP);
	}

	$OP = self::read_settings();
  print self::view('inform-form', array('OP' => $OP));
?>
</div>

<div class="sub-page" id="IUD_page_3">
<?php

  if (self::status != 'production' && $_POST['op'] == 'generate') {
    check_admin_referer( 'generate_dummies' );
    $this->create_arb_user($_POST['N'] + 0);
  }
  $OP = self::read_settings();

  print self::view('about-page', array(
    'OP' => $OP,
    'version' => self::actual_version,
    'status' => self::status,
    'assetsDir' => plugin_dir_url(__FILE__) . 'assets/',
  ));

?>
</div>

</div>

<?php
	}

	public function add_action_links($links, $file) {
		if (strpos($file, 'inactive-user-deleter.php' ) === false ) return $links;
		$mylinks = array(
			'<a href="' . admin_url( 'users.php?page=' . $file ) . '">' . __('Settings') . '</a>',
		);

		return array_merge( $links, $mylinks );
	}

	/* fast user generation routine - only for test purposes */
	private function create_arb_user($n = 100) {
		while ($n-- > 0) {
			$asr = rand(1000000, 10000000);
			wp_create_user('usr_' . $asr, 'pass_'. $asr, $asr . '@mail.ru');
		}
	}

	static function save_settings($OP) {
    foreach(self::default_settings() as $key => $value) {
      if (is_numeric($value)) {
        $OP[$key] = floatval($OP[$key]);
      }
    }
		update_option('UsrInDeleter_settings', $OP);
	}

	static function read_settings() {
		return array_merge(self::default_settings(), get_option('UsrInDeleter_settings', array()));
	}

	static function default_settings() {
		return array(
			'informME' => 0,
			'informUsersNumber' => 50,
			'informPeriod' => 7, //days
			'informCOND' => 'OR',
			'last-inform' => 0,
			//since v1.2
			'ver' => 1.0,
			//since v1.31
			'informUsers' => 0,
			'confirmPeriod' => 7,
			'confirmLetter' => '<p>Dear subscriber!</p>
<p>Please confirm your interest to continue be part of :sitename community.<br />
Your account will be deleted in :confirmPeriod days. To prevent deletion, please visit out website using next link:<br />
:link</p>
<p>
---<br />
Sincerely yours, webmaster of :sitename.</p>',
      //since v1.50
      'trial-role'   => '',
      'trial-period' => 7,
		);
	}
}
}

$inactive_user_deleter_obj = new InactiveUserDeleter();
