$(function () {

    var ViewModel = function () {
        this.visible = ko.observable(true);
    };

    ViewModel.prototype.mark = function (taskId) {
        $.post('/task/done/' + taskId, {}, function () {

        }.bind(this)).fail(function () {
            alert("error");
            this.visible(true);
        }.bind(this));

        this.visible(false);
    };

    ko.applyBindings(new ViewModel(), $('#task-list-container')[0]);
});