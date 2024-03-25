// Ajouter un élément à l'éditeur lorsque la fenêtre de dialogue est validée
CKEDITOR.dialog.add('logigramDialog', function(editor) {
  return {
    title: 'Lien vers le logigramme interractif',
    minWidth: 400,
    minHeight: 100,
    contents: [
      {
        id: 'general',
        label: 'General',
        elements: [
          {
            type: 'text',
            id: 'buttonText',
            label: 'Texte du bouton vers le logigramme de cette page'
          }
        ]
      }
    ],
    onOk: function() {
      let buttonText = this.getValueOf('general', 'buttonText');

      let element = CKEDITOR.dom.element.createFromHtml('<a href="javascript: document.body.scrollIntoView(false);">' + buttonText + '</a>');

      editor.insertElement(element);
    }
  };
});
