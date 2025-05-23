<?php
include $headerPath;
?>

<div class="pdf-reader-container">
    <!-- Fixed Top Navigation Bar -->
    <div class="pdf-reader-navbar">
        <div class="navbar-left">
            <a href="/user/reading-sessions" class="back-btn">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h5 class="book-title"><?= $session['b_title'] ?></h5>
            <span class="book-author"><?= $session['b_author'] ?></span>
        </div>
        
        <div class="navbar-center d-none d-md-flex">
            <button id="prev" class="nav-btn">
                <i class="bi bi-chevron-left"></i>
            </button>
            <div class="page-display">
                <span id="page_num">0</span> / <span id="page_count">0</span>
            </div>
            <button id="next" class="nav-btn">
                <i class="bi bi-chevron-right"></i>
            </button>
        </div>
        
        <div class="navbar-right">
            <?php if ($hasExpired && !$isPurchased): ?>
                <div class="session-expired">
                    <i class="bi bi-exclamation-triangle-fill"></i> Session expired
                </div>
                <a href="/purchase/checkout?book_id=<?= $session['b_id'] ?>" class="purchase-btn">
                    <i class="bi bi-cart-plus"></i> Purchase
                </a>
            <?php else: ?>
                <div class="session-badge <?= $isPurchased ? 'purchased' : '' ?>">
                    <?php if ($isPurchased): ?>
                        <i class="bi bi-check-circle-fill"></i> Purchased
                    <?php else: ?>
                        <i class="bi bi-clock"></i> Expires: <?= date('M d, Y', strtotime($session['rs_expires_at'])) ?>
                    <?php endif; ?>
                </div>
                <div class="controls-group">
                    <button id="zoomOut" class="control-btn" title="Zoom Out">
                        <i class="bi bi-zoom-out"></i>
                    </button>
                    <button id="zoomIn" class="control-btn" title="Zoom In">
                        <i class="bi bi-zoom-in"></i>
                    </button>
                    <button id="toggleAntialiasing" class="control-btn" title="Toggle Text Sharpness">
                        <i class="bi bi-type"></i>
                    </button>
                    <button id="fullscreen" class="control-btn" title="Fullscreen">
                        <i class="bi bi-arrows-fullscreen"></i>
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Floating Mobile Controls -->
    <div class="mobile-controls d-md-none">
        <button id="prev-mobile" class="mobile-nav-btn">
            <i class="bi bi-chevron-left"></i>
        </button>
        <div class="mobile-page-display">
            <span id="page_num_mobile">0</span> / <span id="page_count_mobile">0</span>
        </div>
        <button id="next-mobile" class="mobile-nav-btn">
            <i class="bi bi-chevron-right"></i>
        </button>
    </div>

    <!-- PDF Viewer Container -->
    <div id="viewerContainer">
        <!-- Error Message -->
        <div id="errorMessage">
            <div class="error-content">
                <i class="bi bi-exclamation-triangle"></i>
                <span id="errorText">Error loading PDF</span>
            </div>
        </div>

        <!-- Loading Spinner -->
        <div id="loadingSpinner">
            <div class="spinner"></div>
            <p>Loading your book...</p>
        </div>

        <!-- Zoom Indicator -->
        <div id="zoomIndicator" class="zoom-indicator">
            <span id="zoomPercent">100%</span>
        </div>

        <!-- PDF Container for continuous scrolling -->
        <div id="pdfPagesContainer"></div>
    </div>
    
    <!-- Progress Bar -->
    <div class="reading-progress-bar">
        <div id="progressIndicator"></div>
    </div>
</div>

