<template>
<v-app>
  <div class="content">
    <div class="container-fluid">
      <!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
      <div
        class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
        <p class="_title0">
          Role Manangement
          <Button @click="addModal = true">
            <Icon type="md-add" />
            Add a new role
          </Button>
        </p>
        <!--<div class="_overflow _table_div">-->
        <v-data-table :headers="headers" :items="roles">
          <template v-slot:item="row">
            <!-- ITEMS -->
            <tr>
              <td class="">{{ row.item.id }}</td>
              <td class="">{{ row.item.name }}</td>
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

      <!-- role adding modal -->
      <Modal v-model="addModal" title="Add role" :mask-closable="false" :closable="false">
        <Input v-model="data.name" placeholder="Role name" />
        <div slot="footer">
          <Button type="default" @click="addModal = false">Close</Button>
          <Button
            type="primary"
            @click="add"
            :disabled="isAdding"
            :loading="isAdding">
            {{ isAdding ? "Adding.." : "Add Role" }}
          </Button>
        </div>
      </Modal>

      <!-- tag editing modal -->
      <Modal v-model="editModal" title="Edit role" :mask-closable="false" :closable="false">
        <Input v-model="editData.name" placeholder="Edit tag name" />
        <div slot="footer">
          <Button type="default" @click="editModal = false">Close</Button>
          <Button
            type="primary"
            @click="edit"
            :disabled="isAdding"
            :loading="isAdding">
            {{ isAdding ? "Editing.." : "Edit Role" }}
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
        { text: "Role type", value: "name" },
        { text: "Created at", value: "createdAt", sortable: false },
        { text: "Actions", value: "actions", sortable: false },
      ],
      // TABLE TITLE

      data: {
        name: "",
      },

      addModal: false,
      editModal: false,
      isAdding: false,
      roles: [],
      editData: {
        name: "",
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
      const res = await this.callApi("get", "/api/get_roles");
      if (res.status == 200) {
        this.roles = res.data;
      } else {
        this.swr();
      }
      this.$store.dispatch("setDeleteModalImmediate", false);
    },

    clearInputs(event){
      this.data.name = "";
    },    
    async add() {
      if (this.data.name.trim() == "")
        return this.e("Name is required");
      const res = await this.callApi("post", "/api/create_role", this.data);
      console.log("add data, ok: ", res.status);
      if (res.status === 201) {
        this.roles.unshift(res.data);
        this.fetchItems();
        this.s("Data has been added successfully!");
        this.addModal = false;
        //this.data.name = "";
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
      const res = await this.callApi("post", "/api/edit_role", this.editData);
      if (res.status === 200) {       
        this.roles.splice(this.index, 1, this.editData);
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
      this.index = this.roles.indexOf(tag);
      
      //let i = -1;
      //if (typeof role.name != "undefined") {
      /*  
      let i = 0;
      while (this.roles[i].name != tag.name && i < this.roles.length) {
        i++;
      }
      */
      //}
      
      let obj = {
        id: this.roles[this.index].id,
        name: this.roles[this.index].name,
        created_at: this.roles[this.index].created_at,
      };
      this.editData = obj;
      this.editModal = true;   
    },
    //showDeletingModal(tag, i) {
    showDeletingModal(tag) {
      const deleteModalObj = {
        showDeleteModal: true,  //enable modal show
        deleteUrl: "/api/delete_role",
        data: tag,
        //deletingIndex: i,
        //deletingIndex: this.index = this.roles.indexOf(tag),
        deletingIndex: this.roles.indexOf(tag),
        isDeleted: false,
      };
      //assign deleted item into store
      this.$store.dispatch("setDeletingModalObj", deleteModalObj);
      //this.$store.commit("setDeletingModalObj", deleteModalObj);
      console.log("call showDeletingModal method...");
    },
  },
  //async created() {
  created() {
    console.log("role module, isReadPermitted: ", this.isReadPermitted);

    this.fetchItems();
    /*
    const res = await this.callApi("get", "/api/get_roles");
    if (res.status == 200) {
      this.roles = res.data;
    } else {
      this.swr();
    }
    */
  },
  components: {
    deleteModal,
  },
  watch: {
    '$store.state.immediateD': function() {
      console.log("watch: refresh role....");
      this.fetchItems();
    },
  },
  
};
</script>