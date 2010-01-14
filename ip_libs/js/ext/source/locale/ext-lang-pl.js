/*
 * Ext JS Library 1.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */

/**
 * Polish Translations
 * By vbert 17-April-2007
 * Encoding: utf-8
 */

Ext.UpdateManager.defaults.indicatorText = '<div class="loading-indicator">Wczytywanie danych...</div>';

if(Ext.View){
   Ext.View.prototype.emptyText = "";
}

if(Ext.grid.Grid){
   Ext.grid.Grid.prototype.ddText = "{0} wybrano wiersze(y)";
}

if(Ext.TabPanelItem){
   Ext.TabPanelItem.prototype.closeText = "Zamknij zakładkę";
}

if(Ext.form.Field){
   Ext.form.Field.prototype.invalidText = "Wartość tego pola jest niewłaściwa";
}

if(Ext.LoadMask){
    Ext.LoadMask.prototype.msg = "Wczytywanie danych...";
}

Date.monthNames = [
    "Styczeń",
    "Luty",
    "Marzec",
    "Kwiecień",
    "Maj",
    "Czerwiec",
    "Lipiec",
    "Sierpień",
    "Wrzesień",
    "Październik",
    "Listopad",
    "Grudzień"
];

Date.dayNames = [
    "Niedziela",
    "Poniedziałek",
    "Wtorek",
    "Środa",
    "Czwartek",
    "Piątek",
    "Sobota"
];

if(Ext.MessageBox){
   Ext.MessageBox.buttonText = {
      ok     : "OK",
      cancel : "Anuluj",
      yes    : "Tak",
      no     : "Nie"
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
		startDay			: 1,
		todayText			: "Dzisiaj",
		minText				: "Data jest wcześniejsza od daty minimalnej",
		maxText				: "Data jest późniejsza od daty maksymalnej",
		disabledDaysText	: "",
		disabledDatesText	: "",
		monthNames			: Date.monthNames,
		dayNames			: Date.dayNames,
		nextText			: "Następny miesiąc (Control+StrzałkaWPrawo)",
		prevText			: "Poprzedni miesiąc (Control+StrzałkaWLewo)",
		monthYearText		: "Wybierz miesiąc (Control+Up/Down aby zmienić rok)",
		todayTip			: "{0} (Spacja)",
		format				: "Y-m-d"
	});
}

if(Ext.PagingToolbar){
	Ext.apply(Ext.PagingToolbar.prototype, {
		beforePageText	: "Strona",
		afterPageText	: "z {0}",
		firstText		: "Pierwsza strona",
	    prevText		: "Poprzednia strona",
		nextText		: "Następna strona",
	    lastText		: "Ostatnia strona",
		refreshText		: "Odśwież",
	    displayMsg		: "Wyświetlono {0} - {1} z {2}",
		emptyMsg		: "Brak danych do wyświetlenia"
	});
}

if(Ext.form.TextField){
	Ext.apply(Ext.form.TextField.prototype, {
	    minLengthText	: "Minimalna ilość znaków dla tego pola to {0}",
		maxLengthText	: "Maksymalna ilość znaków dla tego pola to {0}",
	    blankText		: "To pole jest wymagane",
		regexText		: "",
	    emptyText		: null
	});
}

if(Ext.form.NumberField){
	Ext.apply(Ext.form.NumberField.prototype, {
	    minText	: "Minimalna wartość dla tego pola to {0}",
	    maxText	: "Maksymalna wartość dla tego pola to {0}",
		nanText	: "{0} to nie jest właściwa wartość"
	});
}

if(Ext.form.DateField){
	Ext.apply(Ext.form.DateField.prototype, {
	    disabledDaysText	: "Wyłączony",
	    disabledDatesText	: "Wyłączony",
		minText				: "Data w tym polu musi być późniejsza od {0}",
	    maxText				: "Data w tym polu musi być wcześniejsza od {0}",
		invalidText			: "{0} to nie jest prawidłowa data - prawidłowy format daty {1}",
	    format				: "Y-m-d"
	});
}

if(Ext.form.ComboBox){
	Ext.apply(Ext.form.ComboBox.prototype, {
		loadingText       : "Wczytuję...",
		valueNotFoundText : undefined
	});
}

if(Ext.form.VTypes){
	Ext.apply(Ext.form.VTypes, {
	    emailText		: 'To pole wymaga podania adresu e-mail w formacie: "nazwa@domena.pl"',
	    urlText			: 'To pole wymaga podania adresu strony www w formacie: "http:/'+'/www.domena.pl"',
		alphaText		: 'To pole wymaga podania tylko liter i _',
		alphanumText	: 'To pole wymaga podania tylko liter, cyfr i _'
	});
}

if(Ext.grid.GridView){
	Ext.apply(Ext.grid.GridView.prototype, {
	    sortAscText		: "Sortuj rosnąco",
	    sortDescText	: "Sortuj malejąco",
		lockText		: "Zablokuj kolumnę",
	    unlockText		: "Odblokuj kolumnę",
		columnsText		: "Kolumny"
	});
}

if(Ext.grid.PropertyColumnModel){
	Ext.apply(Ext.grid.PropertyColumnModel.prototype, {
	    nameText	: "Nazwa",
	    valueText	: "Wartość",
		dateFormat	: "Y-m-d"
	});
}

if(Ext.SplitLayoutRegion){
	Ext.apply(Ext.SplitLayoutRegion.prototype, {
	    splitTip			: "Przeciągnij aby zmienić rozmiar.",
		collapsibleSplitTip	: "Przeciągnij aby zmienić rozmiar. Kliknij dwukrotnie aby ukryć."
	});
}
