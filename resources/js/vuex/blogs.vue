<template>
  <div>
    <div class="content">
      <div class="container-fluid">
        <!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
        <div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
            <p class="_title0">
                Blogs
                <Button @click="watchCreateBlog">
                    <Icon type="md-add" size="small" />
                    &nbsp;Create blog
                </Button>
            </p>

            <div class="_overflow _table_div">
                <table class="_table">
                <!-- TABLE TITLE -->
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Categories</th>
                    <th>Tags</th>
                    <th>Views</th>
                    <th>Action</th>
                </tr>
                <!-- TABLE TITLE -->

                <!-- ITEMS -->
                <!--<div v-if="tags.length" class="_table_vif">-->
                <tr v-for="(blog, i) in blogs" :key="i">
                    <td>{{ blog.id }}</td>
                    <td class="_table_name">{{ blog.title }}</td>
                    <td> 
                      <span v-for="(c, j) in blog.categories" :key="j"><Tag color="success" type="border">{{c.categoryName}}</Tag></span> 
                    </td>
                    <td> 
                      <span v-for="(t, j) in blog.tags" :key="j"><Tag color="primary" type="border">{{t.tagName}}</Tag></span>
                    </td>
                    <td> {{blog.views}}</td>                    
                    <td>
                      <Button type="info" size="small">Visit blog</Button>
                      <Button type="info" size="small" @click="watchEditBlog" v-if="isUpdatePermitted">Edit</Button>
                      <Button type="error" size="small" @click="showDeletingModal(blog, i)" :loading="blog.isDeleting" v-if="isDeletePermitted">Delete</Button>
                    </td>
                </tr>
                <!--</div>-->
                <!-- ITEMS -->
                </table>
            </div>
        </div>

        <delete-modal></delete-modal>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState, mapGetters } from "vuex";
import deleteModal from "./deleteModal";

export default {
  components: {
    deleteModal,
  },

  mounted() {
    //
  },

  data() {
    return {
      addModal: false,
      editModal: false,
      isAdding: false,
      tags: [],
      editData: {
        tagName: "",
      },
      index: -1,
      showDeleteModal: false,
      isDeleting: false,
      deleteItem: {},
      deletingIndex: -1,
      token: "",

      blogs: [],
    };
  },

  async created() {
    console.log("blogs module, isReadPermitted: ", this.isReadPermitted);
    //console.log("tags module, isWritePermitted: ", this.isWritePermitted);
    //console.log("tags module, isUpdatePermitted: ", this.isUpdatePermitted);
    //console.log("tags module, isDeletePermitted: ", this.isDeletePermitted);
    
    //this.token = Cookies.get('XSRF-TOKEN');
    this.token = window.Laravel.csrfToken;
    //console.log("token: ", this.token);

    const res = await this.callApi("get", "/api/blogsdata");
    console.log("blogs module, blog data when create: ", res);
    if (res.status === 200) {
      this.blogs = res.data;
    } else {
      this.swr();
    }
  },

  computed: {
    ...mapState({
      //Spread in object literals
      lastSearchComputed: "lastSearch",
      user: "user",
      isLoggedIn: "isLoggedIn",
      allUsers: "allUsers",
    }),
    /*
		...mapGetters({
            getDeleteModalObj: "getDeleteModalObj",
		}),
		*/
    somethingElse() {
      return 1 + 2;
    },
    users() {
      const nameList = Object.values(this.allUsers).map((item) => item.name);
      return nameList;
    },
  },
 
	watch : {
		getDeleteModalObj(obj) {
			//console.log("watch: ", obj);
			if (obj.isDeleted) {
			//console.log("inside if...");
				this.tags.splice(obj.deletingIndex, 1);
			}
		}
	},

  methods: {
    watchCreateBlog() {
      this.$router.push("/hello1/hello140");
    },
    watchEditBlog() {
        this.$router.push("/hello1/hello142/${blog.id}");        
    },
    async addTag() {
      //if (this.data.tagName.trim()=='')
      //	return this.e('Tag name is required');
      const res = await this.callApi("post", "/api/create_tag", this.data);
      if (res.status === 201) {
        this.tags.unshift(res.data);
        this.s("Tag has been added successfully!");
        this.addModal = false;
        this.data.tagName = "";
      } else {
        if (res.status == 422) {
          if (res.data.errors.tagName) {
            this.e(res.data.errors.tagName[0]);
          }
        } else {
          this.swr();
        }
      }
    },
    async editTag() {
      if (this.editData.tagName.trim() == "")
        return this.e("Tag name is required");
      const res = await this.callApi("post", "/api/edit_tag", this.editData);
      if (res.status === 200) {
        this.tags[this.index].tagName = this.editData.tagName;
        this.s("Tag has been edited successfully!");
        this.editModal = false;
      } else {
        if (res.status == 422) {
          if (res.data.errors.tagName) {
            this.e(res.data.errors.tagName[0]);
          }
        } else {
          this.swr();
        }
      }
    },
    showEditModal(tag, index) {
      let obj = {
        id: tag.id,
        tagName: tag.tagName,
      };
      this.editData = obj;
      this.editModal = true;
      this.index = index;
    },
    async deleteTag() {
      this.isDeleting = true;
      const res = await this.callApi(
        "post",
        "/api/delete_tag",
        this.deleteItem
      );
      if (res.status === 200) {
        this.tags.splice(this.deletingIndex, 1);
        this.s("Tag has been deleted successfully!");
      } else {
        this.swr();
      }
      this.isDeleting = false;
      this.showDeleteModal = false;
    },
    showDeletingModal(tag, i) {
      const deleteModalObj = {
        showDeleteModal: true,
        deleteUrl: "/api/delete_tag",
        data: tag,
        deletingIndex: i,
        isDeleted: false,
      };
      this.$store.dispatch("setDeletingModalObj", deleteModalObj);
      console.log("tags.vue showDeletingModal function is called...");
      //this.deleteItem = tag;
      //this.deletingIndex = i;
      //this.showDeleteModal = true;
      /*
			const deleteModalObj  =  {
				showDeleteModal: true, 
				deleteUrl : '/api/delete_tag', 
				data : tag, 
				deletingIndex: i, 
				isDeleted : false,
			}
			*/
      //this.$store.commit('setDeletingModalObj', deleteModalObj);
      console.log("delete method called");
    },
    showEditModal(tag, index) {
      let obj = {
        id: tag.id,
        tagName: tag.tagName,
      };
      this.editData = obj;
      this.editModal = true;
      this.index = index;
    },

    handleSuccess(res, file) {},
  },
};
</script>

<style scoped>
@import "../../css/fullstack.css";
</style>
