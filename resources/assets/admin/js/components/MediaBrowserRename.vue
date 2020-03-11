<template>

    <div class="media-browser-rename" v-if="visible">

        <div class="overlay" @click.self="hide">

            <div class="simple-modal">

                <div class="form-group">

                    <label for="">New Name</label>

                    <input type="text" class="form-control" v-model="mediaItem.name">

                </div>

                <button type="button" class="media-browser-button media-browser-button-primary" @click.prevent="rename">Rename</button>

            </div>

        </div>

    </div>

</template>

<script>
    export default {
        props: {

        },

        data: () => ({
            visible: false,
            mediaItem: {}
        }),

        mounted() {

        },

        methods: {
            show: function () {
                this.visible = true;
            },

            hide: function () {
                this.visible = false;
            },

            rename: function () {
                var self = this;

                $.post('/pilot/media/rename', { id: self.mediaItem.id, _token: self.getToken(), newName: self.mediaItem.name}, function (data) {
                    self.hide();
                });
            },

            getToken: function () {
                return document.querySelector("meta[name='csrf-token']").getAttribute("content");
            }
        }
    }
</script>

<style scoped>

.overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.7);
    z-index: 200;
    display: flex;
    justify-content: center;
    align-items: center;
}

.simple-modal {
    background-color: #fff;
    width: 400px;
    /* height: 40%; */
    border-radius: 11px;
    display: flex;
    flex-direction: column;

    padding: 20px;
}

</style>
