function show_flash(w, h, swf, color, fvar)
{
    document.write("<object type='application/x-shockwave-flash' data='"+swf+"' width='"+w+"' height='"+h+"'>");
    document.write("<param name='movie' value='"+swf+"' />");
    document.write("<param name='pluginurl' value='http://www.macromedia.com/go/getflashplayer' />");
    document.write("<!-- <param name='wmode' value='transparent' /> -->");
    document.write("<param name='bgcolor' value='"+color+"' />");
    document.write("<param name='menu' value='false' />");
    document.write("<param name='quality' value='best' />"); 
    document.write("<param name='scale' value='exactfit' />"); 
    document.write("<param name='flashvars' value='"+fvar+"' />");
    document.write("</object>");
}