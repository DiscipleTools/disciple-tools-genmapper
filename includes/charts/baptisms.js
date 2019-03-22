(function() {
  "use strict";
  let localizedObject = window.wpApiGenmapper
  let chartDiv = jQuery('#chart')
  jQuery(document).ready(function() {
    if('#baptisms' === window.location.hash) {
      show_template_overview()
    }
  })

  function show_template_overview() {
    let chartDiv = jQuery('#chart') // retrieves the chart div in the metrics page
    const windowHeight = document.documentElement.clientHeight
    chartDiv.empty().html(`
      <span class="section-header">${ __( 'Baptism Generation Tree', 'disciple_tools' ) }</span>
      <!--This tree only show First Generation ba that have multiplied-->
      <div >
        <div class="section-subheader">${ __( 'Search for a specific contact or multiplier', 'disciple_tools' ) }</div>
        <var id="groups-result-container" class="result-container" style="display: block"></var>
        <div id="groups_t" name="form-groups" class="scrollable-typeahead" style="max-width:300px; display: inline-block">
            <div class="typeahead__container">
                <div class="typeahead__field">
                    <span class="typeahead__query">
                        <input class="js-typeahead-contacts input-height"
                               name="groups[query]" placeholder="Search contacts and users"
                               autocomplete="off">
                    </span>
                </div>
            </div>
        </div>
        <button class="button" id="reset_tree" style="margin: 0">${ __( 'Reset', 'disciple_tools' ) }</button>
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
    get_records()

    /**
     * Contacts
     */
    let group_search_input = $('.js-typeahead-contacts')
    $.typeahead({
      input: '.js-typeahead-contacts',
      minLength: 0,
      accent: true,
      searchOnFocus: true,
      maxItem: 20,
      template: function (query, item) {
        return `<span>${_.escape(item.name)}</span>`
      },
      source: TYPEAHEADS.typeaheadSource('contacts', 'dt/v1/contacts/compact/'),
      display: "name",
      templateValue: "{{name}}",
      dynamic: true,
      callback: {
        onClick: function(node, a, item, event){
          // genmapper.rebaseOnNodeID( item.ID ) //disabled because of possibility of multiple parents
          get_records( item.ID)
        },
        onResult: function (node, query, result, resultCount) {
          let text = TYPEAHEADS.typeaheadHelpText(resultCount, query, result)
          $('#groups-result-container').html(text);
        },
        onHideLayout: function () {
          $('#groups-result-container').html("");
        },
        onCancel(node, item, event){
          get_records()
          event.preventDefault()
        }
      }
    });

    $('#reset_tree').on("click", function () {
      group_search_input.val("")
      genmapper.origData()
    })
  }


  function get_records( nodeId = null ){
    let loading_spinner = $(".loading-spinner")
    loading_spinner.addClass("active")
    jQuery(document).ready(function() {
      return jQuery.ajax({
        type: "GET",
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        url: `${localizedObject.root}dt/v1/genmapper/baptisms?node=${nodeId}`,
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
          genmapper.importJSON(e, nodeId === null)
          genmapper.origPosition( true )
        })

    })
  }

  chartDiv.on('rebase-node-requested', function (e, node) {
    get_records( node.data.id )
  })

  chartDiv.on('add-node-requested', function (e, parent) {
    let fields = {
      "title": "New Contact",
      "baptized_by": { "values": [ { "value" : parent.data.id } ] },
      "milestones": { "values": [ { "value" : 'milestone_baptized' } ] },
      "baptism_date": new Date()
    }
    window.API.create_contact(fields).then(( newcontact )=>{
      let newNodeData = {}
      newNodeData['id'] = newcontact["post_id"]
      newNodeData['parentId'] = parent.data.id
      newNodeData['name'] = fields.title
      genmapper.createNode( newNodeData )
    })
  })

  $("#chart").on('node-updated', function (e, nodeID, nodeFields, contactFields) {
    _.forOwn(nodeFields, (value, key)=>{
      if ( key === "name" ){
        contactFields["title"] = value
      }
      if ( key === "active" ){
        if ( value ){
          contactFields["overall_status"] = "active"
        } else {
          contactFields["overall_status"] = "closed"
          // @todo implement genmapper reason
          contactFields["reason_closed"] = "close_from_genmapper"
        }
      }
      if ( key === "date" ){
        contactFields["baptism_date"] = value
      }
    })
    window.API.save_field_api( "contact", nodeID, contactFields )
  })


})();
