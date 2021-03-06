jQuery(document).ready(function() {
    jQuery('#form_password').blur(function() {
        if ($('#form_password').val() !== "") {
            $('#form_repeat_password').attr('required', 'required');
        } else {
            $('#form_repeat_password').removeAttr('required');
        }
    });

    //deleting user modal
    jQuery('.modal-delete-user').click(function() {
        jQuery('#questionModal button.user-name-modal').html($(this).parent().parent().children('.user-name').html());
        var userId = jQuery(this).attr('data-user');
        var deletePath = jQuery('#questionModal .btn-warning').attr('onclick');
        console.log(jQuery('#questionModal .btn-warning').attr('onclick', "location.href='" + deletePath + "&id=" + userId + "'"));
    });

    $(".report-colum").height(400);
    
    $('#myTabContent').slimScroll({
        height: '220px'
    });
    
    $('.scrolling').slimScroll({
        height: '280px'
    });
    
    $('[data-toggle="tooltip"]').tooltip();
    
    if ( $('body').hasClass('schedule') ) {
        var date = new Date();
	var d = date.getDate();
	var m = date.getMonth();
	var y = date.getFullYear();
		
	$('#schedule').fullCalendar({
            editable: true,
            events: "/src/Reports/DashboardBundle/Controller/EventsController.php",
            dayClick: function() {
                $('#addSchift').modal('show');
                var date = $(this).data('date');
                $('#schedule-date').val(date);
            }
	});
    }
});

var tableToExcel = (function() {
  var uri = 'data:application/vnd.ms-excel;base64,'
    , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--><meta http-equiv="content-type" content="text/plain; charset=UTF-8"/></head><body><table>{table}</table></body></html>'
    , base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
    , format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
  return function(table, name, element) {
    if (!table.nodeType) table = document.getElementById(table)
    var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}
    element.href = uri + base64(format(template, ctx));
  }
})()


jQuery('.report-li button').click(function(event){
    jQuery(this).toggleClass('faved');
    //jQuery(this).toggleClass('disabled');
    event.stopPropagation();
});

//datepicker fix
var myPicker = $('.datepicker').datepicker({format: 'yyyy-mm-dd'});
var inputs = jQuery('.form-control.datepicker');
var datepickers = jQuery(".datepicker.dropdown-menu");

inputs.last().mousedown(function(){
   datepickers.eq(0).hide();
});

inputs.eq(0).mousedown(function(){
    datepickers.last().hide();
});

function addSchedule(e) {
    var scheduleText = $('#addSchift textarea').val();
    
    if ( !scheduleText ) {
        $('#schedule-text').addClass('has-error');
    } else {
        var time_start = $('#time-start').val() + ':00';
        var time_end = $('#time-end').val() + ':00';
        var date = $('#schedule-date').val();
        
        var url = location.href.split('?');
        location.href = url[0] + '?add&date=' + date + '&timestart=' + time_start + '&timeend=' + time_end + '&text=' + scheduleText;
    }
}