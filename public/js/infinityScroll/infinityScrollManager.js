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

    function nextHandler(pageIndex) {
        /**
         * locale is globale variable
         */
        return fetch('/api?type=' + data_server.categorie + '&firstResult=' + (pageIndex + firstResult) + '&locale=' + locale)
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
                        Article.clickToShowAllCommentsArticleRecent($(child))
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

            })
    }

    window.ias = new InfiniteAjaxScroll(selectorContainer, {
        item: selectorItemContainer,
        next: nextHandler,
        pagination: false,
        scrollContainer: selectorScrollContainer,
        spinner: selectorSpinner
    });
}