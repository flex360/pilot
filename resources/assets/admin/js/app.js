
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

window.addEventListener('load', (event) => {
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
