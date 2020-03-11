<template>

    <div>

        <div class="media-browser-overlay" v-if="show">
            <div class="media-browser-window">
                <div class="media-browser-header">
                    <div class="media-browser-header-left">
                        Edit Information
                    </div>
                </div>
                <div class="media-browser-body p-3">
                    <form class="" action="" method="post">

                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" name="title" class="form-control" v-model="metadata.title" ref="title">
                        </div>

                        <div class="form-group">
                            <label for="credit">Credit</label>
                            <input type="text" name="credit" class="form-control" v-model="metadata.credit">
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" class="form-control" v-model="metadata.description"></textarea>
                        </div>

                    </form>
                </div>
                <div class="media-browser-footer">
                    <button type="button" class="media-browser-button media-browser-button-default" @click="hideModal()">Close</button>
                    <button type="button" class="media-browser-button media-browser-button-primary ml-1" @click="saveChanges()">Save Changes</button>
                </div>
            </div>
        </div>

    </div>

</template>

<script>
    export default {
        props: ['media'],

        data: () => ({
            show: false,
            mediaItem: {},
            metadata: {}
        }),

        mounted() {
            Vue.set(this, 'mediaItem', this.media);

            var metadata = {};

            if (typeof this.media.custom_properties.title == 'undefined') {
                metadata.title = '';
            } else {
                metadata.title = this.media.custom_properties.title;
            }

            if (typeof this.media.custom_properties.credit == 'undefined') {
                metadata.credit = '';
            } else {
                metadata.credit = this.media.custom_properties.credit;
            }

            if (typeof this.media.custom_properties.description == 'undefined') {
                metadata.description = '';
            } else {
                metadata.description = this.media.custom_properties.description;
            }

            Vue.set(this, 'metadata', metadata);
        },

        methods: {
            showModal: function () {
                this.show = true;
            },

            hideModal: function () {
                this.show = false;
            },

            saveChanges: function () {
                var self = this;
                var metadata = self.metadata;
                metadata._token = self.getToken();
                $.post('/pilot/media/' + self.mediaItem.id + '/info', metadata, function (data) {
                    self.hideModal();
                });
            },

            getToken: function () {
                return document.querySelector("meta[name='csrf-token']").getAttribute("content");
            }
        }
    }
</script>

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

.media-browser-footer {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    /* TODO: find better solution */
    min-height: 62px;
}
</style>

