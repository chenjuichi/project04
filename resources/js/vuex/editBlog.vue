<template>
<v-app>
  <div class="content">
    <div class="container-fluid">

      <!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
      <div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
        <p class="_title0">Update blog</p>
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
            :init-data="content"
            v-if="content"
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
          {{isCreating ? 'Please wait...' : 'Update blog'}}
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
      content: null,
      //{
        //time: 1554508385558,
        //blocks: [
          //{type: "header", data: {text: "Hello Jimmy", level: 2} },        
          //{type: "list",       data: {style: "ordered", items: []}},
          //{type: "quote",      data: {text: "", caption: "", alignment: "left"}},
          //{type: "delimiter",  data: {} },
          //{type: "warning",    data: {title: "", message: ""}},
          //{type: "delimiter",  data: {},}
        //],
        //version: "2.12.3",         
      //},
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
    };
  },
  components: {
     //
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
      console.log('onChange! ', args);
    },

    onInitialized (editor) {
      console.log('onInitialized! ', editor);
    },

    onReady() {
        console.log('onReady!');
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
      const id = parseInt(this.$route.params.id);
      const res = await this.callApi('post', '/api/update_blog' + id);
      
      if (res.status===200) {
        this.s('Blog has been updated successfully!');
        this.clearInputs();
        // redirect...
        this.$router.push("/hello1/hello141");
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
      .then ((data) => {
        // Do what you want with the data here        
        console.log('hi, jimmy, Editor.js is ready to save()!');
        
        this.onSave(data);
      })
      .catch (err => { 
        console.log('Editor.js initialization failed because of ${err}');
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
    //for test customer class
    console.log("this.$MyNameClass", this.$blogService.getFullName());

    console.log("editblog module, isReadPermitted: ", this.isReadPermitted);

    console.log("editblog module, this.$router: ", this.$router);
    
    const id = parseInt(this.$route.params.id);
    console.log("this.$router parameter id: ", id);
    if(!id){
      return this.$router.push('/notfound')
    }

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
      
      let tempJson = JSON.parse(blog.data[0].jsonData);
      console.log("tempJson: ", tempJson);
      this.content = Object.assign({}, tempJson);

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


