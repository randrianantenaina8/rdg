// search data to dataverse and open a new page with results
try {
    let searchDataverse = document.getElementById('searchDataverse')
    if (searchDataverse) {
        let dataDepositUrl = searchDataverse.dataset.depositUrl
        searchDataverse.addEventListener("submit", (e) => {
            e.preventDefault();
            let valueToSearch = e.currentTarget[0].value;
            window.open(dataDepositUrl + valueToSearch, '_blank');
        });
    }
}
catch(err) {
    console.warn('searchDataverse', err);
}

// scroll top of page
try {
    let target = document.getElementById("top");
    let backToTopBtn = document.getElementById("backtotop");
    let rootElement = document.documentElement;
    function callback(entries, observer) {
        entries.forEach(elem => {
            if (!elem.isIntersecting) {
                backToTopBtn.classList.add("showBtn");
            } else {
                backToTopBtn.classList.remove("showBtn");
            }
        });
    }
    function backToTop() {
        rootElement.scrollTo({
            top: 0,
            left: 0,
            behavior: "smooth"
        });
    }
    backToTopBtn.addEventListener("click", backToTop);
    let observer = new IntersectionObserver(callback);
    observer.observe(target);
}
catch(err) {
    console.warn('scroll top of page', err)
}

// Carroussel to Espaces institutionnels
try {
    let containerInstitut = document.querySelectorAll('.institut-wrapper[id^="institut-wrapper-"]');
    
    containerInstitut.forEach(() => {

        let items =JSON.parse(document.querySelector('.js-carousel-items').dataset.items);
        let currentIndex = 0;
        const ITEMS_PER_SLIDE = 4;

        function renderItems(prevNav) {
            let carouselItems = document.getElementById('institut-carousel-inner');
            let html = '';
            html += `<div class="carousel-item active">`
            
            for (let i = currentIndex; i < currentIndex + ITEMS_PER_SLIDE; i++) {
            
            html += `<div class="col-md-3 col-6 fr-px-3v">
                        <div class="fr-tile fr-enlarge-link tile-height tile-bottom-color">` ;

                if (items[i%items.length].urlCollection) {
                    html += `<div class="fr-tile__body tile-title-padding">
                                <h4 class="fr-tile__title">
                                    <a class="fr-tile__link" href="${ items[i%items.length].urlCollection }" target="_blank"></a>
                                </h4>
                            </div>`
                } else {
                    html += `<div class="fr-tile__body tile-title-padding">
                                <h4 class="fr-tile__title">
                                    <a class="fr-tile__link" href="#"></a>
                                </h4>
                            </div>`
                }

                html += `<div class="fr-mx-2w fr-py-2w fr-py-75">`

                if (items[i%items.length].image) {
                    html += `<img src="${ items[i%items.length].image }" class="fr-responsive-img">`
                } else {
                    html += `<img src="build/images/novisual.png" class="fr-responsive-img" alt="">`
                }

                html += `</div>
                    </div>
                </div>`
            
            }   

            html += `</div>`
            html += `<div class="carousel-item">`

            for (let i = prevNav ? currentIndex-4 : currentIndex+4; i < currentIndex+4 + ITEMS_PER_SLIDE ; i++) {
            
                html += `<div class="col-md-3 col-6 fr-px-3v">
                        <div class="fr-tile fr-enlarge-link tile-height tile-bottom-color">`
    
                if (items[i%items.length].urlCollection){
                    html += `<div class="fr-tile__body tile-title-padding">
                                <h4 class="fr-tile__title">
                                    <a class="fr-tile__link" href="${ items[i%items.length].urlCollection }" target="_blank"></a>
                                </h4>
                            </div>`
                } else {
                    html += `<div class="fr-tile__body tile-title-padding">
                                <h4 class="fr-tile__title">
                                    <a class="fr-tile__link" href="#"></a>
                                </h4>
                            </div>`
                }
    
                html += `<div class="fr-mx-2w fr-py-2w fr-py-75">`
    
                if (items[i%items.length].image){
                    html += `<img src="${ items[i%items.length].image }" class="fr-responsive-img">`
                } else {
                    html += `<img src="build/images/novisual.png" class="fr-responsive-img" alt="">`
                }

                html += `</div>
                    </div>
                </div>`    
            }

            html += `</div>`
            carouselItems.innerHTML = html;
        
        }

        document.querySelector('#prev-btn').addEventListener('click', () => {

            var carouselItem = document.querySelector('div.carousel-item.active')

            carouselItem.classList.add('carousel-slide');
            carouselItem.style.transform = 'translateX(100%)';
            currentIndex = (currentIndex - ITEMS_PER_SLIDE + items.length) % items.length;

            ["transitionend", "webkitTransitionEnd", "mozTransitionEnd"].forEach(function(transition) {
                document.addEventListener(transition, handler, false);
            });
        
            function handler() {
                renderItems(true);
                carouselItem.classList.remove('carousel-slide');
            }
        
        });

        function renderNext(){
            var carouselItem = document.querySelector('div.carousel-item.active')

            carouselItem.classList.add('carousel-slide');
            carouselItem.style.transform = 'translateX(-100%)';
            currentIndex = (currentIndex + ITEMS_PER_SLIDE) % items.length;

            ["transitionend", "webkitTransitionEnd", "mozTransitionEnd"].forEach(function(transition) {
                document.addEventListener(transition, handler, false);
            });
        
            function handler() {
                carouselItem.classList.remove('carousel-slide');
                renderItems();
            }
        }

        document.querySelector('#next-btn').addEventListener('click', () => {

            renderNext()
        
        });
        
        renderItems(); 
        setInterval(renderNext, 5000);      

    })
}
catch(err) {
    console.warn('Carroussel to Institutions', err)
}

