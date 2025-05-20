<?php if (! defined('ABSPATH')) exit; ?>
<div class="wrap">
    <div class="d-flex justify-content-center" style="min-height: 100vh; align-items: flex-start; padding-top: 65px;">
        <div class="card shadow-lg p-3" style="width: 100%; max-width: 900px;">
            <div class="d-flex align-items-center mb-3">
                <?php  // phpcs:disable PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage 
                ?>
                <img src="<?php echo esc_url(ASD_P4SSK3Y_PUBLICURL . 'img/logo-medium.webp'); ?>" style="max-width: 35%; margin-right: 15px;" alt="LOGO">
            </div>

            <h2 class="mb-4">Send Notification</h2>
            <form id="sendNotificationForm">
                <div class="mb-3">
                    <label for="notificationTitle" class="form-label">Notification Title</label>
                    <input type="text" class="form-control" name="notificationTitle" id="notificationTitle" placeholder="Enter notification title" required>
                </div>
                <div class="mb-3">
                    <label for="notificationBody" class="form-label">Notification Body</label>
                    <textarea class="form-control" name="notificationBody" id="notificationBody" rows="4" placeholder="Enter notification message" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="notificationUrl" class="form-label">Target URL (optional)</label>
                    <div class="input-group">
                        <input type="url" class="form-control" value="<?php echo esc_url($url) ?>" name="notificationUrl" id="notificationUrl" placeholder="https://example.com">
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#productModal">Choose Product</button>
                    </div>
                </div>
                <div class="text-end">
                    <button type="submit" class="button button-primary">Send Notification</button>
                </div>
            </form>
            <div id="notificationStatus" class="mt-3"></div>
        </div>
    </div>
</div>
<div class="modal" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productModalLabel">Select Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="text" id="searchInput" class="form-control mb-3" placeholder="Search for products...">
                <table class="wp-list-table widefat fixed striped" id="productTable">
                    <thead>
                        <tr>
                            <th scope="col">Product Name</th>
                            <th scope="col" width="100px">Price</th>
                            <th scope="col">Categories</th>
                            <th scope="col">Tags</th>
                            <th scope="col" width="100px">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="productList"></tbody>
                </table>
                <div id="paginationControls" class="d-flex justify-content-center mt-3"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>