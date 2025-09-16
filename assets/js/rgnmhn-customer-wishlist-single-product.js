/**
 * Wishlist UI controller for a single WooCommerce product page.
 * 
 * This class renders one of two HTML <template> blocks (ADD vs. ADDED state)
 * into a root container and wires the "Add to wishlist" click behavior.
 * 
  * It supports both:
 *  - Simple products (single product_id)
 *  - Variable products (multiple variation_ids via WC's found_variation event)
 * 
 * #### Dependencies
 * @typedef {Object} RgnWishlistSingleProduct
 * @property {string} url - The Ajax endpoint URL (e.g., admin-ajax.php).
 * @property {{ add: string, get: string }} nonce - Nonce object for security checks.
 * @property {number|string} product_id - Current product ID (integer).
 * @property {Array<number|string>} added_ids - Already added variation IDs.
 * @property {"simple"|"variable"} product_type - Type of the product.
 * @property {"yes"|"no"} is_added - Whether the product is already in the wishlist.
 * 
 * - Two <template> elements exist in the DOM:
 *   <template id="rgnmhn-template-add-wishlist">...</template>
 *   <template id="rgnmhn-template-added-wishlist">...</template>
 * 
 * - A root container exists with an element id equal to the constructor argument (no '#'):
 *   <div id="rgnmhn-wishlist-single-product"></div>
 * 
 * - For variable products, WooCommerce triggers:
 *   - 'found_variation' on 'form.variations_form' with { variation_id }
 *   - 'reset_data' on 'form.variations_form'
 * 
 * ## Security
 * - Nonce is sent in the POST body under key 'security'
 * - Product/variation id is appended as 'product-id'
*/

/**
 * Global localized object injected via wp_localize_script().
 * @type {RgnWishlistSingleProduct}
 */
const WISHLIST_CONFIG = window.rgnmhn_wishlist_single_product

class WishlistSingleProduct {
  /** @type {HTMLDivElement|null} Root container for injected template content */
  #root

  // Given Template IDs
  /** @type {string} Template id for the "already added" state */
  #added = 'rgnmhn-template-added-wishlist'

  /** @type {string} Template id for the "add to wishlist" state */
  #add = 'rgnmhn-template-add-wishlist'

  // Only for variable products

  /**
 * Variation ids currently in the wishlist (server-sourced on load).
 * Used to decide which template to show for a selected variation.
 * @type {number[]}
 */
  #variationAddedIds = WISHLIST_CONFIG.added_ids.map((n) => Number(n))

  /** @type {number|undefined} The currently selected variation id (variable products) */
  #variationID

  // Only For Simple Product

  /** @type {number|undefined} Simple product id (single id) */
  #productID

  /**
 * Whether the simple product is already in the wishlist ("yes" | "no").
 * Mirrors the server-provided value, kept in sync after successful add.
 * @type {'yes'|'no'}
 */
  #isProductAdded = WISHLIST_CONFIG.is_added

