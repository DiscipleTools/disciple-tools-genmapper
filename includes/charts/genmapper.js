// const { __, _x, _n, _nx } = wp.i18n;
class GenMapper {
  // GenMapper
  // App for mapping generations of simple churches
  // https://github.com/dvopalecky/gen-mapper
  // Copyright (c) 2016-2018 Daniel Vopalecky, MIT license

  /* global d3, XLSX, saveAs, FileReader, template, translations, _, Blob, boxHeight */

  constructor () {
    this.plugin_uri = window.wpApiGenmapper.plugin_uri
    this.appVersion = '0.2.16'

    this.language = 'en'

    this.margin = {top: 50, right: 30, bottom: 50, left: 30}
    this.projectName = "Untitled project"

    this.updateDOMafterLangSwitch()

    this.zoom = d3.zoom()
      .scaleExtent([0.15, 2])
      .on('zoom', function zoomed () {
        d3.select('g').attr('transform', d3.event.transform)
      })

    this.setSvgHeight()
    this.svg = d3.select('#genmapper-graph-svg')
      .call(this.zoom)
      .on('dblclick.zoom', null)
    this.g = this.svg.append('g')
      .attr('id', 'maingroup')
    this.gLinks = this.g.append('g')
      .attr('class', 'group-links')
    this.gLinksText = this.g.append('g')
      .attr('class', 'group-links-text')
    this.gNodes = this.g.append('g')
      .attr('class', 'group-nodes')

    this.csvHeader = template.fields.map(field => field.header).join(',') + '\n'
    this.initialCsv = this.csvHeader + template.fields.map(field => this.getInitialValue(field)).join(',')
    this.data = this.parseCsvData(this.initialCsv)
    this.nodes

    this.origPosition()
    this.redraw(template)

    this.alertElement = document.getElementById('alert-message')
    this.editGroupElement = document.getElementById('edit-group')

    this.setKeyboardShorcuts()

    document.getElementsByTagName('body')[0].onresize = this.setSvgHeight
  }

  // Beginning of function definitions
  setKeyboardShorcuts () {
    document.addEventListener('keyup', (e) => {
      if (e.keyCode === 27) {
        if (this.alertElement.classList.contains('alert-message--active')) {
          this.alertElement.classList.remove('alert-message--active')
        } else {
          document.getElementById('intro').classList.remove('intro--active')
          this.editGroupElement.classList.remove('edit-group--active')
        }
      } else if (e.keyCode === 13) {
        // hitting enter is like submitting changes in the edit window
        if (this.editGroupElement.classList.contains('edit-group--active')) {
          document.getElementById('edit-submit').click()
        }
      }
    })
  }

  setSvgHeight () {
    const windowHeight = document.documentElement.clientHeight
    const leftMenuHeight = document.getElementById('left-menu').clientHeight
    const height = Math.max(windowHeight, leftMenuHeight + 10)
    d3.select('#genmapper-graph-svg')
      .attr('height', height)
  }

