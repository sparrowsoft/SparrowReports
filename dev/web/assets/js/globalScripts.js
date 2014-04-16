jQuery(document).ready(function(){
       
    
   jQuery('#form_password').blur(function(){
        if($('#form_password').val() !== ""){
            $('#form_repeat_password').attr('required', 'required');
        }else{
            $('#form_repeat_password').removeAttr('required');
        }   
   });
   
   
   //deleting user modal
   jQuery('.modal-delete-user').click(function(){
      jQuery('#questionModal button.user-name-modal').html($(this).parent().parent().children('.user-name').html());
      
      var userId = jQuery(this).attr('data-user');
      var deletePath = jQuery('#questionModal .btn-warning').attr('onclick');
      
      console.log(jQuery('#questionModal .btn-warning').attr('onclick', "location.href='"+deletePath+"&id="+userId+"'"));
   });
   
   
   $(".report-colum").height($(".report-colum").eq(0).height());
   
   $('.datepicker').datepicker({
       format: 'yyyy-mm-dd'
   });
   
    $('#myTabContent').slimScroll({
        height: '250px'
   });
});
