<template>
  <collapsable :id="id" :group="group" :expanded="expanded">
    <template v-slot:label="$props">
        <h2 class="flex items-center font-bold text-3xl cursor-pointer">
            <!-- <x-icon.minus-square v-if="!$props.collapsed" class="inline-block mr-2 w-6 h-6"></x-icon.minus-square>
            <x-icon.plus-square v-if="$props.collapsed" class="inline-block mr-2 w-6 h-6"></x-icon.plus-square> -->
            <div height="50" width="50">
                <svg class="mr-3" width="17" height="11" xmlns="http://www.w3.org/2000/svg" style="transition: transform 0.2s linear 0s;" :style="{ transform: $props.collapsed ? '' : 'rotate(180deg)' }">
                    <path stroke="#0C67A9" stroke-width="3" d="M1 2l7.547 6.534L16.017 2" fill="none" fill-rule="evenodd"/>
                </svg>
            </div>
            <div v-html="label"></div>
        </h2>
    </template>
    <template v-slot:default="$props">
        <!-- <div class="p-6 border-iron border-b text-xl font-light" style="background-color: #FAFAFA;"> -->
            <slot></slot>
        <!-- </div> -->
    </template>
</collapsable>
</template>

<script>
export default {
  props: {
    label: {
      type: String,
      default: null
    },
    expanded: {
      type: Boolean,
      default: false
    },
    group: {
      type: String,
      default: null
    },
    id: {
      type: String,
      default: null
    }
  },
}
</script>