//Event handlers
$(".editFor" ).on( "mouseenter", function(){
    // $( this).css( "background-color", "#C6E2FF");
    //$('html,body').css('cursor','crosshair');
    $(this).attr('contenteditable', 'true');
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


    

//sendAgendaTempControlleur
$('.editFor').on( "change keyup", function(){
    var id = $(this).attr('id');
    var letter = $(this).html();
    $('.editFor').css({"border": ""});

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

            $( '#titre').html(data.titre);
            $( '#description').html(data.description);
            $( '#startLegalWeek').text(data.startLegalWeek);
            $( '#endLegalWeek').text(data.endLegalWeek);
            $( '#startDay').text(data.startDay);
            $( '#endDay').text(data.endDay);
            $( '#hoursPerWeek').text(data.hoursPerWeek);
            $( '#intervalBefore').text(data.intervalBefore);
            $( '#intervalAfter').text(data.intervalAfter);
            $( '#DateTimeBefore').text(data.DateTimeBefore);
            $( '#DateTimeAfter').text(data.DateTimeAfter);
            $( '#letter').text(data.interval);
            $( '#average').text(data.average);
            $( '#' + id).text(data.letter);
            
            if (data.bgLetter) {
                $( '#' + id ).css({"border": ""}).removeClass('table-success').removeClass('table-danger').removeClass('table-info').addClass(data.bgLetter);                
            }

            if (data.countErrors) {
                $( '#' + id ).css({"border": "1px solid red"});
            }


        }
    });
});


