// Comments plugin

CKEDITOR.dialog.add('commentsDialog', function(editor) {

    let currentComment;

    function formatDate(date){
        let now = new Date();

        let day = now.getDate();
        let month = now.getMonth() + 1; 
        let year = now.getFullYear();
        let hour = now.getHours();
        let minutes = now.getMinutes();

        if (day < 10) {
            day = '0' + day;
        }
        if (month < 10) {
            month = '0' + month;
        }
        if (hour < 10) {
            hour = '0' + hour;
        }
        if (minutes < 10) {
            minutes = '0' + minutes;
        }

        let amOuPm = hour >= 12 ? 'pm' : 'am';
        hour = hour % 12;
        hour = hour ? hour : 12; 

        return day + '/' + month + '/' + year + ' ' + hour + ':' + minutes + ' ' + amOuPm;
    }

    let deleteButton = {
        type: 'button',
        id: 'deleteButton',
        label: 'Supprimer',
        onClick: function() {
            currentComment.setAttribute('style', '')
            currentComment.setAttribute('data-reply', '[]')
            CKEDITOR.dialog.getCurrent().hide();
        }
    }

    return {
        title: 'Commentaires du texte sélectionné',
        minWidth: 400,
        minHeight: 250,
        contents: [
            {
                id: 'commentsByUser',
                label: 'Comments added by user',
                elements: [
                    {
                        type: 'html',
                        id: 'existingComments',
                        style: '',
                        html: ''
                    },
                    {
                        type: 'textarea',
                        id: 'reply',
                        label: 'Ajouter un commentaire',
                        style: '',
                        rows: 4,
                        cols: 8,
                        'default': '',
                        validate: function() {
                            if (this.getValue().length > 100) {
                                alert('100 caractères maximum')
                                return false
                            } else if (this.getValue().length === 0) {
                                alert('Ne peut pas être vide')
                                return false
                            }
                            else {
                                return this
                            }
                        }
                    }
                ]
            }
        ],
    
        onShow: function() {
            
            let existingCommentsElement = this.getContentElement('commentsByUser', 'existingComments').getElement();
            existingCommentsElement.$.innerHTML = '';

            let element = editor.getSelection().getStartElement();
           
            if (element){
                element = element.getAscendant('comment', true);
            }
                
            if (!element || element.getName() != 'comment'){
                element = editor.document.createElement('comment');
                let selectedText = editor.getSelection().getNative();
                element.setHtml(selectedText)
                editor.insertElement(element);
            }
            
            if (!element.getAttribute('data-reply')){
                element.setAttribute('data-reply', '[]')
            }

            element.setAttribute('style', 'background-color: #FEDE00')
    
            commentsData = JSON.parse(element.getAttribute('data-reply'))
            commentsData[0] && commentsData.forEach(comment => {
                existingCommentsElement.$.innerHTML += '<p>'+ formatDate(comment.date) + ' : ' + comment.text + "</p>"
            })

            currentComment = element;
        },
        
        onOk: function() {

            let element = currentComment

            let reply = this.getValueOf('commentsByUser','reply');
           
            commentsData = element.getAttribute('data-reply') 
            
            let jsonData = JSON.parse(commentsData)
            let replyData = {
                date : new Date(),
                text: reply
            }
            jsonData.push(replyData)
            commentsData = JSON.stringify(jsonData)
            element.setAttribute('data-reply', commentsData)

            let tooltipData = ""

            jsonData.forEach(reply => {
                tooltipData = tooltipData + formatDate(reply.date) + " : " + reply.text + "\x0A"
            })

            element.setAttribute('title', tooltipData)
        },
        buttons: [deleteButton, CKEDITOR.dialog.cancelButton, CKEDITOR.dialog.okButton]
    }
});