  loadHTMLContent () {
    document.getElementById('left-menu').innerHTML = `<div id="template-logo">
    <button onclick="genmapper.introSwitchVisibility()" class="hint--rounded hint--right" aria-label="${ __( 'Help / About', 'disciple_tools' ) }">
     <img src="${this.plugin_uri}/charts/icons/266-question.svg">
     </button>
    <button onclick="genmapper.origData();" class="hint--rounded hint--right" aria-label="${ __( 'Original Zoom &amp; Position', 'disciple_tools' ) }"><img src="${this.plugin_uri}/charts/icons/refresh.svg"></i></button>
    <button onclick="genmapper.zoomIn();" class="hint--rounded hint--right" aria-label="${ __( 'Zoom In', 'disciple_tools' ) }"><img src="${this.plugin_uri}/charts/icons/136-zoom-in.svg"></i></button>
    <button onclick="genmapper.zoomOut();" class="hint--rounded hint--right" aria-label="${ __( 'Zoom Out', 'disciple_tools' ) }"><img src="${this.plugin_uri}/charts/icons/137-zoom-out.svg"></i></button>
  `

    document.getElementById('edit-group').innerHTML = `<div id="edit-group-content">
     <h1> ${ __( 'Edit Record', 'disciple_tools' ) }</h1>
     <form>
       <table>
         <tr>
           <td class="left-field"> ${ __( 'Parent', 'disciple_tools' ) }  </td>
           <td class="right-field"><p id="edit-parent"></p></td>
         </tr>
       </table>
     </form>
     <div id="edit-buttons">
       <button id="edit-submit"> ${ __( 'Save Changes', 'disciple_tools' ) }  </button>
       <button id="edit-cancel"> ${ __( 'Cancel', 'disciple_tools' ) }  </button>
       <button id="open-record"> ${ __( 'Open Record', 'disciple_tools' ) }  </button>
       <button id="rebase-node"> ${ __( 'Center on this node', 'disciple_tools' ) }  </button>
     </div>
    </div>`

    document.getElementById('intro-content').innerHTML = `<h2>
    ${ __( 'GenMapper Help', 'disciple_tools' ) }
    </h2>
    <p>${ __( "Hello, this app should serve for mapping generations of simple churches. I pray it serves you to advance Jesus' kingdom.", 'disciple_tools' ) }</p>
    ${ __( 'Legend', 'disciple_tools' ) }
    <h3>${ __( 'Panning / Zooming', 'disciple_tools' ) }</h3>
    <p>${ __( 'You can pan by draging the map and zoom by mouse wheel or using buttons on the left.', 'disciple_tools' ) }</p>
    <h3>${ __( 'Credits', 'disciple_tools' ) }</h3>
    <p>${ __( 'Thanks to Curtis Sergeant for the idea of generational mapping and for providing useful feedback.', 'disciple_tools' ) }<br>
    ${ __( 'JavaScript/CSS libraries used', 'disciple_tools' ) }
    : <a href="https://github.com/chinchang/hint.css/">hint.css</a>, <a href="https://d3js.org">d3.js</a>, 
    <a href="https://github.com/eligrey/FileSaver.js/">FileSaver.js</a>, <a href="https://github.com/SheetJS/js-xlsx">js-xlsx</a>, 
    <a href="https://lodash.com">lodash</a> 
    ${ __( 'Icons used', 'disciple_tools' ) }
    : <a href="https://github.com/Keyamoon/IcoMoon-Free">IcoMoon-Free</a><br><br>
    ${ __( 'Copyright (c) 2016 - 2018 Daniel Vopalecky', 'disciple_tools' ) }<br>
    ${ __( 'Licensed with MIT Licence', 'disciple_tools' ) }<br>
    <a href="https://github.com/dvopalecky/gen-mapper">${ __( 'Github repository', 'disciple_tools' ) }</a><br>
    <br></p>
    <button onclick="genmapper.introSwitchVisibility()">${ __( 'Close', 'disciple_tools' ) }</button>`

    document.getElementById('alert-message').innerHTML =
    `<div id="alert-message-content">
      <p id="alert-message-text"></p>
      <button onclick="genmapper.closeAlert()">${ __( 'OK', 'disciple_tools' ) }</button>
    </div>`

    // document.getElementById('gen-mapper-version').innerHTML = this.appVersion
    // document.getElementById('template-version').innerHTML = template.name
  }

  getInitialValue (field) {
      return field.initial
  }

  zoomIn () {
    this.zoom.scaleBy(this.svg, 1.2)
  }

  zoomOut () {
    this.zoom.scaleBy(this.svg, 1 / 1.2)
  }

  origData(){
    this.data = this.masterData
    this.redraw(template)
    this.origPosition(true)
  }

