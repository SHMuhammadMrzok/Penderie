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
|	http://codeigniter.com/user_guide/general/routing.html
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
$route['default_controller'] = 'home';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

/*****************Products******************/
$route['Cats_Products/(:any)']    = "products/products/cat_products/$1";
$route['Cat_Products/(:any)']     = "products/products/index/$1";
$route['Product_details/(:any)']  = "products/products/product/$1";

/*****************Users******************/
$route['Register']                = "users/register/index";
$route['First_order_msg']         = "users/register/view_first_msg";
$route['Balance_Recharge']        = "users/user_balance/recharge";

/*****************Inner Menu************/
$route['Payment_Log']             = "payment_options/user_balance/user_balance_log";
$route['Orders_Log']              = "orders/order/user_orders";
$route['Edit_Profile']            = "users/users/edit_mydata";


/*******************Shopping Cart****************/
$route['Shopping_Cart']           = "shopping_cart/cart/view_cart";

/*******************Tickets**********************/
$route['Support_Tickets']         = "tickets/tickets/index";
$route['Ticket_Details/(:any)']   = "tickets/tickets/ticket_details/$1"; 
$route['New_Ticket']              = "tickets/tickets/new_ticket";

/*****************Static Pages*****************/
$route['Page_Details/(:any)']     = "static_pages/view/index/$1";