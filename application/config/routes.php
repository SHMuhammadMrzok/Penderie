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

/*
require_once( BASEPATH .'database/DB'. EXT );
$db =& DB();

// categories routes
$query = $db->get( 'categories' );
$cats  = $query->result();

$set   = $db->get( 'settings' )->row();

$product_route          = $set->product_route;
$main_category_route    = $set->main_category_route;
$sub_category_route     = $set->sub_category_route;

foreach( $cats as $row )
{
    //$route = urlencode($row->route);
    $route[$main_category_route. $row->route.'/(:any)' ]  = "categories/categories/sub_cats/$row->route";//"products/products/cat_products/$row->route/$1";
    $route[$sub_category_route. $row->route.'/(:any)']    = "products/products/index/$row->route/$1";
}

/*
//productts routes
$query      = $db->get( 'products' );
$products   = $query->result();

foreach( $products as $row )
{
    //$route = urlencode($row->route);
    $route[$product_route .$row->route]  = "products/products/product/$row->route";
}
*/



$route['default_controller'] = 'home';
$route['404_override'] = 'not_allowed/index';
$route['translate_uri_dashes'] = FALSE;

/*****************Products******************/
//$route['Cats_Products/(:any)']    = "products/products/cat_products/$1";
//$route['Cat_Products/(:any)']     = "products/products/index/$1";
//$route['Product_details/(:any)']  = "products/products/product/$1";

/*****************Users******************/
$route['Register']                = "users/register/index";
$route['User_login']              = "users/users/user_login";
$route['User_logout']             = "users/users/logout";
$route['First_order_msg']         = "users/register/view_first_msg";
$route['Balance_Recharge']        = "users/user_balance/recharge";
$route['Add_Address']             = "users/user_address/address/";
$route['Addresses_List']          = "users/user_address/list";
$route["UpgradeAccount"]          = "users/upgrade_account";
/*****************Menu***************/
$route['Payment_Log']             = "payment_options/user_balance/user_balance_log";
$route['Orders_Log']              = "orders/order/user_orders";
$route['Edit_Profile']            = "users/users/edit_mydata";
$route['Edit_Wholesaler_data']    = "users/users/edit_wholesaler_data";
$route['All_stores']              = "stores/stores/index";
$route['All_brands']              = "brands/brands/index";
$route['OurCareers']              = "careers/careers/index";
/***************Products*****************/
$route['Store_details/(:any)']    = "products/products/store_products/$1";
$route['Compare_Products']        = "products/products/compare_products";
$route['Wishlist']                = "products/products/user_wishlist";
$route['All_Offers']              = "products/products/all_offers";
$route['Brand_Products/(:any)/(:any)'] = "products/products/brand_products/$1/$2";
/*******************Shopping Cart****************/
$route['Shopping_Cart']           = "shopping_cart/cart/view_cart";

/*******************Tickets**********************/
$route['Support_Tickets']         = "tickets/tickets/index";
$route['Ticket_Details/(:any)']   = "tickets/tickets/ticket_details/$1";
$route['New_Ticket']              = "tickets/tickets/new_ticket";
$route['Contact_US']              = "contact_us/contact_us/index";

/*****************Static Pages*****************/
$route['Page_Details/(:any)']     = "static_pages/view/index/$1";
$route['All_branches']            = "static_pages/view/all_branches";
$route['FAQS']                    = "faq/faq/index";

/*****************Shopping cart*****************/
$route['Cart_Address']      = "shopping_cart/cart/shipping_address";
$route['Cart_Send_As_Gift'] = "shopping_cart/cart/gift_data";
$route['Cart_Payment']      = "shopping_cart/cart/cart_payment_methods";
