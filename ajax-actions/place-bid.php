<script type="text/javascript">
jQuery(document).ready(function($){
    var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
    
    var epattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
    
    $("#wdm-place-bid-now").click(function(){
	
	var e_id = $("#wdm-bidder-email").val();
	var bid_val = new Number;
	bid_val = $("#wdm-bidder-bidval").val();
	
	if($("#wdm-bidder-name").val() == '')
	{
	    alert('Please enter your Name');
	}
	else if(e_id == '')
	{
	    alert('Please enter your Email ID');
	}
	else if(e_id != '' && !epattern.test(e_id))
	{
	   alert('Please enter a valid Email ID');
	}
	else if(!bid_val)
	{
	    alert('Please enter your Bid Amount');
	}
	else if( bid_val && isNaN(bid_val))
	{
	    alert('Please enter a numeric value');
	}
	else
	{
	     var data = {
		action: 'place_bid_now',
                name: $("#wdm-bidder-name").val(),
                email: $("#wdm-bidder-email").val(),
                bid: $("#wdm-bidder-bidval").val(),
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
			alert("Sorry, an another bidder has bid on the previous bid amount. Please enter a bid amount greater than or equal to " + latest_bid);
		    else
		    {
			alert("Please enter a bid amount greater than or equal to " + latest_bid);
			return false;
		    }
		    window.location.reload();
		}
		else if(response.indexOf("Won") != -1)
		{
		    alert("Your Bid Placed Successfully!");
		    alert("Congratulations! You have won this auction since your bid value has reached the 'Buy Now' price.");
		    
		    var w_data = {
				    action: 'bid_notification',
				    email_type: 'winner_email',
				    name: $("#wdm-bidder-name").val(),
				    email: $("#wdm-bidder-email").val(),
				    bid: $("#wdm-bidder-bidval").val(),
				    auction_id: "<?php echo $wdm_auction->ID; ?>",
				    auc_name: "<?php echo esc_js($wdm_auction->post_title); ?>",
				    auc_desc: "<?php echo esc_js($wdm_auction->post_content); ?>",
				    auc_url: "<?php echo get_permalink();?>"
			};
			
			$.post(ajaxurl, w_data, function(resp) {window.location.reload();});
		}
		else if(response.indexOf("Placed") != -1)
		{
		    alert("Your Bid Placed Successfully!");
		    
		    var b_data = {
				    action: 'bid_notification',
				    name: $("#wdm-bidder-name").val(),
				    email: $("#wdm-bidder-email").val(),
				    bid: $("#wdm-bidder-bidval").val(),
				    auction_id: "<?php echo $wdm_auction->ID; ?>",
				    auc_name: "<?php echo esc_js($wdm_auction->post_title); ?>",
				    auc_desc: "<?php echo esc_js($wdm_auction->post_content); ?>",
				    auc_url: "<?php echo get_permalink();?>"
			};
			
			$.post(ajaxurl, b_data, function(r) {window.location.reload();});
		}
		else
		{
		   alert("Sorry, your bid could not be placed");
		   window.location.reload();
		}
		
                $("#wdm-bidder-name").val("");
                $("#wdm-bidder-email").val("");
                $("#wdm-bidder-bidval").val("");
		
	    });
	}
        
        return false;
        });
    
    });
</script>