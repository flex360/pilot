<template>

    <div class="file-or-text-control">

        <div class="file-or-text-chooser">
            <label class="radio-inline">
                <input type="radio" v-model="field_type" value="file"> File
            </label>
            <label class="radio-inline">
                <input type="radio" v-model="field_type" value="text"> URL
            </label>
        </div>

        <div v-if="field_type == 'file'">

            <!-- <input type="file" class="form-control" :class="class_name" :styles="styles" :name="name"> -->

            <gallery-manager
                :name="name"
                :media="media"
                :model_id="model_id"
                :model_type="model_type"
            ></gallery-manager>

        </div>

        <input type="text" class="form-control" :class="class_name" :styles="styles" :placeholder="placeholder" :name="name" :value="value" v-if="field_type == 'text'">

    </div>

</template>

<script>
    export default {
        props: {
            name: {
                type: String
            },
            value: {
                type: String
            },
            type: {
                type: String,
                default: 'file'
            },
            class_name: {
                type: String
            },
            styles: {
                type: String
            },
            placeholder: {
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
            }
        },

        data: () => ({
            field_type: null
        }),

        mounted() {
            Vue.set(this, 'field_type', this.type);
        },

        methods: {

        }
    }
</script>

<style scoped>

.file-or-text-chooser {
    margin-bottom: 10px;
}

</style>
