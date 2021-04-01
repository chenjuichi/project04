<template>
<v-app>
    <div class="content">
        <div class="container-fluid">
            <!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
            <div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
                <div class="row">
                    <div class="col-lg-6"> 
                        <div class="_title0">
                            Role Selection
                            <multiselect 
                                v-model="selectedRole" 
                                :options="roleOptions" 
                                :searchable="false" 
                                :close-on-select="true" 
                                :show-labels="false" 
                                placeholder="Pick a value"
                                @input="updateValuePrimitive"
                                :max-height="100">
                            </multiselect>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="_title0">
                            Permission Selection
                            <div class="aParent">
                                <div style="float:left; width:80%;">
                                    <multiselect 
                                        v-model="selectedPermissions" 
                                        :options="permissionOptions"                             
                                        :close-on-select="false"
                                        :multiple="true" 
                                        :hide-selected="true" 
                                        select-label="select it"  
                                        placeholder="Pick a value"
                                        :max-height="100">    
                                    </multiselect>
                                </div>
                                <div style="float:left; width:15%; margin-left:2px;">
                                    <font-awesome-icon 
                                        icon="plus-square"
                                        @click ="addItem" 
                                        :class="[selectedPermissions.length > 0 ? 'active' : 'inactive', 'plus']"
                                    />
                                </div>
                                <div style="clear:both; font-size:1px;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- / -->
                </div>

                <v-data-table 
                    :headers="headers" 
                    :items="listItems"
                    :single-select="singleSelect"
                    item-key="name"
                     
                    class="elevation-2">
                    <template v-slot:item="row">
                        <tr>
                            <td>
                                <v-checkbox 
                                    v-model="row.item.allTag"
                                    color="blue"
                                    @change="loadCustomerDispensers(row.item.allTag, row.item)">
                                </v-checkbox>
                            </td>
                            <td class="">{{ row.item.name }}</td>
                            <td><v-checkbox v-model="row.item.rTag" color="blue"></v-checkbox></td>
                            <td><v-checkbox v-model="row.item.wTag" color="blue"></v-checkbox></td>
                            <td><v-checkbox v-model="row.item.uTag" color="blue"></v-checkbox></td>
                            <td><v-checkbox v-model="row.item.dTag" color="blue"></v-checkbox></td>
                        </tr>
                    </template>
        <!--
                    <template v-slot:top>
                        <v-switch
                            v-model="singleSelect"
                            label="Single select"
                            class="pa-3">
                        </v-switch>
                    </template>
        -->
                </v-data-table>

                <div v-if="isAssign">
                    <template>
                    <v-btn 
                        color="primary" 
                        class="m-2" 
                        :loading="isSending" 
                        @click="assignRoles">
                        Assign
                    </v-btn>
                    </template>
                </div>
            <!--
                <div class="center_button">
                    <Button type="primary" :loading="isSending" :disabled="isSending" @click="assignRoles">Assign</Button>
                </div>
            -->                
            </div>
        </div>
    </div>
</v-app>
</template>
<script>
import Multiselect from 'vue-multiselect';

