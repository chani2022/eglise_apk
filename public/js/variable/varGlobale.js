
let elmnt = document.querySelector('.info-socket')

var id_connected = elmnt.dataset.userId;
let basePathArticle = elmnt.dataset.pathImage
let basePathGalerie = elmnt.dataset.pathGalerie
let socketIsLaunch = elmnt.dataset.socketActive
let locale = elmnt.dataset.locale
let basePathPhotoUser = elmnt.dataset.pathPhotoUser


let idToRemove = elmnt.dataset.idRemove
let compteForKnowArticleisInit = 0