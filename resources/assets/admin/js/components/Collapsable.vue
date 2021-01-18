<template>
  <div>
    <div :class="'collapsable-container-toggle text-center relative ' + labelClass"  @click="click">
      <slot name="label" :collapsed="state.collapsed">
        <div class="flex justify-center" v-html="label"></div>
        <div class="absolute inset-0 flex justify-end items-center pr-4">
          <svg width="17" height="11" xmlns="http://www.w3.org/2000/svg" style="transition: transform 0.2s linear 0s;" :style="{ transform: state.collapsed ? '' : 'rotate(180deg)' }">
            <path stroke="#0C67A9" stroke-width="3" d="M1 2l7.547 6.534L16.017 2" fill="none" fill-rule="evenodd"/>
          </svg>
        </div>
      </slot>
    </div>
    <div ref="container" class="collapsable-container" :class="classObject">
      <transition-expand>
      <slot v-if="!state.collapsed"></slot>
      </transition-expand>
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
      default: null
    },
    collapsed: {
      type: Boolean,
      default: null
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
      state: {
        collapsed: true,
        animating: false,
        expanding: false,
        collapsing: false,
      },
      $eventHub: new Vue(), // Global event bus,
    }
  },
  mounted () {
    this.$nextTick(() => {
      if (this.expanded !== null) {
        this.state.collapsed = !this.expanded;
      }
      if (this.collapsed !== null) {
        this.state.collapsed = this.collapsed;
      }
    });

    this.$refs.container.addEventListener('transitionstart', (e) => {
      this.state.animating = true;
      this.state.collapsing = this.state.collapsed;
      this.state.expanding = !this.state.collapsed;
    });

    this.$refs.container.addEventListener('transitionend', (e) => {
      this.state.animating = false;
      this.state.collapsing = false;
      this.state.expanding = false;
    });
  },
  computed: {
    classObject() {
      return {
        collapsed: this.state.collapsed,
        expanded: !this.state.collapsed,
        animating: this.state.animating,
        expanding: this.state.expanding,
        collapsing: this.state.collapsing,
      }; 
    }
  },
  created () {
    this.$eventHub.$on('collapsable-group-clicked', (item) => {
      if (this.id !== null && item.id != this.id && this.group == item.group) {
        this.close();
      }
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
      this.state.collapsed ? this.expand() : this.collapse();
    },
    close() {
      this.collapse();
    },
    collapse() {
      this.state.collapsed = true;
    },
    expand() {
      this.state.collapsed = false;
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
  &.expanded:not(.animating) {
      overflow: visible;
  }
}
</style>