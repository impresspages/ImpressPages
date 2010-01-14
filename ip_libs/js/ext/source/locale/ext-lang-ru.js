/*
 * Ext JS Library 1.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */

/*
 * Russian translation
 * By Arikon (utf-8 encoding)
 * 08 April 2007
 */

Ext.UpdateManager.defaults.indicatorText = '<div class="loading-indicator">Идет загрузка...</div>';

if(Ext.View){
   Ext.View.prototype.emptyText = "";
}

if(Ext.grid.Grid){
   Ext.grid.Grid.prototype.ddText = "{0} выбранных строк";
}

if(Ext.TabPanelItem){
   Ext.TabPanelItem.prototype.closeText = "Закрыть эту вкладку";
}

if(Ext.form.Field){
   Ext.form.Field.prototype.invalidText = "Значение в этом поле неверное";
}

Date.monthNames = [
   "Январь",
   "Февраль",
   "Март",
   "Апрель",
   "Май",
   "Июнь",
   "Июль",
   "Август",
   "Сентябрь",
   "Октябрь",
   "Ноябрь",
   "Декабрь"
];

Date.dayNames = [
   "Воскресенье",
   "Понедельник",
   "Вторник",
   "Среда",
   "Четверг",
   "Пятница",
   "Суббота"
];

if(Ext.MessageBox){
   Ext.MessageBox.buttonText = {
      ok     : "OK",
      cancel : "Отмена",
      yes    : "Да",
      no     : "Нет"
   };
}

if(Ext.util.Format){
   Ext.util.Format.date = function(v, format){
      if(!v) return "";
      if(!(v instanceof Date)) v = new Date(Date.parse(v));
      return v.dateFormat(format || "d.m.Y");
   };
}

if(Ext.DatePicker){
   Ext.apply(Ext.DatePicker.prototype, {
      todayText         : "Сегодня",
      minText           : "Эта дата раньше минимальной даты",
      maxText           : "Эта дата позже максимальной даты",
      disabledDaysText  : "",
      disabledDatesText : "",
      monthNames        : Date.monthNames,
      dayNames	        : Date.dayNames,
      nextText          : 'Следующий месяц (Control+Вправо)',
      prevText          : 'Предыдущий месяц (Control+Влево)',
      monthYearText     : 'Выбор месяца (Control+Вверх/Вниз для выбора года)',
      todayTip          : "{0} (Пробел)",
      format            : "d.m.y",
      startDay          : 1
   });
}

if(Ext.PagingToolbar){
   Ext.apply(Ext.PagingToolbar.prototype, {
      beforePageText : "Страница",
      afterPageText  : "из {0}",
      firstText      : "Первая страница",
      prevText       : "Предыдущая страница",
      nextText       : "Следующая страница",
      lastText       : "Последняя страница",
      refreshText    : "Обновить",
      displayMsg     : "Отображаются записи с {0} по {1}, всего {2}",
      emptyMsg       : 'Нет данных для отображения'
   });
}

if(Ext.form.TextField){
   Ext.apply(Ext.form.TextField.prototype, {
      minLengthText : "Минимальная длина этого поля {0}",
      maxLengthText : "Максимальная длина этого поля {0}",
      blankText     : "Это поле обязательно для заполнения",
      regexText     : "",
      emptyText     : null
   });
}

if(Ext.form.NumberField){
   Ext.apply(Ext.form.NumberField.prototype, {
      minText : "Значение этого поля не может быть меньше {0}",
      maxText : "Значение этого поля не может быть больше {0}",
      nanText : "{0} не является числом"
   });
}

if(Ext.form.DateField){
   Ext.apply(Ext.form.DateField.prototype, {
      disabledDaysText  : "Не доступно",
      disabledDatesText : "Не доступно",
      minText           : "Дата в этом поле должна быть позде {0}",
      maxText           : "Дата в этом поле должна быть раньше {0}",
      invalidText       : "{0} не является правильной датой - дата должна быть указана в формате {1}",
      format            : "d.m.y"
   });
}

if(Ext.form.ComboBox){
   Ext.apply(Ext.form.ComboBox.prototype, {
      loadingText       : "Загрузка...",
      valueNotFoundText : undefined
   });
}

if(Ext.form.VTypes){
   Ext.apply(Ext.form.VTypes, {
      emailText    : 'Это поле должно содержать адрес электронной почты в формате "user@domain.com"',
      urlText      : 'Это поле должно содержать URL в формате "http:/'+'/www.domain.com"',
      alphaText    : 'Это поле должно содержать только латинские буквы и символ подчеркивания "_"',
      alphanumText : 'Это поле должно содержать только латинские буквы, цифры и символ подчеркивания "_"'
   });
}

if(Ext.grid.GridView){
   Ext.apply(Ext.grid.GridView.prototype, {
      sortAscText  : "Сортировать по возрастанию",
      sortDescText : "Сортировать по убыванию",
      lockText     : "Закрепить столбец",
      unlockText   : "Снять закрепление столбца",
      columnsText  : "Столбцы"
   });
}

if(Ext.grid.PropertyColumnModel){
   Ext.apply(Ext.grid.PropertyColumnModel.prototype, {
      nameText   : "Название",
      valueText  : "Значение",
      dateFormat : "j.m.Y"
   });
}

if(Ext.SplitLayoutRegion){
   Ext.apply(Ext.SplitLayoutRegion.prototype, {
      splitTip            : "Тяните для изменения размера.",
      collapsibleSplitTip : "Тяните для изменения размера. Двойной щелчок спрячет панель."
   });
}
