<template>
<v-app>
    <div
        v-bind:class="['icon-search-container', { 'active': searchTrigger }]"
        @click="selectSearch">

        <span class="fa fa-search"></span>
        <input
            ref="search"
            @blur="selectInputBlur"
            @focus="searchTrigger=true"
            v-model="valueForSearch"
            type="text"
            class="search-input"
            placeholder="Search"/>
        <span class="fa fa-times-circle"></span>
    </div>
</v-app>
</template>

<script>

export default {
	data() {
		return {
			valueForSearch: "",
            searchTrigger: false,
        };
    },
	methods: {
        selectSearch(e) {
            this.myClick = true;
			this.$refs.search.focus();
            console.log("click search icon.....")
        },
        selectInputBlur(e) {
            console.log("blur function is on...");
            this.searchTrigger = false;
        },
	},
};
</script>

<style scoped>

/* begin search icon */
@import '../css/search.css';
/* end search icon */

</style>
