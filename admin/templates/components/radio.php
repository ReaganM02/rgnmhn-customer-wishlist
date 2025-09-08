<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Template for displaying a component.
 *
 * @var array{
 * id: string,
 * name: string,
 * value: mixed,
 * label: string,
 * type: string,
 * options: array
 * default: string
 * } $variables The data passed to this template.
 */
?>
<div class="">
	<div>
	<label for="<?php echo esc_attr( $variables['id'] ); ?>" class="rgnmhn-text-base rgnmhn-text-zinc-600"><?php echo esc_html( $variables['label'] ); ?></label>
	</div>
	<div class="rgnmhn-flex rgnmhn-gap-2">
	<?php foreach ( $variables['options'] as $key => $value ) : ?>
		<div class="rgnmhn-shadow rgnmhn-border">
		<input
			type="radio"
			name="<?php echo esc_attr( $variables['id'] ); ?>"
			id="<?php echo esc_attr( $key ); ?>"
			class="!rgnmhn-hidden"
			<?php echo $variables['default'] === $key ? 'checked="checked"' : ''; ?>
			value="<?php echo esc_attr( $key ); ?>" />
		<label for="<?php echo esc_attr( $key ); ?>" class="rgnmhn-cursor-pointer rgnmhn-w-8 rgnmhn-block rgnmhn-p-2 peer-checked:rgnmhn-bg-zinc-200"><?php echo wp_kses( $value, rgnmhnCustomerWishlistAllowedSVGTag() ); ?></label>
		</div>
	<?php endforeach; ?>
	</div>
</div>
