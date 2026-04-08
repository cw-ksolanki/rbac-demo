<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'auth';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// Auth
$route['auth']          = 'auth/index';
$route['auth/login']    = 'auth/login';
$route['auth/logout']   = 'auth/logout';

// Admin panel  (controllers/admin/Admin.php)
$route['admin']                   = 'admin/admin/dashboard';
$route['admin/dashboard']         = 'admin/admin/dashboard';
$route['admin/profile']           = 'admin/admin/profile';      
$route['admin/profile/update']      = 'admin/admin/update_profile';  

//Admin panel (admin/admins)
$route['admin/admins']              = 'admin/admin/all_admins';

//Admin panel (admin/users)
$route['admin/users']             = 'admin/admin/users';
$route['admin/users/create']      = 'admin/admin/create_user';
$route['admin/users/edit/(:num)'] = 'admin/admin/edit_user/$1';
$route['admin/users/view/(:num)']   = 'admin/admin/view_user/$1';
$route['admin/users/delete/(:num)'] = 'admin/admin/delete_user/$1';

//Admin panel (admin/roles)
$route['admin/roles']             = 'admin/admin/roles';
$route['admin/roles/create']      = 'admin/admin/create_role';
$route['admin/roles/edit/(:num)'] = 'admin/admin/edit_role/$1';
$route['admin/roles/delete/(:num)'] = 'admin/admin/delete_role/$1';

// User panel
$route['user']              = 'user/user/dashboard';
$route['user/dashboard']    = 'user/user/dashboard';
$route['user/profile']      = 'user/user/profile';
$route['user/profile/update'] = 'user/user/update_profile';
