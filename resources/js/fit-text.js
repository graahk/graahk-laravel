(() => {
  var addEvent = function (el, type, fn) {
    if (el.addEventListener) {
      el.addEventListener(type, fn, false)
    } else {
			el.attachEvent('on' + type, fn)
    }
  }

  var extend = function (obj, ext) {
    for (var key in ext) {
      if (ext.hasOwnProperty(key)) {
        obj[key] = ext[key]
      }
    }

    return obj
  }

  window.fitText = function (el, kompressor, options) {
    var settings = extend({
      'minFontSize' : -1/0,
      'maxFontSize' : 1/0
    }, options)

    var fit = function (el) {
      var compressor = kompressor || 1

      var resizer = function () {
        var size = Math.max(Math.min(el.clientWidth / (compressor*10), parseFloat(settings.maxFontSize)), parseFloat(settings.minFontSize))
        el.style.fontSize = size + 'px'
        el.style.lineHeight = (size * (3 / 2.4)) + 'px'
      }

      resizer()

      addEvent(window, 'resize', resizer)
      addEvent(window, 'orientationchange', resizer)
    }

    if (el.length) {
      for (var i = 0; i < el.length; i++) {
        fit(el[i])
      }
    } else {
      fit(el)
    }

    return el
  }
})()

window.resizeCards = function () {
  const cardCosts = document.querySelectorAll('.graahk-card h2')
  const cardNames = document.querySelectorAll('.graahk-card h3')
  const cardPowers = document.querySelectorAll('.graahk-card h4')
  const cardTexts = document.querySelectorAll('.graahk-card p')
  const cardTribes = document.querySelectorAll('.graahk-card span.tribes')
  
  for (let i = 0; i < cardCosts.length; i++) {
    window.fitText(cardCosts[i], 0.15)
  }

  for (let i = 0; i < cardNames.length; i++) {
    window.fitText(cardNames[i], 1.4)
  }

  for (let i = 0; i < cardPowers.length; i++) {
    window.fitText(cardPowers[i], 0.3)
  }

  for (let i = 0; i < cardTexts.length; i++) {
    window.fitText(cardTexts[i], 1.5)
  }

  for (let i = 0; i < cardTribes.length; i++) {
    window.fitText(cardTribes[i], 2.2)
  }
}

window.resizeCards()
window.onresize = (() => window.resizeCards())

Livewire.hook('request', ({ succeed }) => {
    succeed(({ snapshot, effect }) => {
        window.resizeCards()
    })
})
