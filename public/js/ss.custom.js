$(document).ready(function () {
        var table = $('.example').DataTable({
            "aaSorting": [],
           
            rowReorder: {
            selector: 'td:nth-child(2)'
            },
            responsive: 'true',
            dom: "Bfrtip",
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel-o"></i>',
                    titleAttr: 'Excel',
                   
                    title: $('.download_label').html(),
                    exportOptions: {
                        columns: ':visible'
                    }
                },{
                    extend: 'pdfHtml5',
                    filename : $('.report_name').text(),
                    messageTop : $('.download_label').html(),
                    orientation: 'landscape',
                    pageSize: 'A3',
                    text: '<i class="fa fa-file-pdf-o"></i>',
                    titleAttr: 'PDF',
                    customize: function ( doc ) {
                    doc.content.splice( 1, 0, {
                        margin: [ 0, 0, 0, 5 ],
                            alignment: 'left',
                            image: 'data:image/jpeg;base64,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',
                            width: 250
                        } );
                    },
                    title: '.',
                    exportOptions: {
                        columns: ':visible'
                        
                    }
                },{
                    extend: 'colvis',
                    text: '<i class="fa fa-columns"></i>',
                    titleAttr: 'Columns',
                    title: $('.download_label').html(),
                    postfixButtons: ['colvisRestore']
                },
            ]
        });
    });


function validateNumericInput(input) {
    var numericValue = input.value.trim(); // User dwara di gayi value ko trim karke numericValue mein store karein
    // Regular expression ka istemal karke sirf numeric values ko allow karein
    if (!/^[0-9]*$/.test(numericValue)) {
        alert("Please enter numeric values only."); // Agar user ne numeric value nahi di hai toh use alert message dikhayein
        input.value = ''; // Invalid value ko clear kar dein
        input.focus(); // Input field par focus karein taki user dobara se value daal sake
    }
}

function isAlphabetKey(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode;
    if ((charCode < 65 || charCode > 90) && (charCode < 97 || charCode > 122)) {
        return false;
    }
    return true;
}
