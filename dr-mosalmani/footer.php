<footer class="site-footer mb-5">
    <div class="container">
        <div class="footer-section">
            <div class="footer-top">
                
                <!-- Main section: Includes 4 columns (Quick Access, Services, Systems, and Contact Us) -->
                <div class="footer-links-wrapper">
                    <!-- Quick Access -->
                    <div class="footer-col">
                        <h4 class="footer-title footer-quick-access">
                            <?php pll_e('دسترسی سریع'); ?>
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 9 10" fill="none"><rect x="4.24219" y="0.757324" width="6" height="6" transform="rotate(45 4.24219 0.757324)" fill="currentColor"></rect></svg>
                        </h4>
                        <ul class="footer-menu">
                            <li class="footer-item"><a href="<?php echo home_url('/'); ?>"><?php pll_e('صفحه اصلی'); ?></a></li>
                            <li class="footer-item"><a href="<?php echo home_url('/about'); ?>"><?php pll_e('درباره ما'); ?></a></li>
                            <li class="footer-item"><a href="<?php echo home_url('/contact'); ?>"><?php pll_e('تماس با ما'); ?></a></li>
                        </ul>
                    </div>

                    <!-- Services -->
                    <div class="footer-col">
                        <h4 class="footer-title footer-services">
                            <?php pll_e('خدمات'); ?>
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 9 10" fill="none"><rect x="4.24219" y="0.757324" width="6" height="6" transform="rotate(45 4.24219 0.757324)" fill="currentColor"></rect></svg>
                        </h4>
                        <ul class="footer-menu">
                            <li class="footer-item"><a href="#"><?php pll_e('مشاوره حقوقی'); ?></a></li>
                            <li class="footer-item"><a href="#"><?php pll_e('وکالت دعاوی'); ?></a></li>
                            <li class="footer-item"><a href="#"><?php pll_e('تنظیم قرارداد'); ?></a></li>
                        </ul>
                    </div>

                    <!-- Related Systems -->
                    <div class="footer-col">
                        <h4 class="footer-title footer-useful-links">
                            <?php pll_e('سامانه‌های مرتبط'); ?>
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 9 10" fill="none"><rect x="4.24219" y="0.757324" width="6" height="6" transform="rotate(45 4.24219 0.757324)" fill="currentColor"></rect></svg>
                        </h4>
                        <?php
                            wp_nav_menu([
                                'theme_location' => 'footer_menu',
                                'container' => false,
                                'menu_class' => 'footer-menu',
                                'li_class' => 'footer-item',
                            ]);
                        ?>
                    </div>

                    <!-- Contact Us -->
                    <div class="footer-col">
                        <h4 class="footer-title footer-contact-us">
                            <?php pll_e('ارتباط با ما'); ?>
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 9 10" fill="none"><rect x="4.24219" y="0.757324" width="6" height="6" transform="rotate(45 4.24219 0.757324)" fill="currentColor"></rect></svg>
                        </h4>
                        <ul class="footer-contact">
                            <li>
                                <a href="tel:02112345678">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M18.3083 15.2734C18.3083 15.5734 18.2417 15.8817 18.1 16.1817C17.9583 16.4817 17.775 16.765 17.5333 17.0317C17.125 17.4817 16.675 17.8067 16.1667 18.015C15.6667 18.2234 15.125 18.3317 14.5417 18.3317C13.6917 18.3317 12.7833 18.1317 11.825 17.7234C10.8667 17.315 9.90833 16.765 8.95833 16.0734C8 15.3734 7.09167 14.5984 6.225 13.74C5.36667 12.8734 4.59167 11.965 3.9 11.015C3.21667 10.065 2.66667 9.11504 2.26667 8.17337C1.86667 7.22337 1.66667 6.31504 1.66667 5.44837C1.66667 4.88171 1.76667 4.34004 1.96667 3.84004C2.16667 3.33171 2.48333 2.86504 2.925 2.44837C3.45833 1.92337 4.04167 1.66504 4.65833 1.66504C4.89167 1.66504 5.125 1.71504 5.33333 1.81504C5.55 1.91504 5.74167 2.06504 5.89167 2.28171L7.825 5.00671C7.975 5.21504 8.08333 5.40671 8.15833 5.59004C8.23333 5.76504 8.275 5.94004 8.275 6.09837C8.275 6.29837 8.21667 6.49837 8.1 6.69004C7.99167 6.88171 7.83333 7.08171 7.63333 7.28171L7 7.94004C6.90833 8.03171 6.86667 8.14004 6.86667 8.27337C6.86667 8.34004 6.875 8.39837 6.89167 8.46504C6.91667 8.53171 6.94167 8.58171 6.95833 8.63171C7.10833 8.90671 7.36667 9.26504 7.73333 9.69837C8.10833 10.1317 8.50833 10.5734 8.94167 11.015C9.39167 11.4567 9.825 11.865 10.2667 12.24C10.7 12.6067 11.0583 12.8567 11.3417 13.0067C11.3833 13.0234 11.4333 13.0484 11.4917 13.0734C11.5583 13.0984 11.625 13.1067 11.7 13.1067C11.8417 13.1067 11.95 13.0567 12.0417 12.965L12.675 12.34C12.8833 12.1317 13.0833 11.9734 13.275 11.8734C13.4667 11.7567 13.6583 11.6984 13.8667 11.6984C14.025 11.6984 14.1917 11.7317 14.375 11.8067C14.5583 11.8817 14.75 11.99 14.9583 12.1317L17.7167 14.09C17.9333 14.24 18.0833 14.415 18.175 14.6234C18.2583 14.8317 18.3083 15.04 18.3083 15.2734Z" stroke="currentColor" stroke-miterlimit="10"></path><path opacity="0.4" d="M15.4167 7.49772C15.4167 6.99772 15.025 6.23105 14.4417 5.60605C13.9083 5.03105 13.2 4.58105 12.5 4.58105" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"></path><path opacity="0.4" d="M18.3333 7.49837C18.3333 4.27337 15.725 1.66504 12.5 1.66504" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                                    <span><?php pll_e('02112345678'); // شماره خود را جایگزین کنید ?></span>
                                </a>
                            </li>
                            <li>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M10 16.248C13.4518 16.248 16.25 13.4498 16.25 9.99805C16.25 6.54627 13.4518 3.74805 10 3.74805C6.54822 3.74805 3.75 6.54627 3.75 9.99805C3.75 13.4498 6.54822 16.248 10 16.248Z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"></path><path opacity="0.4" d="M10 12.498C11.3807 12.498 12.5 11.3788 12.5 9.99805C12.5 8.61733 11.3807 7.49805 10 7.49805C8.61929 7.49805 7.5 8.61733 7.5 9.99805C7.5 11.3788 8.61929 12.498 10 12.498Z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"></path><path opacity="0.4" d="M10 3.33171V1.66504" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"></path><path opacity="0.4" d="M3.33333 9.99805H1.66667" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"></path><path opacity="0.4" d="M10 16.665V18.3317" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"></path><path opacity="0.4" d="M16.6667 9.99805H18.3333" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                                <span><?php pll_e('تهران، خیابان یاس، پلاک 9'); ?></span>
                            </li>
                            <li>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M14.1667 17.0817H5.83333C3.33333 17.0817 1.66667 15.8317 1.66667 12.915V7.08171C1.66667 4.16504 3.33333 2.91504 5.83333 2.91504H14.1667C16.6667 2.91504 18.3333 4.16504 18.3333 7.08171V12.915C18.3333 15.8317 16.6667 17.0817 14.1667 17.0817Z" stroke="currentColor" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path><path opacity="0.4" d="M14.1667 7.49805L11.5583 9.58138C10.7 10.2647 9.29166 10.2647 8.43333 9.58138L5.83333 7.49805" stroke="currentColor" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                                <a href="mailto:info@example.com"><span><?php pll_e('info@example.com'); ?></span></a>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Social Media -->
                <div class="footer-social-box">
                    <a href="https://instagram.com/yourpage" target="_blank" rel="noopener noreferrer" class="social-item instagram-item">
                        <div class="social-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
                        </div>
                        <div class="social-text"><?php pll_e('اینستاگرام'); ?></div>
                    </a>
                    <a href="https://ble.ir/yourpage" target="_blank" rel="noopener noreferrer" class="social-item bale-item">
                        <div class="social-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                        </div>
                        <div class="social-text"><?php pll_e('بله'); ?></div>
                    </a>
                </div>

            </div>
            
            <!-- bottom -->
            <div class="footer-bottom mt-5">
                <p>
                    © <?php echo date('Y'); ?>
                    <?php bloginfo('name'); ?> - <?php pll_e('تمامی حقوق محفوظ است'); ?>
                </p>
            </div>
        </div>
    </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>