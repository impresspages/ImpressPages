ModCommunityNewsletter = {

  subscribe : function (newsletterUrl, email){
  	LibDefault.ajaxMessage(newsletterUrl, 'action=subscribe&email=' + escape(email), ModCommunityNewsletter.subscribeAnswer);
  	return false;
  },

	subscribeAnswer : function(answer){	 
	  var variables = eval('(' + answer + ')');
	  if (variables.status == 'incorrect_email') {
	    document.getElementById('modCommunityNewsletterError').style.display = 'block';
	  } else {
  	  document.location = variables.url;
	  }
	},

	unsubscribe : function(newsletterUrl, email){ 
  	LibDefault.ajaxMessage(newsletterUrl, 'action=unsubscribe&email=' + escape(email), ModCommunityNewsletter.unsubscribeAnswer);
  	return false;
	},
	
	unsubscribeAnswer : function(answer){
	  var variables = eval('(' + answer + ')');
	  document.location = variables.url;
	}

}