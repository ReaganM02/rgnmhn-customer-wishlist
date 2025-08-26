jQuery(($) => {
  $('.rgn-wishlist-color-picker').wpColorPicker({
    palettes: themePalette
  });
})
document.addEventListener('DOMContentLoaded', () => {
  const tabs = Array.from(document.querySelectorAll('.rgn-tab'))
  if (tabs.length === 0) {
    return
  }
  tabs.forEach((tab) => {
    tab.addEventListener('click', (e) => {
      e.preventDefault()

      tabs.forEach((t) => t.classList.remove('rgn-tab-active'))

      tab.classList.add('rgn-tab-active')

      const dataTarget = tab.getAttribute('data-target')
      if (dataTarget) {
        const tabContents = Array.from(document.querySelectorAll('.rgn-tab-content'))
        if (tabContents.length > 0) {
          tabContents.forEach((t) => t.classList.remove('rgn-content-active'))
        }
        const currentTab = document.querySelector(`#rgn-${dataTarget}`)
        if (currentTab) {
          currentTab.classList.add('rgn-content-active')
        }
      }
    })
  })
})

class Select {
  constructor() {
    this.selects = Array.from(document.querySelectorAll('.rgn-select'))
    this.selects.forEach((select) => {
      select.addEventListener('click', (e) => {
        e.preventDefault()
        this.click(e.currentTarget)
      })
    })
    this.select()
  }
  click(target) {
    const el = target['nextElementSibling']
    el.classList.toggle('rgn-show-select')
  }
  select() {
    const options = Array.from(document.querySelectorAll('.rgn-select-option'))
    options.forEach((option) => {
      option.addEventListener('click', () => {
        const dataKey = option.getAttribute('data-key')
        console.log(dataKey)
      })
    })
  }
}
new Select()