<!-- PDF.js library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
    // Set up PDF.js
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

    document.addEventListener('DOMContentLoaded', function() {
        const sessionId = <?= $session['rs_id'] ?>;
        const bookId = <?= $session['b_id'] ?>;
        const pdfUrl = '/assets/books/<?= $session['b_file_path'] ?>';
        const startPage = <?= isset($session['current_page']) ? max(1, intval($session['current_page'])) : 1 ?>;
        const totalBookPages = <?= isset($session['b_pages']) ? intval($session['b_pages']) : 0 ?>;
        
        // UI Elements
        const pdfPagesContainer = document.getElementById('pdfPagesContainer');
        const viewerContainer = document.getElementById('viewerContainer');
        const loadingSpinner = document.getElementById('loadingSpinner');
        const errorMessage = document.getElementById('errorMessage');
        const errorText = document.getElementById('errorText');
        const progressIndicator = document.getElementById('progressIndicator');
        const zoomIndicator = document.getElementById('zoomIndicator');
        const zoomPercent = document.getElementById('zoomPercent');
        
        // Navigation elements
        const prevBtn = document.getElementById('prev');
        const nextBtn = document.getElementById('next');
        const prevBtnMobile = document.getElementById('prev-mobile');
        const nextBtnMobile = document.getElementById('next-mobile');
        const pageNumDisplay = document.getElementById('page_num');
        const pageCountDisplay = document.getElementById('page_count');
        const pageNumMobileDisplay = document.getElementById('page_num_mobile');
        const pageCountMobileDisplay = document.getElementById('page_count_mobile');
        
        // Zoom controls
        const zoomInBtn = document.getElementById('zoomIn');
        const zoomOutBtn = document.getElementById('zoomOut');
        const fullscreenBtn = document.getElementById('fullscreen');
        const antialiasingBtn = document.getElementById('toggleAntialiasing');
        
        // State variables
        let pdfDoc = null;
        let currentPage = startPage;
        let pagesRendered = new Set();
        let pageRendering = false;
        let scale = 1.0;
        let useAntialiasing = true; // Default state of antialiasing
        let pageCanvases = [];
        let pageObservers = [];
        let visiblePages = new Set();
        let scrollTimeout = null;
        
        // Initial element states
        errorMessage.style.display = 'none';
        
        // Load settings from localStorage
        loadZoomLevel();
        loadAntialiasingSetting();
        
        /**
         * Load the PDF document
         */
        pdfjsLib.getDocument(pdfUrl).promise.then(function(pdf) {
            pdfDoc = pdf;
            
            // Update page count display
            const numPages = pdf.numPages;
            pageCountDisplay.textContent = numPages;
            pageCountMobileDisplay.textContent = numPages;
            
            // Check if the requested page is valid
            if (currentPage > numPages) {
                currentPage = 1;
            }
            
            // Initialize page containers for all pages
            setupPageContainers(numPages);
            
            // Initial render of visible pages
            renderVisiblePages();
            
            // Hide loading spinner
            loadingSpinner.style.display = 'none';
            
            // Scroll to starting page
            scrollToPage(currentPage);
            
            // Update progress bar based on current scroll position
            updateProgressBar();
            
            // Set up intersection observers to detect visible pages
            setupIntersectionObservers();
        }).catch(function(error) {
            console.error('Error loading PDF:', error);
            loadingSpinner.style.display = 'none';
            errorMessage.style.display = 'flex';
            errorText.textContent = 'Error loading PDF: ' + error.message;
        });
        
        /**
         * Set up page containers for all pages
         */
        function setupPageContainers(numPages) {
            pdfPagesContainer.innerHTML = '';
            pageCanvases = [];
            
            for (let i = 1; i <= numPages; i++) {
                const pageContainer = document.createElement('div');
                pageContainer.className = 'pdf-page-container';
                pageContainer.id = `page-container-${i}`;
                pageContainer.dataset.pageNumber = i;
                
                const pageCanvas = document.createElement('canvas');
                pageCanvas.className = 'pdf-page-canvas';
                pageCanvas.id = `page-${i}`;
                
                const pageLabel = document.createElement('div');
                pageLabel.className = 'pdf-page-label';
                pageLabel.textContent = `Page ${i}`;
                
                pageContainer.appendChild(pageCanvas);
                pageContainer.appendChild(pageLabel);
                pdfPagesContainer.appendChild(pageContainer);
                
                pageCanvases.push({
                    pageNum: i,
                    canvas: pageCanvas,
                    container: pageContainer,
                    rendered: false
                });
            }
        }
        
        /**
         * Setup intersection observers to detect visible pages
         */
        function setupIntersectionObservers() {
            // Disconnect any existing observers
            pageObservers.forEach(observer => observer.disconnect());
            pageObservers = [];
            
            const options = {
                root: viewerContainer,
                rootMargin: '100px 0px',
                threshold: [0.1, 0.5, 0.9]
            };
            
            const observer = new IntersectionObserver((entries) => {
                let needsUpdate = false;
                let mostVisiblePage = null;
                let highestVisibility = 0;
                
                entries.forEach(entry => {
                    const pageNum = parseInt(entry.target.dataset.pageNumber);
                    
                    if (entry.isIntersecting) {
                        visiblePages.add(pageNum);
                        needsUpdate = true;
                        
                        // Track the most visible page
                        if (entry.intersectionRatio > highestVisibility) {
                            highestVisibility = entry.intersectionRatio;
                            mostVisiblePage = pageNum;
                        }
                    } else {
                        visiblePages.delete(pageNum);
                    }
                });
                
                // Update current page based on the most visible page
                if (mostVisiblePage !== null) {
                    updateCurrentPage(mostVisiblePage);
                }
                
                if (needsUpdate) {
                    renderVisiblePages();
                }
                
            }, options);
            
            // Observe all page containers
            pageCanvases.forEach(page => {
                observer.observe(page.container);
            });
            
            pageObservers.push(observer);
        }
        
        /**
         * Render all currently visible pages
         */
        function renderVisiblePages() {
            visiblePages.forEach(pageNum => {
                if (!pagesRendered.has(pageNum)) {
                    renderPage(pageNum);
                }
            });
        }
        
        /**
         * Render a specific page of the PDF
         */
        function renderPage(pageNum) {
            if (pagesRendered.has(pageNum) || pageRendering) {
                return Promise.resolve();
            }
            
            pageRendering = true;
            
            const pageInfo = pageCanvases.find(p => p.pageNum === pageNum);
            if (!pageInfo) {
                pageRendering = false;
                return Promise.reject(new Error(`Page ${pageNum} not found`));
            }
            
            const canvas = pageInfo.canvas;
            const ctx = canvas.getContext('2d');
            
            // Get the page
            return pdfDoc.getPage(pageNum).then(function(page) {
                // Get device pixel ratio
                const devicePixelRatio = window.devicePixelRatio || 1;
                
                // Calculate viewport width based on container
                const containerWidth = viewerContainer.clientWidth - 40; // Add some padding
                
                // Get viewport at scale 1
                const originalViewport = page.getViewport({ scale: 1 });
                
                // Calculate scale to fit width
                const scaleToFit = containerWidth / originalViewport.width;
                
                // Apply user scale on top of fit scale
                const viewport = page.getViewport({ scale: scaleToFit * scale });
                
                // Set canvas dimensions accounting for device pixel ratio
                canvas.width = Math.floor(viewport.width * devicePixelRatio);
                canvas.height = Math.floor(viewport.height * devicePixelRatio);
                canvas.style.width = Math.floor(viewport.width) + "px";
                canvas.style.height = Math.floor(viewport.height) + "px";
                
                // Reset context and prepare for high-quality rendering
                ctx.setTransform(1, 0, 0, 1, 0, 0);
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                
                // Enable font smoothing
                ctx.imageSmoothingEnabled = useAntialiasing;
                ctx.imageSmoothingQuality = 'high';
                
                // Scale context to ensure correct rendering
                ctx.scale(devicePixelRatio, devicePixelRatio);
                
                // Render PDF page into canvas context
                const renderContext = {
                    canvasContext: ctx,
                    viewport: viewport,
                    enableWebGL: true,
                    renderInteractiveForms: true
                };
                
                return page.render(renderContext).promise.then(() => {
                    pagesRendered.add(pageNum);
                    pageInfo.rendered = true;
                    pageRendering = false;
                    
                    // Check if the rendered page is the current page for tracking
                    if (pageNum === currentPage) {
                        saveProgress(pageNum);
                    }
                });
            }).catch(function(error) {
                console.error(`Error rendering page ${pageNum}:`, error);
                pageRendering = false;
                return Promise.reject(error);
            });
        }
        
        /**
         * Update the current page based on visibility
         */
        function updateCurrentPage(pageNum) {
            if (currentPage !== pageNum) {
                currentPage = pageNum;
                
                // Update UI page numbers with page number and percentage
                updatePageNumberDisplay(pageNum);
                
                // Update progress bar
                updateProgressBar();
                
                // Save progress for this page
                saveProgress(pageNum);
            }
        }
        
        /**
         * Update the page number display with both the page number and percentage
         */
        function updatePageNumberDisplay(pageNum) {
            // Calculate percentage through document
            const percent = Math.round((pageNum / pdfDoc.numPages) * 100);
            
            // Update the desktop page display with percentage
            const pageDisplayDesktop = document.querySelector('.page-display');
            if (pageDisplayDesktop) {
                pageDisplayDesktop.innerHTML = `<span id="page_num">${pageNum}</span> / <span id="page_count">${pdfDoc.numPages}</span> <span class="progress-percent">(${percent}%)</span>`;
            }
            
            // Update the mobile page display with percentage
            const pageDisplayMobile = document.querySelector('.mobile-page-display');
            if (pageDisplayMobile) {
                pageDisplayMobile.innerHTML = `<span id="page_num_mobile">${pageNum}</span> / <span id="page_count_mobile">${pdfDoc.numPages}</span> <span class="progress-percent">(${percent}%)</span>`;
            }
        }
        
        /**
         * Scroll to a specific page
         */
        function scrollToPage(pageNum) {
            const pageContainer = document.getElementById(`page-container-${pageNum}`);
            if (pageContainer) {
                // Scroll the page into view with a smooth animation
                pageContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
                
                // Update current page
                updateCurrentPage(pageNum);
            }
        }
        
        /**
         * Display previous page
         */
        function showPrevPage() {
            if (currentPage <= 1) return;
            scrollToPage(currentPage - 1);
        }
        
        /**
         * Display next page
         */
        function showNextPage() {
            if (currentPage >= pdfDoc.numPages) return;
            scrollToPage(currentPage + 1);
        }
        
        /**
         * Update the progress bar
         */
        function updateProgressBar() {
            if (!pdfDoc) return;
            
            // Calculate scroll-based progress instead of just page-based
            const scrollPosition = viewerContainer.scrollTop;
            const scrollHeight = viewerContainer.scrollHeight - viewerContainer.clientHeight;
            
            // Blend page-based and scroll-based progress for a smoother experience
            // 70% based on scroll position, 30% based on current page
            const scrollProgress = scrollHeight > 0 ? (scrollPosition / scrollHeight) * 100 : 0;
            const pageProgress = (currentPage / pdfDoc.numPages) * 100;
            
            const blendedProgress = (scrollProgress * 0.7) + (pageProgress * 0.3);
            
            // Ensure progress is between 0-100%
            const progress = Math.max(0, Math.min(100, blendedProgress));
            
            progressIndicator.style.width = progress + '%';
        }
        
        /**
         * Save reading progress to the server
         */
        function saveProgress(page, scrollPercent = null) {
            fetch('/reading-session/update-progress', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    session_id: sessionId,
                    current_page: page,
                    scroll_percent: scrollPercent,
                    is_completed: page === pdfDoc.numPages || scrollPercent >= 95 // Consider completed if on last page or scrolled to near end
                })
            })
            .then(response => response.json())
            .catch(error => {
                console.error('Error saving reading progress:', error);
            });
        }
        
        /**
         * Handle zoom in
         */
        function zoomIn() {
            if (scale >= 3.0) return;
            scale += 0.2;
            applyZoom();
        }
        
        /**
         * Handle zoom out
         */
        function zoomOut() {
            if (scale <= 0.5) return;
            scale -= 0.2;
            applyZoom();
        }
        
        /**
         * Apply zoom changes and re-render pages
         */
        function applyZoom() {
            // Clear all rendered pages
            pagesRendered.clear();
            pageCanvases.forEach(page => {
                page.rendered = false;
            });
            
            // Save zoom level
            saveZoomLevel();
            
            // Show zoom indicator
            showZoomIndicator();
            
            // Re-render visible pages
            renderVisiblePages();
        }
        
        /**
         * Show zoom indicator and hide after a delay
         */
        function showZoomIndicator() {
            // Update zoom percentage
            const zoomPercentValue = Math.round(scale * 100);
            showMessage(zoomPercentValue + '%');
        }
        
        /**
         * Load zoom level from localStorage
         */
        function loadZoomLevel() {
            // Try to load book-specific zoom level first
            const savedScale = localStorage.getItem(`zoomLevel_${sessionId}`);
            if (savedScale) {
                const parsedScale = parseFloat(savedScale);
                if (!isNaN(parsedScale) && parsedScale >= 0.5 && parsedScale <= 3.0) {
                    scale = parsedScale;
                    return;
                }
            }
            
            // Fall back to global zoom preference
            const globalScale = localStorage.getItem('globalZoomLevel');
            if (globalScale) {
                const parsedScale = parseFloat(globalScale);
                if (!isNaN(parsedScale) && parsedScale >= 0.5 && parsedScale <= 3.0) {
                    scale = parsedScale;
                }
            }
        }
        
        /**
         * Save zoom level to localStorage
         */
        function saveZoomLevel() {
            // Save both session-specific and global zoom preference
            localStorage.setItem(`zoomLevel_${sessionId}`, scale.toString());
            localStorage.setItem('globalZoomLevel', scale.toString());
        }
        
        /**
         * Toggle fullscreen mode
         */
        function toggleFullscreen() {
            const container = document.getElementById('viewerContainer');
            
            if (!document.fullscreenElement) {
                container.requestFullscreen().catch(err => {
                    console.error('Error attempting to enable full-screen mode:', err);
                });
            } else {
                document.exitFullscreen();
            }
        }
        
        /**
         * Toggle text antialiasing for clarity
         */
        function toggleAntialiasing() {
            useAntialiasing = !useAntialiasing;
            
            // Update button visual state
            if (useAntialiasing) {
                antialiasingBtn.classList.remove('active');
            } else {
                antialiasingBtn.classList.add('active');
            }
            
            // Save preference
            localStorage.setItem('pdfAntialiasing', useAntialiasing ? 'true' : 'false');
            
            // Show indicator with current state
            const message = useAntialiasing ? 'Text smoothing: ON' : 'Text sharpening: ON';
            showMessage(message);
            
            // Clear all rendered pages and re-render with new setting
            pagesRendered.clear();
            pageCanvases.forEach(page => {
                page.rendered = false;
            });
            renderVisiblePages();
        }
        
        /**
         * Load antialiasing setting from localStorage
         */
        function loadAntialiasingSetting() {
            const savedSetting = localStorage.getItem('pdfAntialiasing');
            if (savedSetting !== null) {
                useAntialiasing = savedSetting === 'true';
                
                // Update button visual state
                if (!useAntialiasing) {
                    antialiasingBtn.classList.add('active');
                }
            }
        }
        
        /**
         * Show a temporary message notification
         */
        function showMessage(message) {
            // Update zoom indicator to show the message
            zoomPercent.textContent = message;
            
            // Show indicator
            zoomIndicator.classList.add('visible');
            
            // Hide after delay
            clearTimeout(window.zoomTimeout);
            window.zoomTimeout = setTimeout(() => {
                zoomIndicator.classList.remove('visible');
            }, 1500);
        }
        
        // Event listeners for navigation
        prevBtn.addEventListener('click', showPrevPage);
        nextBtn.addEventListener('click', showNextPage);
        prevBtnMobile.addEventListener('click', showPrevPage);
        nextBtnMobile.addEventListener('click', showNextPage);
        
        // Event listeners for zoom
        zoomInBtn.addEventListener('click', zoomIn);
        zoomOutBtn.addEventListener('click', zoomOut);
        fullscreenBtn.addEventListener('click', toggleFullscreen);
        
        // Event listener for antialiasing toggle
        antialiasingBtn.addEventListener('click', toggleAntialiasing);
        
        // Set up keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowRight' || e.key === 'PageDown') {
                showNextPage();
            } else if (e.key === 'ArrowLeft' || e.key === 'PageUp') {
                showPrevPage();
            }
        });
        
        /**
         * Debounce function to limit how often a function is called
         */
        function debounce(func, wait) {
            return function() {
                const context = this;
                const args = arguments;
                clearTimeout(scrollTimeout);
                scrollTimeout = setTimeout(() => {
                    func.apply(context, args);
                }, wait);
            };
        }

        // Set up scroll event to track current page and update progress
        viewerContainer.addEventListener('scroll', function() {
            // Update the progress bar based on scroll position immediately for responsive UI
            updateProgressBar();
        });
        
        // Debounced version for more intensive operations
        viewerContainer.addEventListener('scroll', debounce(function() {
            // Additional scroll handling that should be less frequent
            // Like sending analytics or saving progress to server
            const scrollPercent = Math.round((viewerContainer.scrollTop / (viewerContainer.scrollHeight - viewerContainer.clientHeight)) * 100);
            if (scrollPercent % 10 === 0) { // Save at every 10% of scrolling
                saveProgress(currentPage, scrollPercent);
            }
        }, 300));
        
        // Handle window resize
        window.addEventListener('resize', function() {
            // Clear all rendered pages
            pagesRendered.clear();
            pageCanvases.forEach(page => {
                page.rendered = false;
            });
            renderVisiblePages();
        });
    });
