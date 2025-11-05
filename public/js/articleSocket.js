function connection(id_connected, conn, idToRemove) {
    let data = {}
    data.type = "connection"
    data.id_connected = id_connected;
    data.idRemove = idToRemove
    data = JSON.stringify(data)

    conn.send(data)
}

function unread_article(id_connected, conn, idToRemove) {
    let data = {}
    data.type = "unread_article"
    data.id_connected = id_connected
    data.idRemove = idToRemove
    // console.log(idToRemove)
    conn.send(JSON.stringify(data))

}

function init(conn, id_connected, idToRemove) {

    conn.onopen = (e) => {
        connection(id_connected, conn, idToRemove)
        unread_article(id_connected, conn, idToRemove)
    }
}



$(function () {
    var conn = new WebSocket('ws://localhost:8080')
    // var conn = null

    init(conn, id_connected, idToRemove)

    conn.onmessage = (e) => {
        let data = JSON.parse(e.data)
        switch (data['type']) {
            case 'connection':
                break;
            case 'unread_article':
                let selector_container_recent = '#masonry'
                let selector_container_populaire = '.post-populaire'
                let selector_notification = '#liste_notification'
                let selector_container_evenement = '.event-list'

                compteForKnowArticleisInit++;

                let idRemove = data['idRemove']
                let articles_brute = data['data'];


                if (articles_brute) {
                    // infinityReRun = true
                    /**
                     * init objet article
                     */
                    const types_articles = Object.keys(articles_brute);
                    Article.basePathArticle = basePathArticle
                    Article.basePathGalerie = basePathGalerie
                    Article.id_connected = id_connected
                    Article.articlesRecent = []

                    Article.idRemove = idRemove
                    Article.setTypeArticle(types_articles)

                    if (articles_brute) {
                        notifMsg = new NotificationMsg()
                    }

                    for (const [type_article, articles] of Object.entries(articles_brute)) {

                        articles.map((article) => {
                            const dateOptions = { timeZone: 'UTC', month: 'long', day: 'numeric', year: 'numeric' };
                            const dateFormatter = new Intl.DateTimeFormat('en-US', dateOptions);
                            const date = dateFormatter.format(new Date(article.date_created));
                            /**
                             * create item
                             */
                            Article.createItemArticle(type_article, article);
                            /**
                             * notification
                             */
                            notifMsg.setIdsUnreadArticle({ [article.id_article]: article.ids_unread_article })
                            notifMsg.setTagsNotification(article.id_article, article.titre, article.extrait_commentaire, date)

                        })
                        /**
                         * notification
                         */
                        notifMsg.showTagsNotif(selector_notification)
                        /**
                         * lecture d'une notification
                         */
                        $('.read_notification').each(function () {
                            $(this).click(function (e) {
                                e.preventDefault()
                                let data = {
                                    type: "read_notification_article",
                                    id_connected,
                                    id_article: $(this).attr("id"),
                                    idRemove: idToRemove
                                }

                                conn.send(JSON.stringify(data))
                            })
                        })

                    }

                    notifMsg.showNotification(id_connected, '.pulse-button', '#nb_notification')
                    /**
                     * append or update or delete item to the container
                     */
                    Article.setItemsArticleInContainer(
                        {
                            "Récent": selector_container_recent,
                            "Populaire": selector_container_populaire,
                            "Calendrier d'évènement": selector_container_evenement
                        }
                    )

                    /**
                     * visualisation de l'article populaire par defaut
                     */
                    Article.showInfoArticlePopulaire()
                    /**
                     * visualisation de l'article populaire
                     */

                    Article.clickToshowArticlePopulaire()
                    // Article.clickToShowAllCommentsArticleRecent('.lire-suite')
                    // if (Article.checkContainer('#post-recent')) {
                    //     runInfinityScroll("Récent", '#masonry', '.infinityScrollRecent', false, window, 0, 0)
                    // }

                }
                break;
            default:
                break;
        }
    }

    conn.onclose = (e) => {
        // init(conn, id_connected)
    }
})
