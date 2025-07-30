<?php

namespace Src\Models;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
  exit;
}

class WishlistModel
{
  private $db;
  private $tableName;
  public function __construct()
  {
    global $wpdb;
    $this->db = $wpdb;
    $this->tableName = $this->db->prefix . RGN_CUSTOMER_WISHLIST_TABLE_NAME;
  }

  /**
   * @param array{type: 'user'|'token', id: int|string} $identifier The identifier for the wishlist owner.
   */
  public function add(int $productID, array $identifier)
  {
    $result = $this->db->insert(
      $this->tableName,
      [
        'product_id' => $productID,
        'user_id' => $identifier['type'] === 'user' ? $identifier['id'] : null,
        'token' => $identifier['type'] === 'token' ? $identifier['id'] : null,
      ],
      [
        '%d',
        '%d',
        '%s',
        '%d'
      ]
    );
    return (bool) $result;
  }

  public function mergeGuestWishlist(string $token, int $userID)
  {
    $guestProductIDs = $this->db->get_col($this->db->prepare("SELECT product_id FROM $this->tableName WHERE token = %s", $token));
    $userProductIDs = $this->db->get_col($this->db->prepare("SELECT product_id FROM $this->tableName WHERE user_id = %d", $userID));

    if (!empty($guestProductIDs)) {
      foreach ($guestProductIDs as $productID) {
        // Merge Only if the product is not in the user's wishlist
        if (!in_array($productID, $userProductIDs)) {
          $this->db->update(
            $this->tableName,
            ['user_id' => $userID, $token => null],
            ['token' => $token, 'product_id' => $productID],
          );
        } else {
          // If the user already has this item, just delete the guest
          $this->db->delete($this->tableName, ['token' => $token, 'product_id' => $productID]);
        }
      }
    }
  }

  /**
   * @param array{type: 'user'|'token', id: int|string} $identifier The identifier for the wishlist owner.
   */
  public function isProductInWishlist(int $productID, array $identifier)
  {
    $whereCol = ($identifier['type'] === 'user') ? 'user_id' : 'token';
    $sql = $this->db->prepare("SELECT COUNT(id) FROM {$this->tableName} WHERE product_id = %d AND {$whereCol} = %s", $productID, $identifier['id']);

    $count = $this->db->get_var($sql);

    return $count > 0;
  }
}
