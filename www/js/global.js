var namespace = {}
namespace.stack = {}
namespace.stack.Application = function() {};

/**
 * Stack Application ctor
 * @param uri
 * @param params
 * @param method
 * @return {*}
 */
namespace.stack.Application.prototype.request = function(uri, params, method) {
    if(method === undefined) {
        method = 'POST';
    }

    return jQuery.ajax({
        url: uri,
        type: method,
        data: params
    }).always(function(response) {
            if(response.status) {
                $('#debug').html('<pre>' + response.status + ':' + response.responseText + '</pre>');
            } else {
                $('#debug').html(response);
            }
    });
}

namespace.stack.Application.prototype.login = function(uName, uPass) {
    var params = {
        uName: uName,
        uPass: uPass
    };
    return this.request('/login', params, 'POST');
}