$(function () {

    var container = document.querySelector('#container')
    let type = container.dataset.type
    runInfinityScroll(type, '#container', '.item-article', '.spinner')
})