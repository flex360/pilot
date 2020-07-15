<template>
  <div>
    <div :class="'collapsable-container-toggle text-center relative ' + labelClass"  @click="click">
      <slot name="label" :collapsed="collapsed">
        <div class="flex justify-center" v-html="label"></div>
        <div class="absolute inset-0 flex justify-end items-center pr-4">
          <svg width="17" height="11" xmlns="http://www.w3.org/2000/svg" style="transition: transform 0.2s linear 0s;" :style="{ transform: collapsed ? '' : 'rotate(180deg)' }">
            <path stroke="#0C67A9" stroke-width="3" d="M1 2l7.547 6.534L16.017 2" fill="none" fill-rule="evenodd"/>
          </svg>
        </div>
      </slot>
    </div>
    <div ref="container" class="collapsable-container" :class="{ collapsed: collapsed, expanded: !collapsed, animating: animating }">
      <slot></slot>
    </div>
  </div>
</template>

<script>
import Vue from 'vue';

export default {
  props: {
    label: {
      type: String,
      default: 'Click Here'
    },
    labelClass: {
      type: String,
      default: ''
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
  data() {
    return {
      collapsed: false,
      animating: false,
      $eventHub: new Vue() // Global event bus
    }
  },
  created () {
    this.$eventHub.$on('collapsable-group-clicked', (item) => {
      if (this.id !== null && item.id != this.id && this.group == item.group) {
        this.close();
      }
    });
  },
  mounted () {
    this.$nextTick(() => {
      this.$refs.container.style.maxHeight = this.$refs.container.offsetHeight + 'px';
      this.collapsed = this.expanded ? false : true;
    });
  },
  methods: {
    click() {
      this.toggle();
      if (this.id !== null && this.group !== null) {
        this.$eventHub.$emit('collapsable-group-clicked', this);
      }
    },
    toggle() {
      this.collapsed = !this.collapsed;
      this.startAnimation();
    },
    close() {
      this.collapsed = true;
      this.startAnimation();
    },
    startAnimation() {
        this.animating = true;
        setTimeout(() => {
            this.animating = false;
        }, 250);
    }
  },
}
</script>

<style lang="scss" scoped>
.collapsable-container-toggle {
  svg path {
    stroke: currentColor;
  }
}
.collapsable-container {
  transition: max-height 0.25s linear;
  overflow: hidden;
  &.collapsed {
    max-height: 0px !important;
  }
  &.expanded:not(.animating) {
      overflow: visible;
  }
}
</style>