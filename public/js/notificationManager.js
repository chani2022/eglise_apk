function NotificationMsg() {
    this.idsUnreadArticle = []
    this.totalMsgNotRead = 0
    this.tagsNotification = ""
    this.iconCheckView = "fa fa-check"

    this.checkContainer = function (selectorName) {
        if ($(selectorName).length) { return true } else { return false }
    }

    this.setIdsUnreadArticle = function (obj) {
        this.idsUnreadArticle.push(obj)
    }

    this.getTotalMsgNotRead = function (id_connected) {
        for (let i = 0; i < this.idsUnreadArticle.length; i++) {
            let id_found = false
            for (let id_article in this.idsUnreadArticle[i]) {
                this.idsUnreadArticle[i][id_article].find(elmnt => {
                    if (parseInt(elmnt) === parseInt(id_connected)) {
                        id_found = true
                    }
                })
            }
            if (id_found) {
                this.totalMsgNotRead += 1
                this.iconCheckView = ""
            }
        }
        return this.totalMsgNotRead
    }

    this.setTagsNotification = function (id_article, titre, extraitCommentaire, date, iconCheck) {
        this.tagsNotification += '<li style="background-color: #fcfcff;" class="read_notification" id="' + id_article + '">'
            + '<span style="border-radius: 100px;" class="notification-icon dashbg-gray">'

            + '<i style="color: var(--primary);" class="' + this.iconCheckView + '"></i>'

            + '</span>'
            + '<div class="row">'
            + '<div>'
            + '<h2 style="font-size:15px" class="title-head m-b0"> ' + titre + '</h2>'
            + '<p class="m-b0"> ' + extraitCommentaire + '..</p>'
            + '</div>'
            + '</div>'
            + '<span class="notification-time">'
            + '<a href="#" class="fa fa-close"></a>'
            + '<span>' + date + '</span>'
            + '</span>'
            + '</li>'
    }

    this.showNotification = function (id_connected, selectorPulseButton, selectorNbNotification) {

        const totalMsgNotRead = this.getTotalMsgNotRead(id_connected)

        if (this.checkContainer(selectorPulseButton) && this.checkContainer(selectorNbNotification)) {
            $(selectorPulseButton).text(totalMsgNotRead > 0 ? totalMsgNotRead : '')
            $(selectorNbNotification).text(totalMsgNotRead > 0 ? "Vous avez " + totalMsgNotRead + " notifications" : "Aucune notifications")
        }
    }

    this.showTagsNotif = function (selectorContainer) {
        if (this.checkContainer(selectorContainer)) {
            $(selectorContainer).html(this.tagsNotification)
        }
    }
}