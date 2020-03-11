<template>

    <div class="gallery-control">

        <draggable v-model="mediaItems" group="mediaItems" draggable=".gallery-item-wrapper" @start="drag=true" @end="drag=false" @update="updateSorting()" class="gallery-drag-container">

            <div class="gallery-item-wrapper" v-for="(mediaItem, index) in mediaItems" :key="mediaItem.id">

                <div class="gallery-item" :title="mediaItem.name" v-bind:style="{ backgroundImage: 'url(' + mediaItem.conversion_small + ')' }">

                    <svg width="46" height="62" xmlns="http://www.w3.org/2000/svg" v-if="! isImage(mediaItem)">
                      <path d="M44.13 12.3c1.111 1.113 1.667 2.463 1.667 4.052v39.554c0 1.589-.556 2.939-1.668 4.051-1.112 1.112-2.462 1.668-4.05 1.668H5.765c-1.588 0-2.939-.556-4.05-1.668C.602 58.845.046 57.495.046 55.907V6.343c0-1.589.556-2.939 1.668-4.05C2.827 1.18 4.178.624 5.766.624h24.305c1.588 0 2.939.556 4.05 1.668L44.13 12.301zm-2.741 2.741L31.381 5.033a3.18 3.18 0 0 0-.834-.595v11.437h11.438a3.18 3.18 0 0 0-.596-.834zm-1.31 42.771c.555 0 1.012-.178 1.37-.536.357-.357.536-.814.536-1.37V19.688h-12.39c-.795 0-1.47-.278-2.026-.834a2.757 2.757 0 0 1-.834-2.026V4.438H5.766c-.556 0-1.013.178-1.37.536-.357.357-.536.814-.536 1.37v49.562c0 .556.179 1.013.536 1.37.357.358.814.536 1.37.536h34.312z" fill="#7E7E7E" fill-rule="nonzero"/>
                    </svg>

                    <div v-if="mediaItem.temp">
                        <input type="hidden" :name="name + '[]'" v-model="mediaItem.id">
                    </div>

                    <div class="gallery-item-tools mb-2" v-if="mediaItem.temp == false">

                        <a :href="mediaItem.full_url" target="_blank">
                            <svg width="43" height="43" xmlns="http://www.w3.org/2000/svg" class="mx-1">
                              <g fill="none" fill-rule="evenodd">
                                <circle fill="#FF4747" cx="21.5" cy="21.5" r="21.5"/>
                                <path d="M33.45 20.875c.2.315.3.659.3 1.031 0 .373-.1.716-.3 1.031a13.753 13.753 0 0 1-5.028 4.985c-2.149 1.26-4.498 1.89-7.047 1.89-2.55 0-4.898-.63-7.047-1.89a13.753 13.753 0 0 1-5.027-4.985C9.1 22.622 9 22.28 9 21.907c0-.373.1-.717.3-1.032a13.753 13.753 0 0 1 5.028-4.984C16.477 14.63 18.825 14 21.375 14s4.898.63 7.047 1.89a13.753 13.753 0 0 1 5.027 4.985zM21.374 27.75c2.177 0 4.175-.53 5.994-1.59 1.82-1.06 3.259-2.478 4.318-4.254a12.397 12.397 0 0 0-2.427-2.9 12.063 12.063 0 0 0-3.244-2.041 5.764 5.764 0 0 1 1.203 3.566c0 1.06-.265 2.034-.795 2.922a6.05 6.05 0 0 1-2.127 2.127 5.596 5.596 0 0 1-2.922.795 5.596 5.596 0 0 1-2.922-.795 6.05 6.05 0 0 1-2.127-2.127 5.596 5.596 0 0 1-.795-2.922c0-.974.23-1.89.688-2.75 0 .66.236 1.225.709 1.698a2.316 2.316 0 0 0 1.697.708c.659 0 1.225-.236 1.697-.708a2.316 2.316 0 0 0 .71-1.698 2.28 2.28 0 0 0-.688-1.676 11.868 11.868 0 0 0-5.371 1.805 11.853 11.853 0 0 0-3.91 3.996c1.06 1.776 2.499 3.194 4.318 4.254 1.819 1.06 3.817 1.59 5.994 1.59z" fill="#FFF"/>
                              </g>
                            </svg>
                        </a>

                        <svg width="43" height="43" xmlns="http://www.w3.org/2000/svg" class="mx-1" v-on:click="deleteImage(mediaItem.id, index, $event)">
                          <g fill="none" fill-rule="evenodd">
                            <circle fill="#FF4747" cx="21.5" cy="21.5" r="21.5"/>
                            <path d="M20.25 17.828v9.281c0 .144-.05.265-.15.366-.1.1-.222.15-.366.15h-1.03a.497.497 0 0 1-.366-.15.497.497 0 0 1-.15-.366v-9.28c0-.144.05-.266.15-.366.1-.1.222-.15.365-.15h1.031c.144 0 .265.05.366.15.1.1.15.222.15.365zm3.266-.515h1.03c.144 0 .266.05.366.15.1.1.15.222.15.365v9.281c0 .144-.05.265-.15.366-.1.1-.222.15-.365.15h-1.031a.497.497 0 0 1-.366-.15.497.497 0 0 1-.15-.366v-9.28c0-.144.05-.266.15-.366.1-.1.222-.15.366-.15zm6.703-4.125c.286 0 .53.1.73.3.2.2.301.444.301.73v.516c0 .144-.05.265-.15.366-.1.1-.222.15-.366.15h-.859v14.437c0 .573-.2 1.06-.602 1.461a1.989 1.989 0 0 1-1.46.602H15.436c-.572 0-1.06-.2-1.46-.602a1.989 1.989 0 0 1-.602-1.46V15.25h-.86a.497.497 0 0 1-.365-.15.497.497 0 0 1-.15-.366v-.515c0-.287.1-.53.3-.73.201-.201.445-.301.731-.301h3.18l1.46-2.45a2.098 2.098 0 0 1 1.806-.988h4.296a2.098 2.098 0 0 1 1.805.988l1.461 2.45h3.18zm-11.602 0h6.016l-.774-1.247c-.057-.085-.129-.128-.214-.128h-4.04c-.085 0-.157.043-.214.128l-.774 1.247zm9.195 2.062H15.437v14.18c0 .057.03.114.086.172.058.057.115.085.172.085h11.86c.057 0 .114-.028.172-.085.057-.058.085-.115.085-.172V15.25z" fill="#FFF"/>
                          </g>
                        </svg>

                        <svg width="43" height="43" xmlns="http://www.w3.org/2000/svg" class="mx-1" @click="showModal(mediaItem.id)">
                          <g fill="none" fill-rule="evenodd">
                            <circle fill="#FF4747" cx="21.5" cy="21.5" r="21.5"/>
                            <path d="M28.273 24.574l1.375-1.375c.115-.114.244-.143.387-.086.143.058.215.172.215.344v6.23c0 .573-.2 1.06-.602 1.461a1.989 1.989 0 0 1-1.46.602H13.061c-.572 0-1.06-.2-1.46-.602a1.989 1.989 0 0 1-.602-1.46V14.562c0-.573.2-1.06.602-1.461a1.989 1.989 0 0 1 1.46-.602h11.774c.143 0 .243.072.3.215.058.143.03.272-.085.387l-1.375 1.375a.291.291 0 0 1-.215.086H13.062v15.124h15.125V24.79c0-.086.03-.157.086-.215zm6.746-8.68l-11.3 11.301-3.867.43a1.727 1.727 0 0 1-1.461-.516 1.727 1.727 0 0 1-.516-1.46l.43-3.868 11.3-11.3a2.426 2.426 0 0 1 1.784-.731c.701 0 1.296.244 1.783.73l1.847 1.848c.487.487.73 1.082.73 1.783 0 .702-.243 1.297-.73 1.784zm-4.253 1.333l-2.493-2.493-7.992 7.993-.3 2.793 2.792-.301 7.993-7.992zm2.793-3.438l-1.848-1.848a.437.437 0 0 0-.322-.128.437.437 0 0 0-.323.128l-1.332 1.332 2.493 2.493 1.332-1.332a.437.437 0 0 0 .128-.323.437.437 0 0 0-.128-.322z" fill="#FFF"/>
                          </g>
                        </svg>

                    </div>

                </div>

                <media-meta-editor-modal
                    :media="mediaItem"
                    :ref="'modal_' + mediaItem.id"
                ></media-meta-editor-modal>

            </div>

            <div class="gallery-buttons">

                <div class="gallery-browse-button">
                    <media-browser
                        :multiple="multiple"
                        v-on:media-selected="addSelectedMedia"
                        :strict="strict"
                        :check-orientation="checkOrientation"
                        :max-width="maxWidth"
                        :max-height="maxHeight"
                        :min-width="minWidth"
                        :min-height="minHeight"
                        :width="width"
                        :height="height"
                        :quality="quality"
                        :mime-type="mimeType"
                        :convert-size="convertSize"
                    ></media-browser>
                </div>

            </div>

        </draggable>

    </div>

