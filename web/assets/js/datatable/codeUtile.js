///addRow
$(document).ready(function() {
    let table = $('#example').DataTable();
    let counter = 1;
    let trash = '<i class="fas fa-trash-alt"></i>';
    let update = '<i class="fas fa-check update"></i>';


    $('#addRow').on( 'click', function () {
        table.row.add( [
            counter,
            'F',
            'G',
            '',
            '',
            '',
            '',
            update,
            trash
        ] ).draw( false );

        counter++;
    } );

    // Automatically add a first row of data
    //$('#addRow').click();
} );

//deleteRow
$(document).ready(function() {
    var table = $('#example').DataTable();

    $('#example tbody').on( 'click', '.trash', function () {
        let brand = $(this).closest('tr').find("td:nth-child(1)").html();
        let txt;
        let r = confirm("Etes-vous sûr de vouloir supprimer : \n"+ brand +' ?');
        if (r === true) {
            table
                .row( $(this).parents('tr') )
                .remove()
                .draw();
        } else {
            txt = "opération annulée!";
        }
        document.getElementById("titre").innerHTML = txt;

    } );
} );/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



//delete object in DB
$('.trash').on( "click", function(){
    //$(this).closest('tr').remove();
    var personnage = $(this).closest('tr').find("td:nth-child(0)").html();

    $( '#var' ).text(personnage);
});

//update object in DB
$('.update').on( "click", function(){
    //$(this).closest('tr').remove();
    var personnage = $(this).closest('tr').find("td:nth-child(1)").html();

    $( '#producteur' ).text(personnage);
});



//Event handlers
$(".editFor" ).on( "mouseenter", function(){
    // $( this).css( "background-color", "#C6E2FF");
    //$('html,body').css('cursor','crosshair');
    $(this).attr('contenteditable', 'true');
});
$(".editFor" ).on( "mouseleave", function(){
    //$( this).css( "background-color", "transparent");
    //$('html,body').css('cursor','crosshair');
    $(this).attr('contenteditable', 'false');
});
$(".editFor" ).on( "keypress", function(){
    //$('html,body').css('cursor','crosshair');
    $(this).text('');
});

$(".editFor" ).on( "mouseleave", function(){
    //$( this).css( "background-color", "transparent");
    //$('html,body').css('cursor','crosshair');
    $(this).attr('contenteditable', 'false');
});
$(".editFor" ).on( "keypress", function(){
    //$('html,body').css('cursor','crosshair');
    $(this).text('');
});



//delete object in DB
$('.trash').on( "click", function(){
    //$(this).closest('tr').remove();
    var personnage = $(this).closest('tr').find("td:nth-child(0)").html();

    $( '#var' ).text(personnage);
});

//update object in DB
$('.update').on( "click", function(){
    //$(this).closest('tr').remove();
    var personnage = $(this).closest('tr').find("td:nth-child(1)").html();

    $( '#producteur' ).text(personnage);
});