try{

    let items2 = document.querySelectorAll('.mobile .carousel .carousel-item');

    items2.forEach((el) => {
        const minPerSlide = 2;
        let next = el.nextElementSibling;
        for (var i=1; i<minPerSlide; i++) {
            if (!next) {
                // wrap carousel by using first child
                next = items2[0];
            }
            let cloneChild = next.cloneNode(true);
            el.appendChild(cloneChild.children[0]);
            next = next.nextElementSibling;
        }
    });

}
catch(err){
    console.warn('Carroussel Instituts tablet', err)
}

// Carroussel to Jeu de données
try {
    let containerDatagames = document.querySelector('.datagames-wrapper');
    if (containerDatagames) {
        let items2 = containerDatagames.querySelectorAll('.carousel .carousel-item');

        items2.forEach((el) => {
            const minPerSlide = 2;
            let next = el.nextElementSibling;
            for (var i=1; i<minPerSlide; i++) {
                if (!next) {
                    // wrap carousel by using first child
                    next = items2[0];
                }
                let cloneChild = next.cloneNode(true);
                el.appendChild(cloneChild.children[0]);
                next = next.nextElementSibling;
            }
        });
    }
}
catch(err) {
    console.warn('Carroussel Instituts', err)
}

// homepage change current language for cookies select (tarteaucitron.js)

let currentLanguage = document.documentElement.lang;
window.tarteaucitronForceLanguage = currentLanguage;

// contact page
// button reset
try {
    function resetForm() { 
        document.getElementById("contact_subject_email").value = "";
        document.getElementById("contact_subject_message").value = "";
        document.getElementById("contact_subject_subject").selectedIndex = 0;
    }
    let btnRsetForm = document.getElementById("btnresetform");
    if (btnRsetForm) {
        btnRsetForm.addEventListener("click", resetForm);
    }
}
catch(err) {
    console.warn('contact page | reset button', err)
}

