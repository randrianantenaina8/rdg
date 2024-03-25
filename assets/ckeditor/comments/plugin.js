// Register a plugin with the name of 'comments'
CKEDITOR.plugins.add('comments', {
    // Declare the icons to be used from the icons directory
    icons: 'speech-bubble',
    init: function (editor) {
        // Add a new command to trigger the comments dialog
        editor.addCommand('comments', new CKEDITOR.dialogCommand('commentsDialog'))
        // Add a new button in the editor toolbar
        editor.ui.addButton('Comments', {
            label: 'Ajouter un commentaire',
            // The above created command goes here
            command: 'comments',
            toolbar: 'insert,11',
            icon: 'speech-bubble'
        })
        // Register the new dialog box to open from comments.js
        CKEDITOR.dialog.add('commentsDialog', this.path + 'dialogs/comments.js')
    }
});
