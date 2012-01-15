/**
 * @package ImpressPages
 * @copyright Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */
    
(function( $ ){

    var methods = {
       init : function( options ) {

         return this.each(function(){
           
           var $this = $(this),
           data = $this.data('ipModuleNewsletter');
           // If the plugin hasn't been initialized yet
           if ( ! data ) {
           
             /*
               Do more setup stuff here
             */

             $(this).data('tooltip', {});
             console.log('newsletterInit');
             
             $this.find('.ipmForm').bind('click', function(event) {
                 event.preventDefault();
                 $(this).trigger('cancelWidget.ipWidget');
             });
             
           }
         });
       },
       subscribe : function(newsletterUrl, email) {
           var data = Object();
           data.action = 'subscribe';
           data.email = email;
           $.ajax({
               type : 'POST',
               url : newsletterUrl,
               data : data,
               success : ModCommunityNewsletter.subscribeAnswer,
               dataType : 'json'
           });
           return false;
       },
       unsubscribe : function(newsletterUrl, email) {
           var data = Object();
           data.action = 'unsubscribe';
           data.email = email;
           $.ajax({
               type : 'POST',
               url : newsletterUrl,
               data : data,
               success : ModCommunityNewsletter.unsubscribeAnswer,
               dataType : 'json'
           });
           return false;
       },
       
       unsubscribeAnswer : function($variables) {
           document.location = variables.url;
       }
    };

    $.fn.ipMouleNewsletter = function( method ) {
      
      if ( methods[method] ) {
        return methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));
      } else if ( typeof method === 'object' || ! method ) {
        return methods.init.apply( this, arguments );
      } else {
        $.error( 'Method ' +  method + ' does not exist on jQuery.ipModuleNewsletter' );
      }
    
    };

})( jQuery );
    
    


$(document).ready(function($) {
    var widgetOptions = new Object;
    $('.ipModuleNewsletter').ipModuleNewsletter(widgetOptions);
    //$('.ipModuleNewsletter').ipModuleNewsletter();
});

