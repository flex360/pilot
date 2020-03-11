<template>

    <div style="display: inline-block;">

        <button type="button"
                class="btn btn-default btn-sm"
                style="margin-top: 15px;"
                @click="showModal()"
                >
                    <i class="far fa-edit"></i> Edit Information
        </button>

        <div class="modal fade" v-bind:class="{ in: show  }" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" v-bind:style="{ display: show ? 'block' : 'none' }">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" aria-label="Close" @click="hideModal()"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Edit Information</h4>
                    </div>

                    <div class="modal-body">

                        <form class="" action="" method="post">

                            <div class="form-group">
                                <label for="title">Title</label>
                                <input type="text" name="title" class="form-control" v-model="data.title">
                            </div>

                            <div class="form-group">
                                <label for="credit">Credit</label>
                                <input type="text" name="credit" class="form-control" v-model="data.credit">
                            </div>

                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea name="description" class="form-control" v-model="data.description"></textarea>
                            </div>

                        </form>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-sm" @click="hideModal()">Close</button>
                        <button type="button" class="btn btn-primary btn-sm" @click="saveChanges()">Save Changes</button>
                    </div>
                </div>
            </div>
        </div>

    </div>

</template>

<script>
    export default {
        props: ['media_id', 'title', 'credit', 'description'],

        data: () => ({
            show: false,
            data: {},
        }),

        mounted() {
            Vue.set(this.data, 'title', this.title);
            Vue.set(this.data, 'credit', this.credit);
            Vue.set(this.data, 'description', this.description);
            Vue.set(this.data, '_token', document.querySelector("meta[name='csrf-token']").getAttribute("content"));
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
                $.post('/pilot/media/' + this.media_id + '/info', this.data, function (data) {
                    self.hideModal();
                });
            }
        }
    }
</script>
