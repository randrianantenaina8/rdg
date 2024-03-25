CKEDITOR.plugins.add('dropoff', {
    init: function (editor) {

        function rejectDrop(event)
        {
            event.data.preventDefault(true);
        }

        editor.on('contentDom', function () {
            editor.document.on('drop', rejectDrop);
        });

    }
});
