function hideOrShowFormDynamique(categorie = null, $parent_galerie, $parent_date_event) {

    let display_form_event = 'none'
    let display_form_galerie = 'none'

    if (categorie === null) {
        if ($('.input_event_show').length) {
            display_form_event = 'block'
        }
        if ($('.input_galerie_show').length) {
            display_form_galerie = 'block'
        }
    } else {
        if (categorie == "Calendrier d'évènement") {
            display_form_event = 'block'
            display_form_galerie = 'none'
        }
        else if (categorie == "Populaire") {
            display_form_galerie = 'block'
            display_form_event = 'none'
        } else {
            display_form_event = 'none'
            display_form_galerie = 'none'
        }
    }
    $parent_date_event.css({ display: display_form_event })
    $parent_galerie.css({ display: display_form_galerie })
}

$(function () {
    let $parent_galerie = $('#form_galerie')
    let $parent_date_event = $('#form_event')
    /**
     * event form categorie
     */
    let $article_categorie = $('#article_categorie')

    /**
     * text editor textarea
     */
    $('#article_commentaire').tinymce({
        height: 500,
        menubar: false,
        plugins: [
            'a11ychecker',
            'advlist',
            'advcode',
            'advtable',
            'autolink',
            'checklist',
            'export',
            'lists',
            'link',
            'image',
            'charmap',
            'preview',
            'anchor',
            'searchreplace',
            'visualblocks',
            'powerpaste',
            'fullscreen',
            'formatpainter',
            'insertdatetime',
            'media',
            'table',
            'help',
            'wordcount'
        ],
        toolbar: 'undo redo | a11ycheck casechange blocks | bold italic forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist checklist outdent indent | removeformat | code table help'
    });

    $('#article_event_at').daterangepicker({
        singleDatePicker: true,
        timePicker: true,
        locale: {
            format: 'YYYY-MM-DD hh:mm A'
        }
    });
    hideOrShowFormDynamique(null, $parent_galerie, $parent_date_event)

    $article_categorie.change(() => {
        $.ajax({
            url: '/get/categorie_name',
            data: {
                id_categorie: $('#article_categorie').val()
            },
            beforeSend: (e) => {
                $('#btn-register').prop('disabled', true)
            },
            success: (e) => {
                hideOrShowFormDynamique(e.nom_categorie, $parent_galerie, $parent_date_event)
                $('#btn-register').prop('disabled', false)
            },
            error: (e) => {
                alert('Une erreur est survenu! Veuillez contactez l\'administrateur')
                $('#btn-register').prop('disabled', false)
            }
        })

    })
})
