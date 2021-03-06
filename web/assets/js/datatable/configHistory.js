var table =

    $('#example').DataTable({
        "paging":   true,
        "ordering": true,
        "info":     true,
        "order": [[ 1, "desc" ]],

        "columnDefs": [
            {
                "targets": [ 0 ],
                "visible": false,
                "searchable": false
            },
            {
                "targets": [ 1 ],
                "visible": true,
                "searchable": true
            },
            {
                "targets": [ 2 ],
                "visible": true,
                "searchable": true
            },
            {
                "targets": [ 3 ],
                "visible": true,
                "searchable": true
            },
        ],



        "language": {
            "sProcessing": "Traitement en cours ...",
            "sLengthMenu": "Afficher _MENU_ lignes",
            "sZeroRecords": "Aucun résultat trouvé",
            "sEmptyTable": "Aucune donnée disponible",
            "sInfo": "Lignes _START_ à _END_ sur _TOTAL_",
            "sInfoEmpty": "Aucune ligne affichée",
            "sInfoFiltered": "(Filtrer un maximum de_MAX_)",
            "sInfoPostFix": "",
            "sSearch": "Chercher:",
            "sUrl": "",
            "sInfoThousands": ",",
            "sLoadingRecords": "Chargement...",
            "oPaginate": {
                "sFirst": "Premier", "sLast": "Dernier", "sNext": "Suivant", "sPrevious": "Précédent"
            },
            "oAria": {
                "sSortAscending": ": Trier par ordre croissant", "sSortDescending": ": Trier par ordre décroissant"
            },
            "select": {
                "rows": {
                    _: "%d lignes séléctionnées",
                    0: "Aucune ligne séléctionnée",
                    1: "1 ligne séléctionnée"
                }
            }
        }
    });


new $.fn.dataTable.Buttons( table, {
    name: 'commands',


    buttons: [
        {
            extend: 'print',
            text: 'Imprimer',
            autoPrint: true
        },
        {
            extend: 'copyHtml5',
            text: 'Copier'
        },
        {
            extend: 'excelHtml5',
            text: 'Excel'
        },
        {
            extend: 'pdfHtml5',
            text: 'PDF',
            orientation: 'landscape',
            pageSize: 'LEGAL'
        }
    ],
} );

table.buttons( 0, null ).containers().appendTo( '#buttonPrint' );