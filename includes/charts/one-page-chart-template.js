(function() {
  "use strict";
  let localizedObject = window.wpApiGenmapper

  jQuery(document).ready(function() {
    if('#groups' === window.location.hash) {
      show_template_overview()
    }
  })

  function show_template_overview() {
    let chartDiv = jQuery('#chart') // retrieves the chart div in the metrics page
    chartDiv.empty().html(`
      <span class="section-header">Group Generation Tree</span>

      <div >
        <div class="section-subheader">Groups</div>
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
    
      <section id="genmapper-graph">
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
      source: TYPEAHEADS.typeaheadSource('groups', 'dt/v1/groups/compact/'),
      display: "name",
      templateValue: "{{name}}",
      dynamic: true,
      callback: {
        onClick: function(node, a, item, event){
          get_groups( item.ID)
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
          genmapper.importJSON(e)
          genmapper.origPosition()
        })

    })
  }
  $('.rebaseNode').on("click", function (a, b, c) {
    console.log(a);
    console.log(b);
    console.log(c);
  })



  window.sample_api_call = function sample_api_call( button_data ) {


     // change this object to the one named in ui-menu-and-enqueue.php

    let button = jQuery('#sample_button')

    button.append(localizedObject.spinner)

    let data = { "button_data": button_data };
    return jQuery.ajax({
      type: "POST",
      data: JSON.stringify(data),
      contentType: "application/json; charset=utf-8",
      dataType: "json",
      url: localizedObject.root + 'dt/v1/genmapper/'+localizedObject.name_key+'/sample',
      beforeSend: function(xhr) {
        xhr.setRequestHeader('X-WP-Nonce', localizedObject.nonce);
      },
    })
    .done(function (data) {
      button.empty().append(data)
      console.log( 'success' )
      console.log( data )
    })
    .fail(function (err) {
      console.log("error");
      console.log(err);
    })
  }
})();
