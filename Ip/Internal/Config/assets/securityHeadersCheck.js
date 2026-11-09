// Settings → Advanced: shows which security headers the home page sends, as a visitor sees it.
(function () {
    "use strict";
    var config = window.ipSecurityHeadersCheck || {url: '/', https: false, text: {}};
    var t = function (key, fallback) {
        return (config.text && config.text[key]) || fallback;
    };
    var checks = [
        {name: 'X-Content-Type-Options'},
        {name: 'X-Frame-Options'},
        {name: 'Referrer-Policy'},
        {name: 'Permissions-Policy'},
        {name: 'Strict-Transport-Security', httpsOnly: true},
        {name: 'Content-Security-Policy', alt: 'Content-Security-Policy-Report-Only', optional: true},
        {name: 'X-Powered-By', unwanted: true}
    ];
    var running = false;

    var cell = function (tag, text, cls) {
        var el = document.createElement(tag);
        el.textContent = text;
        if (cls) {
            el.className = cls;
        }
        return el;
    };

    var render = function (headers) {
        var table = document.createElement('table');
        table.className = 'table';
        var headRow = document.createElement('tr');
        [t('header', 'Header'), t('value', 'Value'), t('status', 'Status')].forEach(function (label) {
            var th = cell('th', label);
            th.setAttribute('scope', 'col');
            headRow.appendChild(th);
        });
        var thead = document.createElement('thead');
        thead.appendChild(headRow);
        table.appendChild(thead);
        var tbody = document.createElement('tbody');
        checks.forEach(function (check) {
            var name = check.name;
            var value = headers.get(name);
            if (!value && check.alt && headers.get(check.alt)) {
                name = check.alt;
                value = headers.get(check.alt);
            }
            var status, cls;
            if (check.unwanted) {
                status = value ? t('revealsPhp', 'Reveals the PHP version') : t('hidden', 'Hidden');
                cls = value ? 'text-warning' : 'text-success';
            } else if (value) {
                status = name === check.alt ? t('testing', 'Testing (report-only)') : t('sent', 'Sent');
                cls = 'text-success';
            } else if (check.httpsOnly && !config.https) {
                status = t('needsHttps', 'Needs HTTPS');
                cls = 'text-muted';
            } else {
                status = check.optional ? t('optional', 'Not set (optional)') : t('missing', 'Missing');
                cls = check.optional ? 'text-muted' : 'text-warning';
            }
            var tr = document.createElement('tr');
            tr.appendChild(cell('td', name));
            var valueCell = cell('td', value || '—');
            valueCell.style.overflowWrap = 'anywhere';
            tr.appendChild(valueCell);
            tr.appendChild(cell('td', status, cls));
            tbody.appendChild(tr);
        });
        table.appendChild(tbody);
        return table;
    };

    var run = function () {
        var box = document.querySelector('.ipsSecurityHeadersResult');
        if (!box || running || box.closest('.hidden')) {
            return;
        }
        running = true;
        box.textContent = t('checking', 'Checking…');
        // credentials: 'omit' so the page is checked as a visitor sees it, not as a logged-in admin
        fetch(config.url, {credentials: 'omit', cache: 'no-store'}).then(function (response) {
            box.textContent = '';
            box.appendChild(render(response.headers));
        }).catch(function () {
            box.textContent = t('failed', 'Couldn\'t load the home page to check it.');
        }).then(function () {
            running = false;
        });
    };

    $(document).on('ipSecurityHeadersCheck', function () {
        setTimeout(run, 0);
    });
    $(function () {
        $('.ipsSecurityHeadersRecheck').on('click', run);
        run();
    });
})();
