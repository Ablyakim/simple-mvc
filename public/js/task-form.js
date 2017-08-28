$(function () {

    var ViewModel = function () {
        this.imageData = null;
        this.imageSrc = ko.observable();

        this.fileData = ko.observable({
            dataURL: ko.observable()
        });

        this.fileData.subscribe(function (data) {
            this.imageSrc(data.dataURL());
            this.imageData = data;
        }.bind(this));
    };

    ViewModel.prototype.clear = function () {
        this.imageSrc(null);
        if (!this.imageData) {
            return;
        }

        this.imageData.clear();
    };

    ViewModel.prototype.preview = function () {
        var formFields = $('#create-task-form').serializeArray();

        var clForm = $('<form>', {
            'style': 'display:none;position: fixed; left: -10000px',
            'target': '_blank',
            'action': '/task/preview',
            'method': 'POST',
            'enctype': 'multipart/form-data'
        });

        formFields.forEach(function (field) {
            var el = $('<input>', {
                'value': field.value,
                'name': field.name
            });
            clForm.append(el);
        });

        $('body').append(clForm);

        clForm.append($('#file').clone());

        clForm.submit();
        clForm.remove();
    };

    ko.applyBindings(new ViewModel(), $('#task-form-container')[0]);
});