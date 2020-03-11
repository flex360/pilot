<template>

    <div class="media-browser">

        <button type="button" class="media-browser-button media-browser-button-primary mb-1" @click="openUpload"><i class="fas fa-upload"></i> Upload</button>
        <button type="button" class="media-browser-button media-browser-button-primary" @click="toggleWindow"><i class="fas fa-folder-open"></i> Browse</button>

        <transition
            name="fade-out"
            leave-active-class="animated fadeOut"
            :duration="100"
        >

            <div class="media-browser-overlay" v-show="show" @click.self="toggleWindow">

                <transition
                    name="fade-in"
                    enter-active-class="animated fadeIn"
                    :duration="100"
                >

                    <div class="media-browser-window" v-show="show">

                        <div class="media-browser-header">

                            <div class="media-browser-header-left">

                                <button type="button" class="media-browser-button media-browser-button-primary mr-1" @click="showFilePicker">Upload</button>

                                <button type="button" class="media-browser-button media-browser-button-default mr-1" :disabled="selected.length != 1" @click="showRename">Rename</button>

                                <span v-if="uploadsRemaining > 0">Uploading... (Remaining {{ uploadsRemaining }})</span>

                            </div>

                            <div class="media-browser-header-right">

                                <div class="media-browser-search">

                                    <div>

                                        <label for="">Search</label>

                                        <input type="text" placeholder="Search" v-model="searchQuery" @keypress.enter.prevent="search">

                                    </div>

                                </div>

                            </div>

                        </div>

                        <input type="file" class="media-manager-file-uploader sr-only" :multiple="multiple" @change="handleFiles">

                        <div class="media-browser-body-breadcrumbs">
                            <a href="#" @click.prevent="switchView('type')">Types</a>
                            <span v-if="currentType != null && view != 'type'"> <i class="fas fa-angle-right"></i> {{ currentType.name }}</span>
                        </div>

                        <div class="media-browser-body media-browser-list media-browser-grid">

                            <div v-if="loading" class="media-browser-loader">
                                <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTA1IiBoZWlnaHQ9IjEwNSIgdmlld0JveD0iMCAwIDEwNSAxMDUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgZmlsbD0iI2ZmZiI+ICAgIDxjaXJjbGUgY3g9IjEyLjUiIGN5PSIxMi41IiByPSIxMi41Ij4gICAgICAgIDxhbmltYXRlIGF0dHJpYnV0ZU5hbWU9ImZpbGwtb3BhY2l0eSIgICAgICAgICBiZWdpbj0iMHMiIGR1cj0iMXMiICAgICAgICAgdmFsdWVzPSIxOy4yOzEiIGNhbGNNb2RlPSJsaW5lYXIiICAgICAgICAgcmVwZWF0Q291bnQ9ImluZGVmaW5pdGUiIC8+ICAgIDwvY2lyY2xlPiAgICA8Y2lyY2xlIGN4PSIxMi41IiBjeT0iNTIuNSIgcj0iMTIuNSIgZmlsbC1vcGFjaXR5PSIuNSI+ICAgICAgICA8YW5pbWF0ZSBhdHRyaWJ1dGVOYW1lPSJmaWxsLW9wYWNpdHkiICAgICAgICAgYmVnaW49IjEwMG1zIiBkdXI9IjFzIiAgICAgICAgIHZhbHVlcz0iMTsuMjsxIiBjYWxjTW9kZT0ibGluZWFyIiAgICAgICAgIHJlcGVhdENvdW50PSJpbmRlZmluaXRlIiAvPiAgICA8L2NpcmNsZT4gICAgPGNpcmNsZSBjeD0iNTIuNSIgY3k9IjEyLjUiIHI9IjEyLjUiPiAgICAgICAgPGFuaW1hdGUgYXR0cmlidXRlTmFtZT0iZmlsbC1vcGFjaXR5IiAgICAgICAgIGJlZ2luPSIzMDBtcyIgZHVyPSIxcyIgICAgICAgICB2YWx1ZXM9IjE7LjI7MSIgY2FsY01vZGU9ImxpbmVhciIgICAgICAgICByZXBlYXRDb3VudD0iaW5kZWZpbml0ZSIgLz4gICAgPC9jaXJjbGU+ICAgIDxjaXJjbGUgY3g9IjUyLjUiIGN5PSI1Mi41IiByPSIxMi41Ij4gICAgICAgIDxhbmltYXRlIGF0dHJpYnV0ZU5hbWU9ImZpbGwtb3BhY2l0eSIgICAgICAgICBiZWdpbj0iNjAwbXMiIGR1cj0iMXMiICAgICAgICAgdmFsdWVzPSIxOy4yOzEiIGNhbGNNb2RlPSJsaW5lYXIiICAgICAgICAgcmVwZWF0Q291bnQ9ImluZGVmaW5pdGUiIC8+ICAgIDwvY2lyY2xlPiAgICA8Y2lyY2xlIGN4PSI5Mi41IiBjeT0iMTIuNSIgcj0iMTIuNSI+ICAgICAgICA8YW5pbWF0ZSBhdHRyaWJ1dGVOYW1lPSJmaWxsLW9wYWNpdHkiICAgICAgICAgYmVnaW49IjgwMG1zIiBkdXI9IjFzIiAgICAgICAgIHZhbHVlcz0iMTsuMjsxIiBjYWxjTW9kZT0ibGluZWFyIiAgICAgICAgIHJlcGVhdENvdW50PSJpbmRlZmluaXRlIiAvPiAgICA8L2NpcmNsZT4gICAgPGNpcmNsZSBjeD0iOTIuNSIgY3k9IjUyLjUiIHI9IjEyLjUiPiAgICAgICAgPGFuaW1hdGUgYXR0cmlidXRlTmFtZT0iZmlsbC1vcGFjaXR5IiAgICAgICAgIGJlZ2luPSI0MDBtcyIgZHVyPSIxcyIgICAgICAgICB2YWx1ZXM9IjE7LjI7MSIgY2FsY01vZGU9ImxpbmVhciIgICAgICAgICByZXBlYXRDb3VudD0iaW5kZWZpbml0ZSIgLz4gICAgPC9jaXJjbGU+ICAgIDxjaXJjbGUgY3g9IjEyLjUiIGN5PSI5Mi41IiByPSIxMi41Ij4gICAgICAgIDxhbmltYXRlIGF0dHJpYnV0ZU5hbWU9ImZpbGwtb3BhY2l0eSIgICAgICAgICBiZWdpbj0iNzAwbXMiIGR1cj0iMXMiICAgICAgICAgdmFsdWVzPSIxOy4yOzEiIGNhbGNNb2RlPSJsaW5lYXIiICAgICAgICAgcmVwZWF0Q291bnQ9ImluZGVmaW5pdGUiIC8+ICAgIDwvY2lyY2xlPiAgICA8Y2lyY2xlIGN4PSI1Mi41IiBjeT0iOTIuNSIgcj0iMTIuNSI+ICAgICAgICA8YW5pbWF0ZSBhdHRyaWJ1dGVOYW1lPSJmaWxsLW9wYWNpdHkiICAgICAgICAgYmVnaW49IjUwMG1zIiBkdXI9IjFzIiAgICAgICAgIHZhbHVlcz0iMTsuMjsxIiBjYWxjTW9kZT0ibGluZWFyIiAgICAgICAgIHJlcGVhdENvdW50PSJpbmRlZmluaXRlIiAvPiAgICA8L2NpcmNsZT4gICAgPGNpcmNsZSBjeD0iOTIuNSIgY3k9IjkyLjUiIHI9IjEyLjUiPiAgICAgICAgPGFuaW1hdGUgYXR0cmlidXRlTmFtZT0iZmlsbC1vcGFjaXR5IiAgICAgICAgIGJlZ2luPSIyMDBtcyIgZHVyPSIxcyIgICAgICAgICB2YWx1ZXM9IjE7LjI7MSIgY2FsY01vZGU9ImxpbmVhciIgICAgICAgICByZXBlYXRDb3VudD0iaW5kZWZpbml0ZSIgLz4gICAgPC9jaXJjbGU+PC9zdmc+">
                            </div>

                            <div v-show="view == 'type' && ! loading" v-for="modelType in types" class="media-browser-grid-item folder-item" v-bind:class="{ 'folder-item-selected': hasSelectedOfType(modelType.model_type) }" @click="loadMediaByType(modelType)">
                                <div class="media-browser-grid-icon folder-icon">

                                    <svg width="63" height="48" xmlns="http://www.w3.org/2000/svg">
                                      <path d="M23.994 3.937H5.906c-.574 0-1.046.185-1.415.554-.369.37-.554.841-.554 1.415v35.438c0 .574.185 1.046.554 1.415.37.369.841.553 1.415.553h51.188c.574 0 1.046-.184 1.415-.553.369-.37.553-.841.553-1.415V13.78c0-.574-.184-1.046-.553-1.415-.37-.369-.841-.554-1.415-.554H33.469c-1.067 0-2.01-.369-2.83-1.107l-6.645-6.768zM5.906 0h18.088c1.066 0 1.969.37 2.707 1.107l6.768 6.768h23.625c1.64 0 3.035.574 4.183 1.723C62.426 10.746 63 12.14 63 13.78v27.563c0 1.64-.574 3.035-1.723 4.183-1.148 1.149-2.543 1.723-4.183 1.723H5.906c-1.64 0-3.035-.574-4.183-1.723C.574 44.38 0 42.984 0 41.344V5.906c0-1.64.574-3.035 1.723-4.183C2.87.574 4.266 0 5.906 0z" fill="#7E7E7E" fill-rule="evenodd"/>
                                    </svg>

                                </div>
                                <div>{{ modelType.name }}</div>
                            </div>

                            <div v-show="view == 'media' || view == 'search'" class="media-browser-grid-item media-item" v-bind:class="{ 'media-item-selected': isSelected(mediaItem) }" v-for="mediaItem in media" @click="toggleSelect(mediaItem)">
                                <div class="media-browser-grid-icon media-item-icon"
                                     v-bind:class="{ 'media-item-image': isImage(mediaItem), 'media-item-document': ! isImage(mediaItem) }"
                                     v-bind:style="{ backgroundImage: isImage(mediaItem) ? 'url(' + mediaItem.conversion_small + ')' : '' }">

                                    <svg width="46" height="62" xmlns="http://www.w3.org/2000/svg">
                                      <path d="M44.13 12.3c1.111 1.113 1.667 2.463 1.667 4.052v39.554c0 1.589-.556 2.939-1.668 4.051-1.112 1.112-2.462 1.668-4.05 1.668H5.765c-1.588 0-2.939-.556-4.05-1.668C.602 58.845.046 57.495.046 55.907V6.343c0-1.589.556-2.939 1.668-4.05C2.827 1.18 4.178.624 5.766.624h24.305c1.588 0 2.939.556 4.05 1.668L44.13 12.301zm-2.741 2.741L31.381 5.033a3.18 3.18 0 0 0-.834-.595v11.437h11.438a3.18 3.18 0 0 0-.596-.834zm-1.31 42.771c.555 0 1.012-.178 1.37-.536.357-.357.536-.814.536-1.37V19.688h-12.39c-.795 0-1.47-.278-2.026-.834a2.757 2.757 0 0 1-.834-2.026V4.438H5.766c-.556 0-1.013.178-1.37.536-.357.357-.536.814-.536 1.37v49.562c0 .556.179 1.013.536 1.37.357.358.814.536 1.37.536h34.312z" fill="#7E7E7E" fill-rule="nonzero"/>
                                    </svg>
                                </div>
                                <div>{{ mediaItem.name }}</div>
                            </div>

                        </div>

                        <div class="media-browser-footer">

                            <span class="mr-3" v-if="selected.length > 0">({{ selected.length }} items selected)</span>

                            <button type="button" class="media-browser-button media-browser-button-default" @click="toggleWindow">Close</button>

                            <button v-if="selected.length > 0" type="button" class="media-browser-button media-browser-button-primary ml-1" @click="useSelected">Use Selected</button>

                        </div>

                    </div>

                </transition>

            </div>

        </transition>

        <media-browser-rename
            :ref="'rename-control'"
        ></media-browser-rename>

    </div>

