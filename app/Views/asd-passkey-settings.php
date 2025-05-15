<?php if (! defined('ABSPATH')) exit; ?>
<div class="wrap">
    <div class="d-flex justify-content-center" style="align-items: center; padding-top: 65px;">
        <div class="card shadow-lg p-3" style="width: 100%; max-width: 900px;">
            <div class="d-flex align-items-center mb-3">
                <?php  // phpcs:disable PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage 
                ?>
                <img src="<?php echo esc_url(ASD_PUBLICURL . 'img/logo-medium.webp'); ?>" style="max-width: 35%; margin-right: 15px;" alt="LOGO">
            </div>
            <!-- Tab Navigation -->
            <ul class="nav nav-tabs" id="settingsTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="general-settings-tab" data-bs-toggle="tab" href="#general-settings" role="tab" aria-controls="general-settings" aria-selected="true">General Settings</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="smtp-settings-tab" data-bs-toggle="tab" href="#smtp-settings" role="tab" aria-controls="smtp-settings" aria-selected="false">SMTP</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="template-settings-tab" data-bs-toggle="tab" href="#template-settings" role="tab" aria-controls="template-settings" aria-selected="false">Email Template</a>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content mt-3" id="settingsTabContent">
                <!-- General Settings Tab -->
                <div class="tab-pane fade show active" id="general-settings" role="tabpanel" aria-labelledby="general-settings-tab">
                    <form id="passkeySettingsForm">
                        <!-- Textbox Section -->
                        <div class="form-text text-muted">
                            <i>After updating the package and completing the payment, click the button to synchronize the API settings automatically.</i>
                        </div>
                        <div id="usernameTextbox" class="mb-3 mt-3 d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center" style="flex-grow: 1;">
                                <input
                                    readonly
                                    type="text"
                                    class="form-control"
                                    id="asd_package_name"
                                    name="asd_package_name"
                                    value="<?php echo esc_attr(get_option('asd_membership', '')); ?>">
                            </div>
                            <button type="button" id="btnSyncPackage" class="button button-secondary ms-1">Sync Package</button>
                        </div>

                        <h2 class="settings-heading">Freemium Settings</h2>
                        <div class="mb-3 small-option-text">
                            <label class="form-label fw-bold">Admin Login Method?</label>
                            <div class="form-text text-muted mt-0 pt-0">
                                <i>Choose how administrators log in to the admin page: with the traditional login form, a passkey button, or a combination of both.</i>
                            </div>
                            <div class="form-select-wrapper mt-3">
                                <select
                                    class="form-select"
                                    name="asd_admin_login_form_style"
                                    id="asd_admin_login_form_style">
                                    <option value="form_only" <?php selected(get_option('asd_admin_login_form_style'), 'form_only'); ?>>Classic Login (Username & Password)</option>
                                    <option value="passkey_only" <?php selected(get_option('asd_admin_login_form_style'), 'passkey_only'); ?>>Passkey Only</option>
                                    <option value="form_and_passkey" <?php selected(get_option('asd_admin_login_form_style'), 'form_and_passkey'); ?>>Hybrid Login (Form + Passkey)</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3 small-option-text mt-4">
                            <label class="form-label fw-bold">Enable Password Confirmation During Passkey Registration?</label>
                            <div class="form-text text-muted mt-0 pt-0">
                                <i>Require administrators to confirm their password during passkey registration. This adds an extra layer of security by verifying credentials before generating and storing the passkey for authentication purposes.</i>
                            </div>
                            <div class="form-check-wrapper mt-3">
                                <div class="form-check  form-check-inline">
                                    <input
                                        class="form-check-input"
                                        type="radio"
                                        name="asd_admin_password_confirmation"
                                        id="asd_admin_password_confirmation-n"
                                        value="N"
                                        <?php checked(get_option('asd_admin_password_confirmation'), 'N'); ?>>
                                    <label class="form-check-label" for="asd_admin_password_confirmation-n">
                                        No
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input
                                        class="form-check-input"
                                        type="radio"
                                        name="asd_admin_password_confirmation"
                                        id="asd_admin_password_confirmation-y"
                                        value="Y"
                                        <?php checked(get_option('asd_admin_password_confirmation'), 'Y'); ?>>
                                    <label class="form-check-label" for="asd_admin_password_confirmation-y">
                                        Yes
                                    </label>
                                </div>
                            </div>
                        </div>

                        <?php if ($wooaccount): ?>
                            <h2 class="settings-heading pro">Pro Settings</h2>
                            <?php if (is_pro_license() === false) { ?>
                                <div class="alert alert-info p-2" role="alert">
                                    <?php printf(
                                        'Upgrade your plan <a href="%s">Learn more</a>.',
                                        esc_url(admin_url('admin.php?page=asd-upgrade-package'))
                                    ); ?>
                                </div>
                            <?php
                            }
                            ?>
                            <div class="mb-3 small-option-text">
                                <label class="form-label fw-bold">WooCommerce Login Method?</label>
                                <div class="form-text text-muted mt-0 pt-0">
                                    <i>Choose how WooCommerce Customer log in to the shop page: with the traditional login form, a passkey button, or a combination of both.</i>
                                </div>
                                <div class="form-select-wrapper mt-3">
                                    <select
                                        <?php echo is_pro_license() === true ? '' : 'disabled' ?>
                                        class="form-select"
                                        name="asd_woo_login_form_style"
                                        id="asd_woo_login_form_style">
                                        <option value="form_only" <?php selected(get_option('asd_woo_login_form_style'), 'form_only'); ?>>Classic Login (Username & Password)</option>
                                        <option value="passkey_only" <?php selected(get_option('asd_woo_login_form_style'), 'passkey_only'); ?>>Passkey Only</option>
                                        <option value="form_and_passkey" <?php selected(get_option('asd_woo_login_form_style'), 'form_and_passkey'); ?>>Hybrid Login (Form + Passkey)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3 small-option-text mt-4">
                                <label class="form-label fw-bold">Enable Password Confirmation During Passkey Registration (WooCommerce)?</label>
                                <div class="form-text text-muted mt-0 pt-0">
                                    <i>Require WooCommerce Customer to confirm their password during passkey registration. This adds an extra layer of security by verifying credentials before generating and storing the passkey for authentication purposes.</i>
                                </div>
                                <div class="form-check-wrapper mt-3">
                                    <div class="form-check  form-check-inline">
                                        <input
                                            <?php echo is_pro_license() === true ? '' : 'disabled' ?>
                                            class="form-check-input"
                                            type="radio"
                                            name="asd_woo_password_confirmation"
                                            id="asd_woo_password_confirmation-n"
                                            value="N"
                                            <?php checked(get_option('asd_woo_password_confirmation'), 'N'); ?>>
                                        <label class="form-check-label" for="asd_woo_password_confirmation-n">
                                            No
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input
                                            <?php echo is_pro_license() === true ? '' : 'disabled' ?>
                                            class="form-check-input"
                                            type="radio"
                                            name="asd_woo_password_confirmation"
                                            id="asd_woo_password_confirmation-y"
                                            value="Y"
                                            <?php checked(get_option('asd_woo_password_confirmation'), 'Y'); ?>>
                                        <label class="form-check-label" for="asd_woo_password_confirmation-y">
                                            Yes
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3 small-option-text mt-4">
                                <label class="form-label fw-bold">Enable FedCM (Google) Login?</label>
                                <div class="form-text text-muted mt-0 pt-0">
                                    <i>Enable login page to use FedCM (Federated Credential Management) for streamlined, secure login without relying on traditional passwords.</i>
                                </div>
                                <div class="form-select-wrapper mt-3">
                                    <select
                                        <?php echo is_pro_license() === true ? '' : 'disabled' ?>
                                        class="form-select"
                                        name="asd_woo_login_fedcm_form"
                                        id="asd_woo_login_fedcm_form">
                                        <option value="disabled" <?php selected(get_option('asd_woo_login_fedcm_form'), 'disabled'); ?>>Disabled</option>
                                        <option value="admin_page" <?php selected(get_option('asd_woo_login_fedcm_form'), 'admin_page'); ?>>Administrator Login Page</option>
                                        <option value="woo_page" <?php selected(get_option('asd_woo_login_fedcm_form'), 'woo_page'); ?>>WooCommerce Login Page</option>
                                        <option value="both" <?php selected(get_option('asd_woo_login_fedcm_form'), 'both'); ?>>Both (Administrator & WooCommerce)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3 small-option-text mt-4">
                                <label class="form-label fw-bold">IDP FedCM (Identity Provider)</label>
                                <div class="form-text text-muted mt-0 pt-0">
                                    <i>An entity that provides credentials or authentication services through the FedCM API.</i>
                                </div>
                                <div class="form-select-wrapper mt-3">
                                    <select
                                        <?php echo is_pro_license() === true ? '' : 'disabled' ?>
                                        class="form-select"
                                        name="asd_woo_idp_provider"
                                        id="asd_woo_idp_provider">
                                        <option value="google" <?php selected(get_option('asd_woo_idp_provider'), 'google'); ?>>Google</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3 small-option-text  mt-4">
                                <label class="form-label fw-bold">Client ID OAuth 2.0 Google</label>
                                <div class="form-text text-muted mt-0 pt-0">
                                    <i>Insert client id if you enable FedCM and use google provider, <b>leave blank if you using JWT EAuth Provider or disable FedCM Login</b>. Learn about <a href="https://developers.google.com/identity/protocols/oauth2" target="_blank"> OAuth 2.0</a> or create a <a href="https://console.cloud.google.com/apis/credentials" target="_blank">google client id</a>.</i>
                                </div>
                                <div class="form-check-wrapper mt-3">
                                    <input
                                        <?php echo is_pro_license() === true ? '' : 'disabled' ?>
                                        style="width: 100%; min-width: 600px;"
                                        type="text"
                                        class="form-control"
                                        id="asd_google_client_id"
                                        name="asd_google_client_id"
                                        value="<?php echo get_option('asd_google_client_id'); ?>"
                                        placeholder="YOUR_GOOGLE_CLIENT_ID.apps.googleusercontent.com">
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="text-end">
                            <button type="submit" id="save-settings" class="button button-primary">Save Settings</button>
                        </div>
                    </form>
                </div>

                <!-- SMTP Settings Tab -->
                <div class="tab-pane fade" id="smtp-settings" role="tabpanel" aria-labelledby="smtp-settings-tab">
                    <form id="passkeySMTPForm">
                        <?php if ($smtpread == 'readonly'): ?>
                            <div class="mb-3 small-option-text">
                                <div class="form-text text-muted">
                                    <i>Upgrade your plan to enable access to and configure your SMTP settings.</i>
                                    <a href="<?php echo esc_url(admin_url('admin.php?page=asd-upgrade-package')); ?>">Upgrade now</a>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="mb-3 small-option-text">
                            <label class="form-label">SMTP Host</label>
                            <input
                                <?php echo esc_attr($smtpread); ?>
                                type="text"
                                class="form-control"
                                id="asd_smtp_host"
                                name="asd_smtp_host"
                                value="<?php echo esc_attr(get_option('asd_smtp_host', '')); ?>" />
                        </div>

                        <div class="mb-3 small-option-text">
                            <label class="form-label">SMTP Port</label>
                            <div>
                                <input
                                    <?php echo esc_attr($smtpread); ?>
                                    type="text"
                                    class="form-control"
                                    id="asd_smtp_port"
                                    name="asd_smtp_port"
                                    value="<?php echo esc_attr(get_option('asd_smtp_port', '')); ?>" />
                            </div>
                        </div>

                        <div class="mb-3 small-option-text">
                            <label class="form-label">SMTP User</label>
                            <div>
                                <input
                                    <?php echo esc_attr($smtpread); ?>
                                    type="text"
                                    class="form-control"
                                    id="asd_smtp_user"
                                    name="asd_smtp_user"
                                    value="<?php echo esc_attr(get_option('asd_smtp_user', '')); ?>" />
                            </div>
                        </div>

                        <div class="mb-3 small-option-text">
                            <label class="form-label">SMTP Password</label>
                            <div>
                                <input
                                    <?php echo esc_attr($smtpread); ?>
                                    type="text"
                                    class="form-control"
                                    id="asd_smtp_password"
                                    name="asd_smtp_password"
                                    value="<?php echo esc_attr(get_option('asd_smtp_password', '')); ?>" />
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="button" id="test-smtp" class="button button-primary" <?php echo ($smtpread == 'readonly') ? 'disabled' : ''; ?>>Test SMTP</button>
                            <button type="submit" id="save-smtp" class="button button-primary" <?php echo ($smtpread == 'readonly') ? 'disabled' : ''; ?>>Save SMTP</button>
                        </div>
                    </form>
                </div>

                <!-- Template Settings Tab -->
                <div class="tab-pane fade" id="template-settings" role="tabpanel" aria-labelledby="template-settings-tab">
                    <?php  // phpcs:disable PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage
                    ?>
                    <img src="<?php echo esc_url(ASD_PUBLICURL . 'img/template-setting.webp'); ?>" class="card-img-top" alt="Template Setting">
                    <h5 class="text-center mt-3">Email Template Creation and Modification</h5>
                    <p class="text-center">
                        The creation and modification of email templates can only be done through the website. Users are not allowed to manually alter or create email templates outside the platform. To manage your email templates, simply log in to the website, navigate to the email settings section, and use the provided tools to create or edit templates. This ensures that all templates are consistent, secure, and easy to manage directly from the platform without requiring external interventions.
                    </p>
                    <p class=" text-center">
                        <a href="https://passwordless.alciasolusidigital.com/emailtemplate" target="_blank" class="btn btn-primary">Go website</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
</div>