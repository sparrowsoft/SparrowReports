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
   jQuery('li.report-li').removeClass('active');
   jQuery(this).addClass('active');
   jQuery('.btn.get-report').removeAttr('disabled').removeClass('btn-disabled')
           .addClass('btn-primary');
});

//create url
jQuery('.get-report').click(function() {
   var url = location.href;
   var report = jQuery('li.report-li.active').children('a').attr('href');
   var start = jQuery('#startDate').val();
   var end = jQuery('#endDate').val();
   
   url = url.split('&report');
   
   if ( jQuery('.leads-input').hasClass('visible') ) {
       var leads = jQuery('.leads-input input').val();
       location.href = url[0] + '&report=' + report + '&leads=' + (leads > 0 ? leads : 0);
   } else {
        location.href = url[0] + '&report=' + report + '&from=' + start + '&to=' + end;
   }
});

//button yesterday
jQuery('.btn-wczoraj').click(function(){
    jQuery('.btn-wczoraj').toggleClass('btn-info');
    console.log(jQuery('.btn-wczoraj').text());
    if(jQuery('.btn-wczoraj').text() === 'Wczoraj'){
       jQuery('.btn-wczoraj').text('Dzisiaj');
       
       jQuery('#startDate').datepicker('setValue', jQuery('.btn-wczoraj').attr('data-date'));
       jQuery('#endDate').datepicker('setValue', jQuery('.btn-wczoraj').attr('data-date'));
    }else{
        jQuery('.btn-wczoraj').text('Wczoraj');
        jQuery('#startDate').datepicker('setValue', jQuery('#startDate').attr('value'));
        jQuery('#endDate').datepicker('setValue', jQuery('#startDate').attr('value'));
    }
});

jQuery('.leads-report').click(function() {
    jQuery('.leads-input').show().addClass('visible');
});