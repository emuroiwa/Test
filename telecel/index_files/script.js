var $ja = jQuery.noConflict();
//Check div system-message-container exist
$ja(document).ready(function(){
	if(($ja("#system-message-container").html() || '').length > 1){
		if(($ja("#system-message").html() || '').length > 1){
			$ja("#system-message-container").show();
			$ja("#system-message a.close").click(function(){
				$ja("#system-message-container").hide();
			});
		}else{
			$ja("#system-message-container").hide();
		}
	}else{
		$ja("#system-message-container").hide();
	}
});
