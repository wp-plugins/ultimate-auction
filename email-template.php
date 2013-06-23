<?php
//email template to be sent to auction winners
function ultimate_auction_email_template($auction_name, $auction_id, $auction_desc, $winner_bid, $winner_email, $return_url)
{
	global $wpdb;
	$name_qry = "SELECT name FROM ".$wpdb->prefix."wdm_bidders WHERE bid =".$winner_bid." AND auction_id =".$auction_id;
	$winner_name = $wpdb->get_var($name_qry);
	
        $rec_email    	= get_option('wdm_paypal_address');
        $cur_code     	= substr(get_option('wdm_currency'), -3);
	$site_name 	= get_bloginfo('name');
        $subject      	= '['.$site_name.'] Congratulations! You have won an auction';
        $auction_email 	= get_option('wdm_auction_email');
        $site_url 	= get_bloginfo('url');
	
        $message = "";
	$message = "Hi ".$winner_name.", <br /><br />";
        $message .= "This is to inform you that you won the auction at WEBSITE URL (".$site_url."). Here are the auction details: <br /><br />";
        
	$mode = get_option('wdm_account_mode');
	
        $paypal_link  = "";
	
	if($mode == 'Sandbox')
		$paypal_link  = "https://sandbox.paypal.com/cgi-bin/webscr?cmd=_xclick";
	else
		$paypal_link  = "https://www.paypal.com/cgi-bin/webscr?cmd=_xclick";
		
	$paypal_link .= "&business=".urlencode($rec_email);
	//$paypal_link .= "&lc=US";
	$paypal_link .= "&item_name=".urlencode($auction_name);
	$paypal_link .= "&amount=".urlencode($winner_bid);
	$paypal_link .= "&currency_code=".urlencode($cur_code);
	$paypal_link .= "&return=".urlencode($return_url);
	$paypal_link .= "&button_subtype=services";
	$paypal_link .= "&no_note=0";
	$paypal_link .= "&bn=PP%2dBuyNowBF%3abtn_buynowCC_LG%2egif%3aNonHostedGuest";
	
	$message .= "Product URL: ".$return_url." <br />";
	$message .= "<br />Product Name: ".$auction_name." <br />";
	$message .= "<br />Description: <br />".$auction_desc."<br /><br />";
	
	$check_method = get_post_meta($auction_id, 'wdm_payment_method', true);
	
	$pay_amt = "<strong>".$cur_code." ".$winner_bid."</strong>";
	
	if($check_method === 'method_paypal')
	{
	    $auction_data = array();
	
	    $auction_data = array( 'auc_id' => $auction_id,
				'auc_name' => $auction_name,
				'auc_desc' => $auction_desc,
				'auc_bid' => $winner_bid,
				'auc_merchant' => $rec_email,
				'auc_payer' => $winner_email,
				'auc_currency' => $cur_code
			      );
	    
	    $message .= "You can contact ADMIN at ".$auction_email." for delivery of the item and pay ".$pay_amt." through PayPal - <br /><br />";
	    
	    $paypal_link = apply_filters( 'ua_paypal_email_content', $paypal_link, $auction_data );
	    
            $message .= $paypal_link;
	    
	    $message .= "<br/><br /> Kindly, click on above URL to make payment.<br />";
	    
	}
	elseif($check_method === 'method_wire_transfer')
	{
	    $message .= "You can pay ".$pay_amt." by Wire Transfer.<br /><br />";
	    $message .= get_option('wdm_wire_transfer');
	}
	elseif($check_method === 'method_mailing')
	{
	    $message .= "You can pay ".$pay_amt." by Cheque.<br /><br />";
            $message .= get_option('wdm_mailing_address');
	}
	$headers = "";
	//$headers  = "From: ". $site_name ." <". $auction_email ."> \r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
	
	$email_sent = false;
	
	if(!empty($paypal_link))
		$email_sent = wp_mail( $winner_email, $subject, $message, $headers, '' );
        
        if($email_sent)
	{   
            update_post_meta( $auction_id, 'auction_email_sent', 'sent' );
	}
	
	return $email_sent;
}
?>