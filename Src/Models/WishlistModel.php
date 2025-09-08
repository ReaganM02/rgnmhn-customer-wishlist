<?php

namespace ReaganMahinay\RGNCustomerWishlist\Models;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WishlistModel
 *
 * Handles all database operations related to customer wishlists.
 *
 * This class provides methods for adding, removing, merging, and querying wishlist items
 * for both registered users and guest users (identified by a unique token). It abstracts
 * the underlying database interactions and ensures that all queries are performed securely
 * using parameterized statements to prevent SQL injection.
 *
 * ## Purpose
 * - To manage wishlist entries for both logged-in users and guests.
 * - To provide a unified API for adding, removing, merging, and retrieving wishlist items.
 * - To encapsulate all wishlist-related database logic in a single, reusable class.
 *
 * @package Src\Models
 */
class WishlistModel {

	/**
	 * WordPress database access object.
	 *
	 * @var \wpdb
	 */
	private $db;
	/**
	 * Name of the wishlist database table.
	 *
	 * @var string
	 */
	private $tableName;


	/**
	 * WishlistModel constructor.
	 *
	 * Initializes the database access object and sets the wishlist table name.
	 */
	public function __construct() {
		global $wpdb;
		$this->db        = $wpdb;
		$this->tableName = $this->db->prefix . RGNMHN_CUSTOMER_WISHLIST_TABLE_NAME;
	}

	/**
	 * Adds a product to a user's or guest's wishlist.
	 *
	 * This method inserts a new wishlist entry for the specified product and owner.
	 * The owner can be either a registered user (identified by user ID) or a guest (identified by a unique token).
	 *
	 * @param int                                         $productID The ID of the product to add to the wishlist.
	 * @param array{type: 'user'|'token', id: int|string} $identifier - The identifier
	 *        The identifier for the wishlist owner:
	 *        - ['type' => 'user', 'id' => int] for a registered user.
	 *        - ['type' => 'token', 'id' => string] for a guest session.
	 * @return bool True if the product was successfully added, false otherwise.
	 */
	public function add( int $productID, array $identifier ) {
		$result = $this->db->insert(
			$this->tableName,
			array(
				'product_id' => $productID,
				'user_id'    => 'user' === $identifier['type'] ? $identifier['id'] : null,
				'token'      => 'token' === $identifier['type'] ? $identifier['id'] : null,
			),
			array(
				'%d',
				'%d',
				'%s',
			)
		);
		return (bool) $result;
	}

	/**
	 * Merges a guest user's wishlist into a registered user's wishlist.
	 *
	 * This method transfers all wishlist items associated with a guest token to the given user ID.
	 * - If a product exists in the guest wishlist but not in the user's wishlist, it is reassigned to the user.
	 * - If a product exists in both the guest and user wishlists, the guest entry is deleted to avoid duplicates.
	 *
	 * @param string $token  The unique token identifying the guest wishlist.
	 * @param int    $userID The ID of the registered user to merge the wishlist into.
	 * @return void
	 */
	public function mergeGuestWishlist( string $token, int $userID ) {
		$guestProductIDs = $this->db->get_col( $this->db->prepare( "SELECT product_id FROM {$this->tableName} WHERE token = %s", $token ) );
		$userProductIDs  = $this->db->get_col( $this->db->prepare( "SELECT product_id FROM {$this->tableName} WHERE user_id = %d", $userID ) );

		if ( ! empty( $guestProductIDs ) ) {
			foreach ( $guestProductIDs as $productID ) {
				// Merge Only if the product is not in the user's wishlist.
				if ( ! in_array( $productID, $userProductIDs, true ) ) {
					$this->db->update(
						$this->tableName,
						array(
							'user_id' => $userID,
							'token'   => null,
						),
						array(
							'token'      => $token,
							'product_id' => $productID,
						),
					);
				} else {
					// If the user already has this item, just delete the guest.
					$this->db->delete(
						$this->tableName,
						array(
							'token'      => $token,
							'product_id' => $productID,
						)
					);
				}
			}
		}
	}

