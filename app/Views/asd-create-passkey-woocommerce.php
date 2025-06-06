<?php if (! defined('ABSPATH')) exit; ?>

<form id="createPasskeyForm" method="post" class="woocommerce-EditAccountForm edit-account">
    <?php wp_nonce_field('create_passkey', 'create_passkey_nonce'); ?>
    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
        <label for="useremail">Email</label>
        <input type="email" readonly class="woocommerce-Input woocommerce-Input--text input-text"
            name="useremail" id="useremail"
            value="<?php echo esc_html(wp_get_current_user()->user_email); ?>" required>
    </p>

    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide" style="display:<?php echo esc_html($show) ?>">
        <label for="password">Password</label>
        <input type="password" class="woocommerce-Input woocommerce-Input--password input-text"
            name="password" id="password"
            value="" placeholder="Enter password">
    </p>

    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
        <label for="displayName">Display Name</label>
        <input type="text" class="woocommerce-Input woocommerce-Input--text input-text"
            name="displayName" id="displayName" placeholder="Display Name">
    </p>

    <fieldset>
        <legend>Authenticator Type</legend>
        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="auth-type-platform" class="woocommerce-form__label woocommerce-form__label-for-radio">
                <input class="woocommerce-form__input woocommerce-form__input-radio"
                    type="radio" name="authenticator_type"
                    id="auth-type-platform" value="platform" checked>
                Platform (e.g., fingerprint/face id on this device)
            </label>
        </p>
        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="auth-type-cross" class="woocommerce-form__label woocommerce-form__label-for-radio">
                <input class="woocommerce-form__input woocommerce-form__input-radio"
                    type="radio" name="authenticator_type"
                    id="auth-type-cross" value="cross-platform">
                Cross-Platform (e.g., fingerprint/face id on mobile phone or external security)
            </label>
        </p>
    </fieldset>
    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide" style="margin-top:10px;">
        <button type="submit" class="woocommerce-Button button" name="create-passkey">Create Passkey</button>
    </p>
</form>