  origPosition ( atRoot = false) {
    this.zoom.scaleTo(this.svg, 1)
    const origX = this.margin.left + (document.getElementById('genmapper-graph').clientWidth / 2)
    const origY = this.margin.top - ( atRoot ? 150 : 0 )
    const parsedTransform = this.parseTransform(this.g.attr('transform'))
    this.zoom.translateBy(this.svg, origX - parsedTransform.translate[0], origY - parsedTransform.translate[1])
  }

  onLoad (fileInputElementId) {
    const fileInput = document.getElementById(fileInputElementId)
    fileInput.value = ''
    fileInput.click()
  }

  displayAlert (message) {
    this.alertElement.classList.add('alert-message--active')
    document.getElementById('alert-message-text').innerHTML = message
  }

  closeAlert () {
    this.alertElement.classList.remove('alert-message--active')
    document.getElementById('alert-message-text').innerHTML = null
  }

  introSwitchVisibility () {
    document.getElementById('intro').classList.toggle('intro--active')
  }

  popupEditGroupModal (d) {
    this.editGroupElement.classList.add('edit-group--active')
    template.fields.forEach((field) => {
      if (field.type === 'text') {
        this.editFieldElements[field.header].value = d.data[field.header] || ""
      } else if (field.type === 'radio') {
        field.values.forEach((value) => {
          const status = (value.header === d.data[field.header])
          this.editFieldElements[field.header + '-' + value.header].checked = status
        })
      } else if (field.type === 'checkbox') {
        this.editFieldElements[field.header].checked = d.data[field.header]
      }
    })
    // select first element
    this.editFieldElements[Object.keys(this.editFieldElements)[0]].select()

    this.editParentElement.innerHTML = d.parent ? d.parent.data.name : 'N/A'
    const groupData = d.data
    const group = d
    d3.select('#edit-submit').on('click', () => { this.editGroup(groupData) })
    d3.select('#edit-cancel').on('click', () => { this.editGroupElement.classList.remove('edit-group--active') })
    d3.select('#open-record').on('click', () => { this.openRecord( group ) })
    d3.select('#rebase-node').on('click', () => { this.rebaseOnNode( group );this.editGroupElement.classList.remove('edit-group--active') })
  }

  editGroup (groupData) {
    let groupFields = {};
    template.fields.forEach((field) => {
      if (field.type === 'text') {
        groupData[field.header] = this.editFieldElements[field.header].value
        if ( field.header === "name" ){
          groupFields["title"] = this.editFieldElements[field.header].value
        }
      } else if (field.type === 'radio') {
        field.values.forEach((value) => {
          if (this.editFieldElements[field.header + '-' + value.header].checked) {
            groupData[field.header] = value.header
          }
        })
      } else if (field.type === 'checkbox') {
        groupData[field.header] = this.editFieldElements[field.header].checked
      }
    })
    jQuery('#chart').trigger("node-updated", [groupData.id, groupData, groupFields])

    this.editGroupElement.classList.remove('edit-group--active')
    this.redraw(template)
  }

  openRecord( d ){
    let id = d.data.id
    var win = window.open( `${wpApiShare.site_url}/contacts/${id}/`, '_blank');
    win.focus();
  }

