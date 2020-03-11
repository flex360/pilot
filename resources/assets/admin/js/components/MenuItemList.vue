<template>
  <div>
    <div class="menu-item-list-empty" v-if="items.length == 0">
      <div v-if="loaded">This menu has no items.</div>
      <div v-if="! loaded" class="menu-item-loader text-center">
        <!-- <i class="fas fa-sync-alt fa-2x fa-spin"></i> -->
        <i class="fas fa-circle-notch fa-2x fa-spin"></i>
      </div>
    </div>

    <div class="menu-item-list" v-if="items.length > 0">
      <draggable
        v-model="items"
        group="menuItems"
        draggable=".menu-item"
        handle=".item-handle"
        @start="drag=true"
        @end="drag=false"
        @update="updateSorting()"
        class="menu-item-drag-container"
      >
        <div
          class="menu-item"
          v-for="(item, index) in items"
          :key="index"
          :style="{ marginLeft: ((item.level - 1) * 30) + 'px' }"
        >
          <div class="menu-item-title">
            <span class="item-handle">
              <i class="fas fa-grip-vertical"></i>
            </span>
            {{ item.title }}
          </div>
          <div class="menu-item-buttons">
            <button class="btn btn-light btn-sm" @click.prevent="levelDown(index)">
              <i class="fas fa-chevron-left"></i>
            </button>

            <button class="btn btn-light btn-sm ml-1" @click.prevent="levelUp(index)">
              <i class="fas fa-chevron-right"></i>
            </button>

            <menu-edit-modal :data="item" class="ml-1"></menu-edit-modal>

            <button class="btn btn-danger btn-sm ml-1" @click.prevent="deleteItem(index)">Delete</button>
          </div>
        </div>
      </draggable>
    </div>

    <textarea name="items" v-model="json" style="display: none;"></textarea>
  </div>
</template>

<script>
import draggable from "vuedraggable";

export default {
  components: {
    draggable
  },
  props: ["id"],

  data: () => ({
    items: [],
    loaded: false
  }),

  computed: {
    json: function() {
      return JSON.stringify(this.items);
    }
  },

  mounted() {
    var self = this;

    axios
      .get("/pilot/menu/" + self.id + "/items")
      .then(function(response) {
        // handle success
        self.items = response.data;
        self.loaded = true;
      })
      .catch(function(error) {
        // handle error
        console.log(error);
      })
      .finally(function() {
        // always executed
      });
  },

  created() {
    this.$eventHub.$on("menu-item-saved", this.addItem);
  },
  beforeDestroy() {
    this.$eventHub.$off("menu-item-saved");
  },

  methods: {
    addItem: function(item) {
      console.log(item);
      this.items.push(item);
    },

    deleteItem: function(index) {
      // var sure = confirm('Are you sure?');

      // if (sure) {
      this.items.splice(index, 1);
      // }
    },

    updateSorting: function() {
      //
    },

    levelDown: function(index) {
      if (this.items[index].level > 1) {
        this.items[index].level--;
      }
    },

    levelUp: function(index) {
      if (this.getMaxLevel(index) > this.items[index].level) {
        this.items[index].level++;
      }
    },

    getMaxLevel: function(index) {
      if (index == 0) {
        return 1;
      }

      return this.items[index - 1].level + 1;
    }
  }
};
</script>

<style scoped lang="scss">
.menu-item-list-empty {
  border: 1px solid #ccc;
  border-radius: 0.25rem;
  padding: 10px;
}

.menu-item-loader {
  color: #808080;
}

.menu-item-list {
  border: 1px solid #ccc;
  border-radius: 0.25rem;
  margin-bottom: 20px;
  background-color: #959baf4f;
}

.menu-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 5px;
  border-bottom: 1px solid #ccc;
  background-color: #fff;
}

.menu-item:last-child {
  border-bottom: none;
}

.menu-item-title {
  margin-left: 5px;
  .item-handle {
    margin-right: 5px;
    cursor: pointer;
    color: #6d6d6d;
  }
}

.menu-item-buttons {
  display: flex;
}
</style>

