<?php
/************************
/*      PayPal          *
/*                      *
/***********************/

$config['PayPalMode'] 		= 'live'; // sandbox or live
$config['PayPalApiUsername'] 	= '';//'islamsharaf-facilitator_api1.shourasoft.com'; //PayPal API Username
$config['PayPalApiPassword'] 	= '';//'FC2HH9TG468JL3BS'; //Paypal API password
$config['PayPalApiSignature'] 	= '';//'AFcWxV21C7fd0v3bYYYRCpSSRl31A2pXcX1PDqR3Cs70ErcJ.adtn.zb'; //Paypal API Signature
$config['PayPalCurrencyCode'] 	= 'USD'; //Paypal Currency Code
$config['PayPalReturnURL'] 	= base_url() . 'orders/payment_gateways/feed_back_paypal'; //Point to process.php page
$config['PayPalCancelURL'] 	= base_url(); //Cancel URL if user clicks cancel

/************************
/*      PayFort Old     *
/*                      *
/***********************/

$config['PayfortOldPspID']         = 'islamsharaf';
$config['PayfortOldMode']          = 'test'; // 'test' or 'prod'
$config['PayfortOldEncryptionKey'] = 'Mysecretsig1875!?';
$config['PayfortOldLanguage']      = 'en_US'; // example: en_US, nl_NL, fr_FR, ...

/************************
/*    PayFort New       *
/*                      *
/***********************/

$config['PayfortMerchantID']            = 'mPeqCIQm';//'69ce2a0f';
$config['PayfortAccessCode']            = 'W5y46sjyNl5c8yfhX1q5';//'TWVE6D2E0KXpZPaDee9T';
$config['PayfortMode']                  = 'prod';//'prod'; // 'test' or 'prod'
$config['PayfortRequestEncryptionKey']  = '$2y$10$6Lz9twU.u';//'$2y$10$vMd7icr36';
$config['PayfortResponseEncryptionKey'] = '$2y$10$XauXAYjs.';//'$2y$10$UUGUDJvJj';
$config['PayfortLanguage']              = 'en'; // example: en, ar
$config['PayfortCommand']               = 'PURCHASE'; //AUTHORIZATION, PURCHASE
$config['PayfortReturnUrl']             = base_url() . 'orders/payment_gateways/feed_back_payfort';

/************************
/*      CashU           *
/*                      *
/***********************/

$config['CashuMerchantID']      = '';
$config['CashuMode']            = '1'; // '1' Test mode, '0' Production mode
$config['CashuEncryptionKey'] 	= '';
$config['CashuLanguage'] 	= 'en'; // example: en, ar, fr, ...



/************************
/*      Knet            *
/*                      *
/***********************/

$config['KnetAlias'] 	       = '';
$config['KnetLanguage'] 	   = 'ARA'; // example: ENG, ARA
$config['KnetResponseURL'] 	   = base_url() . 'orders/payment_gateways/feed_back_knet_native'; //Point to process.php page
$config['KnetErrorURL'] 	   = base_url() . 'orders/payment_gateways/feed_back_knet_native'; //Cancel URL if user clicks cancel



/************************
/*      Sadad           *
/*                      *
/***********************/

$config['dynamicMerchantLandingURL'] 	   = base_url() . 'orders/payment_gateways/feed_back_sadad'; //Point to process.php page
$config['dynamicMerchantFailureURL'] 	   = base_url() . 'orders/payment_gateways/feed_back_sadad'; //Cancel URL if user clicks cancel

/************************
/*      Hyper Pay       *
/*                      *
/***********************/

$config['HyperPayReturnURL']        = base_url().'orders/payment_gateways/feed_back_hyperpay';
$config['SadadHyperpayLandingURL']  = base_url().'orders/payment_gateways/feed_back_hyperpay_sadad';
$config['SadadHyperpayFailureURL']  = base_url().'orders/payment_gateways/feed_back_hyperpay_sadad';

/************************
/*       Moyasar        *
/*                      *
/***********************/

$config['moyasarRedirectURL']       = base_url().'Orders_Log';//orders/payment_gateways/feed_back_moyasar';
