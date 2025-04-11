// Script para la página de SecureDrone Firewall

// Esperar a que el DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', function() {
    // Navegación móvil
    const mobileMenuButton = document.querySelector('.mobile-menu-button');
    const mainNav = document.querySelector('.main-nav');
    
    if (mobileMenuButton) {
        mobileMenuButton.addEventListener('click', function() {
            this.classList.toggle('active');
            mainNav.classList.toggle('active');
            
            // Animar las barras del botón hamburguesa
            const spans = this.querySelectorAll('span');
            if (this.classList.contains('active')) {
                spans[0].style.transform = 'rotate(45deg) translate(5px, 5px)';
                spans[1].style.opacity = '0';
                spans[2].style.transform = 'rotate(-45deg) translate(5px, -5px)';
            } else {
                spans[0].style.transform = 'none';
                spans[1].style.opacity = '1';
                spans[2].style.transform = 'none';
            }
        });
    }
    
    // Control de slider de testimonios
    const testimonialSlider = document.querySelector('.testimonial-slider');
    const prevBtn = document.querySelector('.prev-btn');
    const nextBtn = document.querySelector('.next-btn');
    const testimonials = document.querySelectorAll('.testimonial');
    
    if (testimonialSlider && prevBtn && nextBtn) {
        let currentIndex = 0;
        const maxIndex = Math.ceil(testimonials.length / 2) - 1;
        
        // Función para actualizar el slider
        function updateSlider() {
            const testimonialWidth = testimonials[0].offsetWidth + 30; // Ancho + gap
            testimonialSlider.style.transform = `translateX(-${currentIndex * testimonialWidth}px)`;
            testimonialSlider.style.transition = 'transform 0.5s ease';
        }
        
        // Event listeners para los botones
        prevBtn.addEventListener('click', function() {
            if (currentIndex > 0) {
                currentIndex--;
                updateSlider();
            }
        });
        
        nextBtn.addEventListener('click', function() {
            if (currentIndex < maxIndex) {
                currentIndex++;
                updateSlider();
            }
        });
        
        // Responsive behavior
        function handleResponsive() {
            if (window.innerWidth <= 768) {
                // En móvil, un testimonio a la vez
                currentIndex = 0;
                testimonialSlider.style.transform = 'translateX(0)';
            } else {
                updateSlider();
            }
        }
        
        window.addEventListener('resize', handleResponsive);
        handleResponsive(); // Inicializar
    }
    
    // Animación para secciones cuando aparecen en el viewport
    const sections = document.querySelectorAll('section');
    
    // Opciones para el Intersection Observer
    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.2
    };
    
    // Callback cuando las secciones entran en el viewport
    const sectionObserver = new IntersectionObserver(function(entries, observer) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    // Observar todas las secciones
    sections.forEach(section => {
        section.classList.add('section-hidden');
        sectionObserver.observe(section);
    });
    
    // Agregar estilos CSS para las animaciones
    const styleSheet = document.createElement('style');
    styleSheet.type = 'text/css';
    styleSheet.innerText = `
        .section-hidden {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }
        
        .visible {
            opacity: 1;
            transform: translateY(0);
        }
        
        .main-nav.active {
            display: block;
            position: absolute;
            top: 70px;
            left: 0;
            width: 100%;
            background: white;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .main-nav.active ul {
            flex-direction: column;
        }
        
        .main-nav.active ul li {
            margin: 10px 0;
        }
    `;
    document.head.appendChild(styleSheet);
    
    // Efecto parallax para hero section
    const hero = document.querySelector('.hero');
    
    if (hero) {
        window.addEventListener('scroll', function() {
            const scrollPosition = window.pageYOffset;
            hero.style.backgroundPositionY = scrollPosition * 0.5 + 'px';
        });
    }
    
    // Contador de estadísticas para la sección de beneficios
    function animateCounter(el, start, end, duration) {
        let startTime = null;
        
        function animation(currentTime) {
            if (!startTime) startTime = currentTime;
            const timeElapsed = currentTime - startTime;
            const progress = Math.min(timeElapsed / duration, 1);
            const value = Math.floor(progress * (end - start) + start);
            
            el.textContent = value;
            
            if (progress < 1) {
                requestAnimationFrame(animation);
            }
        }
        
        requestAnimationFrame(animation);
    }
    
    // Efecto de resaltado para características al pasar el cursor
    const featureCards = document.querySelectorAll('.feature-card');
    
    featureCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px)';
            this.style.boxShadow = '0 20px 30px rgba(0, 0, 0, 0.2)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.boxShadow = '0 15px 30px rgba(0, 0, 0, 0.15)';
        });
    });
    
    // Smooth scroll para enlaces internos
    const links = document.querySelectorAll('a[href^="#"]');
    
    links.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                const headerOffset = 80;
                const elementPosition = targetElement.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
                
                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
                
                // Cerrar menú móvil si está abierto
                if (mainNav.classList.contains('active')) {
                    mainNav.classList.remove('active');
                    mobileMenuButton.classList.remove('active');
                    
                    const spans = mobileMenuButton.querySelectorAll('span');
                    spans[0].style.transform = 'none';
                    spans[1].style.opacity = '1';
                    spans[2].style.transform = 'none';
                }
            }
        });
    });
    
    // Efecto de resaltado para enlaces de navegación activos
    function setActiveLink() {
        const scrollPosition = window.scrollY;
        
        // Obtener todas las secciones para determinar la posición
        const pageSection = document.querySelectorAll('section');
        
        pageSection.forEach(section => {
            const sectionTop = section.offsetTop - 100;
            const sectionHeight = section.offsetHeight;
            const sectionId = section.getAttribute('id');
            
            if (scrollPosition >= sectionTop && scrollPosition < sectionTop + sectionHeight) {
                document.querySelectorAll('.main-nav a').forEach(link => {
                    link.classList.remove('active');
                    if (link.getAttribute('href') === '#' + sectionId) {
                        link.classList.add('active');
                    }
                });
            }
        });
    }
    
    // Llamar a la función al cargar la página y al hacer scroll
    window.addEventListener('scroll', setActiveLink);
    setActiveLink();
    function startSimulation() {
        document.getElementById('start-menu').style.display = 'none';
      }
      
      function showConnectForm() {
        document.getElementById('connect-form').style.display = 'block';
      }
      
      // Mostrar el menú automáticamente al cargar la página
      document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('start-menu').style.display = 'flex';
      });
      
});