</template>

<script>
    import Compressor from 'compressorjs';

    export default {
        props: {
            multiple: {
                type: Boolean,
                default: false
            },
            strict: {
                type: Boolean,
                default: true
            },
            checkOrientation: {
                type: Boolean,
                default: true
            },
            maxWidth: {
                type: Number,
                default: 1000
            },
            maxHeight: {
                type: Number,
                default: Infinity
            },
            minWidth: {
                type: Number,
                default: 0
            },
            minHeight: {
                type: Number,
                default: 0
            },
            width: {
                type: Number,
                default: undefined
            },
            height: {
                type: Number,
                default: undefined
            },
            quality: {
                type: Number,
                default: 0.8
            },
            mimeType: {
                type: String,
                default: 'auto'
            },
            convertSize: {
                type: Number,
                default: 5000000
            }
        },

        data: () => ({
            show: false,
            types: [],
            media: [],
            view: 'type',
            loading: false,
            currentType: null,
            selected: [],
            uploadsRemaining: 0,
            searchQuery: null
        }),

        mounted() {
            this.loadTypes();
        },

        methods: {
            toggleWindow: function () {
                this.show = ! this.show;

                if (this.show) {
                    this.reload();
                }
            },

            openUpload: function (event) {
                this.toggleWindow()
                this.showFilePicker(event);
            },

            reload: function () {
                if (this.view == 'type') {
                    this.loadTypes();
                } else if (this.view == 'media') {
                    this.loadMediaByType(this.currentType);
                }
            },

            loadTypes: function () {
                var self = this;

                self.loading = true;

                $.get('/pilot/media/types', function (data) {
                    self.types = data;
                    self.loading = false;
                });
            },

            loadMediaByType: function (type) {
                this.switchView('media');
                this.switchType(type);
            },

            switchView: function (view) {
                if (view != 'search') {
                    this.searchQuery = null;
                }
                this.view = view;
            },

            switchType: function (type) {
                var self = this;

                self.media = [];
                self.loading = true;
                self.currentType = type;

                if (type.name == 'Search') {

                    var params = {
                        query: self.searchQuery
                    };

                    $.get('/pilot/media/search', params, function (data) {
                        // console.log(data);
                        self.media = data;
                        self.loading = false;
                    });

                } else {

                    $.get('/pilot/media/type',{ type: type.model_type }, function (data) {
                        self.media = data;
                        self.loading = false;
                    });

                }
            },

            toggleSelect: function (mediaItem) {
                if (this.isSelected(mediaItem)) {
                    var selectedIds = this.selected.map(function (item) {
                        return item.id;
                    });

                    var index = selectedIds.indexOf(mediaItem.id);

                    this.selected.splice(index, 1);
                } else {
                    if (this.multiple == false) {
                        this.selected = [];
                    }

                    this.selected.push(mediaItem);
                }
            },

            isSelected: function (mediaItem) {
                var selectedIds = this.selected.map(function (item) {
                    return item.id;
                });

                return selectedIds.indexOf(mediaItem.id) >= 0;
            },

            isImage: function (mediaItem) {
                return mediaItem.mime_type.substring(0, 6) == 'image/';
            },

            showRename: function () {
                this.$refs['rename-control'].mediaItem = this.selected[0];
                this.$refs['rename-control'].show();
            },

            hasSelectedOfType: function (type) {
                var selected = this.selected.filter(function (item) {
                    return item.model_type == type;
                });

                return selected.length > 0;
            },

            showFilePicker: function (event) {
                // console.log(event);
                var input = event.target.closest('.media-browser').querySelector('.media-manager-file-uploader');
                input.click();
            },

            handleFiles: function (event) {
                var self = this;

                var files = Array.from(event.target.files);

                self.uploadsRemaining = files.length;

                files.forEach(function (file) {
                    // resize any image files
                    if (file.type.substring(0, 6) == 'image/') {
                        new Compressor(file, {
                            strict: self.strict,
                            checkOrientation: self.checkOrientation,
                            maxWidth: self.maxWidth,
                            maxHeight: self.maxHeight,
                            minWidth: self.minWidth,
                            minHeight: self.minHeight,
                            width: self.width,
                            height: self.height,
                            quality: self.quality,
                            mimeType: self.mimeType,
                            convertSize: self.convertSize,
                            success: function (result) {
                                // console.log('Output: ', result);

                                self.uploadFile(result);
                            },
                            error: function (err) {
                                alert(err.message);
                            }
                        });
                    } else {
                        // upload non image files
                        self.uploadFile(file);
                    }
                });

            },

            uploadFile: function (file) {
                var self = this;
                var formData = new FormData();
                formData.append("_token", self.getToken());
                formData.append("file", file, file.name);

                $.ajax({
                    url: '/pilot/media/upload',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        // console.log(data);

                        self.uploadsRemaining = self.uploadsRemaining - 1;

                        self.selected = self.selected.concat([data]);

                        if (self.uploadsRemaining == 0) {
                            self.uploadsComplete();
                        }
                    }
                });
            },

            uploadsComplete: function () {
                this.loadMediaByType({"model_type":"Flex360\\Pilot\\Pilot\\Site","name":"Unassigned"});
            },

            getToken: function () {
                return document.querySelector("meta[name='csrf-token']").getAttribute("content");
            },

            useSelected: function () {
                this.$emit('media-selected', this.selected);
                this.selected = [];
                this.toggleWindow();
            },

            search: function (event) {
                var self = this;

                self.loading = true;
                self.media = [];
                self.switchView('search');
                self.switchType({
                    name: 'Search'
                });
            }
        }
    }
