var Article = {
    typesArticle: {},
    items: [],
    indexArticlePopulaire: 0,
    articlePopulaireRecent: null,
    articlesPopulaires: [],
    articlesRecent: [],
    idRemove: null,

    setTypeArticle: function (listTypeArticle) {
        for (let i = 0; i < listTypeArticle.length; i++) {
            this.typesArticle[listTypeArticle[i]] = ""
        }
        this.items.push(this.typesArticle)
    },
    date: function (date, format = null) {

        if (format) {
            return new Date(date).toLocaleDateString('en-GB')
        } else {
            const dateOptions = { timeZone: 'UTC', month: 'long', day: 'numeric', year: 'numeric' };
            const dateFormatter = new Intl.DateTimeFormat('en-US', dateOptions);
            return dateFormatter.format(new Date(date));
        }

    },
    hours: function (date_event) {
        let jour = ""
        let mois = ""
        let heure = ""
        let min = ""
        if (date_event) {
            let months = ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Decembre"]
            let date = new Date(date_event)
            jour = date.getDate()
            mois = months[date.getMonth()]
            heure = date.getHours().toString().length === 1 ? '0' + date.getHours() : date.getHours()
            min = date.getMinutes().toString().length === 1 ? '0' + date.getMinutes() : date.getMinutes()
        }
        return {
            'jour': jour,
            'mois': mois,
            'heure': heure,
            'min': min
        }
    },
    checkContainer: function (selectorName) {
        if ($(selectorName).length) { return true } { return false }
    },
    createItemArticle: function (type_article, article, isInfinityScroll = false) {
        const image = basePathArticle + '/' + article.image_article
        let date = null

        let item = document.createElement('div');

        date = this.date(article.date_created)


        const email = article.email_author
        const titre = article.titre
        const extrait_commentaire = marked.parse(article.extrait_commentaire) + ' ...'
        const commentaire = article.commentaire.replace(/"(.*?)"/g, '«$1»')

        if (article.date_event) {
            date = this.date(article.date_event)
        }
        switch (type_article) {
            case 'Récent':
                const template = '<div class="action-card col-xl-12 col-lg-12 col-md-12 col-sm-6 pending infinityScrollRecent" id="' + article.id_article + '">'
                    + '<div class="recent-news cours-bx">'
                    + '<div class="action-box">'
                    + '<img src="' + image + '" alt id="image-article-recent-' + article.id_article + '">'
                    + '<a href="#" class="btn lire-suite" id="' + article.id_article + '" data-toggle="modal" data-target="#exampleModalCenter">Lire la suite</a>'
                    + '</div>'
                    + '<div class="info-bx">'
                    + '<ul class="media-post">'
                    + '<li>'
                    + '<a href="#">' +
                    '<i class="fa fa-calendar" id="date-article-recent-' + article.id_article + '"> ' + date + '</i>'
                    + '</a>'
                    + '</li>'
                    + '<li>'
                    + '<a href="#">' +
                    '<i class="fa fa-user" id="auteur-article-recent-' + article.id_article + '"> Par ' + email + '</i>'
                    + '</a>'
                    + '</li>'
                    + '</ul>'
                    + '<div class="info-bx text-center">'
                    + '<h5  class="post-title" style="font-size:15px;font-weight:bold"  id="titre-article-recent-' + article.id_article + '">' + titre + '</h5>'
                    + '</div>'
                    + '<p id="commentaire-article-recent-' + article.id_article + '" data-commentaire="' + commentaire + '" style="overflow: hidden;display: -webkit-box;-webkit-line-clamp: 2;line-clamp: 2;-webkit-box-orient: vertical;">' + extrait_commentaire + '</p>'
                    + '<div class="post-extra">'
                    + '<a href="#" class="comments-bx"></a> 0 commentaire'
                    + '</div>'
                    + '</div>'
                    + '</div>'
                    + '</div>'


                if (isInfinityScroll) {
                    item = document.createElement('div');
                    item.innerHTML = template.trim();

                    return item.firstChild;
                } else {

                    this.items[0][type_article] += template
                }
                this.articlesRecent.push(template)

                break
            case 'Populaire':

                // this.items[0][type_article]

                const templatePopulaire = '<div class="widget-post clearfix item-post-populaire" href="#' + article.id_article + '" id="' + article.id_article + '">'

                    + '<div class="ttr-post-media">'
                    + '<img src="' + image + '" with="200" height="143" alt>'
                    + '</div>'
                    + '<div class="ttr-post-info">'
                    + '<div class="ttr-post-header">'
                    + '<h6 class="post-title" id="' + article.id_article + '">'
                    + '<a href="#crolltou" class="article_populaire" id="' + article.id_article + '">' + article.titre + '</a>'
                    + '</h6>'
                    + '</div>'
                    + '<ul>'
                    + '<li><a href="#"><i class="fa fa-calendar"></i>' + date + '</a></li>'
                    + '<li><a href="#"><i class="fa fa-comments-o"></i> 0 Commentaire </a></li>'
                    + '</ul>'
                    + '</div>'
                    + '</div>'
                // if (this.indexArticlePopulaire === 0) {
                //     this.articlePopulaireRecent = article
                // }
                // this.indexArticlePopulaire++
                this.articlesPopulaires.push(templatePopulaire)
                if (isInfinityScroll) {
                    item = document.createElement('div');
                    item.innerHTML = templatePopulaire.trim();
                    // Article.articlesPopulaires.push(article)
                    return item.firstChild;
                } else {
                    this.items[0][type_article] += template
                }
                // this.articlesPopulaires.push(templatePopulaire)
                break
            case 'Calendrier d\'évènement':

                const templateCalendrier = '<div class="post action-card col-lg-4 col-md-6 col-sm-12 col-xs-12 m-b40" id="' + article.id_article + '">'
                    + '<div class="recent-news">'
                    + '<div class="action-box">'
                    + '<img src="' + image + '" alt>'
                    + '</div>'
                    + '<div class="info-bx">'
                    + '<ul class="media-post">'
                    + '<li>'
                    + '<a href="#">' +
                    '<i class="fa fa-calendar"> ' + date + '</i>'
                    + '</a>'
                    + '</li>'
                    + '<li>'
                    + '<a href="#">' +
                    '<i class="fa fa-user"> Par ' + article.email_author + '</i>'
                    + '</a>'
                    + '</li>'
                    + '</ul>'
                    + '<h5 style="display:block;text-overflow: ellipsis;width: 300px;overflow: hidden; white-space: nowrap;" class="post-title">' + article.titre + '</h5>'
                    + '<p style="overflow: hidden;display: -webkit-box;-webkit-line-clamp: 2;line-clamp: 2;-webkit-box-orient: vertical;">' + marked.parse(commentaire) + '</p>'
                    + '<div class="post-extra">'
                    + '<a href="#" class="comments-bx"></a> 0 commentaire'
                    + '</div>'
                    + '</div>'
                    + '</div>'
                    + '</div>'

                if (isInfinityScroll) {
                    let item = document.createElement('div');
                    item.innerHTML = template.trim();

                    return item.firstChild;
                } else {
                    this.items[0][type_article] += templateCalendrier
                }
                break
        }

    },
    createItemForAllArticle: function (article) {
        const image = basePathArticle + '/' + article.image_article
        let date = null
        let dateEvent = null

        date = this.date(article.date_created, "slash")


        const email = article.email_author
        const titre = article.titre
        const extrait_commentaire = marked.parse(article.extrait_commentaire) + ' ...'
        const commentaire = article.commentaire.replace(/"(.*?)"/g, '«$1»')

        if (article.date_event) {
            dateEvent = this.date(article.date_event, "slash")
        }
        let template = '<div class="action-card col-xl-12 col-lg-12 col-md-12 col-sm-6 pending infinityScrollRecent" id="' + article.id_article + '">'
        + '<div class="recent-news cours-bx">'
        + '<div class="action-box">'
        + '<img src="' + image + '" alt id="image-article-recent-' + article.id_article + '">'
        + '<a href="#" class="btn lire-suite" id="' + article.id_article + '" data-toggle="modal" data-target="#exampleModalCenter">Lire la suite</a>'
        + '</div>'
        + '<div class="info-bx">'
        + '<ul class="media-post">'
        + '<li>'
        + '<a href="#">' +
        '<i class="fa fa-calendar" id="date-article-recent-' + article.id_article + '"> ' + date + '</i>'
        + '</a>'
        + '</li>'
        + '<li>'
        + '<a href="#">' +
        '<i class="fa fa-user" id="auteur-article-recent-' + article.id_article + '"> Par ' + email + '</i>'
        + '</a>'
        + '</li>'
        + '</ul>'
        + '<div class="info-bx text-center">'
        + '<h5  class="post-title" style="font-size:15px;font-weight:bold"  id="titre-article-recent-' + article.id_article + '">' + titre + '</h5>'
        + '</div>'
        + '<p id="commentaire-article-recent-' + article.id_article + '" data-commentaire="' + commentaire + '" style="overflow: hidden;display: -webkit-box;-webkit-line-clamp: 2;line-clamp: 2;-webkit-box-orient: vertical;">' + extrait_commentaire + '</p>'
        + '<div class="post-extra">'
        + '<a href="#" class="comments-bx"></a> 0 commentaire'
        + '</div>'
        + '</div>'
        + '</div>'
        + '</div>'
        let item = document.createElement('div');
        item.innerHTML = template.trim();

        return item.firstChild;
    },
    showInfoArticlePopulaire: function (article = null) {
        if (article === null) {
            article = this.articlePopulaireRecent
        }
        if (article) {

            if (this.checkContainer('#image_article_populaire') &&
                this.checkContainer('#date_populaire_post') &&
                this.checkContainer('#titre_article_populaire') &&
                this.checkContainer('#comment_article_populaire') &&
                this.checkContainer('.galerie-post')) {
                $('#image_article_populaire').children().attr({ 'src': basePathArticle + '' + article.image_article })
                console.log(article.date_created)
                /**
                 * date de publication de l'article
                 */
                const dateOptions = { timeZone: 'UTC', month: 'long', day: 'numeric', year: 'numeric' };
                const dateFormatter = new Intl.DateTimeFormat('en-US', dateOptions);
                const date = dateFormatter.format(new Date(article.date_created));

                $('#date_populaire_post').text(date)
                /**
                 * changement du titre de l'article populaire
                 */
                $('#titre_article_populaire').children().text(article.titre)
                /**
                 * changement de description de l'article populaire
                 */
                $('#comment_article_populaire').html($('<p>' + marked.parse(article.commentaire.replace(/"(.*?)"/g, '«$1»')) + '</p>'))

                /**
                 * affichage galerie
                 */
                let tags_galerie = ""
                for (let i = 0; i < article.galeries.length; i++) {
                    let img_galerie = basePathGalerie + '' + article.galeries[i]
                    tags_galerie += '<li>'
                        + '<a href="' + img_galerie + '" class="magnific-anchor">'
                        + '<img src="' + img_galerie + '" alt >'
                        + '</a>'
                        + '</li>'
                }
                $('.galerie-post').html($(tags_galerie))

            }
        }
        this.indexArticlePopulaire = 0
    },
    setItemsArticleInContainer: function (obj) {

        for (let type in obj) {
            if (this.checkContainer(obj[type])) {
                // console.log(type)
                if (type === "Récent") {
                    // if (Article.idRemove) {
                    //     console.log(Article.idRemove)
                    //     if (this.checkContainer($('.infinityScrollRecent#' + Article.idRemove))) {
                    //         console.log($('#' + Article.idRemove))
                    //         // console.log()
                    //         $('#' + Article.idRemove).remove()
                    //     }
                    // }

                    if (Article.articlesRecent.length > 0) {

                        Article.articlesRecent.map(function (art) {
                            let articleRecentExist = false
                            let idArticleRecent = null
                            $('.infinityScrollRecent').each(function (index) {
                                let id = $(this).attr("id")
                                if (typeof id === "undefined") {
                                    $(this).remove()
                                }
                                if ($(this).attr("id") == Article.idRemove) {
                                    $(this).remove()
                                }
                                if ($(this).attr("id") == $(art).attr("id")) {
                                    articleRecentExist = true
                                    idArticleRecent = $(this).attr("id")
                                }
                            })

                            if (!articleRecentExist) {
                                // console.log("3")
                                if (compteForKnowArticleisInit == 1) {
                                    $(obj[type]).append($(art))
                                } else {
                                    $(obj[type]).prepend($(art))
                                }

                            } else {
                                console.log(idArticleRecent)
                                console.log($('.infinityScrollRecent#' + idArticleRecent).attr("class"))
                                // $('#' + idArticleRecent).replaceWith($(art))
                                // $('#recent-' + idArticleRecent).replaceWith($(art))
                                $($('.infinityScrollRecent#' + idArticleRecent)).replaceWith($(art))
                            }
                        })
                    }

                } else {
                    $(obj[type]).html($(this.items[0][type]))
                }
            }
        }
    },
    clickToshowArticlePopulaire: function () {

        // console.log(obj.find('.article_populaire'))
        $('.article-populaire').each(function (e) {
            // console.log(Article.articlesPopulaires)
            $(this).click(function (e) {
                e.preventDefault()
                let id = $(this).attr("id")
                let article_selected = null
                console.log(Article.articlesPopulaires)
                Article.articlesPopulaires.map((article) => {
                    if (parseInt(id) === parseInt(article.id_article)) {
                        article_selected = article
                    }
                })
                if (article_selected) {
                    Article.showInfoArticlePopulaire(article_selected)
                }
            })
        })
    },
    clickToShowAllCommentsArticleRecent: function (selectorNameOrObj) {
        let elmnt = null;
        if (typeof selectorNameOrObj === 'string') {
            elmnt = $(selectorNameOrObj)
        } else {
            elmnt = selectorNameOrObj
        }
        elmnt.each(function () {
            $(this).click(function (e) {
                e.preventDefault()
                let id = $(this).attr("id")
                let img = $('#image-article-recent-' + id).attr("src")
                let auteur = $('#auteur-article-recent-' + id).text()
                let date = $('#date-article-recent-' + id).text()
                let commentaire = document.querySelector('#commentaire-article-recent-' + id).dataset.commentaire
                let titre = $('#titre-article-recent-' + id).text()
                // console.log(Article.articlesRecent)
                // console.log(id, img, auteur, date, titre, commentaire)
                Article.showPopupArticleRecent(id, img, auteur, date, titre, commentaire)
            })
        })
    },
    showPopupArticleRecent: function (id, img, auteur, date, titre, commentaire) {
        $('#modal-titre').text(titre)
        let paragraphe = '<p style="text-align:justify" id=' + id + '> ' + marked.parse(commentaire)
            + '<br/>'
            + '<img src="' + img + '" alt/>'
            + '<br/>'
            + '<br/>'
            + '<strong>Auteur:</strong>' + auteur
            + '<br/>'
            + '<strong>Date:</strong>' + date
            + '</p>'
        $('#modal-commentaire').html(paragraphe)
    }
}




