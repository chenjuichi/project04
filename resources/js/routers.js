
// Define route components.
// These can be imported from other files
import VueRouter from 'vue-router';

import Hello1 from "./Hello1";
import Hello2 from "./Hello2";
import Hello11 from "./Hello11";
import Hello12 from "./Hello12";
import Hello131 from "./components/ListUsers.vue";
import Hello132 from "./components/ListRoles.vue";
//import Hello132 from "./components/iViewHome.vue";

import Hello133 from "./vuex/useCom.vue";
import Hello134 from "./vuex/tags.vue";
import Hello135 from "./vuex/category.vue";
import Hello136 from "./vuex/adminUsers.vue";
import Hello137 from "./vuex/role.vue";
import Hello138 from "./vuex/assignRole.vue";
import Hello139 from "./vuex/permission.vue";
import Hello140 from "./vuex/createBlog.vue";
import Hello141 from "./vuex/blogs.vue";
import Hello142 from "./vuex/editBlog.vue";
import notfound from "./vuex/notfound.vue";

import Welcome from "./Welcome";

// Define some routes, each route should map to a component.
const routes = [
    {
        path: '/hello1', component: Hello1, name: "hello1",
        children: [
            { path: 'hello11', name: "hello1.hello11", component: Hello11 },
            { path: 'hello12', name: "hello1.hello12", component: Hello12 },
            { path: 'hello131', name: "hello1.hello131", component: Hello131 },
            { path: 'hello132', name: "hello1.hello132", component: Hello132 },
            { path: 'hello133', name: "hello1.hello133", component: Hello133 },
            { path: 'hello134', name: "hello1.hello134", component: Hello134 },
            { path: 'hello135', name: "hello1.hello135", component: Hello135 },
            { path: 'hello136', name: "hello1.hello136", component: Hello136 },
            { path: 'hello137', name: "hello1.hello137", component: Hello137 },
            { path: 'hello138', name: "hello1.hello138", component: Hello138 },
            { path: 'hello139', name: "hello1.hello139", component: Hello139 },
            { path: 'hello140', name: "hello1.hello140", component: Hello140 },
            { path: 'hello141', name: "hello1.hello141", component: Hello141 },
            { path: 'hello142/:id', name: "hello1.hello142", component: Hello142 },
        ]
    },
    { path: '/hello2', component: Hello2, name: "hello2"},
    //{path: '/hello3',   component: Hello3,  name: "hello3"},
    { path: '/', name: "welcome", component: Welcome},
    { path: '/notfound', name: 'notfound', component: notfound},
    { path: '*', redirect: '/' },
];

// Create the router instance and pass the `routes` option
const router = new VueRouter({
    routes,			// short for `routes: routes
    mode: 'history',
});

export default router;
