<?php if (! defined('ABSPATH')) exit; ?>
<div class="wrap">
    <div class="d-flex justify-content-center" style="align-items: center; padding-top: 65px;">
        <div class="card shadow-lg p-3" style="width: 100%; max-width: 900px;">
            <div class="d-flex align-items-center mb-3">
                <?php  // phpcs:disable PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage 
                ?>
                <img src="<?php echo esc_url(ASD_P4SSK3Y_PUBLICURL . 'img/logo-medium.webp'); ?>" style="max-width: 35%; margin-right: 15px;" alt="LOGO">
            </div>
            <!-- Tab Navigation -->
            <ul class="nav nav-tabs" id="settingsTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="general-settings-tab" data-bs-toggle="tab" href="#general-settings" role="tab" aria-controls="general-settings" aria-selected="true">General Settings</a>
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
                        <div class="mb-3 small-option-text">
                            <label class="form-label fw-bold">Admin Login Method?</label>
                            <div class="form-text text-muted mt-0 pt-0">
                                <i>Choose how administrators log in to the admin page: with the traditional login form, a passkey button, or a combination of both.</i>
                            </div>
                            <div class="form-select-wrapper mt-3">
                                <select
                                    class="form-select"
                                    name="asd_p4ssk3y_admin_login_form_style"
                                    id="asd_p4ssk3y_admin_login_form_style">
                                    <option value="form_only" <?php selected(get_option('asd_p4ssk3y_admin_login_form_style'), 'form_only'); ?>>Classic Login (Username & Password)</option>
                                    <option value="passkey_only" <?php selected(get_option('asd_p4ssk3y_admin_login_form_style'), 'passkey_only'); ?>>Passkey Only</option>
                                    <option value="form_and_passkey" <?php selected(get_option('asd_p4ssk3y_admin_login_form_style'), 'form_and_passkey'); ?>>Hybrid Login (Form + Passkey)</option>
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
                                        name="asd_p4ssk3y_admin_password_confirmation"
                                        id="asd_p4ssk3y_admin_password_confirmation-n"
                                        value="N"
                                        <?php checked(get_option('asd_p4ssk3y_admin_password_confirmation'), 'N'); ?>>
                                    <label class="form-check-label" for="asd_p4ssk3y_admin_password_confirmation-n">
                                        No
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input
                                        class="form-check-input"
                                        type="radio"
                                        name="asd_p4ssk3y_admin_password_confirmation"
                                        id="asd_p4ssk3y_admin_password_confirmation-y"
                                        value="Y"
                                        <?php checked(get_option('asd_p4ssk3y_admin_password_confirmation'), 'Y'); ?>>
                                    <label class="form-check-label" for="asd_p4ssk3y_admin_password_confirmation-y">
                                        Yes
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="text-end">
                            <button type="submit" id="save-settings" class="button button-primary">Save Settings</button>
                        </div>
                    </form>
                </div>

                <!-- Template Settings Tab -->
                <div class="tab-pane fade" id="template-settings" role="tabpanel" aria-labelledby="template-settings-tab">
                    <?php if ($smtpread == 'readonly'): ?>
                        <div class="mb-3 small-option-text">
                            <div class="form-text text-muted">
                                <i>Upgrade your plan to enable access to and configure your email template.</i>
                                <a href="<?php echo esc_url(admin_url('admin.php?page=asd-upgrade-package')); ?>">Upgrade now</a>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php  // phpcs:disable PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage
                    ?>
                    <img src="<?php echo esc_url(ASD_P4SSK3Y_PUBLICURL . 'img/template-setting.webp'); ?>" class="card-img-top" alt="Template Setting">
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