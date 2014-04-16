jQuery('.reports-search').keyup(function( event ){
    var searchStr = jQuery(this).val();
    var values = jQuery(this).parent().find('li.campaigns-li, li.clients-li');
    if(searchStr !== ""){
        values.addClass('hidden');
        values.each(function(){
           console.log(jQuery(this).text().toLowerCase().replace(' ', ''));
           if(jQuery(this).text().toLowerCase().replace(' ', '').search(searchStr.toLowerCase().replace(' ', '')) !== -1){
                jQuery(this).removeClass('hidden'); 
            }
        });
    }else{
       values.removeClass('hidden');
    }
}); 