<template>
<div>
    <div class="content">
        <div class="container-fluid">
            <!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
            <div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">

		        <div class="row">
                    <div class="col-xs-6"> 
                        <p class="_title0">
                            Role Selection
                            <Select v-model="data.id" placeholder="Select role type">
                                <Option :value="r.id" v-for="(r, i) in roles" :key="i">
                                    {{ r.name }}
                                </Option>
                            </Select>
                        </p>
                    </div>

                    <!-- Category Multiselect Dropdown Facet -->
                    <div class="col-xs-6"> 
                        <fieldset>
                            <div id="categoryDropdown" class="resource-dropdown">

                                <label for="select-category" class="select-category">  
                                    <legend>By Category</legend>		

                                    <!-- REFRESH buttons should not display until options are selected -->
                                    <button type="refresh" id="reset-button" class="btn btn-clear pull-right">
                                        <i class="custom-icon fa fa-refresh"></i> 
                                    </button>     
                                    <!-- / -->

                                    <div class="form-group">   									
                                        <!-- optgroup  for groups and sub-options on click -->
                                        <!-- span within options class=amount for # of instances-->
                                        <select name="select-category" data-size="5" class="selectpicker dropdown-content form-control" multiple data-width="auto" data-actions-box="true" title="Select Category" data-selected-text-format="count > 1"> 
                                            <option value="">Job Search 
                                                <span style="color: red;" class="amount">2</span>
                                            </option>
                                            <option value="">Resume
                                                <span class="amount">2</span>
                                            </option> 
                                            <option value="">Careers 
                                                <span class="amount">2</span>
                                            </option> 
                                            <option value="">Call Center 
                                                <span class="amount">2</span>
                                            </option> 
                                            <option value="">Industrial &amp; Manufacturing 
                                                <span class="amount">2</span>
                                            </option> 
                                        </select>									
                                    </div>

                                </label> 
                            </div>
                        </fieldset>
                    </div>
                    <!-- / -->
		        </div>

            </div>
        </div>
    </div>
</div>
</template>


<script>
export default {
  data() {
    return {
        data: {
            id: null
        },
        isSending : false,
        roles: [],
        permissions: [],
        resources: [
            {resourceName: 'Home', read: false, write: false, update: false, delete: false, name: '/'},
            {resourceName: 'Tags', read: false, write: false, update: false, delete: false, name: 'tags'},
            {resourceName: 'Category', read: false, write: false, update: false, delete: false, name: 'category'},
            {resourceName: 'Create blogs', read: false, write: false, update: false, delete: false, name: 'createBlog'},
            {resourceName: 'Blogs', read: false, write: false, update: false, delete: false, name: 'blogs'},
            {resourceName: 'Admin users', read: false, write: false, update: false, delete: false, name: 'adminusers'},
            {resourceName: 'Role', read: false, write: false, update: false, delete: false, name: 'role'},
            {resourceName: 'Assign Role', read: false, write: false, update: false, delete: false, name: 'assignRole'},
        ],
        defaultResourcesPermission: [
            {resourceName: 'Home', read: false, write: false, update: false, delete: false, name: '/'},
            {resourceName: 'Tags', read: false, write: false, update: false, delete: false, name: 'tags'},
            {resourceName: 'Category', read: false, write: false, update: false, delete: false, name: 'category'},
            {resourceName: 'Create blogs', read: false, write: false, update: false, delete: false, name: 'createBlog'},
            {resourceName: 'Blogs', read: false, write: false, update: false, delete: false, name: 'blogs'},
            {resourceName: 'Admin users', read: false, write: false, update: false, delete: false, name: 'adminusers'},
            {resourceName: 'Role', read: false, write: false, update: false, delete: false, name: 'role'},
            {resourceName: 'Assign Role', read: false, write: false, update: false, delete: false, name: 'assignRole'},
        ],
    };
  },
  methods: {

  },
  async created() {
    const [res, resRole] = await Promise.all([
        this.callApi('get', 'app/get_permissions'), 
        this.callApi('get', 'app/get_roles')
    ])
    if(res.status==200){
        this.permissions = res.data
    }else{
        this.swr()
    }
    if(resRole.status==200){
        this.roles = resRole.data
    }else{
        this.swr()
    }
  },
};

