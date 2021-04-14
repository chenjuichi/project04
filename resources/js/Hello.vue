<template>
  <div>
    <div class="links">
      <!-- 建立 Vue Router 連結, MENU LIST-->
      <router-link to="/hello1"></router-link>
      <router-link to="/hello2"></router-link>
      <!--<router-link to="/hello3"></router-link>-->
    </div>
    <router-view></router-view>
    <!-- Vue Router 代入的內容 -->
  </div>
</template>

<script>

export default {
	components: {
      //       
  },
  props:[
    'user', 
    //'permission'
  ],
	data() {
		return {
      curds: []
    };
  },  
  mounted() {
      //
  },

	created() {
      if (!this.user)
        console.log("Hello..., Please Login!");
      else {
        console.log("Hello, this.user: ", this.user);
        console.log("and");
        this.curds = JSON.parse(this.user.roles[0].curds);
        console.log("Hello, this.curds: ", this.curds);

        this.$store.dispatch('setCurrentUserPermissionCurds', this.curds);
      }
	},
	computed: {
      //
    },
	methods: {

	},
};
</script>
