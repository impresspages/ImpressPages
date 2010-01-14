/*
 * Ext JS Library 1.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */

/**
 * Greek translation
 * By thesilentman (utf8 encoding)
 * 06 May 2007
 */

Ext.UpdateManager.defaults.indicatorText = '<div class="loading-indicator">Μεταφόρτωση δεδομένων...</div>';

if(Ext.View){
   Ext.View.prototype.emptyText = "";
}

if(Ext.grid.Grid){
   Ext.grid.Grid.prototype.ddText = "{0} Επιλεγμένες σειρές";
}

if(Ext.TabPanelItem){
   Ext.TabPanelItem.prototype.closeText = "Κλείστε το tab";
}

if(Ext.form.Field){
   Ext.form.Field.prototype.invalidText = "Το περιεχόμενο του πεδίου δεν είναι αποδεκτό";
}

if(Ext.LoadMask){
    Ext.LoadMask.prototype.msg = "Μεταφόρτωση δεδομένων...";
}

Date.monthNames = [
   "Ιανουάριος",
   "Φεβρουάριος",
   "Μάρτιος",
   "Απρίλιος",
   "Μάιος",
   "Ιούνιος",
   "Ιούλιος",
   "Αύγουστος",
   "Σεπτέμβριος",
   "Οκτώβριος",
   "Νοέμβριος",
   "Δεκέμβριος"
];

Date.dayNames = [
   "Κυριακή",
   "Δευτέρα",
   "Τρίτη",
   "Τετάρτη",
   "Πέμπτη",
   "Παρασκευή",
   "Σάββατο"
];

if(Ext.MessageBox){
   Ext.MessageBox.buttonText = {
      ok     : "OK",
      cancel : "Άκυρο",
      yes    : "Ναι",
      no     : "Όχι"
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
      todayText         : "Σήμερα",
      minText           : "Η Ημερομηνία είναι προηγούμενη από την παλαιότερη αποδεκτή",
      maxText           : "Η Ημερομηνία είναι μεταγενέστερη από την νεότερη αποδεκτή",
      disabledDaysText  : "",
      disabledDatesText : "",
      monthNames	: Date.monthNames,
      dayNames		: Date.dayNames,
      nextText          : 'Επόμενος Μήνας (Control+Δεξί Βέλος)',
      prevText          : 'Προηγούμενος Μήνας (Control + Αριστερό Βέλος)',
      monthYearText     : 'Επιλογή Μηνός (Control + Επάνω/Κάτω Βέλος για μεταβολή ετών)',
      todayTip          : "{0} (ΠΛήκτρο Διαστήματος)",
      format            : "d/m/y"
   });
}

if(Ext.PagingToolbar){
   Ext.apply(Ext.PagingToolbar.prototype, {
      beforePageText : "Σελίδα",
      afterPageText  : "από {0}",
      firstText      : "Πρώτη Σελίδα",
      prevText       : "Προηγούμενη Σελίδα",
      nextText       : "Επόμενη Σελίδα",
      lastText       : "Τελευταία Σελίδα",
      refreshText    : "Ανανέωση",
      displayMsg     : "Εμφάνιση {0} - {1} από {2}",
      emptyMsg       : 'Δεν υπάρχουν δεδομένα'
   });
}

if(Ext.form.TextField){
   Ext.apply(Ext.form.TextField.prototype, {
      minLengthText : "Το μικρότερο αποδεκτό μήκος για το πεδίο είναι {0}",
      maxLengthText : "Το μεγαλύτερο αποδεκτό μήκος για το πεδίο είναι {0}",
      blankText     : "Το πεδίο έιναι υποχρεωτικό",
      regexText     : "",
      emptyText     : null
   });
}

if(Ext.form.NumberField){
   Ext.apply(Ext.form.NumberField.prototype, {
      minText : "Η μικρότερη τιμή του πεδίου είναι {0}",
      maxText : "Η μεγαλύτερη τιμή του πεδίου είναι {0}",
      nanText : "{0} δεν είναι αποδεκτός αριθμός"
   });
}

if(Ext.form.DateField){
   Ext.apply(Ext.form.DateField.prototype, {
      disabledDaysText  : "Ανενεργό",
      disabledDatesText : "Ανενεργό",
      minText           : "Η ημερομηνία αυτού του πεδίου πρέπει να είναι μετά τη {0}",
      maxText           : "Η ημερομηνία αυτού του πεδίου πρέπει να είναι πριν της {0}",
      invalidText       : "{0} δεν είναι έγκυρη ημερομηνία - πρέπει να είναι στη μορφή {1}",
      format            : "d/m/y"
   });
}

if(Ext.form.ComboBox){
   Ext.apply(Ext.form.ComboBox.prototype, {
      loadingText       : "Μεταφόρτωση δεδομένων...",
      valueNotFoundText : undefined
   });
}

if(Ext.form.VTypes){
   Ext.apply(Ext.form.VTypes, {
      emailText    : 'Το πεδίο δέχεται μόνο διευθύνσεις Email σε μορφή "user@domain.com"',
      urlText      : 'Το πεδίο δέχεται μόνο URL σε μορφή "http:/'+'/www.domain.com"',
      alphaText    : 'Το πεδίο δέχεται μόνο χαρακτήρες και _',
      alphanumText : 'Το πεδίο δέχεται μόνο χαρακτήρες, αριθμούς και _'
   });
}

if(Ext.grid.GridView){
   Ext.apply(Ext.grid.GridView.prototype, {
      sortAscText  : "Αύξουσα ταξινόμηση",
      sortDescText : "Φθίνουσα ταξινόμηση",
      lockText     : "Κλείδωμα στήλης",
      unlockText   : "Ξεκλείδωμα στήλης",
      columnsText  : "Στήλες"
   });
}

if(Ext.grid.PropertyColumnModel){
   Ext.apply(Ext.grid.PropertyColumnModel.prototype, {
      nameText   : "Όνομα",
      valueText  : "Περιεχόμενο",
      dateFormat : "m/d/Y"
   });
}

if(Ext.SplitLayoutRegion){
   Ext.apply(Ext.SplitLayoutRegion.prototype, {
      splitTip            : "Τραβήξτε για αλλαγή μεγέθους.",
      collapsibleSplitTip : "Τραβήξτε για αλλαγή μεγέθους. Διπλό κλικ για απόκρυψη."
   });
}
