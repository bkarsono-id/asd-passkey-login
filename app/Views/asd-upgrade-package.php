<?php if (! defined('ABSPATH')) exit; ?>
<div class="wrap">
    <div class="d-flex justify-content-center" style="min-height: 100vh; align-items: flex-start; padding-top: 65px;">
        <div class="container mt-3">
            <div class="section-title text-center mb-2">
                <h2>Pricing</h2>
            </div>
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card text-center h-100  m-1 p-2 <?php echo (($existing === 1) ? 'existing' : 'standard') ?> ">
                        <div class="card-body">
                            <h3>Freemium</h3>
                            <h4>$0</h4>
                            <p><span>Free</span></p>
                            <ul class="list-unstyled text-start">
                                <li><i class="dashicons dashicons-yes text-success"></i> Up to 5 Users</li>
                                <li><i class="dashicons dashicons-yes text-success"></i> Unlimited Login</li>
                                <li><i class="dashicons dashicons-yes text-success"></i> 1-day Log</li>
                                <li><i class="dashicons dashicons-yes text-success"></i> Platform & Cross Platform</li>
                                <li><i class="dashicons dashicons-no text-danger"></i> Support</li>
                                <li><i class="dashicons dashicons-yes text-success"></i> WordPress Plugin</li>
                                <li><i class="dashicons dashicons-no text-danger"></i> WooCommerce Plugin</li>
                                <li><i class="dashicons dashicons-no text-danger"></i> Custom SMTP</li>
                                <li><i class="dashicons dashicons-no text-danger"></i> Custom Email Notification</li>
                                <li><i class="dashicons dashicons-no text-danger"></i> Web Push Notification</li>
                            </ul>
                            <?php echo get_option('asd_p4ssk3y_membership') === "freemium" ? '<a href="#"  class="btn btn-sm btn-light  mt-3 w-100">Current Package</a>
                       ' : ""; ?>
                        </div>
                    </div>
                </div>
                <!-- Starter Plan -->
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card text-center h-100 <?php echo ($existing === 2 ? 'existing' :  'standard') ?>  m-1 p-2 ">
                        <div class="card-body">
                            <h3>Starter</h3>
                            <h4>$3</h4>
                            <p><span>per month/domain</span></p>
                            <ul class="list-unstyled text-start">
                                <li><i class="dashicons dashicons-yes text-success"></i> Up to 1,000 Users</li>
                                <li><i class="dashicons dashicons-yes text-success"></i> Unlimited Login</li>
                                <li><i class="dashicons dashicons-yes text-success"></i> 14-day Log</li>
                                <li><i class="dashicons dashicons-yes text-success"></i> Platform & Cross Platform</li>
                                <li><i class="dashicons dashicons-yes text-success"></i> Office Hour Support (UTC +7)</li>
                                <li><i class="dashicons dashicons-yes text-success"></i> WordPress Plugin</li>
                                <li><i class="dashicons dashicons-yes text-success"></i> WooCommerce Plugin</li>
                                <li><i class="dashicons dashicons-no text-danger"></i> Custom SMTP</li>
                                <li><i class="dashicons dashicons-no text-danger"></i> Custom Email Notification</li>
                                <li><i class="dashicons dashicons-no text-danger"></i> Web Push Notification</li>
                            </ul>
                            <?php
                            if ($existing <= 2) {
                                echo (get_option('asd_p4ssk3y_membership') === "starter") ? '<a href="#"  class="btn btn-sm btn-light  mt-3 w-100">Current Package</a>
                       ' : '<a href="https://passwordless.alciasolusidigital.com/upgrade/wp?package=starter" target="_blank" class="btn btn-primary mt-3 w-100">Get Starter</a>';
                            }
                            ?>

                        </div>
                    </div>
                </div>
                <!-- Growth Plan -->
                <div class="col-lg-3 col-md-6 mb-4">
                    <?php echo ($choose === 3 ? '<div class="recommended-badge">Recommended</div>' :  '') ?>
                    <div class="card text-center h-100 m-1 p-2 <?php echo ($existing === 3 ? 'existing' : 'standard') ?> <?php echo ($choose === 3 ? 'choose' :  '') ?> ">
                        <div class="card-body">
                            <h3>Growth</h3>
                            <h4>$6</h4>
                            <p><span>per month/domain</span></p>
                            <ul class="list-unstyled text-start">
                                <li><i class="dashicons dashicons-yes text-success"></i> Up to <b>5,000</b> Users</li>
                                <li><i class="dashicons dashicons-yes text-success"></i> Unlimited Login</li>
                                <li><i class="dashicons dashicons-yes text-success"></i> 14-days Log</li>
                                <li><i class="dashicons dashicons-yes text-success"></i> Platform & Cross Platform</li>
                                <li><i class="dashicons dashicons-yes text-success"></i> Office Hour Support (UTC +7)</li>
                                <li><i class="dashicons dashicons-yes text-success"></i> WordPress Plugin</li>
                                <li><i class="dashicons dashicons-yes text-success"></i> WooCommerce Plugin</li>
                                <li><i class="dashicons dashicons-yes text-success"></i> Custom SMTP</li>
                                <li><i class="dashicons dashicons-yes text-success"></i> Custom Email Notification</li>
                                <li><i class="dashicons dashicons-no text-danger"></i> Web Push Notification</li>
                            </ul>
                            <?php
                            if ($existing <= 3) {
                                echo get_option('asd_p4ssk3y_membership') === "growth" ? '<a href="#"  class="btn btn-sm btn-light  mt-3 w-100">Current Package</a>
                                ' : '<a href="https://passwordless.alciasolusidigital.com/upgrade/wp?package=growth" target="_blank" class="btn btn-primary mt-3 w-100">Get  Growth</a>';
                            } ?>
                        </div>
                    </div>
                </div>
                <!-- Scale Plan -->
                <div class="col-lg-3 col-md-6 mb-4">
                    <?php echo ($choose === 4 ? '<div class="recommended-badge">Recommended</div>' :  '') ?>
                    <div class="card text-center h-100 m-1 p-2 <?php echo ($existing === 4 ? 'existing' : 'standard') ?> <?php echo ($choose === 4 ? 'choose' :  '') ?>">
                        <div class="card-body">
                            <h3>Scale</h3>
                            <h4>$15</h4>
                            <p><span>per month/domain</span></p>
                            <ul class="list-unstyled text-start">
                                <li><i class="dashicons dashicons-yes text-success"></i> <b>Unlimited Users</b></li>
                                <li><i class="dashicons dashicons-yes text-success"></i> Unlimited Login</li>
                                <li><i class="dashicons dashicons-yes text-success"></i> <b>30-days Log</b></li>
                                <li><i class="dashicons dashicons-yes text-success"></i> Platform & Cross Platform</li>
                                <li><i class="dashicons dashicons-yes text-success"></i> <b>24x7 Support</b></li>
                                <li><i class="dashicons dashicons-yes text-success"></i> WordPress Plugin</li>
                                <li><i class="dashicons dashicons-yes text-success"></i> WooCommerce Plugin</li>
                                <li><i class="dashicons dashicons-yes text-success"></i> Custom SMTP</li>
                                <li><i class="dashicons dashicons-yes text-success"></i> Custom Email Notification</li>
                                <li><i class="dashicons dashicons-yes text-success"></i> Web Push Notification</li>
                            </ul>
                            <?php echo get_option('asd_p4ssk3y_membership') === "scale" ? '<a href="#"  class="btn btn-sm btn-light  mt-3 w-100">Current Package</a>
                       ' : '<a href="https://passwordless.alciasolusidigital.com/upgrade/wp?package=scale" target="_blank" class="btn btn-primary mt-3 w-100">Get Scale</a>'; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-12 col-md-12 mb-4">
        <div class="card text-center mx-auto">
            <div class="card-body">
                <h3>Custom Solution</h3>
                <p style="font-size:16px;">
                    For large scale, dedicated server, on-premise server, or custom integration. Contact us for custom solutions tailored specifically to your business requirements, providing flexibility and personalized support to achieve your goals.
                </p>
                <a class="btn btn-primary mt-3" target="_blank" href="https://api.whatsapp.com/send?phone=+6281310778810&amp;text=Good day,i want custom authentication solution.">Contact Us</a>
            </div>
        </div>
    </div>
</div>