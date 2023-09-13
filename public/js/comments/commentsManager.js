var Comments = {
    id_article: null,
    setModalDialog: function (id_article, img, auteur, date, titre, commentaire) {
        $('#modal-titre-comment').text(titre)
        let paragraphe = '<p style="text-align:justify" id=' + id_article + '> ' + marked.parse(commentaire)
            + '<br/>'
            + '<img src="' + img + '" alt/>'
            + '<br/>'
            + '<br/>'
            + '<strong>Auteur:</strong>' + auteur
            + '<br/>'
            + '<strong>Date:</strong>' + date
            + '</p>'
        $('#modal-commentaire-comment').html(paragraphe)
    },

    showDialog: function (id_article, isPostRecent) {
        let img, auteur, date, commentaire, titre;
        Comments.id_article = id_article
        if (isPostRecent) {
            img = $('#image-article-recent-' + id_article).attr("src")
            auteur = $('#auteur-article-recent-' + id_article).text()
            date = $('#date-article-recent-' + id_article).text()
            commentaire = document.querySelector('#commentaire-article-recent-' + id_article).dataset.commentaire
            titre = $('#titre-article-recent-' + id_article).text()
            // Comments.id_article = id_article


        } else {
            // const elmnt = $('#' + id_article + " .comments")
            $('.comments').each(function () {
                if ($(this).attr("id") == id_article) {
                    const data = $(this).data()
                    img = data.image
                    auteur = data.auteur
                    commentaire = data.commentaire
                    date = data.date
                    titre = data.titre
                }
            })
        }
        $('#comments-article').empty()
        Comments.setModalDialog(Comments.id_article, img, auteur, date, titre, commentaire)
        Comments.loadComments()
        Comments.sendComments()
    },
    sendComments: function () {
        let emojiarea = null
        el = $("#emojionearea1").emojioneArea({
            pickerPosition: "top",
            tonesStyle: "bullet",
            events: {
                keyup: function (editor, e) {
                    if ((e.keyCode === 13 || e.key === "enter") && !e.shiftKey) {
                        emojiarea = $(this)[0]
                        const value = $(this)[0].getText()
                        const id_article = Comments.id_article
                        $.ajax({
                            url: '/api/create/comments',
                            type: 'post',
                            dataType: 'json',
                            data: {
                                id_user: id_connected,
                                id_article,
                                value
                            },
                            success: function (data) {
                                $('#comments-article').prepend(Comments.createItem(data))
                                emojiarea.setText("")
                            },
                            error: function () {
                                emojiarea.setText("")
                            }
                        })
                    }
                }
            }
        });
    },
    createItem: function (data, isInfinity = false) {
        const id = data.id
        const date = new Date(data.date)
        const time = moment(date).fromNow()
        const photo = basePathPhotoUser + '' + data.user.photo
        const contenu = data.contenu
        const name = data.user.nom ? data.user.nom + ' ' + data.user.prenom : data.user.username

        const defaultAvatar = '{{ asset("assets/images/profil.jpg") }}'; // Utilisez le chemin de votre image par défaut

        // Vérifie si photo est null
        const avatarSrc = photo !== null ? photo : defaultAvatar;
        
        const li = '<li class="d-flex justify-content-between mb-4 row item-comments" id="' + id + '">'
                    + '<p class="col-md-2 col-lg-2 col-xl-2 m-0">'
                    + '<img src="' + avatarSrc + '" alt="avatar" class="rounded-circle d-flex align-self-start me-3 shadow-1-strong" width="60">'
                    + '</p>'
                    + '<div class="col-md-10 col-lg-10 col-xl-10">'
                    + '<div class="card" height="75px;">'
                    + '<div class="card-header d-flex justify-content-between p-3">'
                    + '<p class="fw-bold mb-0">' + name + '</p>'
                    + '<p class="text-muted small">'
                    + '<i class="fa fa-clock-o"></i> '
                    + time + '</p>'
                    + '</div>'
                    + '<div class="card-body">'
                    + '<p class="mb-0">'
                    + contenu
                    + '</p>'
                    + '</div>'
                    + '</div>'
                    + '</div>'
                    + '</li>';
        
        

        if (isInfinity) {
            let item = document.createElement('div');
            item.innerHTML = li.trim();

            return item.firstChild;
        }
        return li

    },
    loadComments: function () {
        const limit = 10
        let firstResult = 0

        function nextHandler(pageIndex) {
            return fetch('/api/comments/' + Comments.id_article + '/' + (pageIndex + firstResult))
                .then(response => response.json())
                .then((data) => {
                    let frag = document.createDocumentFragment();
                    for (let i = 0; i < data.length; i++) {
                        const id_comment = data[i].id
                        if (!Comments.itemExist(id_comment)) {
                            item = Comments.createItem(data[i], true)
                            frag.appendChild(item);
                        }
                    }

                    let hasNextPage = true
                    if (data.length === 0) {
                        hasNextPage = false
                    }
                    firstResult += limit

                    return this.append(Array.from(frag.childNodes))
                        // indicate that there is a next page to load
                        .then(() => hasNextPage);

                })
        }

        window.ias = new InfiniteAjaxScroll("#comments-article", {
            item: ".item-comments",
            next: nextHandler,
            pagination: false,
            scrollContainer: ".scroll-comments",
            spinner: '#loading-comments'
        });
    },

    itemExist: function (id_comment) {
        return $('.item-comments#' + id_comment).length > 0 ? true : false

    }
}