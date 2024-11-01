jQuery(document).ready(function(jQuery) {
	jQuery(".wplyricslink").show();
	jQuery(".wplyrics").hide();
});


function open_lyrics(el){
 	jQuery(el).parent().next('.wplyrics').slideToggle() ;
 	more = WPLyricsSettings.more;
 	less = WPLyricsSettings.less;
	symb1 = WPLyricsSettings.symbol1;
	symb2 = WPLyricsSettings.symbol2; 
	//change [+]
	if (jQuery(el).html() == symb1){
		jQuery(el).html(symb2);
		jQuery(el).attr('title', less);	
	}else{
		jQuery(el).html(symb1).attr('title', more);
	}
}