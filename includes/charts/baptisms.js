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

      <div class="grid-x">
        <div class="cell medium-9">
            <span class="section-header">${localizedObject.translation.string1 /*contact Generation Tree*/}</span>
        </div>
        <div class="cell medium-3">
        <var id="contacts-result-container" class="result-container" style="display: block"></var>
            <div class="input-group">
              <div id="contacts_t" name="form-contacts" class="scrollable-typeahead">
                  <div class="typeahead__container">
                      <div class="typeahead__field">
                          <span class="typeahead__query">
                              <input class="js-typeahead-contacts input-height"
                                     name="contacts[query]" placeholder="${localizedObject.translation.string2 /*This tree only show First Generation contacts that have multiplied*/}"
                                     autocomplete="off">
                          </span>
                      </div>
                  </div>
              </div>
              <div class="input-group-button">
                <button type="button" class="button small hollow" style="border:0"><span class="loading-spinner active"></span></button>
              </div>
            </div>
        </div>
        <div class="cell">
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

          <section id="genmapper-graph" style="height:${document.documentElement.clientHeight -250}px">
            <svg id="genmapper-graph-svg" width="100%"></svg>
          </section>
        </div>
      </div>


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
      source: TYPEAHEADS.typeaheadSource('contacts', 'dt-posts/v2/contacts/compact/'),
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
          displayError(err)
          console.log("error")
          console.log(err)
        })
        .then(e=>{
          loading_spinner.removeClass("active")
          genmapper.importJSON(e, nodeId === null)
          genmapper.origPosition( true )
        })

    })
  }
  function displayError (err, msg) {
    window.genmapper.displayAlert()
    if ( err.responseJSON && err.responseJSON.data && err.responseJSON.data.record ){
      let msg = err.responseJSON.message + ` <a target="_blank" href="${err.responseJSON.data.link}">Open Record</a>`
      window.genmapper.displayAlert(msg)
    }
  }

  chartDiv.on('rebase-node-requested', function (e, node) {
    get_records( node.data.id )
  })

  chartDiv.on('add-node-requested', function (e, parent) {
    let loading_spinner = $(".loading-spinner")
    loading_spinner.addClass("active")
    let baptismDate = moment().format("YYYY-MM-DD")
    let fields = {
      "title": localizedObject.translation.string4,
      "baptized_by": { "values": [ { "value" : parent.data.id } ] },
      "milestones": { "values": [ { "value" : 'milestone_baptized' } ] },
      "baptism_date": baptismDate
    }
    window.API.create_post( 'contacts', fields ).then(( newContact )=>{
      let newNodeData = {}
      newNodeData['id'] = newContact["ID"]
      newNodeData['parentId'] = parent.data.id
      newNodeData['name'] = fields.title
      newNodeData["date"] = baptismDate
      genmapper.createNode( newNodeData )
      loading_spinner.removeClass("active")
    })
  })

  $("#chart").on('node-updated', function (e, nodeID, nodeFields, contactFields) {
    let loading_spinner = $(".loading-spinner")
    loading_spinner.addClass("active")
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
    window.API.update_post( "contacts", nodeID, contactFields ).then(resp=>{
      loading_spinner.removeClass("active")
    })
  })


})();
