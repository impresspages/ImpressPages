/*
 * Ext JS Library 1.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */

/**
 * Swedish translation (utf8-encoding)
 * By Erik Andersson, Monator Technologies
 * 24 April 2007
 */

Ext.UpdateManager.defaults.indicatorText = '<div class="loading-indicator">Laddar...</div>';

if(Ext.View){
   Ext.View.prototype.emptyText = "";
}

if(Ext.grid.Grid){
   Ext.grid.Grid.prototype.ddText = "{0} markerade rad(er)";
}

if(Ext.TabPanelItem){
   Ext.TabPanelItem.prototype.closeText = "Stäng denna tabb";
}

if(Ext.form.Field){
   Ext.form.Field.prototype.invalidText = "Värdet i detta fält är inte tillåtet";
}

if(Ext.LoadMask){
    Ext.LoadMask.prototype.msg = "Laddar...";
}

Date.monthNames = [
   "Januari",
   "Februari",
   "Mars",
   "April",
   "Maj",
   "Juni",
   "Juli",
   "Augusti",
   "September",
   "Oktober",
   "November",
   "December"
];

Date.dayNames = [
   "Söndag",
   "Måndag",
   "Tisdag",
   "Onsdag",
   "Torsdag",
   "Fredag",
   "Lördag"
];

if(Ext.MessageBox){
   Ext.MessageBox.buttonText = {
      ok     : "OK",
      cancel : "Avbryt",
      yes    : "Ja",
      no     : "Nej"
   };
}

if(Ext.util.Format){
   Ext.util.Format.date = function(v, format){
      if(!v) return "";
      if(!(v instanceof Date)) v = new Date(Date.parse(v));
      return v.dateFormat(format || "Y-m-d");
   };
}

if(Ext.DatePicker){
   Ext.apply(Ext.DatePicker.prototype, {
      todayText         : "Idag",
      minText           : "Detta datum inträffar före det tidigast tillåtna",
      maxText           : "Detta datum inträffar efter det senast tillåtna",
      disabledDaysText  : "",
      disabledDatesText : "",
      monthNames	: Date.monthNames,
      dayNames		: Date.dayNames,
      nextText          : 'Nästa Månad (Ctrl + höger piltangent)',
      prevText          : 'Föregående Månad (Ctrl + vänster piltangent)',
      monthYearText     : 'Välj en månad (Ctrl + Uppåt/Neråt pil för att ändra årtal)',
      todayTip          : "{0} (Mellanslag)",
      format            : "y-m-d",
      startDay          : 1
   });
}

if(Ext.PagingToolbar){
   Ext.apply(Ext.PagingToolbar.prototype, {
      beforePageText : "Sida",
      afterPageText  : "av {0}",
      firstText      : "Första sidan",
      prevText       : "Föregående sida",
      nextText       : "Nästa sida",
      lastText       : "Sista sidan",
      refreshText    : "Uppdatera",
      displayMsg     : "Visar {0} - {1} av {2}",
      emptyMsg       : 'Det finns ingen data att visa'
   });
}

if(Ext.form.TextField){
   Ext.apply(Ext.form.TextField.prototype, {
      minLengthText : "Minsta tillåtna längd för detta fält är {0}",
      maxLengthText : "Största tillåtna längd för detta fält är {0}",
      blankText     : "Detta fält är obligatoriskt",
      regexText     : "",
      emptyText     : null
   });
}

if(Ext.form.NumberField){
   Ext.apply(Ext.form.NumberField.prototype, {
      minText : "Minsta tillåtna värde för detta fält är {0}",
      maxText : "Största tillåtna värde för detta fält är {0}",
      nanText : "{0} är inte ett tillåtet nummer"
   });
}

if(Ext.form.DateField){
   Ext.apply(Ext.form.DateField.prototype, {
      disabledDaysText  : "Inaktiverad",
      disabledDatesText : "Inaktiverad",
      minText           : "Datumet i detta fält måste inträffa efter {0}",
      maxText           : "Datumet i detta fält måste inträffa före {0}",
      invalidText       : "{0} är inte ett tillåtet datum - datum skall anges i formatet {1}",
      format            : "y/m/d"
   });
}

if(Ext.form.ComboBox){
   Ext.apply(Ext.form.ComboBox.prototype, {
      loadingText       : "Laddar...",
      valueNotFoundText : undefined
   });
}

if(Ext.form.VTypes){
   Ext.apply(Ext.form.VTypes, {
      emailText    : 'Detta fält skall vara en e-post adress i formatet "user@domain.com"',
      urlText      : 'Detta fält skall vara en länk (URL) i formatet "http:/'+'/www.domain.com"',
      alphaText    : 'Detta fält får bara innehålla bokstäver och "_"',
      alphanumText : 'Detta fält får bara innehålla bokstäver, nummer och "_"'
   });
}

if(Ext.grid.GridView){
   Ext.apply(Ext.grid.GridView.prototype, {
      sortAscText  : "Sortera stigande",
      sortDescText : "Sortera fallande",
      lockText     : "Lås kolumn",
      unlockText   : "Lås upp kolumn",
      columnsText  : "Kolumner"
   });
}

if(Ext.grid.PropertyColumnModel){
   Ext.apply(Ext.grid.PropertyColumnModel.prototype, {
      nameText   : "Namn",
      valueText  : "Värde",
      dateFormat : "Y/m/d"
   });
}

if(Ext.SplitLayoutRegion){
   Ext.apply(Ext.SplitLayoutRegion.prototype, {
      splitTip            : "Dra för att ändra storleken.",
      collapsibleSplitTip : "Drag för att ändra storleken. Dubbelklicka för att gömma."
   });
}
