<template>
<v-app>
  <div class="content">
    <div class="container-fluid">

      <!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
      <div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
        <p class="_title0">Create blog</p>
        <div class="_input_field">
					<Input type="text" v-model="data.title" placeholder="Title" />
				</div>
        <div class="_overflow _table_div blog_editor">
          <Editor  v-if="initData"
            ref="editor"
            
            autofocus=true
            holder-id="codex-editor"
            save-button-id="save-button"
            :init-data="initData"
            @save="onSave"
            :config="config"
            :initialized="onInitialized"            
          />
        </div>

        <div class="_input_field">
          <Input type="textarea" v-model="data.post_excerpt" :rows="4" placeholder="Post excerpt " />
        </div>
        <div class="_input_field">
          <Select filterable multiple placeholder="Select category" v-model="data.category_id">
            <Option v-for="(c, i) in category" :value="c.id" :key="i">
              {{ c.categoryName }}
            </Option>
          </Select>
        </div>
        <div class="_input_field">
          <Select  filterable multiple placeholder="Select tag" v-model="data.tag_id">
            <Option v-for="(t, i) in tag" :value="t.id" :key="i">
              {{ t.tagName }}
            </Option>
          </Select>
        </div>
        <div class="_input_field">
            <Input  type="textarea" v-model="data.metaDescription" :rows="4" placeholder="Meta description" />
        </div>
        <div class="_input_field">
          <Button 
          @click="save" 
          :loading="isCreating" 
          :disabled="isCreating">
          {{isCreating ? 'Please wait...' : 'Create blog'}}
        </Button>
        </div>
      </div>
    </div>
  </div>
</v-app>
</template>

<script>
import { mapState, mapGetters } from "vuex";
import myHeader from '@editorjs/header';
import myImage from '@editorjs/image';
import myList from '@editorjs/list';
import myEmbed from '@editorjs/embed';
import myLink from '@editorjs/link';
import myChecklist from '@editorjs/checklist';
import myParagraph from '@editorjs/paragraph';
import myCode from '@editorjs/code';
import myQuote from '@editorjs/quote';
import myRaw from '@editorjs/raw';
import myWarning from '@editorjs/warning';
import myTable from '@editorjs/table';
import myDelimiter from '@editorjs/delimiter';

