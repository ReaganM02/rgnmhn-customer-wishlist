/**
 * @typedef {Object} WishlistMyAccountOBJ
 * @property {string} url - The Ajax endpoint URL (e.g., admin-ajax.php).
 * @property {{ add: string, remove: string }} nonces - Nonce object for security checks.
 */

/**
 * Global localized object injected via wp_localize_script().
 * @type {WishlistMyAccountOBJ}
 */
const CONFIG = window.rgnmhn_wishlist_my_account


/**
 * @class WishlistMyAccount
 * @classdesc
 * Binds a single `click` listener to a root container (event delegation) and
 * handles two actions for items inside that container:
 *  1) **Add to cart** via `.rgnmhn-add-to-cart`
 *  2) **Delete from wishlist** via `.rgnmhn-wishlist-delete-btn`
 *
 * 
 *  On success, it:
 *  - Re-renders the wishlist markup returned by the server (delete flow)
 *  - Triggers WooCommerce fragment refresh to update cart totals/mini-cart
 *  - Optionally opens the Side Cart panel if that plugin is present
 * 
* @param {string} id
 * The DOM **id** of the root `<div>` that wraps the wishlist (e.g. `"rgnmhn-my-account-wishlist"`).
 * The constructor is fail-safe: if the element is missing or not a `<div>`, it no-ops.
 * 
 * @requires window.rgnmhn_wishlist_my_account
 * A localized global config object with shape:
 * `{ url: string, nonces: { add: string, remove: string } }`.
 * Used for AJAX destination and nonce verification.
 * 
 * @fires wc_fragment_refresh
 * Emits `jQuery(document.body).trigger('wc_fragment_refresh')` after a successful add-to-cart.
 *
 * 
 * @see openSideCart
 * Integrates with the optional “Side Cart for WooCommerce” plugin by clicking `.xoo-wsc-basket` if present.
 * 
 * @summary Selectors & data requirements
 * - **Container**: a `<div>` with the provided `id`.
 * - **Buttons**:
 *   - `.rgnmhn-add-to-cart[data-id="<number>"]`
 *   - `.rgnmhn-wishlist-delete-btn[data-id="<number>"]`
 * - **data-id** must be a numeric product/variation ID; parsed with base-10.
 * 
 * @description Network & security
 * - Sends `FormData` POSTs to `CONFIG.url` with `credentials: "same-origin"` and `Accept: "application/json"`.
 * - Actions:
 *     - `rgnmhn_wishlist_add_to_cart`  (nonce: `CONFIG.nonces.add`)
 *     - `rgnmhn_wishlist_delete_item`  (nonce: `CONFIG.nonces.remove`)
 * - Expects a JSON response: `{ success: boolean, data?: any, message?: string }`
 *   - For **delete**, `data` should contain fresh HTML for the wishlist section.
 *
 * @since 1.0.0
 */
