function trim(myString){
	return myString.replace(/^\s+/g,'').replace(/\s+$/g,'')
}

var videoWidth = 480;
var videoHeight = 390;
var pluginUrl = CKEDITOR.plugins.getPath('Video');

CKEDITOR.dialog.add('Video',function(a) {
	
	// Youtube
    youtube = {type: 'html'};
    youtube.html = '<div style="margin:0 auto;width:280px"><img src="'+CKEDITOR.getUrl(pluginUrl+'images/youtube_large.jpg')+'" /></div><br /><br /><h2 style="text-align:center;font-size:14px;font-weight:bold">Entrez l\'ID de la vidéo YouTube</h2><p style="text-align:center">( ex. : http://www.youtube.com/watch?v=<span style="color:#f00;font-weight:bold">kXhy7ZsiR50</span> )</p><br /><br /><p style="text-align:center;font-weight:bold">ID : <input id="youtube" size="10" style="border:1px solid gray;background:white"></p>';
    
	// Dailymotion
	dailymotion = {type: 'html'};
    dailymotion.html = '<div style="margin:0 auto;width:280px"><img src="'+CKEDITOR.getUrl(pluginUrl+'images/dailymotion_large.jpg')+'" /></div><br /><br /><h2 style="text-align:center;font-size:14px;font-weight:bold">Entrez l\'ID de la vidéo Dailymotion</h2><p style="text-align:center">( ex. : http://www.dailymotion.com/swf/video/<span style="color:#f00;font-weight:bold">xjc80p</span> )</p><br /><br /><p style="text-align:center;font-weight:bold">ID : <input id="dailymotion" size="10" style="border:1px solid gray;background:white"></p>';
	
    return {
        
		title: "Insérer votre id de vidéo",
        
		onOk: function() {
			
			var inputYoutube = trim(document.getElementById('youtube').value);
			var inputDailymotion = trim(document.getElementById('dailymotion').value);
			
			// Youtube
			if (inputYoutube != '') {
				var objectYoutube = '<div style="text-align:center"><object width="'+videoWidth+'" height="'+videoHeight+'"><param name="movie" value="http://www.youtube.com/v/'+inputYoutube+'"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/'+inputYoutube+'" type="application/x-shockwave-flash" width="'+videoWidth+'" height="'+videoHeight+'" allowscriptaccess="always" allowfullscreen="true"></embed></object></div>';
				this.getParentEditor().insertHtml(objectYoutube);
			}
			
			// Dailymotion
			else if (inputDailymotion != '') {
				var objectDailymotion = '<div style="text-align:center"><object width="'+videoWidth+'" height="'+videoHeight+'"><param name="movie" value="http://www.dailymotion.com/swf/video/'+inputDailymotion+'"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.dailymotion.com/swf/video/'+inputDailymotion+'" type="application/x-shockwave-flash" width="'+videoWidth+'" height="'+videoHeight+'" allowscriptaccess="always" allowfullscreen="true"></embed></object></div>';
				this.getParentEditor().insertHtml(objectDailymotion); 
			}

        },
        contents: [
        {
            id: 'Tab1',
            label: '<img src="'+CKEDITOR.getUrl(pluginUrl+'images/youtube.png')+'" /> Youtube',
            elements: [youtube]
        },
        {
            id: 'Tab2',
            label: '<img src="'+CKEDITOR.getUrl(pluginUrl+'images/dailymotion.gif')+'" /> Dailymotion',
			elements: [dailymotion]
        }
        ],
        buttons: [CKEDITOR.dialog.okButton, CKEDITOR.dialog.cancelButton]
    };
});




