<template>

    <div>

        <button class="btn btn-success btn-sm" @click.prevent="toggle">{{ action == 'create' ? 'New Menu Item' : 'Edit' }}</button>

        <div class="modal-overlay" v-if="show" @click.self="toggle">

            <div class="modal-window">

                <div class="modal-header">
                    <h3 class="mb-0">{{ action == 'create' ? 'Add' : 'Edit' }} Menu Item</h3>
                </div>

                <div class="modal-body">

                    <form action="">

                        <div class="form-group">
                            <label for="">Title</label>
                            <input type="text" class="form-control" v-model="item.title" ref="title" required>
                        </div>

                        <div class="form-group">
                            <label for="">Link Type</label>
                            <select class="form-control" v-model="item.type" required>
                                <option value="url">Static URL</option>
                                <option value="page">Existing Page</option>
                            </select>
                        </div>

                        <div class="form-group" v-if="item.type == 'url'">
                            <label for="">Url for the Menu Item</label>
                            <input type="text" class="form-control" v-model="item.url" required>
                        </div>

                        <div class="form-group" v-if="item.type == 'page'">
                            <label for="">Page</label>
                            <select class="form-control" v-model="item.page" required>
                                <option v-for="(page, index) in pages" :value="index" :key="index">{{ page }}</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="">Open In</label>
                            <select class="form-control" v-model="item.target" required>
                                <option value="_self">Same Tab/Window</option>
                                <option value="_blank">New Tab/Window</option>
                            </select>
                        </div>

                    </form>

                </div>

                <div class="modal-footer">

                    <button class="btn btn-link" @click.prevent="toggle" v-if="action == 'create'">Cancel</button>
                    <button class="btn btn-primary" @click.prevent="save">{{ action == 'create' ? 'Add Item' : 'Close' }}</button>

                </div>

            </div>

        </div>

    </div>

</template>

<script>
    export default {
        props: {
            data: {
                type: Object,
                default: null
            }
        },

        data: () => ({
            show: false,
            item: null,
            pages: [],
            action: 'create',
        }),

        mounted() {
            var self = this;

            if (self.data != null) {
                self.item = self.data;
                self.action = 'update';
            } else {
                self.reset();
                self.action = 'create';
            }

            axios.get('/pilot/page/select-list')
                .then(function (response) {
                    // handle success
                    self.pages = response.data;
                })
                .catch(function (error) {
                    // handle error
                    console.log(error);
                })
                .finally(function () {
                    // always executed
                });
        },

        watch: {
            'item.type': function (val, oldVal) {
                if (oldVal == 'page') {
                    this.item.page = null;
                }

                if (oldVal == 'url') {
                    this.item.url = null;
                }
            }
        },

        methods: {
            toggle: function () {
                this.show = ! this.show;
                this.$nextTick(function () {
                    // console.log(this.$refs.title);
                    if (typeof this.$refs.title != 'undefined') {
                        this.$refs.title.focus();
                    }
                });
            },

            save: function () {
                if (this.action == 'create') {
                    var itemCopy = Object.assign({}, this.item);
                    this.$eventHub.$emit('menu-item-saved', itemCopy);
                    this.reset();
                }
                this.toggle();
            },

            reset: function () {
                this.item = { "title": "", "type": "url", "url": null, "target": "_self", "page": null, "level": 1 };
            }
        }
    }
</script>

<style scoped>
.modal-overlay {
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

.modal-window {
    background-color: #fff;
    width: 90%;
    max-width: 400px;
    max-height: 90%;
    /* border-radius: 11px; */
    display: flex;
    flex-direction: column;
}

.modal-header,
.modal-footer {
    padding: 15px 20px;
}

.modal-header {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
}

.modal-header .modal-header-left {
    display: flex;
}

.modal-body {
    flex-grow: 9999999999;
    background-color: #E8E8E8;
    overflow-y: auto;
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    /* TODO: find better solution */
    /* min-height: 62px; */
}
</style>

