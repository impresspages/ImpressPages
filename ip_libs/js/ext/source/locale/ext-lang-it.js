/*
 * Ext JS Library 1.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */

/*
 * Italian translation
 * By eric_void
 * 04-10-2007, 11:25 AM
 */

Ext.UpdateManager.defaults.indicatorText = '<div class="loading-indicator">Caricamento in corso...</div>';

if(Ext.View){
   Ext.View.prototype.emptyText = "";
}

if(Ext.grid.Grid){
   Ext.grid.Grid.prototype.ddText = "{0} righe selezionate";
}

if(Ext.TabPanelItem){
   Ext.TabPanelItem.prototype.closeText = "Chiudi pannello";
}

if(Ext.form.Field){
   Ext.form.Field.prototype.invalidText = "Valore del campo non valido";
}

Date.monthNames = [
   "Gennaio",
   "Febbraio",
   "Marzo",
   "Aprile",
   "Maggio",
   "Giugno",
   "Luglio",
   "Agosto",
   "Settembre",
   "Ottobre",
   "Novembre",
   "Dicembre"
];

Date.dayNames = [
   "Domenica",
   "Lunedi",
   "Martedi",
   "Mercoledi",
   "Giovedi",
   "Venerdi",
   "Sabato"
];

if(Ext.MessageBox){
   Ext.MessageBox.buttonText = {
      ok     : "OK",
      cancel : "Annulla",
      yes    : "Si",
      no     : "No"
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
      todayText         : "Oggi",
      minText           : "Data precedente alla data minima",
      maxText           : "Data successiva alla data massima",
      disabledDaysText  : "",
      disabledDatesText : "",
      monthNames	: Date.monthNames,
      dayNames		: Date.dayNames,
      nextText          : 'Mese successivo (Ctrl+Destra)',
      prevText          : 'Mese precedente (Ctrl+Sinistra)',
      monthYearText     : 'Scegli un mese (Ctrl+Su/Giu per cambiare anno)',
      todayTip          : "{0} (Barra spaziatrice)",
      format            : "d/m/y"
   });
}

if(Ext.PagingToolbar){
   Ext.apply(Ext.PagingToolbar.prototype, {
      beforePageText : "Pagina",
      afterPageText  : "di {0}",
      firstText      : "Prima pagina",
      prevText       : "Pagina precedente",
      nextText       : "Pagina successiva",
      lastText       : "Ultima pagina",
      refreshText    : "Aggiorna",
      displayMsg     : "Vista {0} - {1} di {2}",
      emptyMsg       : 'Nessun dato da mostrare'
   });
}

if(Ext.form.TextField){
   Ext.apply(Ext.form.TextField.prototype, {
      minLengthText : "La lunghezza minima del campo risulta {0}",
      maxLengthText : "La lunghezza massima del campo risulta {0}",
      blankText     : "Campo obbligatorio",
      regexText     : "",
      emptyText     : null
   });
}

if(Ext.form.NumberField){
   Ext.apply(Ext.form.NumberField.prototype, {
      minText : "Il valore minimo del campo risulta {0}",
      maxText : "Il valore massimo del campo risulta {0}",
      nanText : "{0} non &grave; un numero corretto"
   });
}

if(Ext.form.DateField){
   Ext.apply(Ext.form.DateField.prototype, {
      disabledDaysText  : "Disabilitato",
      disabledDatesText : "Disabilitato",
      minText           : "La data del campo deve essere successiva a {0}",
      maxText           : "La data del campo deve essere precedente a {0}",
      invalidText       : "{0} non &grave; una data valida. Deve essere nel formato {1}",
      format            : "d/m/y"
   });
}

if(Ext.form.ComboBox){
   Ext.apply(Ext.form.ComboBox.prototype, {
      loadingText       : "Caricamento in corso...",
      valueNotFoundText : undefined
   });
}

if(Ext.form.VTypes){
   Ext.apply(Ext.form.VTypes, {
      emailText    : 'Il campo deve essere un indirizzo e-mail nel formato "user@domain.com"',
      urlText      : 'Il campo deve essere un indirizzo web nel formato "http:/'+'/www.domain.com"',
      alphaText    : 'Il campo deve contenere solo lettere e _',
      alphanumText : 'Il campo deve contenere solo lettere, numeri e _'
   });
}

if(Ext.grid.GridView){
   Ext.apply(Ext.grid.GridView.prototype, {
      sortAscText  : "Ordinamento crescente",
      sortDescText : "Ordinamento decrescente",
      lockText     : "Blocca colonna",
      unlockText   : "Sblocca colonna",
      columnsText  : "Colonne"
   });
}

if(Ext.grid.PropertyColumnModel){
   Ext.apply(Ext.grid.PropertyColumnModel.prototype, {
      nameText   : "Nome",
      valueText  : "Valore",
      dateFormat : "j/m/Y"
   });
}

if(Ext.SplitLayoutRegion){
   Ext.apply(Ext.SplitLayoutRegion.prototype, {
      splitTip            : "Trascina per cambiare dimensioni.",
      collapsibleSplitTip : "Trascina per cambiare dimensioni. Doppio click per nascondere."
   });
}

