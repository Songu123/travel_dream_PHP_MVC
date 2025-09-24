// ==========================================================================
// TravelDream - Custom JavaScript
// ==========================================================================

(function() {
    'use strict';

    // DOM Elements
    const navbar = document.querySelector('.navbar');
    const backToTopBtn = document.getElementById('backToTop');
    const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
    const heroSection = document.getElementById('home');

    // Initialize everything when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        initializeAOS();
        initializeNavbar();
        initializeScrollEffects();
        initializeBackToTop();
        initializeSmoothScrolling();
        initializeFormValidation();
        initializeCarousel();
        initializeTypingEffect();
        initializeParallax();
        initializeLazyLoading();
    });

    // Initialize AOS (Animate On Scroll)
    function initializeAOS() {
        if (typeof AOS !== 'undefined') {
            AOS.init({
                duration: 1000,
                delay: 100,
                once: true,
                offset: 100,
                easing: 'ease-out-cubic'
            });
        }
    }

    // Navbar Scroll Effect
    function initializeNavbar() {
        if (!navbar) return;

        window.addEventListener('scroll', function() {
            if (window.scrollY > 100) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Close mobile menu when clicking on a link
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                const navbarCollapse = document.querySelector('.navbar-collapse');
                if (navbarCollapse.classList.contains('show')) {
                    const navbarToggler = document.querySelector('.navbar-toggler');
                    navbarToggler.click();
                }
            });
        });
    }

    // Scroll Effects
    function initializeScrollEffects() {
        // Active nav link on scroll
        window.addEventListener('scroll', function() {
            let current = '';
            const sections = document.querySelectorAll('section[id]');
            
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.clientHeight;
                if (pageYOffset >= sectionTop - 200) {
                    current = section.getAttribute('id');
                }
            });

            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === '#' + current) {
                    link.classList.add('active');
                }
            });
        });
    }

    // Back to Top Button
    function initializeBackToTop() {
        if (!backToTopBtn) return;

        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                backToTopBtn.classList.add('show');
            } else {
                backToTopBtn.classList.remove('show');
            }
        });

        backToTopBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    // Smooth Scrolling for anchor links
    function initializeSmoothScrolling() {
        const links = document.querySelectorAll('a[href^="#"]');
        
        links.forEach(link => {
            link.addEventListener('click', function(e) {
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                
                const targetSection = document.querySelector(targetId);
                if (targetSection) {
                    e.preventDefault();
                    const offsetTop = targetSection.offsetTop - 80;
                    
                    window.scrollTo({
                        top: offsetTop,
                        behavior: 'smooth'
                    });
                }
            });
        });
    }

    // Form Validation
    function initializeFormValidation() {
        const forms = document.querySelectorAll('form');
        
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                if (!this.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                    showNotification('Vui lòng điền đầy đủ thông tin!', 'error');
                } else if (this.classList.contains('newsletter-form')) {
                    e.preventDefault();
                    handleNewsletterSubmit(this);
                }
                this.classList.add('was-validated');
            });
        });

        // Email validation
        const emailInputs = document.querySelectorAll('input[type="email"]');
        emailInputs.forEach(input => {
            input.addEventListener('blur', function() {
                if (this.value && !isValidEmail(this.value)) {
                    this.setCustomValidity('Email không hợp lệ');
                } else {
                    this.setCustomValidity('');
                }
            });
        });
    }

    // Newsletter Form Handler
    function handleNewsletterSubmit(form) {
        const email = form.querySelector('input[type="email"]').value;
        
        if (isValidEmail(email)) {
            // Simulate API call
            showLoadingSpinner(form);
            
            setTimeout(() => {
                hideLoadingSpinner(form);
                showNotification('Đăng ký thành công! Cảm ơn bạn đã theo dõi chúng tôi.', 'success');
                form.reset();
            }, 2000);
        }
    }

    // Email Validation
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    // Carousel Enhancement
    function initializeCarousel() {
        const carousel = document.getElementById('testimonialCarousel');
        if (!carousel) return;

        // Auto-play pause on hover
        carousel.addEventListener('mouseenter', function() {
            const carouselInstance = bootstrap.Carousel.getInstance(this);
            if (carouselInstance) carouselInstance.pause();
        });

        carousel.addEventListener('mouseleave', function() {
            const carouselInstance = bootstrap.Carousel.getInstance(this);
            if (carouselInstance) carouselInstance.cycle();
        });

        // Touch/swipe support for mobile
        let touchStartX = 0;
        let touchEndX = 0;

        carousel.addEventListener('touchstart', function(e) {
            touchStartX = e.changedTouches[0].screenX;
        });

        carousel.addEventListener('touchend', function(e) {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
        });

        function handleSwipe() {
            const carouselInstance = bootstrap.Carousel.getInstance(carousel);
            if (!carouselInstance) return;

            if (touchEndX < touchStartX - 50) {
                carouselInstance.next();
            }
            if (touchEndX > touchStartX + 50) {
                carouselInstance.prev();
            }
        }
    }

    // Typing Effect for Hero Title
    function initializeTypingEffect() {
        const heroTitle = document.querySelector('.hero-title');
        if (!heroTitle) return;

        const originalText = heroTitle.innerHTML;
        const words = originalText.split('<br>');
        
        heroTitle.innerHTML = '';
        
        let wordIndex = 0;
        let charIndex = 0;
        let isDeleting = false;
        
        function type() {
            const currentWord = words[wordIndex];
            
            if (isDeleting) {
                heroTitle.innerHTML = currentWord.substring(0, charIndex - 1);
                charIndex--;
            } else {
                heroTitle.innerHTML = currentWord.substring(0, charIndex + 1);
                charIndex++;
            }
            
            let typeSpeed = isDeleting ? 50 : 100;
            
            if (!isDeleting && charIndex === currentWord.length) {
                typeSpeed = 2000;
                isDeleting = true;
            } else if (isDeleting && charIndex === 0) {
                isDeleting = false;
                wordIndex = (wordIndex + 1) % words.length;
                typeSpeed = 500;
            }
            
            setTimeout(type, typeSpeed);
        }
        
        // Start typing effect after a delay
        setTimeout(() => {
            heroTitle.innerHTML = originalText; // Show original text
        }, 1000);
    }

    // Parallax Effect
    function initializeParallax() {
        const parallaxElements = document.querySelectorAll('.hero-section');
        
        if (window.innerWidth > 768) {
            window.addEventListener('scroll', function() {
                parallaxElements.forEach(element => {
                    const scrolled = window.pageYOffset;
                    const rate = scrolled * -0.5;
                    element.style.transform = `translateY(${rate}px)`;
                });
            });
        }
    }

    // Lazy Loading Images
    function initializeLazyLoading() {
        const images = document.querySelectorAll('img[data-src]');
        
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('loading');
                    observer.unobserve(img);
                }
            });
        });

        images.forEach(img => {
            img.classList.add('loading');
            imageObserver.observe(img);
        });
    }

    // Show Loading Spinner
    function showLoadingSpinner(element) {
        const button = element.querySelector('button');
        if (button) {
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang xử lý...';
            button.disabled = true;
        }
    }

    // Hide Loading Spinner
    function hideLoadingSpinner(element) {
        const button = element.querySelector('button');
        if (button) {
            button.innerHTML = '<i class="fas fa-paper-plane"></i>';
            button.disabled = false;
        }
    }

    // Notification System
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type === 'error' ? 'danger' : type} notification`;
        notification.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
                <span>${message}</span>
                <button type="button" class="btn-close ms-auto" onclick="this.parentElement.parentElement.remove()"></button>
            </div>
        `;
        
        // Add styles
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            border: none;
            border-radius: 8px;
            animation: slideInRight 0.3s ease;
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentElement) {
                notification.style.animation = 'slideOutRight 0.3s ease';
                setTimeout(() => {
                    notification.remove();
                }, 300);
            }
        }, 5000);
    }

    // Add CSS animations
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
        
        .notification {
            animation: slideInRight 0.3s ease;
        }
    `;
    document.head.appendChild(style);

    // Counter Animation
    function animateCounters() {
        const counters = document.querySelectorAll('.stat-number');
        
        counters.forEach(counter => {
            const target = parseInt(counter.textContent.replace(/[^\d]/g, ''));
            const duration = 2000;
            const step = target / (duration / 16);
            let current = 0;
            
            const timer = setInterval(() => {
                current += step;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                
                const suffix = counter.textContent.includes('+') ? '+' : '';
                counter.textContent = Math.floor(current).toLocaleString() + suffix;
            }, 16);
        });
    }

    // Intersection Observer for counters
    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounters();
                counterObserver.unobserve(entry.target);
            }
        });
    });

    const aboutSection = document.getElementById('about');
    if (aboutSection) {
        counterObserver.observe(aboutSection);
    }

    // Search Functionality (for future use)
    function initializeSearch() {
        const searchInput = document.querySelector('.search-input');
        if (!searchInput) return;

        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const query = this.value.toLowerCase();
                performSearch(query);
            }, 300);
        });
    }

    function performSearch(query) {
        // This would typically make an API call
        console.log('Searching for:', query);
        showNotification(`Tìm kiếm: "${query}"`, 'info');
    }

    // Booking Modal (for future implementation)
    function initializeBookingModal() {
        const bookingButtons = document.querySelectorAll('[data-bs-target="#bookingModal"]');
        
        bookingButtons.forEach(button => {
            button.addEventListener('click', function() {
                const tourName = this.closest('.tour-card')?.querySelector('h5')?.textContent || 
                                this.closest('.destination-card')?.querySelector('h5')?.textContent;
                
                if (tourName) {
                    const modalTitle = document.querySelector('#bookingModal .modal-title');
                    if (modalTitle) {
                        modalTitle.textContent = `Đặt tour: ${tourName}`;
                    }
                }
            });
        });
    }

    // Price Calculator (for future use)
    function calculateTourPrice(tourId, adults, children, extras) {
        // This would typically make an API call to calculate pricing
        const basePrice = 5000000; // Base price in VND
        const childDiscount = 0.7; // 30% discount for children
        
        const total = (adults * basePrice) + (children * basePrice * childDiscount);
        return total;
    }

    // Initialize tooltips and popovers
    function initializeTooltips() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl);
        });
    }

    // Initialize progress bar animation
    function initializeProgressBars() {
        const progressBars = document.querySelectorAll('.progress-bar');
        
        const progressObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const progressBar = entry.target;
                    const width = progressBar.getAttribute('aria-valuenow');
                    progressBar.style.width = width + '%';
                    progressObserver.unobserve(progressBar);
                }
            });
        });

        progressBars.forEach(bar => {
            bar.style.width = '0%';
            progressObserver.observe(bar);
        });
    }

    // Weather Widget (for future implementation)
    async function getWeatherForDestination(city) {
        try {
            // This would typically use a real weather API
            return {
                city: city,
                temperature: Math.floor(Math.random() * 30) + 15,
                condition: ['Sunny', 'Cloudy', 'Rainy'][Math.floor(Math.random() * 3)]
            };
        } catch (error) {
            console.error('Weather data not available:', error);
            return null;
        }
    }

    // Currency Converter (for future use)
    function convertCurrency(amount, fromCurrency, toCurrency) {
        // This would typically use a real currency conversion API
        const exchangeRates = {
            'VND': { 'USD': 0.00004, 'EUR': 0.000037 },
            'USD': { 'VND': 24000, 'EUR': 0.92 },
            'EUR': { 'VND': 26000, 'USD': 1.08 }
        };
        
        if (exchangeRates[fromCurrency] && exchangeRates[fromCurrency][toCurrency]) {
            return amount * exchangeRates[fromCurrency][toCurrency];
        }
        return amount;
    }

    // Performance monitoring
    function measurePerformance() {
        window.addEventListener('load', function() {
            const navigationTiming = performance.getEntriesByType('navigation')[0];
            const loadTime = navigationTiming.loadEventEnd - navigationTiming.loadEventStart;
            console.log(`Page load time: ${loadTime}ms`);
        });
    }

    // Call performance monitoring
    measurePerformance();

    // Error handling
    window.addEventListener('error', function(e) {
        console.error('JavaScript error:', e.error);
        // In production, you might want to send this to an error tracking service
    });

    // Service Worker registration (for future PWA features)
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function() {
            // navigator.serviceWorker.register('/sw.js')
            //     .then(function(registration) {
            //         console.log('SW registered: ', registration);
            //     })
            //     .catch(function(registrationError) {
            //         console.log('SW registration failed: ', registrationError);
            //     });
        });
    }

    // Expose useful functions to global scope
    window.TravelDream = {
        showNotification,
        calculateTourPrice,
        convertCurrency,
        getWeatherForDestination
    };

})();