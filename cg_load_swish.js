
var clientTel = jQuery('#billing_phone').val();
var clientTelstr1 =  clientTel.substr(0,3)
var clientTelstr2 = '*** ** **';

// option 1 copy client tel to Swish directly (potential security issue)
//jQuery("#swish-payer-alias").val(jQuery('#billing_phone').val());	

// option 2 copy part of client tel to Swish 
//jQuery("#swish-payer-alias").val(clientTelstr1.concat(clientTelstr2));	



  