  redraw (template) {
    // declares a tree layout and assigns the size
    const tree = d3.tree()
        .nodeSize([template.settings.nodeSize.width,
          template.settings.nodeSize.height])
        .separation(function separation (a, b) {
          return a.parent === b.parent ? 1 : 1.2
        })

    const stratifiedData = d3.stratify()(this.data)
    this.nodes = tree(stratifiedData)
    // update the links between the nodes
    const link = this.gLinks.selectAll('.link')
          .data(this.nodes.descendants().slice(1))

    link.exit()
        .remove()

    link.enter()
        .append('path')
      .merge(link)
          .attr('class', function (d) {
            return (d.parent.id == 0)? 'link-dummy' : 'link'   // #customtft dummy root node
          })
          .attr('d', function (d) {
            return 'M' + d.x + ',' + d.y +
               'C' + d.x + ',' + (d.y + (d.parent.y + boxHeight)) / 2 +
               ' ' + d.parent.x + ',' + (d.y + (d.parent.y + boxHeight)) / 2 +
               ' ' + d.parent.x + ',' + (d.parent.y + boxHeight)
          })

    // update the link text between the nodes
    const LINK_TEXT_POSITION = 0.3 // 1 -> parent, 0 -> child
    const linkText = this.gLinksText.selectAll('.link-text')
          .data(this.nodes.descendants().slice(1))
    linkText.exit()
        .remove()
    linkText.enter()
        .append('text')
      .merge(linkText)
        .attr('class', function (d) {
          return 'link-text ' + (d.data.active ? ' link-text--active' : ' link-text--inactive')
        })
        .attr('x', function (d) { return d.x * (1 - LINK_TEXT_POSITION) + d.parent.x * LINK_TEXT_POSITION })
        .attr('y', function (d) { return d.y * (1 - LINK_TEXT_POSITION) + (d.parent.y + boxHeight) * LINK_TEXT_POSITION })
        .text(function (d) { return d.data.coach })

    // update nodes
    const node = this.gNodes.selectAll('.node')
          .data(this.nodes.descendants())

    node.exit()
        .remove()

    // NEW ELEMENTS
    const newGroup = node.enter()
      .append('g')

    this.appendRebaseButton(newGroup)
    this.appendAddButton(newGroup)

    // append SVG elements without fields
    Object.keys(template.svg).forEach((svgElement) => {
      const svgElementValue = template.svg[svgElement]
      const element = newGroup.append(svgElementValue['type'])
      element.attr('class', 'node-' + svgElement)
    })

    // append SVG elements related to fields
    template.fields.forEach((field) => {
      if (field.svg) {
        const element = newGroup.append(field.svg['type'])
        element.attr('class', 'node-' + field.header)
        Object.keys(field.svg.attributes).forEach((attribute) => {
          element.attr(attribute, field.svg.attributes[attribute])
        })
        if (field.svg.style) {
          Object.keys(field.svg.style).forEach((styleKey) => {
            element.style(styleKey, field.svg.style[styleKey])
          })
        }
      }
    })

    // UPDATE including NEW
    const nodeWithNew = node.merge(newGroup)
    nodeWithNew.attr('class', function (d) {
      return 'node' + ((d.data.id === 0) ? ' node--dummyroot' : (d.data.active ? ' node--active' : ' node--inactive'))
    })
      .attr('transform', function (d) {
        return 'translate(' + d.x + ',' + d.y + ')'
      })
      .on('click', (d) => {
        this.popupEditGroupModal(d) }
      )

    nodeWithNew.select('.removeNode')
        .on('click', (d) => {
          console.log("removeNode");
          this.removeNode(d); d3.event.stopPropagation() })
    nodeWithNew.select('.rebaseNode')
        .on('click', (d) => {
          console.log("rebaseNode");
          jQuery('#chart').trigger("rebase-node-requested", [d])
          // this.rebaseOnNode(d);
          d3.event.stopPropagation()
        })

    nodeWithNew.select('.addNode')
        .on('click', (d) => { this.addNode(d); d3.event.stopPropagation() })

    // refresh class and attributes in SVG elements without fields
    // in order to remove any additional classes or settings from inherited fields
    Object.keys(template.svg).forEach((svgElement) => {
      const svgElementValue = template.svg[svgElement]
      const element = nodeWithNew.select('.node-' + svgElement)
        .attr('class', 'node-' + svgElement)
      Object.keys(svgElementValue.attributes).forEach((attribute) => {
        element.attr(attribute, svgElementValue.attributes[attribute])
      })
    })

    // update node elements which have SVG in template
    template.fields.forEach((field) => {
      if (field.svg) {
        const element = nodeWithNew.select('.node-' + field.header)
        this.updateSvgForFields(field, element)
      }
      if (field.inheritsFrom) {
        const element = nodeWithNew.select('.node-' + field.inheritsFrom)
        this.updateFieldWithInherit(field, element)
      }
    })
  }