  /**
 * @constructor
 * @param {string} rootSelector - The element ID (without '#') of the root container:
 *                                e.g., 'rgnmhn-wishlist-single-product'
 *
 * Sets up a delegated click handler inside the root to capture clicks
 * on elements matching `.rgnmhn-add-to-wishlist`.
 */
  constructor(rootSelector) {
    this.#root = document.getElementById(rootSelector)

    // Guard: only attach events if a valid div root exists
    if (this.#root && this.#root instanceof HTMLDivElement) {
      this.#root.addEventListener('click', async (e) => {
        console.log('clicked')
        const btn = e.target.closest('.rgnmhn-add-to-wishlist')
        if (btn && this.#root.contains(btn)) {
          try {
            // UI lock while request is in-flight (prevent double-taps)
            btn.disabled = true
            btn.classList.add('rgnmhn-btn-disabled')
            await this.handleAddWishlist()
          } catch (error) {
            // Swallow errors to keep UI responsive; log for debugging.
            console.log(error)
          } finally {
            // Always restore button state
            btn.disabled = false
            btn.classList.remove('rgnmhn-btn-disabled')
          }
        }
      })
    }
  }


  /**
 * Clears the root container.
 * Useful when a variable product is reset (WC's `reset_data` event).
 * @public
 */
  removeTemplate() {
    if (this.#root) {
      this.#root.innerHTML = ''
    }
  }


  /**
   * Handles the "Add to wishlist" flow:
   * - Builds a FormData payload with nonce + product/variation id
   * - Sends POST to the configured endpoint
   * - On success:
   *    - For variable products: push new variation id to local cache
   *    - For simple products: set internal flag to 'yes'
   *    - Re-render the appropriate template for the selected id
   * @returns {Promise<void>}
   * @public
   */
  async handleAddWishlist() {
    try {
      const formData = new FormData()
      formData.append('action', 'rgnmhn_add_customer_wishlist')

      // Decide which id to submit based on product type
      if (this.productType === "simple") {
        formData.append('product-id', this.#productID)
      } else {
        formData.append('product-id', this.#variationID)
      }
      formData.append('security', WISHLIST_CONFIG.nonce.add)

      const request = await fetch(WISHLIST_CONFIG.url, {
        method: 'POST',
        body: formData
      })
      const response = await request.json()
      if (response.success) {
        // Update local state based on product type
        if (this.productType === 'variable') {
          // Normalize the returned id in case API returns a string
          this.addNewVariationID = Number(response.data)
        } else {
          this.#isProductAdded = 'yes'
        }

        // Re-render the UI with the id that was just added
        this.renderTemplate(Number(response.data))
      }
    } catch (error) {
      console.log(error)
    }
  }

  /**
 * Internal: clones and injects the given template into the root.
 * Replaces existing children if present to keep a single node inside root.
 * @param {string} templateID - DOM id of the <template> to render
 * @private
 */
  #showTemplate(templateID) {
    const template = document.getElementById(templateID)
    if (template && template instanceof HTMLTemplateElement) {
      const node = template.content.firstElementChild.cloneNode(true)
      const root = this.#root
      if (root.hasChildNodes()) {
        root.replaceChildren(node)
      } else {
        root.appendChild(node)
      }
    }
  }

  /**
 * Renders the correct template for either:
 * - The selected variation (variable product), or
 * - The product id itself (simple product).
 *
 * @param {number} id - For variable: the selected variation_id.
 *                      For simple:   the product_id.
 * @public
 */
  renderTemplate(id) {
    if (this.productType === 'variable') {
      this.#variationID = id
      const isAdded = this.#variationAddedIds.includes(id)
      const templateID = isAdded ? this.#added : this.#add
      this.#showTemplate(templateID)
    }

    if (this.productType === 'simple') {
      this.#productID = id
      // For simple products we rely on a yes/no flag
      const templateID = this.#isProductAdded === 'yes' ? this.#added : this.#add
      this.#showTemplate(templateID)
    }

  }

  /**
 * Adds a newly-added variation id into local memory so subsequent renders
 * show the "ADDED" template for that variation.
 * @param {number} id
 * @public
 */
  set addNewVariationID(id) {
    this.#variationAddedIds.push(id)
  }

  /**
 * @returns {'simple'|'variable'} Current product type from server config
 * @public
 */
  get productType() {
    return WISHLIST_CONFIG.product_type
  }

  /**
 * @returns {number} Current product id (normalized to number)
 * @public
 */
  get productID() {
    return Number(WISHLIST_CONFIG.product_id)
  }

  /**
  * Initialize wishlist state on page load.
  * 
  * Purpose:
  * - Refreshes potentially stale, client-side `WISHLIST_CONFIG` by fetching
  *   the authoritative state from the server (via admin-ajax).
  * - Mitigates browser/optimizer caching by requesting fresh data.
  * 
  *  Behavior:
  * - For **simple** products: updates `WISHLIST_CONFIG.is_added` and triggers
  *   a template re-render via `renderTemplate(product_id)`.
  * 
  * - For **variable** products: updates `WISHLIST_CONFIG.added_ids` with an
  *   integer array of variation IDs currently in the wishlist.
  * 
  * Side effects:
  * - Mutates the global `WISHLIST_CONFIG` object.
  * - May update the DOM (simple products) by re-rendering the template.
  * 
  * Network:
  * - POSTs to `WISHLIST_CONFIG.url` (admin-ajax) with action `rgnmhn_customer_wishlist_get_data`.
  * - Intended to bypass caches; server should send no-cache headers. The client
  *   may also set `cache: 'no-store'` to avoid browser-level caching.
  * 
  *  Errors:
 * - On failure, logs a concise error to the console; no exceptions are thrown.
 * 
 * @async
 * @returns {Promise<void>} Resolves after state hydration and any DOM updates.
  */
  async init() {
    try {
      const fd = new FormData()
      fd.append('action', 'rgnmhn_customer_wishlist_get_data')
      fd.append('security', WISHLIST_CONFIG.nonce.get)
      fd.append('product-id', WISHLIST_CONFIG.product_id)
      const request = await fetch(WISHLIST_CONFIG.url, {
        method: 'POST',
        body: fd,
        cache: 'no-store'
      })
      const response = await request.json()
      if (response.success) {
        if (WISHLIST_CONFIG.product_type === 'simple') {
          WISHLIST_CONFIG.is_added = response.data
          this.renderTemplate(WISHLIST_CONFIG.product_id)
        }
        if (WISHLIST_CONFIG.product_type === 'variable') {
          WISHLIST_CONFIG.added_ids = response.data.map((d) => parseInt(d))
        }
      }
    } catch (error) {
      console.error('Failed to fetch single product data')
    }
  }
}


/* -------------------------
 * Bootstrap / Event wiring
 * -------------------------
 */

// Instantiate using the root element id (no leading '#')
const wishlist = new WishlistSingleProduct('rgnmhn-wishlist-single-product')

// Trigger on page load
wishlist.init()


// WooCommerce variation lifecycle events (jQuery-based)
jQuery(($) => {
  // When a variation is found/selected, render for its variation_id
  $(document).on('found_variation', 'form.variations_form', function (e, variation) {
    wishlist.renderTemplate(Number(variation['variation_id']))
  })

  // When variation data is reset (e.g., user clears selection), clear UI
  $(document).on('reset_data', 'form.variations_form', function () {
    wishlist.removeTemplate()
  })
})
