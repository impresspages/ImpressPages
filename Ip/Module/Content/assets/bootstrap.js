function ipFileUrl(path)
{
    return ip.baseUrl + path;
}

function ipThemeUrl(path)
{
    return ipFileUrl('Theme/' + ip.theme + '/' + path);
}

function ipHomeUrl()
{
    return ip.homeUrl;
}