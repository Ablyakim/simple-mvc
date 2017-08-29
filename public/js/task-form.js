$(function () {

    var ViewModel = function () {
        this.imageData = null;
        this.imageSrc = ko.observable();

        this.taskData = {
            username: ko.observable(window.taskData.username),
            email: ko.observable(window.taskData.email),
            content: ko.observable(window.taskData.content)
        };

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
        $('#task-preview-modal').modal('show');
    };

    ko.applyBindings(new ViewModel(), $('#task-form-container')[0]);
});