$('#example').DataTable({

    autoFill: {
        vertical: false,
        columns: [ 1, 2, 3, 4, 5, 6],
        cancel: 'Fermer sans mise à jour',
        fill: 'Etendre la cellule avec la même valeur',
        fillHorizontal: 'Copier les valeurs horizontallement',
        increment: 'Changer chaque cellule par : <input type="number" value="1">'
    },

    dom: 'Bfrtip',
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
    "paging":   true,
    "ordering": true,
    "info":     true,
    "order": [[ 1, "desc" ]],

    "columnDefs": [
        {
            "targets": [ 0 ],
            "visible": true,
            "searchable": true
        },
        {
            "targets": [ 1 ],
            "visible": true,
            "searchable": false
        },
        {
            "targets": [ 2 ],
            "visible": true,
            "searchable": false
        },
        {
            "targets": [ 3 ],
            "visible": true,
            "searchable": false
        },
        {
            "targets": [ 4 ],
            "visible": true,
            "searchable": false
        },
        {
            "targets": [ 6 ],
            "visible": true,
            "searchable": false
        } 
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


