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


//Check
/*$('.editFor').on( "keyup", function(){
    var personnage = $(this).html();
    $.ajax({
        url:'/ajax_request',
        type: "POST",
        dataType: "json",
        data: {
            "personnage": personnage
        },
        async: true,
        success: function (data)
        {
            console.log(data);
            $( '#titre' ).text(data.titre);
            $( '#producteur' ).text(data.producteur);
        }
    });
});
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

//sendAgendaControlleur
/*$('.editFor').on( "keyup", function(){
    var id = $(this).attr('id');
    var letter = $(this).html();
    $.ajax({
        url:'/agendaEdit',
        type: "POST",
        dataType: "json",
        data: {
            "id": id,
            "letter": letter
        },
        async: true,
        success: function (data)
        {
            console.log(data);
            $( '#titre' ).text(data.titre);
            $( '#description' ).text(data.description);
        }
    });
});*/

//sendAgendaTempControlleur
$('.editFor').on( "change paste keyup", function(){
    var id = $(this).attr('id');
    var letter = $(this).html();
    $.ajax({
        url:'/agendaTemp/edit',
        type: "POST",
        dataType: "json",
        data: {
            "id": id,
            "letter": letter
        },
        async: true,
        success: function (data)
        {
            console.log(data);

            $( '#titre').text(data.titre);
            $( '#description').text(data.description);
            $( '#startLegalWeek').text(data.startLegalWeek);
            $( '#endLegalWeek').text(data.endLegalWeek);
            $( '#startDay').text(data.startDay);
            $( '#endDay').text(data.endDay);
            $( '#hoursPerWeek').text(data.hoursPerWeek);
            $( '#intervalBefore').text(data.intervalBefore);
            $( '#intervalAfter').text(data.intervalAfter);
            $( '#letter').text(data.interval);
            $( '#average').text(data.average);
            $( '#' + id).text(data.letter);
            if (data.bgLetter) {
                $( '#' + id ).removeClass('table-success').removeClass('table-danger').removeClass('table-info').addClass(data.bgLetter);
            }




            // $( '#' + id ).css({"border": "2px solid red"});

        }
    });
});

