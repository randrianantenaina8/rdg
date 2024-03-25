(function(){
    const form = document.querySelector('.card-interact__form'),
        coorX = form['center_map_coord[x]'],
        coorY = form['center_map_coord[y]'],
        fId = form['center_map_coord[id]'],
        inpName = form['center_map_coord[name]'],
        selectInstitution = form['center_map_coord[institution]'],
        selectWorkshop = form['center_map_coord[dataworkshop]'],
        buttonAdd = document.querySelector('.add-interactive'),
        btnDeletePoint = document.querySelector('.btn-point-delete'),
        btnReset = document.querySelector('[type="reset"]'),
        cardInteract = document.querySelector('.card-interact'),
        imgInteract = document.querySelector('.card-interact img')

    let removeUrl = null,
        btnAddValue = mapUtils.translate.labelAdd,
        btnModifyValue = mapUtils.translate.labelModify,
        pointSelected = null,
        SIZE = {},
        tmOutResize = null,
        elemSelected = null,
        btndisabled = false,
        btnModifyDisabled = false

    const btnDeleteClick = (evt) => {
        evt.preventDefault()
        fetch(removeUrl)
        .then(()=>{
            window.location.reload()
        })
        .catch(error=>{
            console.warn(error)
            document.location.refresh()
        })
        removeUrl = null
    }

    const hydrateForm = (o) => {
        fId.value = o.id || null
        coorX.value = o.point.x
        coorY.value = o.point.y
        inpName.value = o.name
        removeUrl = o.removeUrl || null
        btnDeletePoint.classList.remove('button-hidden')
    }

    const btnResetClick = () => {
        btnDeletePoint.classList.add('button-hidden')
        if(pointSelected) pointSelected.classList.remove('point-modify')
        buttonAdd.innerText = btnAddValue
        const provisPoint = document.querySelector('#pi_provisoire_point')
        if (provisPoint) provisPoint.remove()
        removeUrl = null
        pointSelected = null
    }

    const changeModifyMapBtn = () => {
        buttonAdd.innerText = btnModifyValue
        pointSelected.classList.add('point-modify')
        btnModifyDisabled = true
    }

    const pointInterestClick = evt => {
        const point = evt.target
        if (btnModifyDisabled) {
            elemSelected = evt.target
            elemSelected.classList.add('point-moving')
            initMouseDetect()
        } else {
            evt.preventDefault()
            pointSelected = evt.target
            const ref = (evt.target.dataset?.ref)?JSON.parse(atob(evt.target.dataset.ref)): null
            if (ref?.institution) {
                hydrateForm(ref)
                changeModifyMapBtn()
                return selectInstitution.value = ref.institution
            }
            if (ref?.dataworkshop) {
                hydrateForm(ref)
                changeModifyMapBtn()
                return selectWorkshop.value = ref.dataworkshop
            }
            if (ref?.id) {
                changeModifyMapBtn()
                return hydrateForm(ref)
            }
        }
    }

    const addPointClick = (elem) => {
        if(!elem) {
            return console.warn('error',elem)
        }
        elem.addEventListener('click', pointInterestClick)
    }

    const _resize = () => {
        SIZE = imgInteract.getBoundingClientRect()

        cardInteract.style.setProperty('--width', SIZE.width)
        cardInteract.style.setProperty('--height',SIZE.height)
    }



    const resizing = () => {
        if (tmOutResize) clearTimeout(tmOutResize)
        cardInteract.classList.add('hide-point')
        tmOutResize = setTimeout(stopResize, 1000)
    }

    const stopResize = () => {
        cardInteract.classList.remove('hide-point')
        _resize()
    }
     
    const _init = () => {
        _resize()
        POINTS.forEach(pointSelected => {
            const sClass = (pointSelected.institution)?' etablis':' atelier'
            let style = "background-color : "

            const currentURL = window.location.href;
            const urlParts = currentURL.split('/');
            const previousURL = urlParts.slice(0, -2).join('/');

            fetch(previousURL+'/dataworkshop/?id='+pointSelected.dataworkshop)
            .then(response => response.json())
            .then(data => {
                if (data.type == "Labellisé"){
                    style = style + '#8585F6'
                    
                }else {
                    style = style + '#56C8B6'  
                }
                cardInteract.insertAdjacentHTML('beforeend', `<div id="pi_${pointSelected.id}" class="card-interact__point${sClass}" style="--top:${pointSelected.point.y};--left:${pointSelected.point.x};${style}" data-ref="${btoa(JSON.stringify(pointSelected))}"><div class="card-interact__point__tootltip"><div class="card-interact__point-title">${pointSelected.name}</div><a href="#" class="card-interact__selector">Cliquez pour modifier</a></div></div>`)
            })
            
        });
        buttonAdd.addEventListener('click', toggleImageSelector)
        form.setAttribute('method', 'POST')
        imgInteract.style.border = "1px solid red";
        cardInteract.style.border = "1px solid blue";
        setTimeout(_addMoreActionEventsAfter,500)
        window.addEventListener('resize', resizing)
        window.addEventListener('keydown',keypress)
    }

    const keypress = evt => {
        if(evt.key === 'Escape' && btndisabled) {
            toggleImageSelector()
        }
    }

    const _addMoreActionEventsAfter = () => {
        Array.from(document.querySelectorAll('.card-interact__point')).forEach(elem=>addPointClick(elem))
        btnReset.addEventListener('click', btnResetClick)
        btnDeletePoint.addEventListener('click', btnDeleteClick)
    }

    const changePointInterest = (pointChange) => {
        POINTS = POINTS.map(point=>{
            if (`pi_${point.id}`=== pointChange.id) {
                return pointChange
            }
            return point
        })
    }

    const pointFromEvent = (evt) => {
        const x = (evt.offsetX / SIZE.width)
        const y = (evt.offsetY / SIZE.height)
        return {x,y}
    }

    const setElementStyle = (x,y) => {
        if (elemSelected) {
            elemSelected.style.setProperty('--top', y)
            elemSelected.style.setProperty('--left', x)
        }
    }

    const cardMouseMove = (evt) => {
        const {x,y} = pointFromEvent(evt)
        if (btnModifyDisabled) {
            setElementStyle(x,y)
        } else {
            coorX.value = x
            coorY.value = y
        }
    }

    const cardMouseDown = (evt) => {
        const {x,y} = pointFromEvent(evt)
        if (btnModifyDisabled) {
            setElementStyle(x,y)
            changePointInterest({
                id: elemSelected.id,
                point:{
                    x: elemSelected.style.getPropertyValue('--left'),
                    y: elemSelected.style.getPropertyValue('--top'),
                },
                name: elemSelected.dataset.title,
            })
            elemSelected.classList.remove('point-moving')
            elemSelected = null
            destroyMouseDetect()
            toggleImageModifySelector()
        } else {
            coorX.value = x
            coorY.value = y
            toggleImageSelector()
            cardInteract.insertAdjacentHTML('beforeend', `<div id="pi_provisoire_point" class="card-interact__point" style="--top:${y};--left:${x};" data-title="Point provisoire"></div>`)
            setTimeout(()=>inpName.focus(),300)
            destroyMouseDetect()
        }
    }

    const initMouseDetect = () => {
        imgInteract.addEventListener('mousemove', cardMouseMove)
        imgInteract.addEventListener('mousedown', cardMouseDown)
        cardInteract.style.cursor = 'crosshair'
        SIZE = imgInteract.getBoundingClientRect()
    }

    const destroyMouseDetect = () => {
        imgInteract.removeEventListener('mousemove', cardMouseMove)
        imgInteract.removeEventListener('mousedown', cardMouseDown)
        cardInteract.style.cursor = 'default'
    }

    const toggleImageSelector = () => {
        if(btnModifyDisabled) toggleImageModifySelector()
        if(btndisabled) {
            // Disable detector
            buttonAdd.classList.remove('button')
            buttonAdd.classList.add('button-secondary')
            destroyMouseDetect()
        } else {
            // Enable detector
            buttonAdd.classList.remove('button-secondary')
            buttonAdd.classList.add('button')
            initMouseDetect()
        }
        btndisabled = !btndisabled
    }

    const toggleImageModifySelector = () => {
        if(btndisabled) toggleImageSelector()
        if (btnModifyDisabled) {
            buttonAdd.classList.remove('button')
            buttonAdd.classList.add('button-secondary')
        } else {
            buttonAdd.classList.remove('button-secondary')
            buttonAdd.classList.add('button')
        }
        btnModifyDisabled = !btnModifyDisabled
    }

    window.addEventListener('load', function () {
        _init()
    })
})()
