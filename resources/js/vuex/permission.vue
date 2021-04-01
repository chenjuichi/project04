<template>
<v-app>
  <div class="content">
    <div class="container-fluid">
      <!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
      <div
        class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
        <p class="_title0">
          Permission Manangement
          <Button @click="addModal = true">
            <Icon type="md-add" />
            Add a new permission
          </Button>
        </p>
        <!--<div class="_overflow _table_div">-->
        <v-data-table :headers="headers" :items="permissions">
          <template v-slot:item="row">
            <!-- ITEMS -->
            <tr>
              <td class="">{{ row.item.id }}</td>
              <td class="">{{ row.item.name }}</td>
              <td class="">{{ row.item.description }}</td>
              <td class="">{{ row.item.created_at }}</td>
              <td>
                <Button type="info" size="small" @click="showEditModal(row.item)">
                  Edit
                </Button>
<!--                
                <Button
                  type="error"
                  size="small"
                  @click="showDeletingModal(row.item)"
                  :loading="row.item.isDeleting">
                  Delete
                </Button>
-->              
                <Button type="error" size="small" @click="showDeletingModal(row.item)">
                  Delete
                </Button>
              </td>
            </tr>
            <!-- ITEMS -->
          </template>
        </v-data-table>
        <!--</div>-->
      </div>

      <!-- tag adding modal -->
      <Modal v-model="addModal" title="Add permission" :mask-closable="false" :closable="false">

        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text">Name</span>
          </div>
          <input type="text" class="form-control" v-model="data.name">
        </div>
        <br>
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text">Description</span>
          </div>
          <input type="text" class="form-control" v-model="data.description">
        </div>

        <div slot="footer">
          <Button type="default" @click="addModal = false">Close</Button>
          <Button
            type="primary"
            @click="add"
            :disabled="isAdding"
            :loading="isAdding">
            {{ isAdding ? "Adding.." : "Add Permission" }}
          </Button>
        </div>
      </Modal>

      <!-- tag editing modal -->
      <Modal v-model="editModal" title="Edit permission" :mask-closable="false" :closable="false">

        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text">Name</span>
          </div>
          <input type="text" class="form-control" v-model="editData.name">
        </div>
        <br>
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text">Description</span>
          </div>
          <input type="text" class="form-control" v-model="editData.description">
        </div>

        <div slot="footer">
          <Button type="default" @click="editModal = false">Close</Button>
          <Button
            type="primary"
            @click="edit"
            :disabled="isAdding"
            :loading="isAdding">
            {{ isAdding ? "Editing.." : "Edit Permission" }}
          </Button>
        </div>
      </Modal>

      <!-- delete alert modal -->
      <deleteModal></deleteModal>
    </div>
  </div>
</v-app>
</template>

<script>
import { mapState, mapGetters } from "vuex";
import deleteModal from "./deleteModal";

export default {
  data() {
    return {
      // TABLE TITLE
      headers: [
        { text: "ID", value: "id" },
        { text: "Permission name", value: "name" },
        { text: "Description", value: "description", sortable: false },
        { text: "Created at", value: "createdAt", sortable: false },
        { text: "Actions", value: "actions", sortable: false },
      ],
      // TABLE TITLE

      data: {
        name: "",
        description: "",
      },

      addModal: false,
      editModal: false,
      isAdding: false,
      permissions: [],
      editData: {
        name: "",
        description: "",
      },
      index: -1,
      showDeleteModal: false,
      //isDeleting: false,
      deletingIndex: -1,
      
      websiteSettings: [],
    };
  },
  methods: {
    async fetchItems() {
      const res = await this.callApi("get", "/api/get_permissions");
      if (res.status == 200) {
        this.permissions = res.data;
      } else {
        this.swr();
      }
      this.$store.dispatch("setDeleteModalImmediate", false);
    },

    clearInputs(event){
      this.data.name = "";
      this.data.description = "";
    },    
    async add() {
      if (this.data.name.trim() == "")
        return this.e("Name is required");
      const res = await this.callApi("post", "/api/create_permission", this.data);
      console.log("add data, ok: ", res.status);
      if (res.status === 201) {
        this.permissions.unshift(res.data);
        this.fetchItems();
        this.s("Data has been added successfully!");
        this.addModal = false;
        this.clearInputs();
      } else {
        if (res.status == 422) {
          if (res.data.errors.name) {
            this.e(res.data.errors.name[0]);
          }
        } else {
          this.swr();
        }
      }
    },
    async edit() {
      if (this.editData.name.trim() == "")
        return this.e("Name is required");
      const res = await this.callApi("post", "/api/edit_permission", this.editData);
      if (res.status === 200) {       
        this.permissions.splice(this.index, 1, this.editData);
        this.s("Data has been edited successfully!");
        this.editModal = false;
      } else {
        if (res.status == 422) {
          if (res.data.errors.name) {
            this.e(res.data.errors.name[0]);
          }
        } else {
          this.swr();
        }
      }
    },
    showEditModal(tag) {
      this.index = this.permissions.indexOf(tag);
      
      let obj = {
        id: this.permissions[this.index].id,
        name: this.permissions[this.index].name,
        description: this.permissions[this.index].description,
        created_at: this.permissions[this.index].created_at,
      };
      this.editData = obj;
      this.editModal = true;   
      console.log("edit i:", obj );
    },
    showDeletingModal(tag) {
      const deleteModalObj = {
        showDeleteModal: true,  //enable modal show
        deleteUrl: "/api/delete_permission",
        data: tag,
        deletingIndex: this.permissions.indexOf(tag),
        isDeleted: false,
      };
      //assign deleted item into store
      this.$store.dispatch("setDeletingModalObj", deleteModalObj);
      console.log("call showDeletingModal method...");
    },
  },
  //async created() {
  created() {
    this.fetchItems();
  },
  components: {
    deleteModal,
  },
  watch: {
    '$store.state.immediateD': function() {
      console.log("watch: refresh permission....");
      this.fetchItems();
    },
  },
  
};
</script>