</script>

<style>

.media-browser-button {
    display: inline-block;
    font-weight: 400;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    border: 1px solid transparent;
    padding: 4px 24px;
    font-size: 14px;
    line-height: 1.6;
    /* border-radius: .25rem; */
}

.media-browser-button-primary {
    color: #fff;
    /* background-color: #e3342f; */
    background-color: #43ac6a;
    /* border-color: #e3342f; */
    border-color: #43ac6a;
}

.media-browser-button-default {
    color: #fff;
    background-color: #6d6d6d;
    border-color: #6d6d6d;
}

.media-browser-button:disabled {
    color: #fff;
    background-color: #ddd;
    border-color: #ddd;
}

</style>

<style scoped>

.media-browser-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.7);
    z-index: 100;
    display: flex;
    justify-content: center;
    align-items: center;
}

.media-browser-window {
    background-color: #fff;
    width: 90%;
    height: 90%;
    /* border-radius: 11px; */
    display: flex;
    flex-direction: column;
}

.media-browser-header,
.media-browser-footer {
    padding: 15px 20px;
}

.media-browser-header {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
}

.media-browser-header .media-browser-header-left {
    display: flex;
}

.media-browser-body {
    flex-grow: 9999999999;
    background-color: #E8E8E8;
    overflow-y: scroll;
}

