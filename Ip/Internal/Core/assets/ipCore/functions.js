/*!
 Default ImpressPages functions
 */
function ipFileUrl(path) {
    for (var prefix in ipUrlOverrides) {
        if (path.indexOf(prefix) == 0) {
            return ipUrlOverrides[prefix] + path.substr(prefix.length);
        }
    }
    return ip.baseUrl + path;
}

function ipThemeUrl(path) {
    return ipFileUrl('Theme/' + ip.theme + '/' + path);
}

function ipHomeUrl() {
    return ip.homeUrl;
}


function ipInitForms() {
    if (typeof(ipModuleFormPublic) !== 'undefined') {
        ipModuleFormPublic.init();
    }
}
