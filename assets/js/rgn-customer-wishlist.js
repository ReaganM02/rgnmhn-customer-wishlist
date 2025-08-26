const state = PetiteVue.reactive({
  variationID: null,
  attributes: {},
  content: this,
  alreadyAdded: false,
  disabled: true,
})

const attemptToAdd = async (formData) => {
  try {
    formData.append('security', rgn_add_customer_wishlist.nonce)
    formData.append('action', 'rgn_add_customer_wishlist')
    const send = await fetch(rgn_add_customer_wishlist.url, {
      method: 'POST',
      body: formData
    });
    const response = await send.json()
    return response;
  } catch (error) {
    throw new Error(error)
  }
}


const product = PetiteVue.createApp({
  async add(e) {
    const button = e.currentTarget
    const productID = parseInt(button.getAttribute('data-product-id')) || 0

    try {

      if (button instanceof HTMLButtonElement) {
        button.classList.add('rgn-btn-disabled')
        button.disabled = true
      }

      const formData = new FormData()
      formData.append('productID', productID)

      const added = await attemptToAdd(formData);
      if (added.success) {
        const data = added.data
        const el = document.getElementById('rgn-customer-product-wishlist')
        if (el) {
          el.innerHTML = data
        }
      }
    } catch (error) {
      console.log(error)
    } finally {
      button.classList.remove('rgn-btn-disabled')
      button.disabled = false
    }
  },
  async mount() {
    state['content'] = this.$refs['rgn-customer-wishlist']
  }
})

product.mount('#rgn-customer-product-wishlist');

jQuery(function ($) {

  $(document).on('found_variation', 'form.variations_form', function (e, variation) {
    const parentEl = document.getElementById('rgn-customer-product-wishlist')
    parentEl.innerHTML = variation['rgn-wishlist']
    console.log(variation['rgn-wishlist'])
    product.mount('#rgn-customer-product-wishlist')
  })

  // (Optional) clear when no match
  $(document).on('reset_data', 'form.variations_form', function () {
    const addToWishlistBtn = document.querySelector('.rgn-add-to-wishlist')
    if (addToWishlistBtn && addToWishlistBtn instanceof HTMLButtonElement) {
      addToWishlistBtn.setAttribute('data-product-id', 0);
      const productID = parseInt(addToWishlistBtn.getAttribute('data-product-id')) || 0
      if (productID === 0) {
        addToWishlistBtn.disabled = true
        addToWishlistBtn.classList.add('rgn-btn-disabled')
      }
    }
  })
})