</script>

<style>
/* PDF Reader Styles */
.pdf-reader-container {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    display: flex;
    flex-direction: column;
    background-color: #f0f2f5;
    z-index: 1030; /* Above normal content */
}

.pdf-reader-navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 1rem;
    background-color: #fff;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
    z-index: 1;
}

.navbar-left {
    display: flex;
    align-items: center;
}

.back-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 2rem;
    height: 2rem;
    border-radius: 50%;
    background: #f8f9fa;
    color: #495057;
    margin-right: 1rem;
    text-decoration: none;
}

.book-title {
    margin: 0;
    font-size: 1rem;
    max-width: 200px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.book-author {
    color: #6c757d;
    font-size: 0.8rem;
    margin-left: 0.5rem;
}

.navbar-center {
    display: flex;
    align-items: center;
}

.nav-btn {
    background: none;
    border: none;
    color: #495057;
    font-size: 1.25rem;
    cursor: pointer;
    padding: 0.25rem 0.5rem;
}

.page-display {
    margin: 0 0.75rem;
    font-weight: 500;
}

.progress-percent {
    font-size: 0.75rem;
    color: #6c757d;
    font-weight: normal;
    margin-left: 4px;
}

.navbar-right {
    display: flex;
    align-items: center;
}

.session-expired {
    color: #dc3545;
    font-size: 0.875rem;
    margin-right: 1rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.purchase-btn {
    padding: 0.375rem 0.75rem;
    background-color: #28a745;
    color: #fff;
    border-radius: 0.25rem;
    text-decoration: none;
    font-size: 0.875rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.session-badge {
    padding: 0.25rem 0.5rem;
    background-color: #f0f2f5;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    margin-right: 1rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.session-badge.purchased {
    background-color: #d4edda;
    color: #155724;
}

.controls-group {
    display: flex;
    gap: 0.5rem;
}

.control-btn {
    width: 2rem;
    height: 2rem;
    border-radius: 50%;
    background: #f8f9fa;
    color: #495057;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}

.control-btn.active {
    background-color: #0d6efd;
    color: white;
}

#viewerContainer {
    flex: 1;
    position: relative;
    overflow-y: auto;
    overflow-x: hidden;
    background-color: #444;
}

#pdfPagesContainer {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 20px;
    min-height: 100%;
}

.pdf-page-container {
    position: relative;
    margin-bottom: 40px;
    box-shadow: 0 0 8px rgba(0, 0, 0, 0.15);
    background-color: white;
}

.pdf-page-canvas {
    display: block;
    margin: 0 auto;
    image-rendering: -webkit-optimize-contrast;
    image-rendering: crisp-edges;
    transform: translateZ(0);
}

.pdf-page-label {
    position: absolute;
    bottom: -20px;
    left: 50%;
    transform: translateX(-50%);
    background-color: rgba(0, 0, 0, 0.5);
    color: white;
    padding: 2px 8px;
    border-radius: 10px;
    font-size: 0.8rem;
    opacity: 0.7;
    pointer-events: none;
}

.reading-progress-bar {
    height: 4px;
    background-color: #dee2e6;
    width: 100%;
}

#progressIndicator {
    height: 100%;
    background-color: #007bff;
    width: 0;
    transition: width 0.3s ease;
}

.mobile-controls {
    position: fixed;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    align-items: center;
    background-color: rgba(255, 255, 255, 0.9);
    padding: 0.5rem;
    border-radius: 50px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    z-index: 100;
}

.mobile-nav-btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #f8f9fa;
    color: #495057;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}

.mobile-page-display {
    margin: 0 1rem;
    font-weight: 500;
}

.zoom-indicator {
    position: fixed;
    bottom: 80px;
    left: 50%;
    transform: translateX(-50%);
    background-color: rgba(0, 0, 0, 0.7);
    color: white;
    padding: 8px 16px;
    border-radius: 20px;
    font-weight: bold;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.3s, visibility 0.3s;
    z-index: 1000;
}

.zoom-indicator.visible {
    opacity: 1;
    visibility: visible;
}

@media (max-width: 767.98px) {
    .book-title {
        max-width: 150px;
    }
    
    .book-author {
        display: none;
    }
    
    .control-btn {
        width: 1.8rem;
        height: 1.8rem;
        font-size: 0.9rem;
    }
}

#errorMessage {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.9);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 10;
}

.error-content {
    text-align: center;
    color: #dc3545;
}

#loadingSpinner {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.8);
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    z-index: 10;
}

.spinner {
    width: 3rem;
    height: 3rem;
    border: 4px solid rgba(0, 0, 0, 0.1);
    border-left-color: #007bff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>

<?php
include $footerPath;
?> 