  updateFieldWithInherit (field, element) {
    if (!element.empty()) {
      if (field.type === 'checkbox') this.updateCheckboxField(field, element)
      if (field.type === 'radio') this.updateRadioField(field, element)
    }
  }

  updateCheckboxField (field, element) {
    // add class to the element which the field inherits from
    if (field.class) {
      element.attr('class', function (d) {
        const checked = d.data[field.header]
        const class_ = checked ? field.class.checkedTrue : field.class.checkedFalse
        return this.classList.value + ' ' + class_
      })
    }
    if (typeof field.attributes !== 'undefined' &&
        typeof field.attributes.rx !== 'undefined') {
      element.attr('rx', function (d) {
        const checked = d.data[field.header]
        const rxObj = field.attributes.rx
        const rx = checked ? rxObj.checkedTrue : rxObj.checkedFalse
        return String(rx)
      })
    }
  }

  updateRadioField (field, element) {
    // add class to the element which the field inherits from
    element.attr('class', function (d) {
      const fieldValue = GenMapper.getFieldValueForRadioType(field, d)
      if (fieldValue.class) {
        return this.classList.value + ' ' + fieldValue.class
      } else {
        return this.classList.value
      }
    })
    element.attr('rx', function (d) {
      const fieldValue = GenMapper.getFieldValueForRadioType(field, d)
      if (typeof fieldValue.attributes !== 'undefined' &&
          typeof fieldValue.attributes.rx !== 'undefined') {
        return String(fieldValue.attributes.rx)
      } else {
        return this.rx.baseVal.valueAsString
      }
    })
  }

  static getFieldValueForRadioType (field, d) {
    let fieldValue = _.filter(field.values, {header: d.data[field.header]})[0]
    if (typeof fieldValue === 'undefined') {
      fieldValue = _.filter(field.values, {header: field.initial})[0]
    }
    return fieldValue
  }

  updateSvgForFields (field, element) {
    element.text(function (d) { return d.data[field.header] })
    if (field.svg.type === 'image') {
      element.style('display', function (d) { return d.data[field.header] ? 'block' : 'none' })
    }
  }

  appendRemoveButton (group) {
    group.append('g')
      .attr('class', 'removeNode')
      .append('svg')
      .html(
        '<rect x="40" y="0" rx="7" width="25" height="40">' +
          '<title>' + __( 'Delete group &amp; subtree', 'disciple_tools' ) + '</title>' +
        '</rect>' +
        '<line x1="46" y1="13.5" x2="59" y2="26.5" stroke="white" stroke-width="3"></line>' +
        '<line x1="59" y1="13.5" x2="46" y2="26.5" stroke="white" stroke-width="3"></line>'
      )
  }

  appendRebaseButton (group) {
    group.append('g')
      .attr('class', 'rebaseNode')
      .append('svg')
      .html(`
        <rect x="40" y="0" rx="7" width="25" height="40">
          <title>${ __( 'Rebase', 'disciple_tools' ) }</title>
        </rect>
        <line x1="46" y1="13.5" x2="46" y2="26.5" stroke="white" stroke-width="3"></line>
        <line x1="59" y1="13.5" x2="46" y2="13.5" stroke="white" stroke-width="3"></line>
        <line x1="59" y1="26.5" x2="59" y2="13.5" stroke="white" stroke-width="3"></line>
        <line x1="59" y1="26.5" x2="46" y2="26.5" stroke="white" stroke-width="3"></line>
      `)
  }

  appendAddButton (group) {
    group.append('g')
      .attr('class', 'addNode')
      .append('svg')
      .html(`
        <rect x="40" y="40" rx="7" width="25" height="40">
          <title> ${ __( 'Add child', 'disciple_tools' ) }</title>
        </rect>
        <line x1="45" y1="60" x2="60" y2="60" stroke="white" stroke-width="3"></line>
        <line x1="52.5" y1="52.5" x2="52.5" y2="67.5" stroke="white" stroke-width="3"></line>
      `)
  }

