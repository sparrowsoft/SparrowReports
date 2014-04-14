jQuery(document).ready(function(){
       
    
   jQuery('#form_password').blur(function(){
        if($('#form_password').val() !== ""){
            $('#form_repeat_password').attr('required', 'required');
        }else{
            $('#form_repeat_password').removeAttr('required');
        }   
   });
   
   
   
   jQuery('.modal-delete-user').click(function(){
      jQuery('#questionModal button.user-name-modal').html($(this).parent().parent().children('.user-name').html());
   });
});