.media-browser-body-breadcrumbs {
    border-top: 1px solid #dedede;
    padding: 10px 20px;
    background-color: #fff;
    font-size: 12px;
}

.media-browser-footer {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    /* TODO: find better solution */
    min-height: 62px;
}

.folder-item {

}

.folder-item-selected .folder-icon {
    border: 3px solid red;
}

.media-browser-grid {
    display: flex;
    flex-direction: row;
}

.media-browser-list {
    padding: 10px;
    flex-wrap: wrap;
    align-content: flex-start;
}

.media-browser-grid-item {
    text-align: center;
    margin: 10px;
    font-size: 10px;
    color: #3B3B3B;
    letter-spacing: 0.03px;
    width: 120px;
    word-break: break-all;
}

.media-browser-grid-icon {
    width: 120px;
    height: 120px;
    border-radius: 4px;
    background-color: #fff;
    display: flex;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    background-repeat: no-repeat;
    background-size: cover;
}

.media-browser-grid-icon.media-item-image svg {
    display: none;
}

.media-item-selected .media-browser-grid-icon {
    border: 3px solid red;
}

.media-browser-loader {
    width: 100%;
    display: flex;
    align-content: center;
    justify-content: center;
    padding: 40px;
}

.media-browser-search label {
    display: none;
}
.media-browser-search input {
    padding: 3px;
}

</style>