  addNode (d) {
    jQuery('#chart').trigger("add-node-requested", [d])
  }
  /* required: id, parentId, name
   *
   */
  createNode( newNode ){
    template.fields.forEach((field) => {
      if ( !newNode[field.header] ){
        newNode[field.header] = this.getInitialValue(field)
      }
    })
    this.data.push(newNode)
    this.redraw(template)
    let node = this.findNodeById( newNode.id )
    if ( node ){
      this.popupEditGroupModal( node )
    }
  }

  findNodeById( id ){
    let nodes = this.nodes.descendants().filter( x=>x.data.id === id )
    if ( nodes.length === 1 ){
      return nodes[0];
    } else {
      return false
    }
  }

  findNewId () {
    const ids = _.map(this.data, function (row) { return row.id })
    return this.findNewIdFromArray(ids)
  }

  /*
   * Find smallest int >= 0 not in array
   */
  findNewIdFromArray (arr) {
    // copy and sort
    arr = arr.slice().sort(function (a, b) { return a - b })
    let tmp = 0
    for (let i = 0; i < arr.length; i++) {
      if (arr[i] >= 0) { // ids must be >= 0
        if (arr[i] === tmp) {
          tmp += 1
        } else {
          break
        }
      }
    }
    return tmp
  }

  rebaseOnNode(d){
    const tmpData = _.cloneDeep(d.descendants().map(x => x.data))
    tmpData[0].parentId = ''
    this.data = tmpData
    this.redraw(template)
    this.origPosition()
  }

  rebaseOnNodeID(id){
    this.data = this.masterData
    this.redraw(template)
    let node = this.findNodeById( id );
    if ( node ){
      this.rebaseOnNode( node );
    }
  }

  removeNode (d) {
    console.log("remove");
    // if (!d.parent) {
    //   this.displayAlert(i18next.t('messages.errDeleteRoot'))
    // } else {
    //   let confirmMessage
    //   if (!d.children) {
    //     confirmMessage = i18next.t('messages.confirmDeleteGroup', {groupName: d.data.name})
    //   } else {
    //     confirmMessage = i18next.t('messages.confirmDeleteGroupWithChildren', {groupName: d.data.name})
    //   }
    //   if (window.confirm(confirmMessage)) {
    //     this.deleteAllDescendants(d)
    //     const nodeToDelete = _.filter(this.data, {id: d.data.id})
    //     if (nodeToDelete) {
    //       this.data = _.without(this.data, nodeToDelete[0])
    //     }
    //   }
    // }
    // this.editGroupElement.classList.remove('edit-group--active')
    // this.redraw(template)
  }

  parseCsvData (csvData) {
    return d3.csvParse(csvData, function (d) {
      const parsedId = parseInt(d.id)
      if (parsedId < 0 || isNaN(parsedId)) { throw new Error('Group id must be integer >= 0.') }
      const parsedLine = {}
      parsedLine['id'] = parsedId
      parsedLine['parentId'] = d.parentId !== '' ? parseInt(d.parentId) : ''
      template.fields.forEach((field) => {
        if (field.type === 'checkbox') {
          const fieldValue = d[field.header].toUpperCase()
          parsedLine[field.header] = !!['TRUE', '1'].includes(fieldValue)
        } else if (field.type) {
          parsedLine[field.header] = d[field.header]
        }
      })
      return parsedLine
    })
  }

  parseTransform (a) {
    const b = {}
    for (let i in a = a.match(/(\w+\((-?\d+.?\d*e?-?\d*,?)+\))+/g)) {
      const c = a[i].match(/[\w.-]+/g)
      b[c.shift()] = c
    }
    return b
  }

  importJSON (jsonString, initial = false) {
    let tree = {}
    if ( typeof jsonString === "string" ){
      tree = JSON.parse(jsonString)
    } else {
      tree = jsonString
    }
    try {
      this.validTree(tree)
    } catch (err) {
      this.displayImportError(err)
      return
    }
    if ( initial ){
      this.masterData = tree
    }
    this.data = tree
    this.redraw(template)
  }

