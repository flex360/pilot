
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('media-meta-editor', require('./components/MediaMetaEditor.vue').default);
Vue.component('media-meta-editor-modal', require('./components/MediaMetaEditorModal.vue').default);
Vue.component('file-or-text', require('./components/FileOrText.vue').default);
Vue.component('gallery-manager', require('./components/GalleryManager.vue').default);
Vue.component('media-browser', require('./components/MediaBrowser.vue').default);
Vue.component('media-browser-rename', require('./components/MediaBrowserRename.vue').default);
Vue.component('menu-edit-modal', require('./components/MenuEditModal.vue').default);
Vue.component('menu-item-list', require('./components/MenuItemList.vue').default);
Vue.component('collapsable', require('./components/Collapsable.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.prototype.$eventHub = new Vue(); // Global event bus

const app = new Vue({
  el: '#app'
});

// polyfill for Element.closest()
if (window.Element && !Element.prototype.closest) {
  Element.prototype.closest =
    function (s) {
      var matches = (this.document || this.ownerDocument).querySelectorAll(s),
        i,
        el = this;
      do {
        i = matches.length;
        while (--i >= 0 && matches.item(i) !== el) { };
      } while ((i < 0) && (el = el.parentElement));
      return el;
    };
}

//TRUMBOWYG & FROALA WYSIWYG EDITOR 
require('trumbowyg');
import 'trumbowyg/dist/ui/trumbowyg.css';
import 'trumbowyg/plugins/emoji/ui/sass/trumbowyg.emoji.scss';
require('trumbowyg/plugins/fontsize/trumbowyg.fontsize.js');
require('trumbowyg/plugins/emoji/trumbowyg.emoji.js');
require('trumbowyg/plugins/upload/trumbowyg.upload.js');
require('trumbowyg/plugins/pasteimage/trumbowyg.pasteimage.js');
require('trumbowyg/plugins/pasteembed/trumbowyg.pasteembed.js');
import 'trumbowyg/plugins/table/ui/sass/trumbowyg.table.scss';
require('trumbowyg/plugins/table/trumbowyg.table.js');
require('trumbowyg/plugins/resizimg/trumbowyg.resizimg.js');
// require('trumbowyg/plugins/resizimg/resizable-resolveconflict.js');
require('trumbowyg/plugins/specialchars/trumbowyg.specialchars.js');
import 'trumbowyg/plugins/specialchars/ui/sass/trumbowyg.specialchars.scss';
require('trumbowyg/plugins/colors/trumbowyg.colors.js');
import 'trumbowyg/plugins/colors/ui/sass/trumbowyg.colors.scss';

window.addEventListener('load', (event) => {
        //get wysiwygSetting setting
        const wysiwygSetting = document.getElementById("wysiwygSetting").getAttribute("value");
        console.log(wysiwygSetting);
    
        /* froala editor */
        if (wysiwygSetting == 10) {
    
        // Froala license
        $.FroalaEditor.DEFAULTS.key = "4Wa1WDPTf1ZNRGb1OG1g1==";
        if ($(".wysiwyg-editor").length > 0) {
          $(".wysiwyg-editor").froalaEditor({
            toolbarInline: false,
            // linkText: true,
            height: 300,
            theme: "dark",
            toolbarButtons: [
              "bold",
              "italic",
              "underline",
              "strikeThrough",
              "fontFamily",
              "fontSize",
              "color",
              "|",
              "paragraphFormat",
              "paragraphStyle",
              "align",
              "formatOL",
              "formatUL",
              "outdent",
              "indent",
              "|",
              "insertLink",
              "insertImage",
              "insertVideo",
              "insertFile",
              "insertTable",
              "insertHR",
              "undo",
              "redo",
              "html"
            ],
            toolbarButtonsSM: [
              "bold",
              "italic",
              "underline",
              "strikeThrough",
              "fontFamily",
              "fontSize",
              "color",
              "|",
              "paragraphFormat",
              "paragraphStyle",
              "align",
              "formatOL",
              "formatUL",
              "outdent",
              "indent",
              "|",
              "insertLink",
              "insertImage",
              "insertVideo",
              "insertFile",
              "insertTable",
              "insertHR",
              "undo",
              "redo",
              "html"
            ],
            toolbarButtonsXS: [
              "bold",
              "italic",
              "underline",
              "strikeThrough",
              "fontFamily",
              "fontSize",
              "color",
              "|",
              "paragraphFormat",
              "paragraphStyle",
              "align",
              "formatOL",
              "formatUL",
              "outdent",
              "indent",
              "|",
              "insertLink",
              "insertImage",
              "insertVideo",
              "insertFile",
              "insertTable",
              "insertHR",
              "undo",
              "redo",
              "html"
            ],
            imageUploadURL: "/assets/upload",
            imageManagerLoadURL: "/assets/get",
            imageManagerDeleteURL: "/assets/delete",
            fileUploadURL: "/assets/upload",
            paragraphFormat: {
              N: "Normal",
              BLOCKQUOTE: "Quote",
              PRE: "Code",
              H1: "Heading 1",
              H2: "Heading 2",
              H3: "Heading 3",
              H4: "Heading 4",
              H5: "Heading 5",
              H6: "Heading 6"
            },
            htmlRemoveTags: []
          });
        }
      } else {
    
        $('.wysiwyg-editor').trumbowyg({
            btnsDef: {
                // Create a new dropdown
                image: {
                    dropdown: ['insertImage', 'upload'],
                    ico: 'insertImage'
                }
            },
            btns: [
                ['viewHTML'],
                ['undo', 'redo'], // Only supported in Blink browsers
                ['formatting'],
                ['strong', 'em', 'fontsize', 'foreColor', 'backColor', 'del', 'specialChars', 'removeformat'],
                ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
                ['unorderedList', 'orderedList'],
                ['horizontalRule'],
                ['link'],
                ['image'],
                ['fullscreen'],
                ['table'],
                ['emoji'],
            ],
            plugins: {
                upload: {
                    serverPath: '/assets/upload',
                    fileFieldName: 'file',
                    data: [{ name: "_token", value: $('[name=csrf-token]').attr('content')}, { name: "type", value: "trumbowyg" }],
                }
            },
            tagsToRemove: ['script', 'link'],
            tagsToKeep: [['hr', 'img', 'embed', 'iframe', 'input', 'i']],
            imageWidthModalEdit: true,
            defaultLinkTarget: '_blank',
            svgPath: '/pilot-assets/admin/svg/icons.svg',
        });
      }

    var el = document.getElementById("block_1");

    if (el != null) {
    CodeMirror.fromTextArea(el, {
        lineNumbers: true,
        theme: 'monokai',
        matchBrackets: true,
        mode: "application/x-httpd-php",
        indentUnit: 4,
        indentWithTabs: false,
        autoRefresh: true,
    });
    }

    var el = document.getElementById("block_2");

    if (el != null) {
    CodeMirror.fromTextArea(el, {
        lineNumbers: true,
        theme: 'monokai',
        matchBrackets: true,
        mode: "application/x-httpd-php",
        indentUnit: 4,
        indentWithTabs: false,
        autoRefresh: true,
    });
    }
});

