//Search in blocks
jQuery('.reports-search').keyup(function( event ){
    var searchStr = jQuery(this).val();
    var values = jQuery(this).parent().parent().find('li.campaigns-li, li.clients-li, li.report-li');
    if(searchStr !== ""){
        values.addClass('hidden');
        values.each(function(){
           //console.log(jQuery(this).text().toLowerCase().replace(' ', ''));
           if(jQuery(this).text().toLowerCase().replace(' ', '').search(searchStr.toLowerCase().replace(' ', '')) !== -1){
                jQuery(this).removeClass('hidden'); 
            }
        });
    }else{
       values.removeClass('hidden');
    }
}); 



//select multiple reports
jQuery('li.report-li a').click(function( event ){
    event.preventDefault();
});
jQuery('li.report-li').click(function(){
   jQuery(this).toggleClass('active');
   if(jQuery('.raports-list li.active').length){
       jQuery('.report-buttons-strip .btn-disabled').removeClass('btn-disabled').prop('disabled', false);
       jQuery('.report-buttons-strip .btn:not(.btn-primary)').addClass('btn-primary');
   }else{
       jQuery('.report-buttons-strip .btn-primary').removeClass('btn-primary');
       jQuery('.report-buttons-strip .btn:not(.btn-disabled)').addClass('btn-disabled').prop('disabled', true);
   }
});

jQuery('.get-report').click(function() {
   var url = location.href;
   var report = jQuery('li.report-li.active').children('a').attr('href');
   var start = jQuery('#startDate').val();
   var end = jQuery('#endDate').val();
   
   location.href = url + '&report=' + report + '&from=' + start + '&to=' + end;
});