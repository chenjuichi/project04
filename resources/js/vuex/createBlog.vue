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

export default {

data() {
  return {
    config: {},
    content: null,
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
    
    editorData: '',
  };
},
methods: {
  clearInputs(event) {
    this.data.title = '';
    this.data.post = '';
    this.data.post_excerpt = '';
    this.data.metaDescription = '';
    this.data.category_id = [];
    this.data.tag_id = [];
    this.data.jsonData = null;
    
    this.content = null;
  },    

  onChange(args) {
    console.log('hi, Editor.js is onChange!', args);
    //args.saver.onSave();
  },

  onInitialized (editor) {
    console.log('Hi, Editor.js is onInitialized!', editor);
  },

  onReady() {
      console.log('hi, Editor.js is onReady!');
  },

  async onSave(response) {
    var data = response;
    await this.outputHtml(data.blocks);

    console.log("1.response on save, this.articleHTML: ", this.articleHTML);
    console.log("2.response on save, data: ", data);      

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
    const res = await this.callApi('post', '/api/create_blog', this.data);
    
    if (res.status===200) {
      this.s('Blog has been created successfully!');
      this.clearInputs();
      // redirect...
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

  save() {
    this.$refs.editor._data.state.editor.save() 
    .then((data) => {
      // Do what you want with the data here        
      console.log('hi, jimmy, Editor.js is ready to save()!', data);
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
},	//end method

async created() {
  console.log("this.$MyNameClass", this.$blogService.getFullName());

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


