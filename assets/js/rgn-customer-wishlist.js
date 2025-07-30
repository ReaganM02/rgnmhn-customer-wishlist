class RGNAddToWishList {
  constructor() {
    this.wishListEl = document.querySelector('#rgn-add-to-wishlist')

    if (this.wishListEl) {
      this.wishListEl.addEventListener('click', async (e) => {
        e.preventDefault()
        const productID = this.wishListEl.getAttribute('data-product-id')
        this.add(productID)
      })
    }
  }
  async add(productID) {
    try {
      const formData = new FormData()
      formData.append('action', 'rgn_add_customer_wishlist')
      formData.append('security', rgn_add_customer_wishlist.nonce)
      formData.append('productID', productID)

      const request = await fetch(rgn_add_customer_wishlist.url, {
        method: 'POST',
        body: formData
      });

      const response = await request.json()
      console.log(response)

    } catch (error) {
      console.log(error)
    }
  }
}

new RGNAddToWishList()

jQuery(($) => {
  $('form.variations_form').on('show_variation', function (event, variation) {
    console.log(variation)
    const wishList = document.querySelector('#rgn-add-to-wishlist')
    if (wishList) {
      wishList.setAttribute('data-variation-id', variation['variation_id'])
    }
  });

})
