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
    var request = jQuery.get('/login', params);
    request.success(function(response) {
        console.log(response)
    });
    request.error(function() {
        console.log(this)
    });
}