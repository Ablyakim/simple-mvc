$(function () {
    var ViewModel = function (params) {
        this.params = params;

        this.checked = ko.observable(Boolean(params.status));
    };

    ViewModel.prototype.doneTask = function () {

        if (this.checked() || !this.params.loggedIn) {
            return;
        }

        $.post('/task/done/' + this.params.id, {})
            .fail(function () {
                    alert("error");
                    this.checked(false);
                }.bind(this)
            );

        this.checked(true);
    };

    window.taskItemsData.forEach(function (taskItem) {
        ko.applyBindings(new ViewModel(taskItem), $(taskItem.selector)[0]);
    });
});