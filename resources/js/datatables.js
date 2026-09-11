// resources/js/datatables.js

table = $('#table-1').DataTable( {
    paging: false
} );

table.destroy();

table = $('#table-1').DataTable( {
    searching: false
} );

// Menginisialisasi DataTable yang baru
$(document).ready(function() {
    $('#table-1').DataTable();
});

$('#table-1').dataTable( {
    paging: false,
    searching: false
} );