  /**
   * Checks if parsedCsv creates a valid tree.
   * If not, raises error
   */
  validTree (parsedCsv) {
    const treeTest = d3.tree()
    const stratifiedDataTest = d3.stratify()(parsedCsv)
    treeTest(stratifiedDataTest)
  }

  displayImportError (err) {
    if (err.toString().includes('>= 0.') || err.toString().includes('Wrong type')) {
      this.displayAlert(` ${ __( 'Error when importing file.', 'disciple_tools' ) }  <br>${err.toString()}`)
    } else {
      this.displayAlert(` ${ __( 'Error when importing file.', 'disciple_tools' ) }<br><br> ${ __( 'Please check that the file is in correct format (comma separated values), that the root group has no parent, and that all other relationships make a valid tree.<br>Also check that you use the correct version of the App.', 'disciple_tools' ) } `)
    }
  }

  deleteAllDescendants (d) {
    let idsToDelete = _.map(d.children, function (row) { return parseInt(row.id) })
    while (idsToDelete.length > 0) {
      const currentId = idsToDelete.pop()
      const childrenIdsToDelete = _.map(_.filter(this.data, {parentId: currentId}),
        function (row) { return row.id })
      idsToDelete = idsToDelete.concat(childrenIdsToDelete)
      const nodeToDelete = _.filter(this.data, {id: currentId})
      if (nodeToDelete) { this.data = _.without(this.data, nodeToDelete[0]) }
    }
  }

  addFieldsToEditWindow (template) {
    template.fields.forEach((field) => {
      if (field.type) {
        // add table row
        const tr = d3.select('#edit-group-content')
          .select('form')
          .select('table')
          .append('tr')
        // add left column
        const fieldDesciption = field.label + ':'
        tr.append('td')
          .text(fieldDesciption)
          .attr('class', 'left-field')
        // add right column
        const td = tr.append('td')
          .attr('class', 'right-field')
        if (field.type === 'radio') {
          for (let value of field.values) {
            const valueDescription = value.label
            td.append('input')
              .attr('type', field.type)
              .attr('name', field.header)
              .attr('value', value.header)
              .attr('id', 'edit-' + field.header + '-' + value.header)
            td.append('span')
              .html(valueDescription)
            td.append('br')
          }
        } else {
          td.append('input')
            .attr('type', field.type)
            .attr('name', field.header)
            .attr('id', 'edit-' + field.header)
        }
      }
    })
  }

  updateDOMafterLangSwitch () {
    this.loadHTMLContent()
    this.addFieldsToEditWindow(template)
    // document.getElementById('lang-' + this.language).className = 'current-lang'
    // d3.select('#project-name')
    //   .attr('aria-label', i18next.t('messages.editProjectName') + ': ' + this.projectName)
    //   .on('click', () => {
    //     let userInput = window.prompt(i18next.t('messages.editProjectName'), this.projectName)
    //     if (userInput === null) { return }
    //     userInput = userInput.trim()
    //     if (userInput === '') { this.displayAlert(i18next.t('messages.errProjectNameEmpty')) } else {
    //       this.projectName = userInput
    //       d3.select('#project-name')
    //         .attr('aria-label', i18next.t('messages.editProjectName') + ': ' + this.projectName)
    //     }
    //   })
    this.editFieldElements = {}
    template.fields.forEach((field) => {
      if (field.type === 'radio') {
        field.values.forEach((value) => {
          this.editFieldElements[field.header + '-' + value.header] =
            document.getElementById('edit-' + field.header + '-' + value.header)
        })
      } else if (field.type) {
        this.editFieldElements[field.header] = document.getElementById('edit-' + field.header)
      }
    })
    this.editParentElement = document.getElementById('edit-parent')
  }
}

window.genMapperClass = GenMapper