export default {    
    components: { 
        Multiselect 
    },
    computed: {
    /* 方法2:  
    itemsWithIndex() {
      return this.listItems.map(
        (listItems, index) => ({
          ...listItems,
          index: index + 1
        }))
    },
    */
    },    
    data() {
        return {
            data: {
                id: null
            },
            isSending: false,
            isAddItem: true,
            isAssign: false,

            singleSelect: false,
            roleWithPm: [],
            roles: [],
            permissions: [],

            selectedRole: [],
            selectedPermissions: [],

            roleOptions: [],
            permissionOptions: [],
            name: 'name',

            listItems: [],
            listItems2: [],

            headers: [                    
                {text: '', value: 'allTag',  sortable: false},
                {text: 'Resource name', value: 'name'},
                {text: 'Read', value: 'rTag',  sortable: false},
                {text: 'Write', value: 'wTag',  sortable: false},
                {text: 'Update', value: 'uTag',  sortable: false},
                {text: 'Delete', value: 'dTag',  sortable: false},
            ]            
        };
    },
    watch: {
        listItems: {
            handler: function (val, oldVal) {
                //console.log("a thing changed, val", val);
                //console.log("a thing changed, oldVal", oldVal);
                for (var i = 0, len = val.length; i < len; i++) {
                    if (val[i]['rTag'] && val[i]['wTag'] && val[i]['uTag'] && val[i]['dTag'] && !val[i]['allTag']) {
                        val[i]['allTag'] = true;
                    } else {
                        if ((!val[i]['rTag'] && !val[i]['wTag'] && !val[i]['uTag'] && !val[i]['dTag'] && val[i]['allTag'])
                            || !(val[i]['rTag'] && val[i]['wTag'] && val[i]['uTag'] && val[i]['dTag'])) {
                            val[i]['allTag'] = false;
                        }
                    }
                }
            },
        deep: true
        }
    },  //end watch
    methods: {
        async updateValuePrimitive(value) {
            this.isAssign = true;
            const res = await this.callApi("post", "/api/get_roleWithPermissions", {
                name: value,
            });
            //console.log("updateValuePrimitive, res: ", res);
            console.log("step 1...");
            if (res.status == 200) {
                //console.log("update step 1...");
                this.data.id = res.data.id;                
                this.roleWithPm = res.data.permissions;
                //console.log("update step 2...");
                let roleWithCurd = {
                    allTag: false,
                    name: null, 
                    rTag: false, 
                    wTag: false, 
                    uTag: false, 
                    dTag: false,                    
                };
                //if (res.data.curds == null) {
                //    var roleWithCurd = null;
                //} else {
                if (res.data.curds != null) {
                    roleWithCurd = JSON.parse(res.data.curds);
                }
                console.log("roleWithCurd=> ", roleWithCurd);
                //console.log("update step 3...");
                //console.log("* updateValuePrimitive: ", res.data.curds);
                //console.log("* updateValuePrimitive: ", roleWithCurd);
                this.listItems = [];
                this.selectedPermissions = [];
                for (var i = 0, len = this.roleWithPm.length; i < len; i++) {
                    //console.log("update step 4...");
                    //let temp = this.roleWithPm[i].name;
                    let temp = this.roleWithPm[i]['name'];

                    //console.log("update step 5...");
                    if (this.permissionOptions.indexOf(temp) >= 0) {
                        this.selectedPermissions.push(temp);
                    }
            
                    //console.log("updateValuePrimitive, roleWithCurd[i]: ", roleWithCurd);
                    console.log("step 2...");
                    //let objCopy = {};

                    //if (roleWithCurd == null) {
                    //    console.log("step 3...");
                    //    objCopy = {
                    //        allTag: false,
                    //        name: temp, 
                    //        rTag: false, 
                    //        wTag: false, 
                    //        uTag: false, 
                    //        dTag: false,                    
                    //    };
                    //} else { 

                    //if (roleWithCurd != null) {
                    if (roleWithCurd[i] !=null) {
                        console.log("step 4...");
                        let objCopy = Object.assign({}, roleWithCurd[i]);
                        //objCopy = {
                        //    allTag: roleWithCurd[i]['allTag'],
                        //    name: temp, 
                        //    rTag: roleWithCurd[i]['rTag'], 
                        //    wTag: roleWithCurd[i]['wTag'], 
                        //    uTag: roleWithCurd[i]['uTag'], 
                        //    dTag: roleWithCurd[i]['dTag'],                    
                        //};
                        this.listItems.push(objCopy);                
                    }
                    //this.listItems.push(objCopy);                
                }
            } else {
                this.swr();
            }
        },
        async addItem() {
            //if (typeof array != "undefined" && array != null && array.length != null && array.length > 0) {
            //    // array exists and is not empty
            //}
            this.listItems2 = this.listItems;
            this.listItems = [];
            console.log("addItem, this.listItems2(old): ", this.listItems2)
            if (this.selectedPermissions.length == 0) {
                return;
            }

            for (var i = 0, len = this.selectedPermissions.length; i < len; i++) {
                let temp = this.selectedPermissions[i];
                let objCopy = {};
                objCopy = {
                    allTag: false,
                    name: temp, 
                    rTag: false, 
                    wTag: false, 
                    uTag: false, 
                    dTag: false,                    
                };
                this.listItems.push(objCopy);   
            }

            console.log("addItem, this.listItems(new): ", this.listItems);
            const nameList = Object.values(this.listItems2).map(item => item.name);
            for (var i = 0, len = nameList.length; i < len; i++) {
                var tmpIndex = nameList.indexOf(this.listItems[i]['name']);
                if ( tmpIndex >= 0) {
                    this.listItems[i] = Object.assign({}, this.listItems2[tmpIndex]);
                }
            }

            //const result = this.listItems.map(item =>  this.listItems2.find(item2 => item.id === item2.id) || item)
            //this.listItems = result;
            console.log("addItem, this.listItems(result): ", this.listItems);

            let request = {
                role: this.selectedRole,
                permissions: this.selectedPermissions,
            };
            console.log("request: ", request.permissions);
            
            const res = await this.callApi("post", "/api/select_permissions", request);
            if (res.status === 200) {
                this.s("Role's permissions has been edited successfully!");
            } else {
                if (res.status == 422) {
                    for (let i in res.data.errors) {
                        this.e(res.data.errors[i][0]);
                    }
                } else {
                    this.swr();
                }
            }
            
        },
        async assignRoles(){
            let data = JSON.stringify(this.listItems)
            console.log("assignRoles:", data);
            
            const res = await this.callApi('post','/api/assign_roles', {'permission' : data, id: this.data.id})
            if (res.status==200) {
                this.s('Role has been assigned successfully!')
                let index = this.roles.findIndex(role => role.id == this.data.id)
                this.roles[index].permission = data
                //console.log("assignroles, before saving to store: ", data);
                
                let newCurds = [];                
                newCurds =JSON.parse(data);

                //console.log("assignroles, value, saving to store: ", JSON.parse(data));
                this.$store.dispatch('setCurrentUserPermissionCurds', newCurds);
            } else {
                this.swr()
            }            
        },
        loadCustomerDispensers(customerStatus, index) {
            let temp = this.listItems.indexOf(index);
            this.changeServicePlanData(customerStatus, temp);
        },
        changeServicePlanData(status, index) {            
            if (status) {
                this.listItems[index].rTag = true;
                this.listItems[index].wTag = true;
                this.listItems[index].uTag = true;
                this.listItems[index].dTag = true;
            } else {
                this.listItems[index].rTag = false;
                this.listItems[index].wTag = false;
                this.listItems[index].uTag = false;
                this.listItems[index].dTag = false;
            }                  
        },        
    },  //end method
    async created() {
        const [res, resRole] = await Promise.all([
            this.callApi('get', '/api/get_permissions'), 
            this.callApi('get', '/api/get_roles'),
        ]);

        if (res.status==200) {
            this.permissions = res.data;
        } else {
            this.swr();
        }

        if (resRole.status==200) {
            this.roles = resRole.data;
            //if (resRole.data.length) {
            //    //this.data.id = resRole.data[0].id
            //    if (resRole.data[0].curds) {
            //        this.listItems = JSON.parse(resRole.data[0].curds)
            //    }
            //}            
        } else {
            this.swr();
        }

        for (var i = 0, len = this.roles.length; i < len; i++) {
            this.roleOptions.push(this.roles[i].name);
        }

        for (var i = 0, len = this.permissions.length; i < len; i++) {
            this.permissionOptions.push(this.permissions[i].name);
        }       
    }   // end created()
};
</script>

<style scoped>

</style>
