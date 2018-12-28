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
            {
                "targets": [ 4 ],
                "visible": true,
                "searchable": true
            },
            {
                "targets": [ 5 ],
                "visible": true,
                "searchable": true
            },
            {
                "targets": [ 6 ],
                "visible": true,
                "searchable": false
            },
            {
                "targets": [ 7 ],
                "visible": true,
                "searchable": false
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
        'copy', 'excel', 'pdf'
    ]
} );

table.buttons( 0, null ).containers().appendTo( '#buttonPrint' );