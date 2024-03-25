// # EasyAdmin back-office custom Js features # \\
// Media Library - Manage previews
const easyadminThumbnail = document.getElementById('ea-index-S3File')
const lightboxThumbnail = document.querySelector('.ea-lightbox-thumbnail')
const url = process.env.S3_ENDPOINT
const bucket = process.env.S3_BUCKET

if (easyadminThumbnail) {
    const tableRows = document.querySelector('.datagrid').rows
    Array.from(tableRows).forEach(function (row) {
        let fileHTML = row.cells[1].childNodes[1].firstElementChild
        let category = row.cells[2].innerText
        let originName = row.cells[5].innerText
        fileHTML.offsetParent.setAttribute('style', 'pointer-events: none')

        let fileName = row.cells[5].innerText
        function hasMimeType(extension) {
            return fileName.includes(extension)
        }

        function getMimeTypeIconPath(iconPath) {
            return fileHTML.setAttribute('src', iconPath)
        }

        if (fileHTML.hasAttribute('src')) {
            if (hasMimeType('.pdf')) getMimeTypeIconPath('/build/images/icons/picto-pdf.png')
            else if (hasMimeType('.txt')) getMimeTypeIconPath('/build/images/icons/picto-txt.png')
            else if (hasMimeType('.md')) getMimeTypeIconPath('/build/images/icons/picto-md.png')
            else if (hasMimeType('.csv')) getMimeTypeIconPath('/build/images/icons/picto-csv.png')
            else if (hasMimeType('.ods')) getMimeTypeIconPath('/build/images/icons/picto-ods.png')
            else if (hasMimeType('.odt')) getMimeTypeIconPath('/build/images/icons/picto-odt.png')
            else if (hasMimeType('.docx')) getMimeTypeIconPath('/build/images/icons/picto-docx.png')
            else if (hasMimeType('.ppt') || hasMimeType('.pptx')) getMimeTypeIconPath('/build/images/icons/picto-ppt.png')
            else if (hasMimeType('.xls') || hasMimeType('.xlsx')) getMimeTypeIconPath('/build/images/icons/picto-xlsx.png')
            else getMimeTypeIconPath(buildFilePath())
        }

        function buildFilePath() {
            return `${url}${bucket}/${category}/${originName}`
        }

        let iconThumbnailRows = row.querySelectorAll('.ea-lightbox')
        for (const iconNode of iconThumbnailRows) {
            if (iconNode.hasChildNodes()) {
                iconNode.firstElementChild.remove()
            }
        }
    })
    // force https for Media Library filter button in production environment
    forceHttps()

}

function forceHttps() {
    const actionFiltersButton = document.querySelector('.action-filters-button').href
    let forceHttps = actionFiltersButton.replace('http://', 'https://')

    return forceHttps
}
