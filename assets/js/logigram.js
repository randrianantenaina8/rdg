// This script handles logigrams logic 

export const renderLogigram = (logigram) => {

    function isFr () {
        return window.location.href.includes("/fr/")
    }
    
    let logigramEl = document.querySelector('.logigramContent');
    
    let fr = isFr()
    
    let logigramTitleEl = document.querySelector('.logigramTitle')
    let logigramSubTitleEl = document.querySelector('.logigramSubTitle')
    
    if (fr){
        logigramTitleEl.innerHTML = logigram.title ? logigram.title : ""
        logigramSubTitleEl.innerHTML = logigram.subTitle ? logigram.subTitle : ""
    }else{
        logigramTitleEl.innerHTML = logigram.title ? logigram.title : ""
        logigramSubTitleEl.innerHTML = logigram.subTitle ? logigram.subTitle : ""
    }
    
    // Logigram steps loop
    logigram.logigramSteps && logigram.logigramSteps.forEach((step, stepIndex) => {
    
        let stepEl = document.createElement('div')
        stepEl.classList.add("step")
        stepEl.id = "step-"+stepIndex
    
        // hide all steps exept the first
        if (stepIndex > 0){
            stepEl.setAttribute("hidden", true);
            let arrow = document.createElement("img");
            arrow.setAttribute("src", "data:image/svg+xml;utf8;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyNCAyNCI+PHBhdGggZD0iTTExLjI5MywyMi43MDdhMSwxLDAsMCwwLDEuNDE0LDBsNS01YTEsMSwwLDAsMC0xLjQxNC0xLjQxNEwxMywxOS41ODZWMmExLDEsMCwwLDAtMiwwVjE5LjU4Nkw3LjcwNywxNi4yOTNhMSwxLDAsMCwwLTEuNDE0LDEuNDE0WiIvPjwvc3ZnPg==");
            arrow.classList.add("logigramArrow")
            stepEl.appendChild(arrow);
        }
    
        logigramEl.appendChild(stepEl)
    
        let titleEl = document.createElement('h2');
        
        let choicesEl = document.createElement('div');
        let nextStepsEl = document.createElement('div');
     
        titleEl.innerHTML = step.title;
        
        titleEl.classList.add("logigramStepTitle")
        stepEl.appendChild(titleEl);
    
        // If the step have an info box
        if (step.info){
            let infoEl = document.createElement('div');
    
            let infoIcon = document.createElement('img')
            infoIcon.src= "https://img.icons8.com/material-rounded/24/000000/info.png"
            infoIcon.classList.add('infoIcon')
            infoEl.appendChild(infoIcon)
               
            infoEl.innerHTML += step.info; 
            
            infoEl.classList.add("logigramStepInfo")
            titleEl.appendChild(infoEl);
        }
    
        // Displays the choices button for the step
        step.choices && step.choices.forEach((choice, index) => {
                
            let choiceEl = document.createElement('button');
            
            choiceEl.innerHTML = choice; 
            
            choiceEl.classList.add("logigramStepChoice")
            choicesEl.appendChild(choiceEl); 
    
            choiceEl.addEventListener("click", function() {

                let answer = document.querySelector('#logigramNextStepChoice-'+stepIndex+"-"+index)
                let isHidden = answer.getAttribute("hidden") === "true";

                if (isHidden){
                    answer.removeAttribute("hidden")
                }else {
                    answer.setAttribute("hidden", true)
                }
                
                if (choiceEl.classList.contains("choiceClicked")){
                    choiceEl.classList.remove("choiceClicked")
                } else {
                    choiceEl.classList.add("choiceClicked")
                }
                
                // Checks if they are next steps and display them
                if (step.nextSteps[index].nextStep !== 0 && step.nextSteps[index].nextStep !== null){
                    let nextStepEl = document.querySelector('#step-'+step.nextSteps[index].nextStep)
                    let isNextHidden = nextStepEl.getAttribute("hidden") === "true";

                    if (isNextHidden){
                        nextStepEl.removeAttribute("hidden");
                        
                    }else {
                        nextStepEl.setAttribute('hidden', true)
                    }
                   
                }

                //Hide the other answers and steps if one is already selected
                let selector = 'logigramNextStepChoice-'+stepIndex+'-'
                let otherAnswers = document.querySelectorAll(`[id^=${selector}]`)
                otherAnswers = Array.from(otherAnswers).filter(element => element !== answer);

                otherAnswers.forEach((element) => {
                    element.setAttribute('hidden', true)
                });

                for (let i = 0; i < step.nextSteps.length; i++) {
                    if (step.nextSteps[i].nextStep !== step.nextSteps[index].nextStep && step.nextSteps[i].nextStep !== 0 && step.nextSteps[i].nextStep !== null){
                        let nextStepToHide = document.querySelector('#step-'+step.nextSteps[i].nextStep)
                        nextStepToHide.setAttribute('hidden', true)
                    }
                }
            });	
        });
    
        stepEl.appendChild(choicesEl);
    
        // Displays the answers for the selected choice
        step.nextSteps && step.nextSteps.forEach((choice, choiceIndex) => {
            let nextStepEl = document.createElement('div');
    
            nextStepEl.innerHTML = choice.title;
        
            // If the answer have an info box
            if (choice.info){
                let infoEl = document.createElement('div');
                let infoIcon = document.createElement('img')
                infoIcon.src= "https://img.icons8.com/material-rounded/24/000000/info.png"
                infoEl.appendChild(infoIcon)
    
                infoEl.innerHTML += choice.info; 
                 
                infoEl.classList.add("logigramStepInfo")
                nextStepEl.appendChild(infoEl);
            }
    
            nextStepEl.classList.add("logigramNextStepChoice")			
            nextStepEl.id = "logigramNextStepChoice-"+stepIndex+"-"+choiceIndex
            nextStepEl.setAttribute("hidden", true);
            nextStepsEl.appendChild(nextStepEl);
        })
    
        stepEl.appendChild(nextStepsEl);			
    });

}

try{
    
    let logigram = JSON.parse(document.querySelector('.logigramBody').dataset.items);
    if (logigram){
        console.log(logigram)
        renderLogigram(logigram)
    }
}
catch(err) {
    console.warn('Logigram loading error', err)
}

// Prevent easyadmin bug by auto saving the form when there are several steps
const form = document.querySelector('#edit-Logigram-form, #new-Logigram-form');
const parentDiv = form.querySelector('.field-collection');
const widget = parentDiv.querySelector('.form-widget');
const logigramAddStep = widget.querySelectorAll('.field-collection-add-button');
const saveAndContinueButton = document.querySelector('.action-saveAndContinue');

if(form){
    window.scrollTo({
        top: document.body.scrollHeight,
        behavior: 'smooth'
    });
}

logigramAddStep[logigramAddStep.length-1].addEventListener("click", function() {

    if (form.id === 'new-Logigram-form'){
        const saveAndReturn = document.querySelector('.action-saveAndReturn');
        saveAndReturn.click();
    }

    setTimeout(function() {

        const elements = document.querySelectorAll('[id^="Logigram_logigramSteps_"][id$="_title"]:not([id*="logigramNextSteps"])');

        const titleInput = document.querySelector('#'+elements[elements.length-1].id);
        titleInput.value = `Question`;
        
        saveAndContinueButton.click();
        }, 1000);

});
