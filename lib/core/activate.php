<?php	require('../../../../../wp-config.php');	$wp->init(); $wp->parse_request(); $wp->query_posts();	$wp->register_globals(); $wp->send_headers();	if(isset($_POST['activate'])){		$mode = 'activate';		$module = $_POST['activate'];	} elseif(isset($_POST['activate_all'])){		$modules = array('invoice', 'htmlmails', 'paypal', 'search', 'hourlycal', 'lang', 'import', 'useredit', 'statistics', 'styles', 'coupons', 'multical');		$slugs = '';		foreach($modules as $module){			if(file_exists(WP_PLUGIN_DIR.'/easyreservations/lib/modules/'.$module.'/'.$module.'.php')) $slugs[] = $module;		}		update_option("reservations_active_modules", $slugs);		wp_redirect( admin_url().'admin.php?page=reservation-settings&site=plugins&activate_all' );		exit;	} elseif(isset($_POST['deactivate_all'])){		update_option("reservations_active_modules", "");		wp_redirect( admin_url().'admin.php?page=reservation-settings&site=plugins&deactivate_all' );		exit;	} else {		$mode = 'deactivate';		$module = $_POST['deactivate'];	}	if(isset($module) && !empty($module)){		if($mode == 'activate'){			$active = get_option('reservations_active_modules');			if(empty($active) || !is_array($active) || !in_array($module, $active)){				$active[] = $module;				update_option("reservations_active_modules",$active);			}		} else {			$active = get_option('reservations_active_modules');			if(!empty($active)){				foreach($active as $key => $mod){					if($mod == $module){						unset($active[$key]);						break;					}				}			}			update_option("reservations_active_modules", $active);		}	}	wp_redirect( admin_url().'admin.php?page=reservation-settings&site=plugins&'.$mode.'='.$module );	exit;?>