class WishlistMyAccount {
  #root
  constructor(id) {
    this.#root = document.getElementById(id)

    if (!this.#root) {
      return
    }

    if (this.#root instanceof HTMLDivElement === false) {
      return
    }

    this.#root.addEventListener('click', (e) => {
      this.addToCart(e)
      this.deleteWishlist(e)
    })
  }

  /**
   * Replace the wishlist container’s markup with fresh server-rendered HTML.
   *
   * Clears the current contents of `#root` and injects the provided HTML string.
   * Because this class uses event delegation on the root container, you do not
   * need to re-bind per-row event listeners after the update.
   * @param {string} html - A **sanitized** HTML string returned by the server.
   * @returns {void}
  */
  updateHTML(html) {
    this.#root.innerHTML = ''
    this.#root.innerHTML = html
  }

  /**
   * Handle “Delete from wishlist” clicks via event delegation.
   *
   * Walks up from the event target to find a `.rgnmhn-wishlist-delete-btn` inside the
   * class root, validates the element type, extracts its `data-id` (parsed base-10),
   * and posts a WordPress AJAX request to delete the item. On a successful JSON
   * response (`{ success: true, data: "<html>" }`), the wishlist markup is replaced
   * with the server-rendered HTML via {@link updateHTML}.
   * 
   * Guards:
   *  - No-ops if the click didn’t originate from a `.rgnmhn-wishlist-delete-btn`
   *  - Ensures the button is inside this instance’s root container
   *  - Ensures the element is an `HTMLButtonElement`
   *  - Defaults `productID` to `0` if `data-id` is missing/invalid (server should reject)
   * 
  * Side effects:
  *  - Updates DOM by calling {@link updateHTML} with server HTML on success
  *  - Logs errors to the console on failure (no UI notifications built in)
  * @param {PointerEvent} e
  * The bubbled pointer/click event attached to the wishlist root.
  * @returns {Promise<void>}
 */
  async deleteWishlist(e) {
    const deleteBtn = e.target.closest('.rgnmhn-wishlist-delete-btn')

    if (!deleteBtn) {
      return
    }

    if (!this.#root.contains(deleteBtn)) {
      return
    }

    if (deleteBtn instanceof HTMLButtonElement === false) {
      return
    }

    const rawDataID = deleteBtn.getAttribute('data-id')
    const productID = rawDataID !== null ? parseInt(rawDataID, 10) : 0

    const fd = new FormData()
    fd.append('action', 'rgnmhn_wishlist_delete_item')
    fd.append('security', CONFIG.nonces.remove)
    fd.append('product-id', productID)

    try {
      const response = await this.requestAttempt(fd)
      if (response.success) {
        this.updateHTML(response.data)
      }
    } catch (error) {
      console.log(error)
    }
  }

  /**
   * Handle “Add to cart” clicks from within the wishlist (event delegation).
   * 
   * Walks up from the event target to find a `.rgnmhn-add-to-cart` button inside this
   * instance’s root container, validates it, extracts its numeric `data-id` (base-10),
   * and sends a WordPress AJAX request to add the product/variation to the cart.
   * 
   * On a successful JSON response (`{ success: true, ... }`) it:
   *  - Triggers WooCommerce fragment refresh so mini-cart/totals update
   *  - Attempts to open the Side Cart panel via {@link openSideCart} (if present)
   * 
   *  Guards (safe no-ops):
   *  - No matching `.rgnmhn-add-to-cart` element
   *  - Element not contained within this instance’s root
   *  - Element is not an `HTMLButtonElement`
   *  - Missing/invalid `data-id` (falls back to `0`; server should reject)
   *
   * @param {PointerEvent} e
   * The bubbled pointer/click event attached to the wishlist root container.
   * 
   * @returns {Promise<void>}
   * 
   * @fires wc_fragment_refresh
   * Emits `jQuery(document.body).trigger('wc_fragment_refresh')` after success.
 */
  async addToCart(e) {

    const btn = e.target.closest('.rgnmhn-add-to-cart')

    if (!btn) {
      return
    }

    if (!this.#root.contains(btn)) {
      return
    }

    if (btn instanceof HTMLButtonElement === false) {
      return
    }

    const rawDataID = btn.getAttribute('data-id')
    const productID = rawDataID !== null ? parseInt(rawDataID, 10) : 0

    const formData = new FormData()
    formData.append('action', 'rgnmhn_wishlist_add_to_cart')
    formData.append('security', CONFIG.nonces.add)
    formData.append('product-id', productID)
    try {
      const response = await this.requestAttempt(formData)
      if (response.success) {
        jQuery(document.body).trigger('wc_fragment_refresh');
        this.openSideCart()
      }
    } catch (error) {
      console.log(error)
    }
  }

  /**
   * Perform a POST request to the WordPress AJAX endpoint and return parsed JSON.
   *
   * Sends the provided `FormData` to `CONFIG.url` (typically `admin-ajax.php`)
   * with `credentials: "same-origin"` so logged-in cookies are included. Expects
   * a JSON response (e.g., from `wp_send_json_success()` / `wp_send_json_error()`).
   * 
   * 
   * @param {FormData} formData
   * A `FormData` payload containing `action`, nonces, and other fields required by
   * your AJAX handler.
   * 
   * 
   * @returns {Promise<any>}
   * Resolves with the parsed JSON body returned by the server. Common shape:
   * `{ success: boolean, data?: any, message?: string }` 
   * 
   * 
   * @throws {Error}
   * Rethrows as `Error` if the network request fails or JSON parsing throws.
 */
  async requestAttempt(formData) {
    try {
      const request = await fetch(CONFIG.url, {
        method: 'POST',
        body: formData,
        headers: { 'Accept': 'application/json' },
        credentials: 'same-origin',
      })
      const response = await request.json()
      return response
    } catch (error) {
      throw new Error(error)
    }
  }

  /**
   * Open the Side Cart panel (if the 3rd-party plugin is present).
   * Integrates with **Side Cart for WooCommerce** by programmatically clicking the
   * plugin’s basket trigger element (`.xoo-wsc-basket`). If the plugin or element
   * isn’t present, this method safely no-ops.
   * 
   * @see https://wordpress.org/plugins/side-cart-woocommerce/
   * 
   */
  openSideCart() {
    const sideCartEl = document.querySelector('.xoo-wsc-basket')
    if (sideCartEl && sideCartEl instanceof HTMLDivElement) {
      sideCartEl.click()
    }
  }
}

new WishlistMyAccount('rgnmhn-my-account-wishlist')