export default {
  data() {
    return {
      config: {
        //holderId: 
        tools: {
          header:   {class: myHeader,   inlineToolbar: ['link']},
          list:     {class: myList,     inlineToolbar: ['link', 'bold']},
          embed:    {class: myEmbed,    inlineToolbar: false, config: {services: {youtube: true, coub: true}}},
          checklist:{class: myChecklist,inlineToolbar: true},
          paragraph:{class: myParagraph,inlineToolbar: true},
          warning:  {class: myWarning,  inlineToolbar: true,  config: {
                                                                titlePlaceholder: 'Title', 
                                                                messagePlaceholder: 'Message'}, 
                                                              shortcut: 'CMD+SHIFT+W'},
          table:    {class: myTable, inlineToolbar: true,     config: {rows: 2, cols: 3}},
          raw:      {class: myRaw},
          delimiter:{class: myDelimiter},
          code:     {class: myCode},
          image:    {class:myImage},
          linkTool: {class: myLink},
          quote:    {class: myQuote, inlineToolbar: true, config: {
                                                            quotePlaceholder: 'Enter a quote',
                                                            captionPlaceholder: 'Quote\'s author'},
                                                          shortcut: 'CMD+SHIFT+O'},
                               
        },  //end tools:       
      },    //end config:

      initData: null,
			category : [],
      tag: [],

      data: {
          title : '',
          post : '',
          post_excerpt : '',
          metaDescription : '',
          category_id : [],
          tag_id : [],
          jsonData: null,			
      },
      articleHTML: '',
      isCreating: false, 
      
      element: null,
      blocks: [],      
    };
  },
  components: {
     //
  },
  mounted() {

  },
  methods: {
    upload() {
      console.log("hello, EditorJS image....")
    },
    async fetchItems() {
      const res = await this.callApi("get", "/api/get_roles");
      if (res.status == 200) {
        this.roles = res.data;
      } else {
        this.swr();
      }
      this.$store.dispatch("setDeleteModalImmediate", false);
    },

    clearInputs(event) {
      this.data.title = '';
      this.data.post = '';
      this.data.post_excerpt = '';
      this.data.metaDescription = '';
      this.data.category_id = [];
      this.data.tag_id = [];
      this.data.jsonData = null;

      //this.blocks = [];     // remove all Blocks
      //let e = document.getElementById("vue-editor-j")
      //this.removeAllText(e);
      
      this.initData = null;
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
        //this.addModal = false;
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
    onChange(args) {
      console.log('hi jimmy, Editor.js is onChange!');
      args.saver.onSave();
    },
    onInitialized (editor) {
      console.log('Hi jimmy, Editor.js is onInitialized!', editor);
    },
    onReady() {
        console.log('hi jimmy, Editor.js is onReady!');
    },    
	  async onSave(response) {
      var data = response;
      await this.outputHtml(data.blocks);

      console.log("1.response on save, this.articleHTML: ", this.articleHTML);
      console.log("2.response on save, data: ", data);      

      this.blocks = data.blocks;
      console.log("this.element(data.blocks): ", this.element);


      this.data.post = this.articleHTML;
      this.data.jsonData = JSON.stringify(data);

      if (this.data.post.trim()=='') 
        return this.e('Post is required');
      if (this.data.title.trim()=='') 
        return this.e('Title is required');
      if (this.data.post_excerpt.trim()=='') 
        return this.e('Post exerpt is required');
      if (this.data.metaDescription.trim()=='') 
        return this.e('Meta description is required');
      if (!this.data.tag_id.length) 
        return this.e('Tag is required');
      if (!this.data.category_id.length) 
        return this.e('Category is required');
        
      this.isCreating = true;
      const res = await this.callApi('post', '/api/create-blog', this.data);
      
      if (res.status===200) {
        this.s('Blog has been created successfully!');
        this.clearInputs();
        // redirect...
        //this.$router.push('/blogs');
        this.$router.push("/hello1/hello140");
      } else {
        if (res.status==422) {
          for (let i in res.data.errors) {
            this.e(res.data.errors[i][0]);
          }
        } else {
          this.swr();
        }
      }
      this.isCreating = false;
	  },
    /*
    async save() {
      try {
        await this.$refs.editor._data.state.editor.save();
        //Do anything you need after editor initialization
        //....
      } catch (reason) {
        console.log(`Editor.js initialization failed because of ${reason}`)
      }
    },
   */
    save() {
      this.$refs.editor._data.state.editor.save() 
      .then((data) => {
        // Do what you want with the data here        
        console.log('hi, jimmy, Editor.js is ready to save()!');
        
        this.onSave(data);
      })
      .catch(err => { 
        console.log(`Editor.js initialization failed because of ${err}`);
      })
    },
    outputHtml(articleObj){
      articleObj.map(obj => {
        switch (obj.type) {
          case 'paragraph':
            this.articleHTML += this.makeParagraph(obj);
            break;
          case 'image':
            this.articleHTML += this.makeImage(obj);
            break;
          case 'header':
            this.articleHTML += this.makeHeader(obj);
            break;
          case 'raw':
            this.articleHTML += `<div class="ce-block">
            <div class="ce-block__content">
            <div class="ce-code">
              <code>${obj.data.html}</code>
            </div>
            </div>
          </div>\n`;
            break;
          case 'code':
            this.articleHTML += this.makeCode(obj);
            break;
          case 'list':
            this.articleHTML += this.makeList(obj)
            break;
          case "quote":
            this.articleHTML += this.makeQuote(obj)
            break;
          case "warning":
            this.articleHTML += this.makeWarning(obj)
            break;
          case "checklist":
            this.articleHTML += this.makeChecklist(obj)
            break;
          case "embed":
            this.articleHTML += this.makeEmbed(obj)
            break;
          case 'delimeter':
            this.articleHTML += this.makeDelimeter(obj);
            break;
          default:
            return '';
        }
    });
    },    
    removeAllText(el) {

      // loop through all the nodes of the element
      var nodes = el.children;

      for (var i = 0; i < nodes.length; i++) {
        var node = nodes[i];

        // if it's a text node, remove it
        if (node.nodeType == Node.TEXT_NODE) {
          node.parentNode.removeChild(node);
          // have to update our incrementor since we just removed a node from childNodes
          i--;
        } else {
          // if it's an element, repeat this process
          if (node.nodeType == Node.ELEMENT_NODE) {
            removeAllText(node);
          }
        }
      }
    }
  },	//end method

  async created() {
    //for test customer class
    console.log("this.$MyNameClass", this.$blogService.getFullName());

    console.log("editblog module, isReadPermitted: ", this.isReadPermitted);

    console.log("editblog module, this.$router: ", this.$router);
    
    const id = parseInt(this.$route.params.id);
    console.log("this.$router parameter id: ", id);
    if(!id){
      return this.$router.push('/notfound')
    }
    
    this.token = window.Laravel.csrfToken;
    //console.log("token: ", this.token);

		const [blog, cat, tag] = await Promise.all([
			this.callApi('get', '/api/blog_single/' + id),
			this.callApi('get', '/api/get_category'),
			this.callApi('get', '/api/get_tags'),
		]);

    console.log("editblog module, blog: ", blog);
    //console.log("editblog module, cat: ", cat);
    //console.log("editblog module, tag: ", tag);

		if (blog.status==200) {
      if (!blog.data) 
        return this.$router.push('/notfound');
      
      this.initData = JSON.parse(blog.data[0].jsonData);
      this.category = cat.data;
      this.tag = tag.data;

      for (let t of blog.data[0].tags) {
          this.data.tag_id.push(t.id);
      }
    
    console.log("editblog module, step1...");

      for (let c of blog.data[0].categories) {
          this.data.category_id.push(c.id);
      }
    
    console.log("editblog module, step2...");

      this.data.title = blog.data[0].title;
      this.data.jsonData = blog.data[0].jsonData;
      this.data.metaDescription = blog.data[0].metaDescription;
      this.data.post_excerpt = blog.data[0].post_excerpt;
    } else {
      this.swr();
    }

  },
  watch: {
    //'$store.state.immediateD': function() {
    //  console.log("watch: refresh role....");
    //  this.fetchItems();
    //},
  },
  
};
</script>

<style scoped>
	.blog_editor {
		width: 717px;
		margin-left: 160px;
		padding: 4px 7px;
		font-size: 14px;
		border: 1px solid #dcdee2;
		border-radius: 4px;
		color: #515a6e;
		background-color: #fff;
		background-image: none;
		z-index:  -1;
	}
	.blog_editor:hover {
		border: 1px solid #57a3f3;
	}
	._input_field{
		margin: 20px 0 20px 160px;
    	width: 717px;
	}
</style>


