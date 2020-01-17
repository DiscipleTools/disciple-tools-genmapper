(function() {
  "use strict";
  let localizedObject = window.wpApiGenmapper
  let chartDiv = jQuery('#chart') // retrieves the chart div in the metrics page
  jQuery(document).ready(function() {
    if('#groups' === window.location.hash) {
      show_template_overview()
    }
  })

  function show_template_overview() {

    const windowHeight = document.documentElement.clientHeight
    chartDiv.empty().html(`
      <span class="section-header">Group Generation Tree</span>
      This tree only show First Generation groups that have multiplied
      <div >
        <div class="section-subheader">See descendants of a specific group</div>
        <var id="groups-result-container" class="result-container" style="display: block"></var>
        <div id="groups_t" name="form-groups" class="scrollable-typeahead" style="max-width:300px; display: inline-block">
            <div class="typeahead__container">
                <div class="typeahead__field">
                    <span class="typeahead__query">
                        <input class="js-typeahead-groups input-height"
                               name="groups[query]" placeholder="Search groups"
                               autocomplete="off">
                    </span>
                </div>
            </div>
        </div>
        <button class="button" id="reset_tree" style="margin: 0">Reset</button>
        <div style="display: inline-block" class="loading-spinner active"></div>

      </div>
      <hr style="max-width:100%;">
      <aside id="left-menu">
      </aside>
    
      <section id="intro" class="intro">
        <div id="intro-content"></div>
      </section>
    
      <section id="alert-message" class="alert-message">
      </section>
    
      <section id="edit-group" class="edit-group">
      </section>
    
      <section id="genmapper-graph" style="height:${document.documentElement.clientHeight -200}px">
        <svg id="genmapper-graph-svg" width="100%"></svg>
      </section>     
    `)

    window.genmapper = new window.genMapperClass()
    get_groups()

    /**
     * Groups
     */
    let group_search_input = $('.js-typeahead-groups')
    $.typeahead({
      input: '.js-typeahead-groups',
      minLength: 0,
      accent: true,
      searchOnFocus: true,
      maxItem: 20,
      template: function (query, item) {
        return `<span>${_.escape(item.name)}</span>`
      },
      source: TYPEAHEADS.typeaheadSource('groups', 'dt-posts/v2/groups/compact/'),
      display: "name",
      templateValue: "{{name}}",
      dynamic: true,
      callback: {
        onClick: function(node, a, item, event){
          //genmapper.rebaseOnNodeID( item.ID ) //disabled because of possibility of multiple parents
          get_groups( item.ID )
        },
        onResult: function (node, query, result, resultCount) {
          let text = TYPEAHEADS.typeaheadHelpText(resultCount, query, result)
          $('#groups-result-container').html(text);
        },
        onHideLayout: function () {
          $('#groups-result-container').html("");
        },
        onCancel(node, item, event){
          get_groups()
          event.preventDefault()
        }
      }
    });

    $('#reset_tree').on("click", function () {
      group_search_input.val("")
      get_groups()
    })
  }


  function get_groups( group = null ){
    let loading_spinner = $(".loading-spinner")
    loading_spinner.addClass("active")
    jQuery(document).ready(function() {
      return jQuery.ajax({
        type: "GET",
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        url: `${localizedObject.root}dt/v1/genmapper/groups?node=${group}`,
        beforeSend: function(xhr) {
          xhr.setRequestHeader('X-WP-Nonce', localizedObject.nonce);
        },
      })
        .fail(function (err) {
          console.log("error")
          console.log(err)
          jQuery("#alert-message").append(err.responseText)
        })
        .then(e=>{
          loading_spinner.removeClass("active")
          genmapper.importJSON(e, group === null)
          genmapper.origPosition( true )
        })

    })
  }

  chartDiv.on('rebase-node-requested', function (e, node) {
    get_groups( node.data.id )
  })

  chartDiv.on('add-node-requested', function (e, parent) {
    let loading_spinner = $(".loading-spinner")
    loading_spinner.addClass("active")
    let fields = {
      "title": "New Group",
      "parent_groups": { "values": [ { "value" : parent.data.id } ] },
      "group_type": "group"
    }
    window.API.create_post('groups', fields).then(( newGroup )=>{
      let newNodeData = {}
      newNodeData['id'] = newGroup["ID"]
      newNodeData['parentId'] = parent.data.id
      newNodeData['name'] = fields.title
      genmapper.createNode( newNodeData )
      loading_spinner.removeClass("active")
    })
  })

  chartDiv.on('node-updated', function (e, nodeID, nodeFields, groupFields) {
    let loading_spinner = $(".loading-spinner")
    loading_spinner.addClass("active")
    _.forOwn(nodeFields, (value, key)=>{
      if ( key === "name" ){
        groupFields["title"] = value
      }
      if ( key === "active" ){
        groupFields["group_status"] = value ? "active" : "inactive"
      }
      if ( key === "group_type"){
        groupFields["group_type"] = value
      }
    })
    window.API.update_post( "groups", nodeID, groupFields ).then(resp=>{
      loading_spinner.removeClass("active")
    })
  })


})();
