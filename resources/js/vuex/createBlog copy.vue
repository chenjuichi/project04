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
          <editor 
            ref="editor"
            autofocus
            holder-id="codex-editor"
            save-button-id="save-button"
            @save="save"
            @ready="onReady"
            @change="onChange"
            :config="config"
            :initialized="onInitialized"
            :init-data="content"
                      
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
  //props: ['init-data'],
  /*
  props: {
    initData: {
      type: Object,
      //default: null,
      //required: true
    },    
    //initData: Object},
  },
  */
  //watch: { 
  //  init-data: function(newVal, oldVal) { // watch it
  //        console.log('Prop changed: ', newVal, ' | was: ', oldVal);
  //      },
  //},
  data() {
    return {
      config: {
        //holderId: 
        tools: {
          /*
          header: require('@editorjs/header'),
          list: require('@editorjs/list'),
          */
          
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
        //data: "hello, today",       
      },    //end config:
      content: {}, 
      /*
      {
        "time": 1591362820044,
        "blocks": [
            {
                "type" : "header",
                "data" : {
                    "text" : "Editor.js",
                    "level" : 2
                }
            },
            {
                "type" : "paragraph",
                "data" : {
                    "text" : "Hey. Meet the new Editor. On this page you can see it in action — try to edit this text."
                }
            },
            {
                "type" : "header",
                "data" : {
                    "text" : "Key features",
                    "level" : 3
                }
            },
            {
                "type" : "list",
                "data" : {
                    "style" : "unordered",
                    "items" : [
                        "It is a block-styled editor",
                        "It returns clean data output in JSON",
                        "Designed to be extendable and pluggable with a simple API"
                    ]
                }
            },
            {
                "type" : "header",
                "data" : {
                    "text" : "What does it mean «block-styled editor»",
                    "level" : 3
                }
            },
            {
                "type" : "paragraph",
                "data" : {
                    "text" : "Workspace in classic editors is made of a single contenteditable element, used to create different HTML markups. Editor.js <mark class=\"cdx-marker\">workspace consists of separate Blocks: paragraphs, headings, images, lists, quotes, etc</mark>. Each of them is an independent contenteditable element (or more complex structure) provided by Plugin and united by Editor's Core."
                }
            },
            {
                "type" : "paragraph",
                "data" : {
                    "text" : "There are dozens of <a href=\"https://github.com/editor-js\">ready-to-use Blocks</a> and the <a href=\"https://editorjs.io/creating-a-block-tool\">simple API</a> for creation any Block you need. For example, you can implement Blocks for Tweets, Instagram posts, surveys and polls, CTA-buttons and even games."
                }
            },
            {
                "type" : "header",
                "data" : {
                    "text" : "What does it mean clean data output",
                    "level" : 3
                }
            },
            {
                "type" : "paragraph",
                "data" : {
                    "text" : "Classic WYSIWYG-editors produce raw HTML-markup with both content data and content appearance. On the contrary, Editor.js outputs JSON object with data of each Block. You can see an example below"
                }
            },
            {
                "type" : "paragraph",
                "data" : {
                    "text" : "Given data can be used as you want: render with HTML for <code class=\"inline-code\">Web clients</code>, render natively for <code class=\"inline-code\">mobile apps</code>, create markup for <code class=\"inline-code\">Facebook Instant Articles</code> or <code class=\"inline-code\">Google AMP</code>, generate an <code class=\"inline-code\">audio version</code> and so on."
                }
            },
            {
                "type" : "paragraph",
                "data" : {
                    "text" : "Clean data is useful to sanitize, validate and process on the backend."
                }
            },
            {
                "type" : "delimiter",
                "data" : {}
            },
            {
                "type" : "paragraph",
                "data" : {
                    "text" : "We have been working on this project more than three years. Several large media projects help us to test and debug the Editor, to make it's core more stable. At the same time we significantly improved the API. Now, it can be used to create any plugin for any task. Hope you enjoy. 😏"
                }
            },
            {
                "type" : "image",
                "data" : {
                    "file" : {
                        "url" : "https://codex.so/public/app/img/external/codex2x.png"
                    },
                    "caption" : "",
                    "withBorder" : false,
                    "stretched" : false,
                    "withBackground" : false
                }
            },
        ],
        "version" : "2.18.0"
      },
      */
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
      category : [],
      tag : [],
      isCreating: false, 
      
      content: '',
      editorData: '',

      element: null,
      blocks: [],      
    };
  },
  components: {
     //
  },
  mounted() {
    //this.$refs.editor._data.state.editor.clear();
    //this.$refs.editor._data.state.blocks.clear() 
    //this.element=document.getElementsByTagName("Editor");
    //this.element.p
    //this.element=document.getElementById("vue-editor-j");
    //console.log("this.element: ", this.element);
    //this.$refs.editor.blocks.clear();
    //this.$refs.editor._data.state.blocks.clear();

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
      console.log('onInitialized, editor.data: ', typeof  editor);
      //editor.clear();
      //this.$refs.editor._data.state.clear();
      console.log("clear ok...");
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
        console.log('hi, jimmy, Editor.js is ready to save()!', data);
        //console.log('hi, jimmy, Editor.js is ready to save()!', data.blocks);
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
    console.log("this.$MyNameClass", this.$blogService.getFullName());

    //$('#vue-editor-js').html('');
    console.log("createBlog module, isReadPermitted: ", this.isReadPermitted);

    const [cat, tag] = await Promise.all([
      this.callApi('get', '/api/get_category'), 
      this.callApi('get', '/api/get_tags'),
    ]);

    if (cat.status==200) {
      this.category = cat.data;
    } else {
      this.swr();
    }

    if (tag.status==200) {
      this.tag = tag.data;
    } else {
      this.swr();
    }

    //    for (var i = 0, len = this.category.length; i < len; i++) {
    //        this.roleOptions.push(this.roles[i].name);
    //    }

    //    for (var i = 0, len = this.tag.length; i < len; i++) {
    //        this.permissionOptions.push(this.permissions[i].name);
    //    }
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


