var editor1 = null;

if (document.getElementById("contact_form_code") != null) {

    editor1 = CodeMirror.fromTextArea(document.getElementById("contact_form_code"), {
        lineNumbers: true,
        theme: 'monokai',
        matchBrackets: true,
        mode: "application/x-httpd-php",
        indentUnit: 4,
        indentWithTabs: false,
    });
}

function activate_code_tab()
{
    setTimeout(cm_refresh, 100);
}

function cm_refresh()
{
    editor1.refresh();
}

$(function () {

    $('.color-picker').colorpicker({
        component: '.input-group-addon',
    });

});