</template>

<script>
    import draggable from 'vuedraggable';

    export default {
        components: {
            draggable,
        },
        props: {
            name: {
                type: String
            },
            media: {
                type: Array
            },
            model_id: {
                type: Number
            },
            model_type: {
                type: String
            },
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
            mediaItems: null,
            pendingUploads: 0
        }),

        mounted() {
            Vue.set(this, 'mediaItems', this.media);
        },

        methods: {
            addImage: function (event) {
                $(event.target).closest('.gallery-control').find('input').click();
            },

            deleteImage: function (id, index, event) {
                var token = document.querySelector("meta[name='csrf-token']").getAttribute("content");

                var sure = confirm('Are you sure?');

                if (sure) {
                    var self = this;
                    $.post('/pilot/media/' + id + '/destroy', { _token: token }, function (data) {
                        self.mediaItems.splice(index, 1);
                    });
                }
            },

            showModal: function (id) {
                this.$refs['modal_' + id][0].showModal();
            },

            updateSelected: function (event) {
                this.pendingUploads = event.target.files.length;
            },

            updateSorting: function () {
                var ids = this.mediaItems.map(function (item) {
                    return item.id;
                });

                var token = this.getToken();

                $.post('/pilot/media/order', { ids: ids, _token: token }, function (data) {
                    console.log('Order updated.');
                });
            },

            isImage: function (mediaItem) {
                return mediaItem.mime_type.substring(0, 6) == 'image/';
            },

            getToken: function () {
                return document.querySelector("meta[name='csrf-token']").getAttribute("content");
            },

            addSelectedMedia: function (selectedMedia) {
                var self = this;
                var postData = {
                    mediaItems: selectedMedia,
                    model_id: self.model_id,
                    model_type: self.model_type,
                    collection: self.name,
                    clear_media: self.multiple ? 0 : 1,
                    _token: self.getToken()
                };

                $.post('/pilot/media/move', postData, function (movedMedia) {
                    var currentMedia = Array.from(self.mediaItems);
                    if (self.multiple) {
                        var finalMedia = currentMedia.concat(movedMedia);
                    } else {
                        var finalMedia = movedMedia;
                    }
                    Vue.set(self, 'mediaItems', finalMedia);
                });
            }
        }
    }
