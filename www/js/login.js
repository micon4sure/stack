var namespace = {}
namespace.stack = {}
namespace.stack.Application = function() {};

/**
 * @param uName
 * @param uPass
 * @return {*}
 */
function login(uName, uPass) {
    var params = {
        uName: uName,
        uPass: uPass
    };
    var request = jQuery.getJSON(window.location, params);
    request.success(function(response) {
        if(response.authorized === true) {
            window.location = response.home;
        }
    });
    request.error(function() {
        console.log(this)
    });
}