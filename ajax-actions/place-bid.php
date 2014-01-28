<script type="text/javascript">
jQuery(document).ready(function($){
    var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
    
    $("#wdm-place-bid-now").click(function(){
	
	var bid_val = new Number;
	bid_val = $("#wdm-bidder-bidval").val();
	
	if(!bid_val)
	{
	    alert('<?php _e("Please enter your Bid Amount", "wdm-ultimate-auction");?>');
	}
	else if( bid_val && isNaN(bid_val))
	{
	    alert('<?php _e("Please enter a numeric value", "wdm-ultimate-auction");?>');
	}
	else
	{
	    
	     var data = {
		action: 'place_bid_now',
		ab_name: "<?php echo esc_js($auction_bidder_name);?>",
                ab_email: "<?php echo $auction_bidder_email;?>",
                ab_bid: $("#wdm-bidder-bidval").val(),
                auction_id: "<?php echo $wdm_auction->ID; ?>"
	    };
	    $.post(ajaxurl, data, function(response) {
		
		var latest_bid;
		var curr_next_bid = new Number;
		curr_next_bid = "<?php echo $inc_price; ?>";
		
		if(response.indexOf("inv_bid") != -1)
		{
		    latest_bid = response.replace("inv_bid","");
		    
		    if(Number(bid_val) >= Number(curr_next_bid))
			alert('<?php printf(__("Sorry, an another bidder has bid on the previous bid amount. Please enter a bid amount greater than or equal to %s", "wdm-ultimate-auction"), "" );?> ' + latest_bid);
		    else
		    {
			alert('<?php printf(__("Please enter a bid amount greater than or equal to %s", "wdm-ultimate-auction"), "" );?>' + latest_bid);
			return false;
		    }
		    window.location.reload();
		}
		else if(response.indexOf("Expired") != -1)
		{
		    alert('<?php _e("Sorry, this auction has been expired.", "wdm-ultimate-auction");?>');
		    window.location.reload();
		}
		else if(response.indexOf("Sold") != -1)
		{
		    alert('<?php _e("Sorry, your bid can not be placed. It seems that either a bidder has outbid you or the auction has been expired recently.", "wdm-ultimate-auction");?>');
		    window.location.reload();
		}
		else if(response.indexOf("Won") != -1)
		{
		    alert('<?php _e("Your Bid Placed Successfully!", "wdm-ultimate-auction");?>');
		    alert("<?php _e("Congratulations! You have won this auction since your bid value has reached the 'Buy it Now' price.", "wdm-ultimate-auction");?>");
		    
		    var w_data = {
				    action: 'bid_notification',
				    email_type: 'winner_email',
				    ab_name: "<?php echo esc_js($auction_bidder_name);?>",
				    ab_email: "<?php echo $auction_bidder_email;?>",
				    ab_bid: $("#wdm-bidder-bidval").val(),
				    auction_id: "<?php echo $wdm_auction->ID; ?>",
				    auc_name: "<?php echo esc_js($wdm_auction->post_title); ?>",
				    auc_desc: "<?php echo esc_js($wdm_auction->post_content); ?>",
				    auc_url: "<?php echo get_permalink();?>",
				    ab_char: "<?php echo $set_char;?>"
			};
			
			$.post(ajaxurl, w_data, function(resp) {window.location.reload();});
		}
		else if(response.indexOf("Placed") != -1)
		{
		    alert('<?php _e("Your Bid Placed Successfully!", "wdm-ultimate-auction");?>');
		    
		    var b_data = {
				    action: 'bid_notification',
				    ab_name: "<?php echo esc_js($auction_bidder_name);?>",
				    ab_email: "<?php echo $auction_bidder_email;?>",
				    ab_bid: $("#wdm-bidder-bidval").val(),
				    auction_id: "<?php echo $wdm_auction->ID; ?>",
				    auc_name: "<?php echo esc_js($wdm_auction->post_title); ?>",
				    auc_desc: "<?php echo esc_js($wdm_auction->post_content); ?>",
				    auc_url: "<?php echo get_permalink();?>",
				    ab_char: "<?php echo $set_char;?>"
			};
			
			$.post(ajaxurl, b_data, function(r) {window.location.reload();});
		}
		else
		{
		   alert('<?php _e("Sorry, your bid can not be placed", "wdm-ultimate-auction");?>');
		   window.location.reload();
		}
		
                $("#wdm-bidder-bidval").val("");
		
	    });
	}
        
        return false;
        });
    
    });
</script>