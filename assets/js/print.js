import printJS from 'print-js'

let printableArea = document.querySelector('.print-input')

function printGuide() {
    printJS({
        printable: 'print-area',
        type: 'html',
        css: [
            '/build/app.css',
            '/build/bo_centermap.css',
            '/build/dsfr/dsfr/dsfr.min.css'
        ],
        scanStyles: false
    })
}

if (printableArea) {
    printableArea.addEventListener('click', printGuide)
    document.addEventListener('keydown', function(event) {event == 80}, true)
}
