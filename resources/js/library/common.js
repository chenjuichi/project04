//import { mapGetters } from 'vuex'
import { mapState, mapGetters } from 'vuex';

export default {
    data() {
        return {

        }
    }, 
    methods: {
        async callApi(method, url, dataObj ) {
            try {
              return await axios({
                    method: method,
                    url: url,
                    data: dataObj
                });
            } catch (e) {
                return e.response
            }
        }, 
/* reference iView */
        i(desc, title="Hey") {
            this.$Notice.info({
                title: title,
                desc: desc
            });
        },
        s(desc, title="Great!") {
            this.$Notice.success({
                title: title,
                desc: desc
            });
        },
        w(desc, title="Oops!") {
            this.$Notice.warning({
                title: title,
                desc: desc
            });
        },
        e(desc, title="Oops!") {
            this.$Notice.error({
                title: title,
                desc: desc
            });
        }, 
        swr(desc='Somethingn went wrong! Please try again.', title="Oops") {
            this.$Notice.error({
                title: title,
                desc: desc
            });
        },
         
        checkUserPermission(key){
            let tempP = this.getPermission;
            console.log("common.js, this.getPermission => ", tempP);
            //if (!tempP) {
            if (tempP.length == 0) {
                return true;
            }
            //console.log("this.getPermission check is ok...", this.$route.name);
            let isPermitted = false;
            let tempRoute = this.$route.name.replace('.', '/');
            for (let i = 0; i < tempP.length; i++) {
                console.log("this.getPermission check name: ", tempRoute, tempP[i]['name'], tempP[i][key]);
                if (tempRoute==tempP[i]['name']) {
                    if (tempP[i][key]) {
                        isPermitted = true;
                        break;
                    }
                    break;
                }                
            }
            return isPermitted;
        }

    },

    computed: {
		...mapState({	//Spread in object literals
			//lastSearchComputed: "lastSearch",
			//user: "user",
			//isLoggedIn: "isLoggedIn",
			//allUsers: "allUsers",
            //userPermission: "currentUserPermissionCurds",
		}),

        ...mapGetters({
            userPermission : "getUserPermission",
        }),

        getPermission() {
            return this.$store.getters.getUserPermission;
        },

        isReadPermitted(){
           return this.checkUserPermission('rTag');
        },

        isWritePermitted(){
            return this.checkUserPermission('wTag');
        },

        isUpdatePermitted(){
            return this.checkUserPermission('uTag');
        },

        isDeletePermitted(){
            return this.checkUserPermission('dTag');
        },
    },    
   
}