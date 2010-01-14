/*
 * Ext JS Library 1.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */


Ext.form.VTypes=function(){var _1=/^[a-zA-Z_]+$/;var _2=/^[a-zA-Z0-9_]+$/;var _3=/^([\w]+)(.[\w]+)*@([\w-]+\.){1,5}([A-Za-z]){2,4}$/;var _4=/(((https?)|(ftp)):\/\/([\-\w]+\.)+\w{2,3}(\/[%\-\w]+(\.\w{2,})?)*(([\w\-\.\?\\\/+@&#;`~=%!]*)(\.\w{2,})?)*\/?)/i;return{"email":function(v){return _3.test(v);},"emailText":"This field should be an e-mail address in the format \"user@domain.com\"","emailMask":/[a-z0-9_\.\-@]/i,"url":function(v){return _4.test(v);},"urlText":"This field should be a URL in the format \"http:/"+"/www.domain.com\"","alpha":function(v){return _1.test(v);},"alphaText":"This field should only contain letters and _","alphaMask":/[a-z_]/i,"alphanum":function(v){return _2.test(v);},"alphanumText":"This field should only contain letters, numbers and _","alphanumMask":/[a-z0-9_]/i};}();