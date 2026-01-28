// Profile Page JavaScript
(function() {
    'use strict';

    function initTabs() {
        // Tab Navigation
        const tabLinks = document.querySelectorAll('.tab-link');
        const tabPanes = document.querySelectorAll('.tab-pane');
        const tabItems = document.querySelectorAll('.tab-item');

        if (tabLinks.length === 0 || tabPanes.length === 0) {
            return;
        }

        // Function to switch tabs
        function switchTab(targetTab) {
            if (!targetTab) {
                return;
            }

            // Remove active class from all tabs, items and panes
            tabLinks.forEach(l => l.classList.remove('active'));
            tabItems.forEach(item => item.classList.remove('active'));
            tabPanes.forEach(p => p.classList.remove('active'));

            // Find and activate the target tab
            const targetLink = document.querySelector(`.tab-link[data-tab="${targetTab}"]`);
            const targetPane = document.getElementById(targetTab);

            if (targetLink && targetPane) {
                targetLink.classList.add('active');
                const parentItem = targetLink.closest('.tab-item');
                if (parentItem) {
                    parentItem.classList.add('active');
                }
                targetPane.classList.add('active');
                return true;
            }
            return false;
        }

        // Add click event to all tab links
        tabLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const targetTab = this.getAttribute('data-tab');
                if (targetTab) {
                    // Save current scroll position
                    const scrollY = window.scrollY;
                    const scrollX = window.scrollX;
                    
                    // Switch tab
                    switchTab(targetTab);
                    
                    // Update URL hash without scrolling
                    if (history.pushState) {
                        history.pushState(null, null, '#' + targetTab);
                        // Restore scroll position immediately
                        window.scrollTo(scrollX, scrollY);
                    } else {
                        // Fallback for older browsers
                        const currentScroll = window.pageYOffset || document.documentElement.scrollTop;
                        window.location.hash = targetTab;
                        window.scrollTo(0, currentScroll);
                    }
                }
            });
        });

        // Handle initial hash on page load
        function handleInitialTab() {
            const hash = window.location.hash.substring(1);
            if (hash) {
                const switched = switchTab(hash);
                if (!switched) {
                    // If hash doesn't match, activate first tab
                    const firstTab = tabLinks[0]?.getAttribute('data-tab');
                    if (firstTab) {
                        switchTab(firstTab);
                    }
                }
            } else {
                // Activate first tab by default if no hash
                const firstTab = tabLinks[0]?.getAttribute('data-tab');
                if (firstTab) {
                    switchTab(firstTab);
                }
            }
        }

        // Handle hash change (back/forward browser buttons)
        window.addEventListener('hashchange', function() {
            const hash = window.location.hash.substring(1);
            if (hash) {
                // Save current scroll position
                const scrollY = window.scrollY;
                const scrollX = window.scrollX;
                
                // Switch tab
                switchTab(hash);
                
                // Restore scroll position
                setTimeout(() => {
                    window.scrollTo(scrollX, scrollY);
                }, 0);
            }
        });

        // Initialize tabs
        handleInitialTab();
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initTabs);
    } else {
        // DOM is already ready
        initTabs();
    }

    // Auto-dismiss alerts after 5 seconds
    function initAlerts() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                const closeBtn = alert.querySelector('.btn-close');
                if (closeBtn) {
                    closeBtn.click();
                } else {
                    alert.style.transition = 'opacity 0.3s ease';
                    alert.style.opacity = '0';
                    setTimeout(() => {
                        alert.style.display = 'none';
                    }, 300);
                }
            }, 5000);
        });
    }

    // Form validation enhancement
    function initForms() {
        const forms = document.querySelectorAll('.profile-form');
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                if (!form.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                form.classList.add('was-validated');
            });
        });
    }

    // Password confirmation validation
    function initPasswordValidation() {
        const passwordInput = document.getElementById('password');
        const passwordConfirmInput = document.getElementById('password_confirmation');

        if (passwordInput && passwordConfirmInput) {
            passwordConfirmInput.addEventListener('input', function() {
                const password = passwordInput.value;
                const confirmPassword = this.value;

                if (confirmPassword && password !== confirmPassword) {
                    this.setCustomValidity('Mật khẩu xác nhận không khớp');
                } else {
                    this.setCustomValidity('');
                }
            });
        }
    }

    // Phone number formatting
    function initPhoneFormatting() {
        const phoneInput = document.getElementById('phone');
        if (phoneInput) {
            phoneInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 0 && value[0] !== '0') {
                    value = '0' + value;
                }
                if (value.length > 10) {
                    value = value.slice(0, 10);
                }
                e.target.value = value;
            });
        }
    }

    // Address Modal Functions
    window.openAddressModal = function(addressId = null) {
        const modal = document.getElementById('addressModal');
        const form = document.getElementById('addressForm');
        const formMethod = document.getElementById('addressFormMethod');
        const modalTitle = document.getElementById('addressModalTitle');
        const addressIdInput = document.getElementById('addressId');

        if (!modal || !form) {
            return;
        }

        // Reset form
        form.reset();
        formMethod.innerHTML = '';

        if (addressId) {
            // Edit mode
            modalTitle.textContent = 'Sửa địa chỉ';
            addressIdInput.value = addressId;
            
            // Get address data from DOM
            const addressItem = document.getElementById('address-' + addressId);
            if (addressItem) {
                const nameEl = addressItem.querySelector('.address-name');
                const phoneEl = addressItem.querySelector('.address-phone');
                const detailEl = addressItem.querySelector('.address-detail');
                const locationEl = addressItem.querySelector('.address-location');
                const notesEl = addressItem.querySelector('.address-notes');
                const isDefault = addressItem.querySelector('.badge-primary');

                if (nameEl) document.getElementById('full_name').value = nameEl.textContent.trim();
                if (phoneEl) {
                    const phoneText = phoneEl.textContent.replace(/[^\d+]/g, '').trim();
                    document.getElementById('phone_number').value = phoneText;
                }
                if (detailEl) document.getElementById('detail_address').value = detailEl.textContent.trim();
                if (locationEl) {
                    const locationParts = locationEl.textContent.split(',').map(s => s.trim());
                    if (locationParts.length >= 3) {
                        document.getElementById('ward').value = locationParts[0];
                        document.getElementById('district').value = locationParts[1];
                        document.getElementById('province').value = locationParts[2];
                    }
                }
                if (notesEl) {
                    const notesText = notesEl.textContent.replace(/.*?fa-sticky-note.*?/, '').trim();
                    document.getElementById('notes').value = notesText;
                }
                if (isDefault) {
                    document.getElementById('is_default').checked = true;
                }
            }

            // Set form action and method
            form.action = '/tai-khoan/dia-chi/' + addressId;
            formMethod.innerHTML = '<input type="hidden" name="_method" value="PUT">';
        } else {
            // Add mode
            modalTitle.textContent = 'Thêm địa chỉ mới';
            addressIdInput.value = '';
            form.action = '/tai-khoan/dia-chi';
        }

        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    };

    window.closeAddressModal = function() {
        const modal = document.getElementById('addressModal');
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    };

    // Close modal when clicking outside
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('addressModal');
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) {
                        closeAddressModal();
                    }
                });
            }
        });
    } else {
        const modal = document.getElementById('addressModal');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeAddressModal();
                }
            });
        }
    }

    // Initialize all features
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            initAlerts();
            initForms();
            initPasswordValidation();
            initPhoneFormatting();
        });
    } else {
        initAlerts();
        initForms();
        initPasswordValidation();
        initPhoneFormatting();
    }
})();
