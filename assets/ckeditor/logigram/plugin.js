CKEDITOR.plugins.add('logigram', {
    icons: 'logigram', 
  
    init: function(editor) {
      
      editor.addCommand('logigramDialog', new CKEDITOR.dialogCommand('logigramDialog'));
      
      editor.ui.addButton('Logigram', {
        label: 'Logigram',
        command: 'logigramDialog',
        toolbar: 'insert'
      });
      CKEDITOR.dialog.add('logigramDialog', this.path + 'dialogs/logigramDialog.js')
    }
  });
  