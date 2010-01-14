/*
 * Ext JS Library 1.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */

/**
 * List compiled by mystix on the extjs.com forums.
 * Thank you Mystix!
 */

/**
 * Turkish translation by Hüseyin Tüfekçilerli
 * 04-11-2007, 09:52 AM 
 */

Ext.UpdateManager.defaults.indicatorText = '<div class="loading-indicator">Yükleniyor...</div>';

if(Ext.View){
   Ext.View.prototype.emptyText = "";
}

if(Ext.grid.Grid){
   Ext.grid.Grid.prototype.ddText = "{0} seçili satır";
}

if(Ext.TabPanelItem){
   Ext.TabPanelItem.prototype.closeText = "Bu sekmeyi kapat";
}

if(Ext.form.Field){
   Ext.form.Field.prototype.invalidText = "Bu alandaki değer geçersiz";
}

Date.monthNames = [
   "Ocak",
   "Şubat",
   "Mart",
   "Nisan",
   "Mayıs",
   "Haziran",
   "Temmuz",
   "Ağustos",
   "Eylül",
   "Ekim",
   "Kasım",
   "Aralık"
];

Date.dayNames = [
   "Pazar",
   "Pazartesi",
   "Salı",
   "Çarşamba",
   "Perşembe",
   "Cuma",
   "Cumartesi"
];

if(Ext.MessageBox){
   Ext.MessageBox.buttonText = {
      ok     : "Tamam",
      cancel : "İptal",
      yes    : "Evet",
      no     : "Hayır"
   };
}

if(Ext.util.Format){
   Ext.util.Format.date = function(v, format){
      if(!v) return "";
      if(!(v instanceof Date)) v = new Date(Date.parse(v));
      return v.dateFormat(format || "d/m/Y");
   };
}

if(Ext.DatePicker){
   Ext.apply(Ext.DatePicker.prototype, {
      todayText         : "Bugün",
      minText           : "Bu tarih minimum tarihten önce",
      maxText           : "Bu tarih maximum tarihten sonra",
      disabledDaysText  : "",
      disabledDatesText : "",
      monthNames	: Date.monthNames,
      dayNames		: Date.dayNames,
      nextText          : 'Sonraki Ay (Ctrl+Sağ)',
      prevText          : 'Önceki Ay (Ctrl+Sol)',
      monthYearText     : 'Bir ay seçin (Yılları değiştirmek için Ctrl+Yukarı/Aşağı)',
      todayTip          : "{0} (Boşluk)",
      format            : "d/m/y",
      startDay          : 1
   });
}

if(Ext.PagingToolbar){
   Ext.apply(Ext.PagingToolbar.prototype, {
      beforePageText : "Sayfa",
      afterPageText  : " / {0}",
      firstText      : "İlk Sayfa",
      prevText       : "Önceki Sayfa",
      nextText       : "Sonraki Sayfa",
      lastText       : "Son Sayfa",
      refreshText    : "Yenile",
      displayMsg     : "{2} satırdan {0} - {1} arası gösteriliyor",
      emptyMsg       : 'Gösterilecek veri yok'
   });
}

if(Ext.form.TextField){
   Ext.apply(Ext.form.TextField.prototype, {
      minLengthText : "Bu alan için minimum uzunluk {0}",
      maxLengthText : "Bu alan için maximum uzunluk {0}",
      blankText     : "Bu alan gerekli",
      regexText     : "",
      emptyText     : null
   });
}

if(Ext.form.NumberField){
   Ext.apply(Ext.form.NumberField.prototype, {
      minText : "Bu alan için minimum değer {0}",
      maxText : "Bu alan için maximum değer {0}",
      nanText : "{0} geçerli bir sayı değil"
   });
}

if(Ext.form.DateField){
   Ext.apply(Ext.form.DateField.prototype, {
      disabledDaysText  : "Pasif",
      disabledDatesText : "Pasif",
      minText           : "Bu alana {0} tarihinden sonraki bir tarih girilmeli",
      maxText           : "Bu alana {0} tarihinden önceki bir tarih girilmeli",
      invalidText       : "{0} geçerli bir tarih değil - şu formatta olmalı {1}",
      format            : "d/m/y"
   });
}

if(Ext.form.ComboBox){
   Ext.apply(Ext.form.ComboBox.prototype, {
      loadingText       : "Yükleniyor...",
      valueNotFoundText : undefined
   });
}

if(Ext.form.VTypes){
   Ext.apply(Ext.form.VTypes, {
      emailText    : 'Bu alan bir e-mail adresi formatında olmalı "kullanici@alanadi.com"',
      urlText      : 'Bu alan bir URL formatında olmalı "http:/'+'/www.alanadi.com"',
      alphaText    : 'Bu alan sadece harf ve _ içermeli',
      alphanumText : 'Bu alan sadece harf, sayı ve _ içermeli'
   });
}

if(Ext.grid.GridView){
   Ext.apply(Ext.grid.GridView.prototype, {
      sortAscText  : "Artarak Sırala",
      sortDescText : "Azalarak Sırala",
      lockText     : "Sütünu Kilitle",
      unlockText   : "Sütunun Kilidini Kaldır",
      columnsText  : "Sütunlar"
   });
}

if(Ext.grid.PropertyColumnModel){
   Ext.apply(Ext.grid.PropertyColumnModel.prototype, {
      nameText   : "İsim",
      valueText  : "Değer",
      dateFormat : "j/m/Y"
   });
}

if(Ext.SplitLayoutRegion){
   Ext.apply(Ext.SplitLayoutRegion.prototype, {
      splitTip            : "Boyutlandırmak için sürükleyin.",
      collapsibleSplitTip : "Boyutlandırmak için sürükleyin. Gizlemek için çift tıklayın."
   });
}