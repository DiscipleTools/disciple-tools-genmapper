const boxHeight = 80
const textHeight = 14
const textMargin = 6
const healthIconHeight = boxHeight / 5
const healthIconGutter = boxHeight / 6
const healthIconSpacing = healthIconHeight / 2

const icons = window.genApiTemplate.plugin_uri + 'charts/church-circles/icons/';
let group_types = window.genApiTemplate.group_fields.group_type.default
const template = {
  'name': 'Church circles 0.6',
  'settings': {
    'nodeSize': {
      'width': boxHeight * 1.4,
      'height': boxHeight * 2.1
    }
  },
  'svg': {
    'big-rect': {
      // Rect with opacity 0, so that one could hover over all the square even
      // if the visible shape is circle
      'type': 'rect',
      'attributes': {
        'x': -boxHeight / 2,
        'y': 0,
        'width': boxHeight,
        'height': boxHeight,
        'opacity': '0'
      }
    },
    'attenders-image': {
       'type': 'image',
       'attributes': {
         'x': -boxHeight * 0.5,
         'y': -2.5 * textHeight,
         'width': boxHeight / 4,
         'height': boxHeight / 4,
         'href': icons + 'attenders.png'
       }
     },
     'believers-image': {
       'type': 'image',
       'attributes': {
         'x': -boxHeight * 0.25,
         'y': -2.5 * textHeight,
         'width': boxHeight / 4,
         'height': boxHeight / 4,
         'href': icons + 'believers.png'
       },
    },
    'baptized-image': {
      'type': 'image',
      'attributes': {
        'x': boxHeight * 0.1,
        'y': -2.5 * textHeight,
        'width': boxHeight / 4,
        'height': boxHeight / 4,
        'href': icons + 'element-baptism.png'
      }
    },
    'church-box': {
      'type': 'rect',
      'attributes': {
        'x': -boxHeight / 2,
        'y': 0,
        'rx': 0.5 * boxHeight,
        'width': boxHeight,
        'height': boxHeight
      }
    },
    'health-fellowship-image': {
      'type': 'image',
      'attributes': {
        'x': -healthIconHeight / 2,
        'y': healthIconSpacing,
        'width': healthIconHeight,
        'height': healthIconHeight,
        'href': icons + 'element-love.png',
        'class': 'health-image health-image--fellowship'
      }
    },
    'health-communion-image': {
      'type': 'image',
      'attributes': {
        'x': boxHeight / 2 - healthIconHeight - healthIconGutter,
        'y': healthIconGutter,
        'width': healthIconHeight,
        'height': healthIconHeight,
        'href': icons + 'health-communion.svg',
        'class': 'health-image health-image--communion'
      }
    },
    'health-leaders-image': {
      'type': 'image',
      'attributes': {
        'x': boxHeight / 2 - healthIconGutter - healthIconSpacing,
        'y':  healthIconHeight + healthIconSpacing * 2,
        'width': healthIconHeight,
        'height': healthIconHeight,
        'href': icons + 'health-leadership.svg',
        'class': 'health-image health-image--leaders'

      }
    },
    'health-sharing-image': {
      'type': 'image',
      'attributes': {
        'x': boxHeight / 2 - healthIconHeight - healthIconGutter,
        'y':  healthIconHeight * 2 + healthIconSpacing * 2,
        'width': healthIconHeight,
        'height': healthIconHeight,
        'href': icons + 'health-evangelism.svg',
        'class': 'health-image health-image--sharing'
      }
    },
    'health-praise-image': {
      'type': 'image',
      'attributes': {
        'x': -healthIconHeight / 2,
        'y':  healthIconHeight * 2 + healthIconSpacing * 3,
        'width': healthIconHeight,
        'height': healthIconHeight,
        'href': icons + 'health-praise.svg',
        'class': 'health-image health-image--praise'
      }
    },
    'health-bible-image': {
      'type': 'image',
      'attributes': {
        'x': -boxHeight / 2 + healthIconGutter,
        'y': healthIconHeight * 2 + healthIconSpacing * 2,
        'width': healthIconHeight,
        'height': healthIconHeight,
        'href': icons + 'health-word.svg',
        'class': 'health-image health-image--bible'
      }
    },
    'health-baptism-image': {
      'type': 'image',
      'attributes': {
        'x': -boxHeight / 2 + healthIconSpacing,
        'y':  healthIconHeight + healthIconSpacing * 2,
        'width': healthIconHeight,
        'height': healthIconHeight,
        'href': icons + 'health-baptism.svg',
        'class': 'health-image health-image--baptism'
      }
    },
    'health-giving-image': {
      'type': 'image',
      'attributes': {
        'x': -boxHeight / 2 + healthIconGutter,
        'y': healthIconGutter,
        'width': healthIconHeight,
        'height': healthIconHeight,
        'href': icons + 'health-giving.svg',
        'class': 'health-image health-image--giving'
      }
    },
    'health-prayer-image': {
      'type': 'image',
      'attributes': {
        'x': -healthIconHeight / 2,
        'y':  healthIconHeight + healthIconSpacing * 2,
        'width': healthIconHeight,
        'height': healthIconHeight,
        'href': icons + 'health-prayer.svg',
        'class': 'health-image health-image--prayer'
      }
    }
  },
  'fields': [
    {
      'header': 'id',
      'label' : 'ID',
      'initial': 0,
      'type': null
    },
    {
      'header': 'parentId',
      'label' :'Parent Id',
      'initial': null,
      'type': null
    },
    {
      'header': 'name',
      'label' : 'Name',
      'initial': '',
      'type': 'text'
    },
    {
      'header': 'label',
      'svg': {
        'type': 'foreignObject',
        'attributes': {
          'x': -(boxHeight*6/2),
          'y': boxHeight,
          'width': boxHeight*6,
          'height': 20,
        },
        'style': {
          'text-align': 'center',
          'font-weight': 'bold'
        }
      }
    },
    {
      'header': 'sub_label',
      'svg': {
        'type': 'foreignObject',
        'attributes': {
          'x': -(boxHeight*6/2),
          'y': boxHeight + 20,
          'width': boxHeight*6,
          'height': 20,
        },
        'style': {
          'text-align': 'center',
        }
      }
    },
    // {
    //   'header': 'email',
    //   'initial': null,
    //   'type': 'text'
    // },
    // {
    //   'header': 'peopleGroup',
    //   'initial': null,
    //   'type': 'text'
    // },
    {
      'header': 'attenders',
      'svg': {
        'type': 'text',
        'attributes': {
          'x': -boxHeight * 0.39,
          'y': -0.5 * textMargin
        },
        'style': {
          'text-anchor': 'center'
        }
      }
    },
    {
      'header': 'believers',
      'svg': {
        'type': 'text',
        'attributes': {
          'x': -boxHeight * 0.13,
          'y': -0.5 * textMargin
        },
        'style': {
          'text-anchor': 'center'
        }
      }
    },
    {
      'header': 'baptized',
      'svg': {
        'type': 'text',
        'attributes': {
          'x': boxHeight * 0.13,
          'y': -0.5 * textMargin
        },
        'style': {
          'text-anchor': 'center'
        }
      }
    },
    {
      'header': 'newlyBaptized',
      'svg': {
        'type': 'text',
        'attributes': {
          'x': boxHeight * 0.39,
          'y': -0.5 * textMargin
        },
        'style': {
          'text-anchor': 'center'
        }
      }
    },
    // {
    //   'header': 'church',
    //   'initial': false,
    //   'type': 'checkbox',
    //   'inheritsFrom': 'church-box',
    //   'class': {
    //     'checkedTrue': 'is-church',
    //     'checkedFalse': 'is-not-church'
    //   }
    // },
    // {
    //   'header': 'churchType',
    //   'initial': 'newBelievers',
    //   'type': 'radio',
    //   'inheritsFrom': 'church-box',
    //   'values': [
    //     {
    //       'header': 'legacy',
    //       'class': 'church-legacy',
    //       'attributes': {
    //         'rx': 0
    //       }
    //     },
    //     {
    //       'header': 'existingBelievers',
    //       'attributes': {
    //         'rx': 0
    //       }
    //     },
    //     {
    //       'header': 'newBelievers'
    //     }
    //   ]
    // },
    {
      'header': 'group_type',
      'label' : window.genApiTemplate.group_fields.group_type.name,
      'initial': 'group',
      'type': 'radio',
      'inheritsFrom': 'church-box',
      'values': Object.keys(group_types).map((key)=>{
        let a = {
          'header': key,
          'label': group_types[key].label,
          'class': `type-${key}`
        }
        if ( key === "church" ){
          a.class += ' is-church'
          // a.attributes = {
          //   'rx': 0
          // }
        } else {
          a.class += ' is-not-church'
        }
        return a
      })

      // [
      // {
      //   'header': 'group',
      //   'class': 'type-group',
      //
      // },
      // {
      //   'header': 'type-church',
      //
      // },

      // ]
    },
    // {
    //   'header': 'church_baptism',
    //   'initial': false,
    //   'type': 'checkbox',
    //   'svg': {
    //     'type': 'image',
    //     'attributes': {
    //       'x': -boxHeight * 0.4,
    //       'y': boxHeight * 0.1,
    //       'width': boxHeight / 4,
    //       'height': boxHeight / 4,
    //       'xlink:href': icons + 'element-baptism.png'
    //     }
    //   }
    // },
    // {
    //   'header': 'church_bible',
    //   'initial': false,
    //   'type': 'checkbox',
    //   'svg': {
    //     'type': 'image',
    //     'attributes': {
    //       'x': -boxHeight * 0.125,
    //       'y': boxHeight * 0.1,
    //       'width': boxHeight / 4,
    //       'height': boxHeight / 4,
    //       'xlink:href': icons + 'element-word.png'
    //     }
    //   }
    // },
    // {
    //   'header': 'church_prayer',
    //   'initial': false,
    //   'type': 'checkbox',
    //   'svg': {
    //     'type': 'image',
    //     'attributes': {
    //       'x': boxHeight * 0.15,
    //       'y': boxHeight * 0.1,
    //       'width': boxHeight / 4,
    //       'height': boxHeight / 4,
    //       'xlink:href': icons + 'element-prayer.png'
    //     }
    //   }
    // },
    // {
    //   'header': 'church_communion',
    //   'initial': false,
    //   'type': 'checkbox',
    //   'svg': {
    //     'type': 'image',
    //     'attributes': {
    //       'x': -boxHeight * 0.4,
    //       'y': boxHeight * 0.375,
    //       'width': boxHeight / 4,
    //       'height': boxHeight / 4,
    //       'xlink:href': icons + 'element-lords-supper.png'
    //     }
    //   }
    // },
    // {
    //   'header': 'church_giving',
    //   'initial': false,
    //   'type': 'checkbox',
    //   'svg': {
    //     'type': 'image',
    //     'attributes': {
    //       'x': -boxHeight * 0.125,
    //       'y': boxHeight * 0.375,
    //       'width': boxHeight / 4,
    //       'height': boxHeight / 4,
    //       'xlink:href': icons + 'element-give.png'
    //     }
    //   }
    // },
    // {
    //   'header': 'church_fellowship',
    //   'initial': false,
    //   'type': 'checkbox',
    //   'svg': {
    //     'type': 'image',
    //     'attributes': {
    //       'x': boxHeight * 0.15,
    //       'y': boxHeight * 0.375,
    //       'width': boxHeight / 4,
    //       'height': boxHeight / 4,
    //       'xlink:href': icons + 'element-love.png'
    //     }
    //   }
    // },
    // {
    //   'header': 'church_praise',
    //   'initial': false,
    //   'type': 'checkbox',
    //   'svg': {
    //     'type': 'image',
    //     'attributes': {
    //       'x': -boxHeight * 0.4,
    //       'y': boxHeight * 0.65,
    //       'width': boxHeight / 4,
    //       'height': boxHeight / 4,
    //       'xlink:href': icons + 'element-worship.png'
    //     }
    //   }
    // },
    // {
    //   'header': 'church_leaders',
    //   'initial': false,
    //   'type': 'checkbox',
    //   'svg': {
    //     'type': 'image',
    //     'attributes': {
    //       'x': -boxHeight * 0.125,
    //       'y': boxHeight * 0.65,
    //       'width': boxHeight / 4,
    //       'height': boxHeight / 4,
    //       'xlink:href': icons + 'element-leaders.png'
    //     }
    //   }
    // },
    // {
    //   'header': 'church_sharing',
    //   'initial': false,
    //   'type': 'checkbox',
    //   'svg': {
    //     'type': 'image',
    //     'attributes': {
    //       'x': boxHeight * 0.15,
    //       'y': boxHeight * 0.65,
    //       'width': boxHeight / 4,
    //       'height': boxHeight / 4,
    //       'xlink:href': icons + 'element-make-disciples.png'
    //     }
    //   }
    // },
    // {
    //   'header': 'place',
    //   'initialTranslationCode': 'initialPlace',
    //   'type': 'text',
    //   'svg': {
    //     'type': 'text',
    //     'attributes': {
    //       'x': 0,
    //       'y': boxHeight + 2 * textHeight
    //     }
    //   }
    // },
    // {
    //   'header': 'date',
    //   'initialTranslationCode': 'initialDate',
    //   'type': 'text',
    //   'svg': {
    //     'type': 'text',
    //     'attributes': {
    //       'x': 0,
    //       'y': boxHeight + 3 * textHeight
    //     }
    //   }
    // },
    // {
    //   'header': 'threeThirds',
    //   'initial': '1234567',
    //   'type': 'text',
    //   'svg': {
    //     'type': 'text',
    //     'attributes': {
    //       'x': boxHeight * -0.7,
    //       'y': boxHeight * 0.6,
    //       'transform': 'rotate(90 -56 48)',
    //       'rotate': -90
    //     },
    //     'style': {
    //       'text-anchor': 'center',
    //       'letter-spacing': '0.35em'
    //     }
    //   }
    // },
    {
      'header': 'active',
      'label' :'Active',
      'initial': true,
      'type': 'checkbox'
      // svg defined currently in genmapper.js
    }
  ]
}
