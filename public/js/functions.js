$(function() {
    $('#apiData').DataTable( {
        ajax: '/ajax/fetch-api-data'
    } );
});
