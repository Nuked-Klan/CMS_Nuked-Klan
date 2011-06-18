(function(){var dailymotionCmd={exec:function(editor){editor.openDialog('dailymotion');return}};
CKEDITOR.plugins.add('dailymotion',{lang:['en','uk', 'fr'],requires:['dialog'],
	init:function(editor){var commandName='dailymotion';editor.addCommand(commandName,dailymotionCmd);
				editor.ui.addButton('Dailymotion',{label:editor.lang.dailymotion.button,command:commandName,icon:this.path+"images/dailymotion.gif"});
				CKEDITOR.dialog.add(commandName,CKEDITOR.getUrl(this.path+'dialogs/dailymotion.js'))}})})();
