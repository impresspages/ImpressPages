/*
 * Ext JS Library 1.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */

/*
 * Norwegian translation
 * By grEvenX 16-April-2007
 */

Ext.UpdateManager.defaults.indicatorText = '<div class="loading-indicator">Laster...</div>';

if(Ext.grid.Grid){
   Ext.grid.Grid.prototype.ddText = "{0} markerte rader";
}

if(Ext.TabPanelItem){
   Ext.TabPanelItem.prototype.closeText = "Lukk denne fanen";
}

if(Ext.form.Field){
   Ext.form.Field.prototype.invalidText = "Verdien i dette felter er ugyldig";
}

if(Ext.LoadMask){
    Ext.LoadMask.prototype.msg = "Laster...";
}

Date.monthNames = [
   "Januar",
   "Februar",
   "Mars",
   "April",
   "Mai",
   "Juni",
   "Juli",
   "August",
   "September",
   "Oktober",
   "November",
   "Desember"
];

Date.dayNames = [
   "Søndag",
   "Mandag",
   "Tirsdag",
   "Onsdag",
   "Torsdag",
   "Fredag",
   "Lørdag"
];

if(Ext.MessageBox){
   Ext.MessageBox.buttonText = {
      ok     : "OK",
      cancel : "Avbryt",
      yes    : "Ja",
      no     : "Nei"
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
      todayText         : "I dag",
      minText           : "Denne datoen er tidligere enn den tidligste tillatte",
      maxText           : "Denne datoen er senere enn den seneste tillatte",
      disabledDaysText  : "",
      disabledDatesText : "",
      monthNames	: Date.monthNames,
      dayNames		: Date.dayNames,
      nextText          : 'Neste måned (Control+Pil Høyre)',
      prevText          : 'Forrige måned (Control+Pil Venstre)',
      monthYearText     : 'Velg en måned (Control+Pil Opp/Ned for å skifte år)',
      todayTip          : "{0} (mellomrom)",
      format            : "d/m/y"
   });
}

if(Ext.PagingToolbar){
   Ext.apply(Ext.PagingToolbar.prototype, {
      beforePageText : "Side",
      afterPageText  : "av {0}",
      firstText      : "Første side",
      prevText       : "Forrige side",
      nextText       : "Neste side",
      lastText       : "Siste side",
      refreshText    : "Oppdater",
      displayMsg     : "Viser {0} - {1} of {2}",
      emptyMsg       : 'Ingen data å vise'
   });
}

if(Ext.form.TextField){
   Ext.apply(Ext.form.TextField.prototype, {
      minLengthText : "Den minste lengden for dette feltet er {0}",
      maxLengthText : "Den største lengden for dette feltet er {0}",
      blankText     : "Dette feltet er påkrevd",
      regexText     : "",
      emptyText     : null
   });
}

if(Ext.form.NumberField){
   Ext.apply(Ext.form.NumberField.prototype, {
      minText : "Den minste verdien for dette feltet er {0}",
      maxText : "Den største verdien for dette feltet er {0}",
      nanText : "{0} er ikke et gyldig nummer"
   });
}

if(Ext.form.DateField){
   Ext.apply(Ext.form.DateField.prototype, {
      disabledDaysText  : "Deaktivert",
      disabledDatesText : "Deaktivert",
      minText           : "Datoen i dette feltet må være etter {0}",
      maxText           : "Datoen i dette feltet må være før {0}",
      invalidText       : "{0} is not a valid date - it must be in the format {1}",
      format            : "m/d/y"
   });
}

if(Ext.form.ComboBox){
   Ext.apply(Ext.form.ComboBox.prototype, {
      loadingText       : "Laster...",
      valueNotFoundText : undefined
   });
}

if(Ext.form.VTypes){
   Ext.apply(Ext.form.VTypes, {
      emailText    : 'Dette feltet skal være en epost adresse i formatet "user@domain.com"',
      urlText      : 'Dette feltet skal være en link (URL) i formatet "http:/'+'/www.domain.com"',
      alphaText    : 'Dette feltet skal kun inneholde bokstaver og _',
      alphanumText : 'Dette feltet skal kun inneholde bokstaver, tall og _'
   });
}

if(Ext.grid.GridView){
   Ext.apply(Ext.grid.GridView.prototype, {
      sortAscText  : "Sorter stigende",
      sortDescText : "Sorter synkende",
      lockText     : "Lås kolonne",
      unlockText   : "Lås opp kolonne",
      columnsText  : "Kolonner"
   });
}

if(Ext.grid.PropertyColumnModel){
   Ext.apply(Ext.grid.PropertyColumnModel.prototype, {
      nameText   : "Navn",
      valueText  : "Verdi",
      dateFormat : "d/m/Y"
   });
}

if(Ext.SplitLayoutRegion){
   Ext.apply(Ext.SplitLayoutRegion.prototype, {
      splitTip            : "Dra for å endre størrelse.",
      collapsibleSplitTip : "Dra for å endre størrelse, dobbelklikk for å skjule."
   });
}