// # 15/09/2022 - AJAX request to generate alerts disabled
// Replaced with a DOMContentLoaded Event:
window.addEventListener('DOMContentLoaded', (event) => {
    const alerts = document.querySelectorAll('.alert__message, .alert__message--text')

    if (alerts) {
        for (const alert of alerts) {
            alert.style.display = 'block'
        }
    }
})

/**
 * NAVBAR MENU HOVER
 * 
 */
;(function(){

    let hover = false

    const _init = () => {
        Array.from(document.querySelectorAll('.fr-nav__item')).forEach(elem=>{
            elem.addEventListener('mouseenter', _mouseenter)
            elem.addEventListener('mouseleave', _mouseleave)
        })
        reportWindowSize()
        window.onresize = reportWindowSize
    }

    const _mouseenter = (evt) => {
        evt.target.dataset.collapse = true
        deploy(evt.target)
    }

    const _mouseleave = (evt) => {
        evt.target.dataset.collapse = null
        evt.target.removeAttribute('data-collapse')
        collapse(evt.target)
    }

    const collapse = (elemMenu) => {
        if(hover && elemMenu.querySelector('.fr-nav__btn'))
            elemMenu.querySelector('.fr-nav__btn').setAttribute('aria-expanded', false)
    }

    const deploy = elemMenu => {
        if(hover && elemMenu.querySelector('.fr-nav__btn'))
            elemMenu.querySelector('.fr-nav__btn').setAttribute('aria-expanded', true)
    }

    const collapseAllDown = () => {
        Array.from(document.querySelectorAll('.fr-nav__item')).forEach(elem=>{
            try {
                if(elem.hasAttribute('data-collapse')) {
                    collapse(elem)
                }
                elem.removeAttribute('data-collapse')
            }
            catch(err){}
        })
    }

    function reportWindowSize() {
        let breakpointUp = window.innerWidth > 991
        if(breakpointUp!==hover && !breakpointUp) {
            collapseAllDown()
        }
        hover = breakpointUp
    }

    function ready(callback){
        // in case the document is already rendered
        if (document.readyState!='loading') callback();
        // modern browsers
        else if (document.addEventListener) document.addEventListener('DOMContentLoaded', callback);
        // IE <= 8
        else document.attachEvent('onreadystatechange', function(){
            if (document.readyState=='complete') callback();
        });
    }

    ready(_init)
})()