</script>

<style scoped>

.gallery-control {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
}

.gallery-drag-container {
    display: flex;
    margin-left: -7px;
    margin-right: -7px;
    align-items: center;
    flex-wrap: wrap;
}

.gallery-control input[type=file] {
    height: 0px;
    width: 0px;
    position: absolute;
    left: 0px;
    bottom: 0px;
    visibility: hidden;
}

.gallery-item {
    width: 200px;
    height: 200px;
    background-repeat: no-repeat;
    border: 1px solid #979797;
    background-size: contain;
    background-position: center;
    margin: 0 7px;
    margin-bottom: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    flex-direction: column;
    position: relative;
}

.gallery-item-tools {
    transform: translateY(300%);
    position: absolute;
}

.gallery-item-tools svg {
    cursor: pointer;
}

.gallery-item:hover .gallery-item-tools {
    transform: translateY(150%);
    transition: .3s ease-in-out;
}

.gallery-buttons {
    margin-left: 7px;
    width: 200px;
}

.gallery-add-button {
    cursor: pointer;
    margin-bottom: 10px;
}

.media-browser-button {
    width: 100%;
}

.gallery-sticky-alert {
    position: fixed;
    bottom:  0;
    left: 0;
    right: 0;
    width: 100%;
    padding: 10px;
    background-color: firebrick;
    color: #fff;
    text-align: center;
    z-index: 100;
}

</style>

<style>
div.gallery-buttons > div.gallery-browse-button > div > button {
    width: 100%;
}
</style>
