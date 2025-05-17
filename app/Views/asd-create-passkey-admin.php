<?php if (! defined('ABSPATH')) exit; ?>
<div class="wrap">
    <div class="d-flex justify-content-center" style="min-height: 100vh; align-items: flex-start; padding-top: 65px;">
        <div class="card shadow-lg p-3" style="width: 100%; max-width: 900px;">
            <div class="d-flex align-items-center mb-3">
                <?php  // phpcs:disable PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage
                ?>
                <img src="<?php echo esc_url($logo); ?>" style="max-width: 35%; margin-right: 15px;" alt="LOGO">
            </div>
            <form id="createPasskeyForm">
                <div class="mb-3" style="display:<?php echo esc_html($show) ?>">
                    <label for="useremail" class="form-label">Email</label>
                    <input type="email" readonly class="form-control" name="useremail" id="useremail" value="<?php echo esc_html(wp_get_current_user()->user_email) ?>" required>
                </div>
                <div class="mb-3" style="display:<?php echo esc_html($show) ?>">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" name="password" value="" id="password" placeholder="Enter password">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Display Name</label>
                    <input type="text" class="form-control" name="displayName" id="displayName" placeholder="e.g. Admin Laptop, Shop Manager Mobile Phone">
                </div>
                <div class="mb-3 small-option-text">
                    <label class="form-label">Authenticator Type</label>
                    <div class="form-check">
                        <input
                            class="form-check-input"
                            type="radio"
                            name="authenticator_type"
                            id="auth-type-platform"
                            value="platform"
                            checked>
                        <label class="form-check-label" for="auth-type-platform">
                            Platform (e.g., fingerprint/face id on this device)
                        </label>
                    </div>

                    <div class="form-check">
                        <input
                            class="form-check-input"
                            type="radio"
                            name="authenticator_type"
                            id="auth-type-cross"
                            value="cross-platform">
                        <label class="form-check-label" for="auth-type-cross">
                            Cross-Platform (e.g., fingerprint/face id on mobile phone or external security.)
                        </label>
                    </div>
                </div>
                <div class="text-end">
                    <button type="submit" class="button button-primary">Create Passkey</button>
                </div>
            </form>
        </div>
    </div>
</div>