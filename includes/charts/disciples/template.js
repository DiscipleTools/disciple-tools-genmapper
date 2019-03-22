const boxHeight = 80
const textHeight = 14
const textMargin = 6
const { __, _x, _n, _nx } = wp.i18n;
const icons = window.genApiTemplate.plugin_uri + 'charts/disciples/icons/';
const template = {
  'name': 'Disciples 0.1',
  'settings': {
    'nodeSize': {
      'width': boxHeight * 1.5,
      'height': boxHeight * 1.8
    }
  },
  'svg': {
    'big-rect': {
      'type': 'rect',
      'attributes': {
        'x': 0,
        'y': 0,
        'width': boxHeight / 2,
        'height': boxHeight,
        'opacity': '0'
      }
    },
    'box': {
      'type': 'rect',
      'attributes': {
        'x': -boxHeight * 0.3,
        'y': 0,
        'width': boxHeight * 0.6,
        'height': boxHeight * 1.05,
        'rx': 10
      }
    }
  },
  'fields': [
    {
      'header': 'id',
      'initial': 0,
      'type': null
    },
    {
      'header': 'parentId',
      'initial': null,
      'type': null
    },
    {
      'header': 'name',
      'label' : __( 'Name', 'disciple_tools' ),
      'initial': '',
      'type': 'text',
      'svg': {
        'type': 'text',
        'attributes': {
          'x': 0,
          'y': -textMargin - textHeight
        }
      }
    },
    {
      'header': 'date',
      'initial': null,
      'label': __( "Baptism Date", 'disciple_tools' ),
      'type': 'text',
      'svg': {
        'type': 'text',
        'attributes': {
          'x': 0,
          'y': -4
        }
      }
    },
    // {
    //   'header': 'believer',
    //   'initial': true,
    //   'type': 'checkbox',
    //   'svg': {
    //     'type': 'image',
    //     'attributes': {
    //       'x': -boxHeight * 0.28,
    //       'y': boxHeight * 0,
    //       'width': boxHeight / 4,
    //       'height': boxHeight / 4,
    //       'xlink:href': icons + 'believer.png'
    //     }
    //   }
    // },
    // {
    //   'header': 'baptized',
    //   'initial': false,
    //   'type': 'checkbox',
    //   'svg': {
    //     'type': 'image',
    //     'attributes': {
    //       'x': boxHeight * 0,
    //       'y': boxHeight * 0,
    //       'width': boxHeight / 4,
    //       'height': boxHeight / 4,
    //       'xlink:href': icons + 'baptism.png'
    //     }
    //   }
    // },
    // {
    //   'header': 'word',
    //   'initial': false,
    //   'type': 'checkbox',
    //   'svg': {
    //     'type': 'image',
    //     'attributes': {
    //       'x': boxHeight * -0.28,
    //       'y': boxHeight * 0.25,
    //       'width': boxHeight / 4,
    //       'height': boxHeight / 4,
    //       'xlink:href': icons + 'word.png'
    //     }
    //   }
    // },
    // {
    //   'header': 'prayer',
    //   'initial': false,
    //   'type': 'checkbox',
    //   'svg': {
    //     'type': 'image',
    //     'attributes': {
    //       'x': boxHeight * 0,
    //       'y': boxHeight * 0.25,
    //       'width': boxHeight / 4,
    //       'height': boxHeight / 4,
    //       'xlink:href': icons + 'prayer.png'
    //     }
    //   }
    // },
    // {
    //   'header': 'field1',
    //   'initial': false,
    //   'type': 'checkbox',
    //   'svg': {
    //     'type': 'image',
    //     'attributes': {
    //       'x': boxHeight * -0.3,
    //       'y': boxHeight * 0.5,
    //       'width': boxHeight / 4,
    //       'height': boxHeight / 4,
    //       'xlink:href': icons + 'field1.png'
    //     }
    //   }
    // },
    // {
    //   'header': 'field2',
    //   'initial': false,
    //   'type': 'checkbox',
    //   'svg': {
    //     'type': 'image',
    //     'attributes': {
    //       'x': boxHeight * 0.05,
    //       'y': boxHeight * 0.5,
    //       'width': boxHeight / 4,
    //       'height': boxHeight / 4,
    //       'xlink:href': icons + 'field2.png'
    //     }
    //   }
    // },
    // {
    //   'header': 'field3',
    //   'initial': false,
    //   'type': 'checkbox',
    //   'svg': {
    //     'type': 'image',
    //     'attributes': {
    //       'x': boxHeight * 0.05,
    //       'y': boxHeight * 0.75,
    //       'width': boxHeight / 4,
    //       'height': boxHeight / 4,
    //       'xlink:href': icons + 'field3.png'
    //     }
    //   }
    // },
    // {
    //   'header': 'field4',
    //   'initial': false,
    //   'type': 'checkbox',
    //   'svg': {
    //     'type': 'image',
    //     'attributes': {
    //       'x': boxHeight * -0.3,
    //       'y': boxHeight * 0.75,
    //       'width': boxHeight / 4,
    //       'height': boxHeight / 4,
    //       'xlink:href': icons + 'field4.png'
    //     }
    //   }
    // },
    // {
    //   'header': 'field5',
    //   'initial': false,
    //   'type': 'checkbox',
    //   'svg': {
    //     'type': 'image',
    //     'attributes': {
    //       'x': boxHeight * -0.1,
    //       'y': boxHeight * 0.65,
    //       'width': boxHeight * 0.2,
    //       'height': boxHeight * 0.2,
    //       'xlink:href': icons + 'field5.png'
    //     }
    //   }
    // },
    // {
    //   'header': 'discipleType',
    //   'initial': 'individual',
    //   'type': 'radio',
    //   'inheritsFrom': 'box',
    //   'values': [
    //     {
    //       'header': 'individual',
    //       'class': 'disciple-individual',
    //       'attributes': {
    //         'rx': 10
    //       }
    //     },
    //     {
    //       'header': 'facilitatesGroup',
    //       'class': 'disciple-facilitates-group',
    //       'attributes': {
    //         'rx': 10
    //       }
    //     },
    //     {
    //       'header': 'facilitatesChurch',
    //       'class': 'disciple-facilitates-church',
    //       'attributes': {
    //         'rx': 0
    //       }
    //     }
    //   ]
    // },
    // {
    //   'header': 'timothy',
    //   'initial': false,
    //   'type': 'checkbox',
    //   'svg': {
    //     'type': 'image',
    //     'attributes': {
    //       'x': boxHeight * -0.3,
    //       'y': -4 - textHeight,
    //       'width': boxHeight * 0.6,
    //       'height': 2,
    //       'xlink:href': icons + 'redline.png'
    //     }
    //   }
    // },
    {
      'header': 'active',
      'label' : __( 'Active', 'disciple_tools' ),
      'initial': true,
      'type': 'checkbox'
      // svg defined currently in genmapper.js
    }
  ]
}
