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

    $(".report-colum").height($(".report-colum").eq(0).height());

    $('.datepicker').datepicker({
        format: 'yyyy-mm-dd'
    });

    $('#myTabContent').slimScroll({
        height: '220px'
    });
});

var tableToExcel = (function() {
  var uri = 'data:application/vnd.ms-excel;base64,'
    , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--><meta http-equiv="content-type" content="text/plain; charset=UTF-8"/></head><body><table>{table}</table></body></html>'
    , base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
    , format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
  return function(table, name) {
    if (!table.nodeType) table = document.getElementById(table)
    var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}
    window.location.href = uri + base64(format(template, ctx))
  }
})()
