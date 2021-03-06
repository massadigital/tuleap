export default AddInPlaceCtrl;

AddInPlaceCtrl.$inject = ["$scope", "$element", "$timeout"];

function AddInPlaceCtrl($scope, $element, $timeout) {
    var self = this,
        is_open = false;

    self.summary = "";
    self.isOpen = isOpen;
    self.close = close;
    self.open = open;
    self.submit = submit;
    self.$onInit = init;

    function init() {
        $scope.$watch(isOpen, function(new_value) {
            if (new_value) {
                $timeout(autoFocusInput);
            }
        });
    }

    function isOpen() {
        return is_open;
    }

    function close() {
        blurInput();
        self.summary = "";
        is_open = false;
    }

    function open() {
        is_open = true;
    }

    function submit() {
        var label = self.summary.trim();

        if (!label) {
            return;
        }

        self.createItem(label, self.column);

        self.summary = "";
        autoFocusInput();
    }

    function autoFocusInput() {
        $element.find("input[type=text]").focus();
    }

    function blurInput() {
        $element.find("input[type=text]").blur();
    }
}
