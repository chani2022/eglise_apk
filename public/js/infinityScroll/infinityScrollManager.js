function runInfinityScroll(
    categorie,
    selectorContainer,
    selectorItemContainer,
    selectorSpinner = false,
    selectorScrollContainer = window) {

    let limit = 10
    let firstResult = 0
    let data_server = {
        categorie
    }
    let isArticleRecent = false

    // if (categorie === "Récent") {
    //     limit = 10
    //     isArticleRecent = true
    // } else if (categorie === "Calendrier d'évènement") {
    //     limit = 10
    // } else if (categorie === "Populaire") {
    //     limit = 10
    // }

    function nextHandler(pageIndex) {
        // if (pageIndex == 1) {
        //     firstResult = -1
        // }
        return fetch('/api?type=' + data_server.categorie + '&firstResult=' + (pageIndex + firstResult))
            .then(response => response.json())
            .then((data) => {

                let frag = document.createDocumentFragment();
                for (let i = 0; i < data.length; i++) {
                    let articleExist = false
                    $(selectorItemContainer).each(function (index) {
                        if (parseInt($(this).attr('id')) === parseInt(data[i].id_article)) {
                            articleExist = true
                        }
                    })
                    if (!articleExist) {
                        let article = data[i];
                        let item = null
                        if (isArticleRecent) {
                            item = Article.createItemArticle(data_server.categorie, article, true)
                        } else {
                            item = Article.createItemForAllArticle(article)
                        }
                        frag.appendChild(item);
                        // console.log("infinity", article.categorie_article)

                    }
                    frag.childNodes.forEach((child) => {
                        let obj = $(child)

                        // if (selectorItemContainer === ".item-post-populaire") {

                        //     Article.clickToshowArticlePopulaire($(child))
                        // } else if (selectorItemContainer === ".infinityScrollRecent") {
                        Article.clickToShowAllCommentsArticleRecent($(child))
                        // }
                        // else if (obj.find('.item-post-populaire')) {
                        //     console.log(obj.find('.item-post-populaire'))
                        //     Article.clickToshowArticlePopulaire('.article_populaire')
                        // }
                        /**
                        if (data[i].categorie_article == "Récent") {
                            Article.clickToShowAllCommentsArticleRecent($(child))
                        } else if (data[i].categorie_article == "Populaire") {
                            Article.clickToshowArticlePopulaire()
                        }
                        **/
                    })
                }

                let hasNextPage = true
                if (data.length === 0) {
                    hasNextPage = false
                }
                firstResult += limit

                return this.append(Array.from(frag.childNodes))
                    // indicate that there is a next page to load
                    .then(() => hasNextPage);
            });
    }

    window.ias = new InfiniteAjaxScroll(selectorContainer, {
        item: selectorItemContainer,
        next: nextHandler,
        pagination: false,
        scrollContainer: selectorScrollContainer,
        spinner: selectorSpinner
    });
}