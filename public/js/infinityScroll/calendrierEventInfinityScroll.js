$(function () {
    $('#post-recent').height(450).css({ overflowY: 'scroll' }) //definir le scroll

    runInfinityScroll("Calendrier d'évènement", '#masonry', '.infinityScrollRecent', '#post-recent', '.spinner')
})