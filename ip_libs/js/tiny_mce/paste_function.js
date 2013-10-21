function ip_paste_preprocess_function(html) {

    html = html.replace(new RegExp('<!(?:--[\\s\\S]*?--\s*)?>', 'g'), '') //remove comments
    html = html.replace(/(<([^>]+)>)/ig,"</p><p>");
    html = html.replace(/\n/ig," "); //remove newlines
    html = html.replace(/\r/ig," "); //remove newlines
    html = html.replace(/[\t]+/ig," "); //remove tabs
    html = html.replace(/[ ]+/ig," ");  //remove multiple spaces

    html = html.replace(/(<\/p><p>([ ]*(<\/p><p>)*[ ]*)*<\/p><p>)/ig, "</p><p>"); //remove multiple paragraphs
    
    return html;
}
