$(document).ready(function() {
    var table = $('#example').DataTable( {
        scrollX: true,
        scrollCollapse: true
        
    } );
 
    new $.fn.dataTable.FixedColumns( table, {
        leftColumns: 6
        
    } );
} );