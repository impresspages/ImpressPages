/*
 * Ext JS Library 1.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */

/**
 * Czech Translations / Český překlad
 * Translated by Tomáš Korčák (72)
 * 2007/05/01 21:29, Ext-1.0.1a
 */

Ext.UpdateManager.defaults.indicatorText = '<div class="loading-indicator">Prosím čekejte...</div>';

if(Ext.View){
   Ext.View.prototype.emptyText = "";
}

if(Ext.grid.Grid){
   Ext.grid.Grid.prototype.ddText = "{0} vybraných řádků";
}

if(Ext.TabPanelItem){
   Ext.TabPanelItem.prototype.closeText = "Zavřít záložku";
}

if(Ext.form.Field){
   Ext.form.Field.prototype.invalidText = "Hodnota v tomto poli je neplatná";
}

if(Ext.LoadMask){
    Ext.LoadMask.prototype.msg = "Prosím čekejte...";
}

Date.monthNames = [
   "Leden",
   "Únor",
   "Březen",
   "Duben",
   "Květen",
   "Červen",
   "Červenec",
   "Srpen",
   "Září",
   "Říjen",
   "Listopad",
   "Prosinec"
];

Date.dayNames = [
   "Neděle",
   "Pondělí",
   "Úterý",
   "Středa",
   "Čtvrtek",
   "Pátek",
   "Sobota"
];

if(Ext.MessageBox){
   Ext.MessageBox.buttonText = {
      ok     : "OK",
      cancel : "Storno",
      yes    : "Ano",
      no     : "Ne"
   };
}

if(Ext.util.Format){
   Ext.util.Format.date = function(v, format){
      if(!v) return "";
      if(!(v instanceof Date)) v = new Date(Date.parse(v));
      return v.dateFormat(format || "m.d.Y");
   };
}

if(Ext.DatePicker){
   Ext.apply(Ext.DatePicker.prototype, {
      todayText         : "Dnes",
      minText           : "Datum nesmí být starší než je minimální",
      maxText           : "Datum nesmí být dřívější než je maximální",
      disabledDaysText  : "",
      disabledDatesText : "",
      monthNames	: Date.monthNames,
      dayNames		: Date.dayNames,
      nextText          : 'Následující měsíc (Control+Right)',
      prevText          : 'Předcházející měsíc (Control+Left)',
      monthYearText     : 'Zvolte měsíc (ke změně let použijte Control+Up/Down)',
      todayTip          : "{0} (Spacebar)",
      format            : "m.d.y"
   });
}

if(Ext.PagingToolbar){
   Ext.apply(Ext.PagingToolbar.prototype, {
      beforePageText : "Strana",
      afterPageText  : "z {0}",
      firstText      : "První strana",
      prevText       : "Přecházející strana",
      nextText       : "Následující strana",
      lastText       : "Poslední strana",
      refreshText    : "Aktualizovat",
      displayMsg     : "Zobrazeno {0} - {1} z celkových {2}",
      emptyMsg       : 'Žádné záznamy nebyly nalezeny'
   });
}

if(Ext.form.TextField){
   Ext.apply(Ext.form.TextField.prototype, {
      minLengthText : "Pole nesmí mít méně {0} znaků",
      maxLengthText : "Pole nesmí být delší než {0} znaků",
      blankText     : "This field is required",
      regexText     : "",
      emptyText     : null
   });
}

if(Ext.form.NumberField){
   Ext.apply(Ext.form.NumberField.prototype, {
      minText : "Hodnota v tomto poli nesmí být menší než {0}",
      maxText : "Hodnota v tomto poli nesmí být větší než {0}",
      nanText : "{0} není platné číslo"
   });
}

if(Ext.form.DateField){
   Ext.apply(Ext.form.DateField.prototype, {
      disabledDaysText  : "Neaktivní",
      disabledDatesText : "Neaktivní",
      minText           : "Datum v tomto poli nesmí být starší než {0}",
      maxText           : "Datum v tomto poli nesmí být novější než {0}",
      invalidText       : "{0} není platným datem - zkontrolujte zda-li je ve formátu {1}",
      format            : "m.d.y"
   });
}

if(Ext.form.ComboBox){
   Ext.apply(Ext.form.ComboBox.prototype, {
      loadingText       : "Prosím čekejte...",
      valueNotFoundText : undefined
   });
}

if(Ext.form.VTypes){
   Ext.apply(Ext.form.VTypes, {
      emailText    : 'V tomto poli může být vyplněna pouze emailová adresa ve formátu "uživatel@doména.cz"',
      urlText      : 'V tomto poli může být vyplněna pouze URL (adresa internetové stránky) ve formátu "http:/'+'/www.doména.cz"',
      alphaText    : 'Toto pole může obsahovat pouze písmena abecedy a znak _',
      alphanumText : 'Toto pole může obsahovat pouze písmena abecedy, čísla a znak _'
   });
}

if(Ext.grid.GridView){
   Ext.apply(Ext.grid.GridView.prototype, {
      sortAscText  : "Řadit vzestupně",
      sortDescText : "Řadit sestupně",
      lockText     : "Ukotvit sloupec",
      unlockText   : "Uvolnit sloupec",
      columnsText  : "Sloupce"
   });
}

if(Ext.grid.PropertyColumnModel){
   Ext.apply(Ext.grid.PropertyColumnModel.prototype, {
      nameText   : "Název",
      valueText  : "Hodnota",
      dateFormat : "m.j.Y"
   });
}

if(Ext.SplitLayoutRegion){
   Ext.apply(Ext.SplitLayoutRegion.prototype, {
      splitTip            : "Tahem změnit velikost.",
      collapsibleSplitTip : "Tahem změnit velikost. Dvojklikem skrýt."
   });
}