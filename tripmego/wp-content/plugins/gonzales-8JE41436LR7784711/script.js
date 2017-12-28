/*jslint browser: true*/

var gonzales;
gonzales = {
    menu: {
        init: function () {
            "use strict";
            var menuItem;

            menuItem = document.getElementById("wp-admin-bar-gonzales");
            if (menuItem) {
                menuItem.addEventListener("click", function () {
                    var panel = document.getElementById("gonzales");
                    //var page = document.getElementById("page");

                    if (panel) {
                        if (panel.style.display === "none") {
                            panel.style.display = "block";
                            //if (page) {
                            //    page.style.display = "none";
                            //}
                        } else {
                            panel.style.display = "none";
                            //if (page) {
                            //    page.style.display = "";
                            //}
                        }
                    }
                });
            }
        }
    },
    UI: {
        init: function () {
            "use strict";
            var elements, submitButton;

            elements = document.querySelectorAll("#gonzales .option-everwhere input[type=checkbox]");
            Array.prototype.forEach.call(elements, function (el) {
                el.addEventListener("change", function () {
                    var enabledCheckboxes = document.querySelectorAll(".options[data-id='" + this.getAttribute("id") + "'] input");
                    var newState = this.checked;

                    Array.prototype.forEach.call(enabledCheckboxes, function (elX) {
                        elX.disabled = !newState;
                    });


                    var disabledHere = document.querySelectorAll(".disable-here[data-id='" + this.getAttribute("id") + "'] input");
                    var newState = this.checked;

                    Array.prototype.forEach.call(disabledHere, function (elX) {
                        elX.disabled = newState;
                    });
                });
            });

            elements = document.querySelectorAll("#gonzales input[type=checkbox]");
            Array.prototype.forEach.call(elements, function (el) {
                el.addEventListener("change", function () {
                    document.gonzalesChanged = true;
                });
            });

            submitButton = document.getElementById("submit-gonzales");
            if (submitButton) {
                submitButton.addEventListener("click", function () {
                    document.gonzalesChanged = false;
                });
            }
        },
        protection: function () {
            "use strict";
            window.addEventListener("beforeunload", function (e) {
                if (document.gonzalesChanged) {
                    var confirmationMessage = "It looks like you have been editing configuration and tried to leave page without saving. Press cancel to stay on page.";
                    (e || window.event).returnValue = confirmationMessage;
                    return confirmationMessage;
                }
            });
        }
    },
    ready: function (fn) {
        "use strict";
        if (document.readyState !== "loading") {
            fn();
        } else {
            document.addEventListener("DOMContentLoaded", fn);
        }
    },
    init: function () {
        "use strict";
        gonzales.ready(gonzales.menu.init);
        gonzales.ready(gonzales.UI.init);
        gonzales.ready(gonzales.UI.protection);
    }
};

setTimeout(function(){ gonzales.init(); }, 100);