	/**
	 * Checks if a product exists in a user's or guest's wishlist.
	 *
	 * This method determines whether a specific product is present in the wishlist
	 * for the given owner, which can be either a registered user (by user ID) or a guest (by token).
	 *
	 * @param int                                         $productID The ID of the product to check.
	 * @param array{type: 'user'|'token', id: int|string} $identifier - The identifier
	 *        The identifier for the wishlist owner:
	 *        - ['type' => 'user', 'id' => int] for a registered user.
	 *        - ['type' => 'token', 'id' => string] for a guest session.
	 * @return bool True if the product is in the wishlist, false otherwise.
	 */
	public function isProductInWishlist( int $productID, array $identifier ) {
		$whereCol = ( 'user' === $identifier['type'] ) ? 'user_id' : 'token';
		$sql      = $this->db->prepare( "SELECT COUNT(id) FROM {$this->tableName} WHERE product_id = %d AND {$whereCol} = %s", $productID, $identifier['id'] );

		$count = $this->db->get_var( $sql );

		return $count > 0;
	}

	/**
	 * Removes a specific product from a user's wishlist.
	 *
	 * This method deletes a wishlist entry that matches both the given product ID and user ID.
	 * It does not affect guest wishlists (entries identified by a token).
	 *
	 * @param int $productID The ID of the product to remove from the wishlist.
	 * @param int $userID    The ID of the registered user whose wishlist will be modified.
	 * @return bool          True if the product was successfully removed, false otherwise.
	 *
	 * @security
	 * - Uses parameterized queries to prevent SQL injection.
	 * - Assumes $productID and $userID are validated integers by the caller.
	 */
	public function delete( int $productID, int $userID ) {
		$result = $this->db->delete(
			$this->tableName,
			array(
				'product_id' => $productID,
				'user_id'    => $userID,
			),
			array( '%d', '%d' )
		);
		return (bool) ( $result > 0 );
	}


	/**
	 * Retrieves all wishlist entries for a specific registered user.
	 *
	 * This method fetches all wishlist items associated with the given user ID,
	 * ordered by the date they were added (most recent first).
	 * Only entries for registered users (not guests) are returned.
	 *
	 * @param int $userID The ID of the registered user whose wishlist entries will be retrieved.
	 * @return array An array of associative arrays representing wishlist entries.
	 *               Returns an empty array if no entries are found.
	 *
	 * @security
	 * - Uses parameterized queries to prevent SQL injection.
	 * - Assumes $userID is a validated integer by the caller.
	 */
	public function getWishlistEntriesByUserID( int $userID ) {
		$sql    = $this->db->prepare( "SELECT * FROM $this->tableName WHERE user_id = %d ORDER BY date_created DESC", $userID );
		$result = $this->db->get_results( $sql, ARRAY_A );
		return $result ? $result : array();
	}

	/**
	 * Get the subset of product IDs that actually exist in this table.
	 *
	 * Accepts a list of candidate product IDs, sanitizes and de-duplicates them,
	 * then performs a parameterized IN(...) query against `$this->tableName` to
	 * return only the IDs that are present. Results are ordered by the most recent
	 * `date_created` first.
	 *
	 * @param array<int|string> $productIDs  Candidate product IDs to check.
	 * @return array<int|string>             Matching product IDs present in the table (newest first).
	 *                                       Returns [] if none match or input is empty.
	 * @since 1.0.0
	 */
	public function getProductIDs( array $productIDs ): array {
		if ( empty( $productIDs ) ) {
			return array();
		}

		$productIDs = array_values( array_filter( array_map( 'absint', array_unique( $productIDs ) ) ) );

		$placeholders = implode( ',', array_fill( 0, count( $productIDs ), '%d' ) );

		$sql = $this->db->prepare(
			"SELECT product_id FROM $this->tableName WHERE product_id IN ($placeholders) ORDER BY date_created DESC",
			...$productIDs
		);

		$results = $this->db->get_col( $sql );

		return $results ? $results : array();
	}
}
