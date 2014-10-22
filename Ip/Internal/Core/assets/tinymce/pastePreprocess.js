ipTinyMceConfigPastePreprocess = function (pl, o, allowedClasses) {

    var tmpContent = o.content;

    /* replace strong with bold */
    tmpContent = tmpContent.replace(/(<strong>)/ig, "<b>");
    tmpContent = tmpContent.replace(/(<\/strong>)/ig, "</b>");

    /* replace h1 h2 h3 h4 h5 h6 h7 h8 h9 with bold */
    tmpContent = tmpContent.replace(/(<(\ )*h[123456789][^<>]*>)/ig, "<b>");
    tmpContent = tmpContent.replace(/(<(\ )*\/h[123456789](\ )*>)/ig, "</b>");

    /* remove unknown classes */
    var pattern = /<[^<>]+class="[^"]+"[^<>]*>/gi;
    /* find all tags containing classes */
    var matches = tmpContent.match(pattern);
    for (var i = 0; matches && i < matches.length; i++) { /* loop through found tags */
        var pattern2 = /class="[^"]+"/gi;
        /* find class name */
        var matches2 = matches[i].match(pattern2);
        for (var i2 = 0; matches2 && i2 < matches2.length; i2++) { /* throw away unknown classes */
            var classExist = false;
            for (var classKey = 0; classKey < allowedClasses.length; classKey++) {
                if ('class="' + allowedClasses[classKey] + '"' == matches2[i2]) {
                    classExist = true;
                }
            }

            if (!classExist) {
                tmpContent = tmpContent.replace(matches2[i2], "");
            }
        }
    }

    o.content = tmpContent;

};