/*
$(".selectpicker").multiselect({
	// Select and Deselect All buttons at Top of Facet Dropdown
	includeSelectAllOption: true,
	//Clickable Optgroups
	enableClickableOptGroups: true,
	//Select All
	onSelectAll: function () {
		("onSelectAll triggered.");
	}
});

// Refresh Reset Button
$("#multiselect-form").on("reset", function () {
	$("option:selected").each(function () {
		$(this).prop("selected", false);
	});
	$(".selectpicker").multiselect("refresh");
});

// Multiselect Mobile
if (/Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent)) {
	$(".selectpicker").selectpicker("mobile");
}

*/

</script>

<style scoped>
/* === Multi Select Facet Dropdown Styles Adecco UK Re-Branding */
#multiselect-form.resource-form {
  overflow: visible;
  padding: 15px;
  margin: 0;
  border: none;
  position: relative;
}
#multiselect-form.resource-form .resource-dropdown label {
  position: relative;
}
#multiselect-form.resource-form legend {
  font-size: 16px;
  border: none;
  color: #dad9d9;
}
#multiselect-form.resource-form .btn {
  border: none;
  background: transparent;
}
#multiselect-form.resource-form #reset-button {
  position: absolute;
  right: 0;
  top: 0;
  display: inline-block;
  color: red;
}
#multiselect-form.resource-form #reset-button:hover {
  color: rgba(255, 0, 0, 0.6);
}
#multiselect-form.resource-form .btn-group .dropdown-menu {
  border: none;
  cursor: pointer;
  width: 100%;
  min-height: 180px;
}
#multiselect-form.resource-form .btn-group .dropdown-menu li {
  font-size: 14px;
  padding: 5px 15px;
  border: none;
}
#multiselect-form.resource-form .btn-group .dropdown-menu li.selected, #multiselect-form.resource-form .btn-group .dropdown-menu li.selected:hover, #multiselect-form.resource-form .btn-group .dropdown-menu li.selected a, #multiselect-form.resource-form .btn-group .dropdown-menu li:hover {
  background-color: #dad9d9;
  cursor: pointer;
}
#multiselect-form.resource-form .btn-group .dropdown-menu li a span.check-mark {
  position: absolute;
  left: 8px;
  top: 6px;
  font-size: 8px;
  background: red;
  color: white;
  width: 12px;
  padding: 2px;
  border-radius: 30px;
}
#multiselect-form.resource-form .btn-group .dropdown-menu li a span.check-mark:hover {
  background-color: #dad9d9;
  cursor: pointer;
}
#multiselect-form.resource-form .btn-group .dropdown-menu li a:hover {
  background-color: #dad9d9;
}
#multiselect-form.resource-form .btn-group .dropdown select {
  box-shadow: 0 1px 1px 0 #b2b2b2;
}
#multiselect-form.resource-form .btn-group .dropdown select:after {
  color: #ef2e24;
  content: "";
  font-family: FontAwesome;
  right: 10px;
  top: 10px;
  position: absolute;
  width: 35px;
  padding: 0 0 5px;
  text-indent: 14px;
  z-index: -1;
}
#multiselect-form.resource-form .btn-group li.dropdown.open > .dropdown-toggle .caret {
  border-top-color: transparent;
  border-bottom: 4px solid #000000;
  position: relative;
  top: -3px;
}
#multiselect-form.resource-form .caret {
  -webkit-appearance: none;
}
#multiselect-form.resource-form .dropdown .open [aria-expanded=true] {
  color: red;
  background-color: red !important;
  color: #ef2e24;
  content: "";
  font-family: FontAwesome;
}
</style>