;(function(){
    let tmOutResize = null

    const _resize = () => {
        for (let key in window.POINTS) {
            const cardInteract = document.querySelector(`#id_map_${key}`),
                imgInteract = cardInteract.querySelector('img'),
                SIZE = imgInteract.getBoundingClientRect()
            if(!cardInteract) {
                console.warn('error', `card wit id id_map_${key} don't exists`)
                continue
            }
            cardInteract.style.setProperty('--width', SIZE.width)
            cardInteract.style.setProperty('--height',SIZE.height)
        }
    }

    const resizing = () => {
        if (tmOutResize) clearTimeout(tmOutResize)
        for (let key in window.POINTS) {
            const cardInteract = document.querySelector(`#id_map_${key}`)
            if(!cardInteract) {
                console.warn('error', `card wit id id_map_${key} don't exists`)
                continue
            }
            cardInteract.classList.add('hide-point')
        }
        tmOutResize = setTimeout(stopResize, 1000)
    }

    const stopResize = () => {
        for (let key in window.POINTS) {
            const cardInteract = document.querySelector(`#id_map_${key}`)
            if(!cardInteract) {
                console.warn('error', `card wit id id_map_${key} don't exists`)
                continue
            }
            cardInteract.classList.remove('hide-point')
        }
        _resize()
    }

    const linkMapClick = (el) => {
        el.preventDefault()
        try {
            const ref = JSON.parse(el.target.closest('.card-interact__point').dataset.ref)
            if (ref.institution) {
                localStorage.setItem('hsh', ref.institution)
                return document.location.href=mapUtils.uri.institution
            }
            if(ref.dataworkshop) {
                localStorage.setItem('hsh', ref.dataworkshop)
                return document.location.href=mapUtils.uri.dataworkshop
            }
        }
        catch(err) {
            console.log('unidentified ref')
        }
    }

    const initLink = (el) => {
        el.addEventListener('click', linkMapClick)
    }

    const removeDNone = (id) => {
        const elem = document.querySelector(`#${id}`)
        if(elem?.classList.contains('card-interact__labels__none')) elem.classList.remove('card-interact__labels__none')
    }
     
    const _init = () => {
        _resize()
        for (let key in window.POINTS) {
            const cardInteract = document.querySelector(`#id_map_${key}`)
            window.POINTS[key].forEach(pointSelected => {

                const sClass = (pointSelected.institution)?' etablis':' atelier'
                cardInteract.insertAdjacentHTML('beforeend', `<div id="pi_${pointSelected.id}" class="card-interact__point${sClass}" style="--top:${pointSelected.point.y};--left:${pointSelected.point.x};" data-ref="${btoa(JSON.stringify(pointSelected))}"><div class="card-interact__point__tootltip"><div class="card-interact__point-title">${pointSelected.name}</div><a href=${mapUtils.uri.dataworkshop} class="card-interact__selector">${mapUtils.translate.labelSeeLink}</a></div></div>`)
                const div = document.getElementById(`pi_${pointSelected.id}`)

                if(pointSelected.institution) {
                    removeDNone(`id_labels_${key}`)
                    removeDNone(`id_labels_institution_${key}`)
                }
                if(pointSelected.dataworkshop) {
                    fetch('dataworkshop/?id='+pointSelected.dataworkshop)
                    .then(response => response.json())
                    .then(data => {
                        if (data.type == "Labellisé"){
                            div.style.backgroundColor = '#8585F6'
                        }else {
                            div.style.backgroundColor = '#56C8B6'  
                        }
                        console.log(data)
                        div.addEventListener('click', function() {
                            window.open(data.url, '_blank');
                        });
                    })
                    removeDNone(`id_labels_${key}`)
                    removeDNone(`id_label_datawokshop_${key}`)
                }
                });
        }
        Array.from(document.querySelectorAll('.card-interact__selector')).forEach(el=>initLink(el))
        window.addEventListener('resize', resizing)
    }
    window.addEventListener('load', function () {
        _init()
    })
})()

// Remove underlined href <a> tag link if first child is <img> or if is Table of contents link lists
const aTags = document.querySelectorAll('a')

for (const aTag of aTags) {
    let contentsTableListTag = aTag.parentElement.parentElement
    let isContentsTableWidget = contentsTableListTag.parentNode.className.includes('widget-toc')
    if (aTag.hasChildNodes() && aTag.firstChild.tagName === 'IMG' || contentsTableListTag.tagName === 'OL' || isContentsTableWidget) {
        aTag.setAttribute('style', 'box-shadow:none')
    }
}

// Footer Contact Form Textarea Conditional field (Other request)
let contactForm = document.getElementById('contactform')
if (contactForm) {
    let choiceField = document.getElementById('contact_subject_subject')
    let conditionalField = document.querySelector('.conditional-field')
    conditionalField.style.display = 'none'

    choiceField.addEventListener('change', handleSelectChange)
    function handleSelectChange() {
        if (choiceField.value === 'Autre demande' || choiceField.value === 'Other request') {
            conditionalField.style.display = 'block'
        } else {
            conditionalField.style.display = 'none'
        }
    }
}

// Remove object comments in front office
let easyAdmin = document.querySelector('.ea')

if (!easyAdmin) {
    const comments = document.querySelectorAll('comment')
    const attributes = ['style', 'title', 'value', 'data-reply']

    for (const comment of comments) {
        for (const attribute of attributes) {
            comment.removeAttribute(attribute)
        }
    }
}
