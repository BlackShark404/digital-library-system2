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
                    <button id="toggleTOC" class="control-btn" title="Table of Contents">
                        <i class="bi bi-list-ul"></i>
                    </button>
                    <button id="zoomOut" class="control-btn" title="Zoom Out">
                        <i class="bi bi-zoom-out"></i>
                    </button>
                    <button id="zoomIn" class="control-btn" title="Zoom In">
                        <i class="bi bi-zoom-in"></i>
                    </button>
                    <button id="toggleAntialiasing" class="control-btn" title="Toggle Text Sharpness">
                        <i class="bi bi-type"></i>
                    </button>
                    <button id="toggleContinuousScroll" class="control-btn" title="Toggle Continuous Scroll">
                        <i class="bi bi-arrows-expand-vertical"></i>
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
    <div class="pdf-reader-content">
        <!-- Table of Contents Sidebar -->
        <div id="tocSidebar" class="toc-sidebar">
            <div class="toc-header">
                <h5>Table of Contents</h5>
                <button id="closeTOC" class="close-toc-btn">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div id="tocContent" class="toc-content">
                <div class="toc-loading">
                    <div class="spinner-sm"></div>
                    <p>Loading table of contents...</p>
                </div>
                <div id="tocEmpty" class="toc-empty" style="display: none;">
                    <i class="bi bi-info-circle"></i>
                    <p>No table of contents available for this book.</p>
                </div>
                <ul id="tocList" class="toc-list"></ul>
            </div>
        </div>

        <!-- Main Content Wrapper -->
        <div class="main-content-wrapper">
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

                <!-- PDF Canvas -->
                <canvas id="pdfCanvas"></canvas>
            </div>
            
            <!-- Progress Bar -->
            <div class="reading-progress-bar">
                <div id="progressIndicator"></div>
            </div>
        </div>
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
        let pdfUrl = '<?= $session['b_file_path'] ?>';
        const startPage = <?= isset($session['current_page']) ? max(1, intval($session['current_page'])) : 1 ?>;
        const totalBookPages = <?= isset($session['b_pages']) ? intval($session['b_pages']) : 0 ?>;
        
        // Fix PDF URL if needed
        if (!pdfUrl.startsWith('/')) {
            pdfUrl = '/assets/books/' + pdfUrl;
        }
        
        // Add cache-busting parameter to avoid caching issues
        pdfUrl += '?t=' + new Date().getTime();
        
        console.log('PDF Reader initializing...', {
            sessionId,
            bookId,
            pdfUrl,
            startPage,
            totalBookPages
        });
        
        // UI Elements
        const canvas = document.getElementById('pdfCanvas');
        const errorMessage = document.getElementById('errorMessage');
        const errorText = document.getElementById('errorText');
        
        if (!canvas) {
            console.error('Canvas element not found!');
            errorMessage.style.display = 'flex';
            errorText.textContent = 'Error: PDF viewer canvas not found.';
            return;
        }

        // Initialize canvas and context
        const ctx = canvas.getContext('2d', { alpha: false });
        if (!ctx) {
            console.error('Could not get 2D context for canvas!');
            errorMessage.style.display = 'flex';
            errorText.textContent = 'Error: Could not initialize PDF rendering context.';
            return;
        }

        // Ensure canvas has a reasonable initial size
        canvas.width = 800;
        canvas.height = 1200;
        
        const loadingSpinner = document.getElementById('loadingSpinner');
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
        const continuousScrollBtn = document.getElementById('toggleContinuousScroll');
        
        // TOC controls
        const toggleTOCBtn = document.getElementById('toggleTOC');
        const tocSidebar = document.getElementById('tocSidebar');
        const closeTOCBtn = document.getElementById('closeTOC');
        const tocList = document.getElementById('tocList');
        const tocEmpty = document.getElementById('tocEmpty');
        
        // State variables
        let pdfDoc = null;
        let pageNum = startPage;
        let pageRendering = false;
        let pageNumPending = null;
        let scale = 1.0;
        let useAntialiasing = true; // Default state of antialiasing
        let continuousScrollMode = true; // Default to continuous scroll mode
        let pageCanvases = []; // Array to store canvas elements for continuous mode
        let renderedPages = new Set(); // Track rendered page numbers
        let outline = null; // Store the PDF outline/table of contents
        
        // Initial element states
        errorMessage.style.display = 'none';
        
        // Initialize the view container right away
        resetView();
        
        // Load settings from localStorage
        loadZoomLevel();
        loadAntialiasingSetting();
        loadContinuousScrollSetting();
        
        /**
         * Load the PDF document
         */
        console.log('Loading PDF from URL:', pdfUrl);

        // Clear any previous errors
        errorMessage.style.display = 'none';
        loadingSpinner.style.display = 'flex';

        // Configure maximum canvas size to avoid browser limitations
        const CMAP_URL = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/cmaps/';
        const CMAP_PACKED = true;

        // Use lower quality for better performance
        const loadingTask = pdfjsLib.getDocument({
            url: pdfUrl,
            cMapUrl: CMAP_URL,
            cMapPacked: CMAP_PACKED,
            disableRange: false,
            disableStream: false,
            disableAutoFetch: false
        });

        loadingTask.onProgress = function(progress) {
            if (progress.total > 0) {
                const percent = progress.loaded / progress.total * 100;
                console.log(`Loading: ${percent.toFixed(2)}%`);
            } else {
                console.log(`Loading: ${progress.loaded} bytes`);
            }
        };

        loadingTask.promise.then(function(pdf) {
            console.log('PDF document loaded successfully');
            pdfDoc = pdf;
            
            // Update page count display
            const numPages = pdf.numPages;
            console.log(`PDF has ${numPages} pages`);
            pageCountDisplay.textContent = numPages;
            pageCountMobileDisplay.textContent = numPages;
            
            // Check if the requested page is valid
            if (pageNum > numPages) {
                console.log(`Requested page ${pageNum} exceeds document length, setting to page 1`);
                pageNum = 1;
            }
            
            // Set up view container before rendering
            resetView();
            
            // Render the view based on mode
            renderCurrentView();
            
            // Load the document outline (table of contents)
            loadDocumentOutline(pdf);
            
            // Hide loading spinner
            loadingSpinner.style.display = 'none';
            
            // Update progress bar
            updateProgressBar();
        }).catch(function(error) {
            console.error('Error loading PDF:', error);
            loadingSpinner.style.display = 'none';
            errorMessage.style.display = 'flex';
            errorText.textContent = 'Error loading PDF: ' + error.message;
        });
        
        /**
         * Load and parse document outline (table of contents)
         */
        function loadDocumentOutline(pdf) {
            pdf.getOutline().then(function(pdfOutline) {
                outline = pdfOutline;
                
                if (outline && outline.length > 0) {
                    // TOC is available
                    renderOutline(outline, tocList);
                    tocEmpty.style.display = 'none';
                } else {
                    // No TOC available
                    tocEmpty.style.display = 'block';
                }
                
                // Remove the loading state
                const tocLoading = document.querySelector('.toc-loading');
                if (tocLoading) {
                    tocLoading.style.display = 'none';
                }
            }).catch(function(error) {
                console.error('Error loading document outline:', error);
                tocEmpty.style.display = 'block';
                
                // Remove the loading state
                const tocLoading = document.querySelector('.toc-loading');
                if (tocLoading) {
                    tocLoading.style.display = 'none';
                }
            });
        }
        
        /**
         * Render the outline as a nested list
         */
        function renderOutline(outlineItems, parentElement) {
            outlineItems.forEach(function(item) {
                const li = document.createElement('li');
                li.className = 'toc-item';
                
                const link = document.createElement('a');
                link.className = 'toc-link';
                link.textContent = item.title;
                
                // Handle destination
                if (item.dest) {
                    link.addEventListener('click', function() {
                        navigateToDestination(item.dest);
                    });
                }
                
                li.appendChild(link);
                
                // Handle nested items
                if (item.items && item.items.length > 0) {
                    const nestedList = document.createElement('ul');
                    nestedList.className = 'toc-list-nested';
                    renderOutline(item.items, nestedList);
                    li.appendChild(nestedList);
                }
                
                parentElement.appendChild(li);
            });
        }
        
        /**
         * Navigate to a specific destination in the PDF
         */
        function navigateToDestination(dest) {
            if (!pdfDoc) return;
            
            // Process the destination to get the page reference
            if (typeof dest === 'string') {
                pdfDoc.getDestination(dest).then(function(destArray) {
                    navigateToDestArray(destArray);
                });
            } else if (Array.isArray(dest)) {
                navigateToDestArray(dest);
            }
        }
        
        /**
         * Process a destination array and navigate to the target page
         */
        function navigateToDestArray(destArray) {
            if (!destArray || !Array.isArray(destArray) || destArray.length < 1) {
                console.error('Invalid destination array:', destArray);
                return;
            }
            
            // The first item is the page ref
            const pageRef = destArray[0];
            
            // Convert the page reference to a page number
            pdfDoc.getPageIndex(pageRef).then(function(pageIndex) {
                // PDF.js uses zero-based indices, but we use one-based page numbers
                const targetPage = pageIndex + 1;
                console.log(`Navigating to destination. Page index: ${pageIndex}, Page number: ${targetPage}`);
                
                if (continuousScrollMode) {
                    scrollToPageInContinuousMode(targetPage);
                } else {
                    queueRenderPage(targetPage);
                }
                
                // Track this as the current page
                pageNum = targetPage;
                pageNumDisplay.textContent = pageNum;
                pageNumMobileDisplay.textContent = pageNum;
                
                // Save progress and update UI
                updateProgressBar();
                saveProgress(pageNum);
                
                // Close the TOC sidebar on small screens
                if (window.innerWidth < 768) {
                    tocSidebar.classList.remove('active');
                }
            }).catch(function(error) {
                console.error('Error resolving page from destination:', error);
            });
        }
        
        /**
         * Reset the view container
         */
        function resetView() {
            console.log('Resetting view, continuous mode:', continuousScrollMode);
            
            // Clear rendered pages tracking
            renderedPages.clear();
            
            const viewerContainer = document.getElementById('viewerContainer');
            if (!viewerContainer) {
                console.error('viewerContainer not found');
                return; // Guard against early execution
            }
            
            // If in continuous mode
            if (continuousScrollMode) {
                console.log('Setting up continuous view');
                
                // Remove any existing continuous container
                const existingContainer = document.getElementById('continuousContainer');
                if (existingContainer) {
                    console.log('Removing existing continuous container');
                    existingContainer.remove();
                }
                
                // Clear page canvases array
                pageCanvases.forEach(canvas => {
                    if (canvas && canvas.parentNode) {
                        try {
                            canvas.parentNode.remove();
                        } catch (e) {
                            console.error('Error removing canvas parent:', e);
                        }
                    }
                });
                pageCanvases = [];
                
                // Create fresh continuous container
                const continuousContainer = document.createElement('div');
                continuousContainer.id = 'continuousContainer';
                continuousContainer.className = 'continuous-container';
                viewerContainer.appendChild(continuousContainer);
                console.log('Created new continuous container');
                
                // Hide the main canvas
                if (canvas) {
                    if (viewerContainer.contains(canvas)) {
                        console.log('Removing main canvas from viewerContainer');
                        viewerContainer.removeChild(canvas);
                    }
                    // Reset canvas size to avoid issues
                    canvas.width = 1;
                    canvas.height = 1;
                }
            } else {
                // In single page mode
                console.log('Setting up single page view');
                
                // Remove continuous container if exists
                const continuousContainer = document.getElementById('continuousContainer');
                if (continuousContainer) {
                    console.log('Removing continuous container');
                    continuousContainer.remove();
                }
                
                // Clear all page canvases
                pageCanvases.forEach(canvas => {
                    if (canvas && canvas.parentNode) {
                        try {
                            canvas.parentNode.remove();
                        } catch (e) {
                            console.error('Error removing canvas parent:', e);
                        }
                    }
                });
                pageCanvases = [];
                
                // Ensure main canvas is in the container
                if (canvas && !viewerContainer.contains(canvas)) {
                    console.log('Adding main canvas to viewerContainer');
                    viewerContainer.appendChild(canvas);
                }
                
                // Reset scroll position
                viewerContainer.scrollTop = 0;
            }
        }
        
        /**
         * Render the current view based on the active mode
         */
        function renderCurrentView() {
            if (continuousScrollMode) {
                // Make sure the continuous container exists
                const viewerContainer = document.getElementById('viewerContainer');
                let continuousContainer = document.getElementById('continuousContainer');
                if (!continuousContainer) {
                    continuousContainer = document.createElement('div');
                    continuousContainer.id = 'continuousContainer';
                    continuousContainer.className = 'continuous-container';
                    viewerContainer.appendChild(continuousContainer);
                }
                renderContinuousPages();
            } else {
                renderPage(pageNum);
            }
        }
        
        /**
         * Render all or a range of pages for continuous scrolling
         */
        function renderContinuousPages() {
            // Skip if PDF document isn't loaded yet
            if (!pdfDoc) {
                console.warn('Attempted to render continuous pages before PDF loaded');
                return;
            }
            
            console.log('Rendering continuous pages');
            
            // Only load a reasonable number of pages at once
            const numPages = pdfDoc.numPages;
            const startPageIndex = Math.max(1, pageNum - 1);
            const endPageIndex = Math.min(numPages, startPageIndex + 4); // Load 5 pages at a time
            
            // Get continuous container
            const continuousContainer = document.getElementById('continuousContainer');
            if (!continuousContainer) {
                console.error('No continuous container found');
                return;
            }
            
            // Clear container
            continuousContainer.innerHTML = '';
            
            // Create loading message
            const loadingMessage = document.createElement('div');
            loadingMessage.className = 'continuous-loading';
            loadingMessage.innerHTML = '<div class="spinner-sm"></div> Loading pages...';
            continuousContainer.appendChild(loadingMessage);
            
            console.log(`Rendering continuous pages ${startPageIndex} to ${endPageIndex}`);
            
            // Render pages sequentially to ensure proper order
            const renderSequential = (index) => {
                if (index > endPageIndex) {
                    // All pages rendered
                    loadingMessage.remove();
                    setupScrollDetection();
                    return;
                }
                
                renderContinuousPage(index, continuousContainer).then(() => {
                    renderSequential(index + 1);
                });
            };
            
            // Start rendering from the first page
            renderSequential(startPageIndex);
            
            // Update progress based on the first visible page
            updateProgressBar();
        }
        
        /**
         * Render a single page in continuous mode
         */
        function renderContinuousPage(pageIndex, container, prepend = false) {
            return new Promise((resolve) => {
                // Skip if this page is already rendered or in progress
                if (renderedPages.has(pageIndex) || document.querySelector(`.page-wrapper[data-page-index="${pageIndex}"]`)) {
                    console.log(`Page ${pageIndex} already rendered or in progress, skipping`);
                    resolve();
                    return;
                }
                
                // Mark as in progress to prevent duplicate rendering attempts
                renderedPages.add(pageIndex);
                
                console.log(`Rendering page ${pageIndex} in continuous mode`);
                
                try {
                    pdfDoc.getPage(pageIndex).then(function(page) {
                        try {
                            // Create a wrapper for this page
                            const pageWrapper = document.createElement('div');
                            pageWrapper.className = 'page-wrapper';
                            pageWrapper.dataset.pageIndex = pageIndex;
                            
                            // Create a canvas for this page
                            const pageCanvas = document.createElement('canvas');
                            pageCanvas.className = 'pdf-page';
                            pageWrapper.appendChild(pageCanvas);
                            
                            // Add page number indicator
                            const pageIndicator = document.createElement('div');
                            pageIndicator.className = 'page-indicator';
                            pageIndicator.textContent = `Page ${pageIndex}`;
                            pageWrapper.appendChild(pageIndicator);
                            
                            // Add to document (prepend if requested, otherwise append)
                            if (prepend && container.firstChild) {
                                container.insertBefore(pageWrapper, container.firstChild);
                            } else {
                                container.appendChild(pageWrapper);
                            }
                            
                            // Store canvas for later reference
                            pageCanvases.push(pageCanvas);
                            
                            // Get device pixel ratio
                            const devicePixelRatio = window.devicePixelRatio || 1;
                            
                            // Calculate scale for fitting page width
                            const viewportWidth = container.clientWidth - 40; // account for padding
                            const originalViewport = page.getViewport({ scale: 1 });
                            
                            // Calculate scale to fit width
                            const widthScale = viewportWidth / originalViewport.width;
                            
                            // Apply user scale on top of fit scale
                            const viewport = page.getViewport({ scale: widthScale * scale });
                            
                            // Set canvas dimensions accounting for device pixel ratio for higher resolution
                            const ctx = pageCanvas.getContext('2d');
                            pageCanvas.width = Math.floor(viewport.width * devicePixelRatio);
                            pageCanvas.height = Math.floor(viewport.height * devicePixelRatio);
                            pageCanvas.style.width = Math.floor(viewport.width) + "px";
                            pageCanvas.style.height = Math.floor(viewport.height) + "px";
                            
                            // Reset context and prepare for high-quality rendering
                            ctx.setTransform(1, 0, 0, 1, 0, 0);
                            ctx.clearRect(0, 0, pageCanvas.width, pageCanvas.height);
                            
                            // Apply current antialiasing setting
                            ctx.imageSmoothingEnabled = useAntialiasing;
                            ctx.imageSmoothingQuality = useAntialiasing ? 'high' : 'low';
                            
                            // Scale context to ensure correct rendering
                            ctx.scale(devicePixelRatio, devicePixelRatio);
                            
                            // Render PDF page into canvas context with high quality settings
                            const renderContext = {
                                canvasContext: ctx,
                                viewport: viewport,
                                enableWebGL: true,
                                renderInteractiveForms: true,
                                textLayer: true
                            };
                            
                            const renderTask = page.render(renderContext);
                            
                            // Wait for rendering to finish
                            renderTask.promise.then(function() {
                                console.log(`Page ${pageIndex} rendered successfully`);
                                resolve();
                            }).catch(function(error) {
                                console.error(`Error rendering page ${pageIndex}:`, error);
                                // Add error indicator to the page
                                pageWrapper.classList.add('render-error');
                                const errorIndicator = document.createElement('div');
                                errorIndicator.className = 'page-error';
                                errorIndicator.innerHTML = '<i class="bi bi-exclamation-triangle"></i> Error rendering page';
                                pageWrapper.appendChild(errorIndicator);
                                resolve(); // Resolve anyway to continue with other pages
                            });
                        } catch (error) {
                            console.error(`Error setting up page ${pageIndex}:`, error);
                            resolve();
                        }
                    }).catch(function(error) {
                        console.error(`Error getting page ${pageIndex}:`, error);
                        resolve(); // Resolve anyway to continue with other pages
                    });
                } catch (error) {
                    console.error(`Unexpected error rendering page ${pageIndex}:`, error);
                    resolve();
                }
            });
        }
        
        /**
         * Set up scroll detection to dynamically load more pages
         */
        function setupScrollDetection() {
            const viewerContainer = document.getElementById('viewerContainer');
            const continuousContainer = document.getElementById('continuousContainer');
            
            // Remove previous event listener if exists
            if (viewerContainer._scrollHandler) {
                viewerContainer.removeEventListener('scroll', viewerContainer._scrollHandler);
            }
            
            // Skip if not in continuous mode
            if (!continuousScrollMode) return;
            
            // Set up scroll detection variables
            let isScrolling = false;
            let lastScrollTop = viewerContainer.scrollTop;
            let scrollTimer = null;
            
            // Get min and max page numbers currently rendered
            function getRenderedPageRange() {
                const pageWrappers = document.querySelectorAll('.page-wrapper');
                if (pageWrappers.length === 0) return { min: 0, max: 0 };
                
                let minPage = Infinity;
                let maxPage = -Infinity;
                
                pageWrappers.forEach(wrapper => {
                    const pageIndex = parseInt(wrapper.dataset.pageIndex);
                    minPage = Math.min(minPage, pageIndex);
                    maxPage = Math.max(maxPage, pageIndex);
                });
                
                return { min: minPage, max: maxPage };
            }
            
            // Handle loading of pages when scrolling
            function handleScroll() {
                if (isScrolling) return;
                isScrolling = true;
                
                cancelAnimationFrame(scrollTimer);
                
                scrollTimer = requestAnimationFrame(() => {
                    const scrollTop = viewerContainer.scrollTop;
                    const clientHeight = viewerContainer.clientHeight;
                    const scrollHeight = viewerContainer.scrollHeight;
                    
                    // Determine scroll direction
                    const scrollingDown = scrollTop > lastScrollTop;
                    lastScrollTop = scrollTop;
                    
                    // Update current page based on scroll position
                    updateCurrentPageFromScroll();
                    
                    // Get current range of rendered pages
                    const pageRange = getRenderedPageRange();
                    
                    // When scrolling down, check if we need to load more pages at the bottom
                    if (scrollingDown) {
                        const scrollBottom = scrollTop + clientHeight;
                        const scrollRatio = scrollBottom / scrollHeight;
                        
                        // If we're near the bottom and not at the end of the document
                        if (scrollRatio > 0.8 && pageRange.max < pdfDoc.numPages) {
                            const nextPageToLoad = pageRange.max + 1;
                            if (nextPageToLoad <= pdfDoc.numPages && !renderedPages.has(nextPageToLoad)) {
                                console.log(`Near bottom, loading next page ${nextPageToLoad}`);
                                renderContinuousPage(nextPageToLoad, continuousContainer);
                            }
                        }
                    } 
                    // When scrolling up, check if we need to load more pages at the top
                    else if (scrollTop < 500 && pageRange.min > 1) {
                        const prevPageToLoad = pageRange.min - 1;
                        if (prevPageToLoad >= 1 && !renderedPages.has(prevPageToLoad)) {
                            console.log(`Near top, loading previous page ${prevPageToLoad}`);
                            
                            // Remember current scroll position and height
                            const beforeHeight = continuousContainer.scrollHeight;
                            const beforeScrollTop = viewerContainer.scrollTop;
                            
                            renderContinuousPage(prevPageToLoad, continuousContainer, true).then(() => {
                                // After rendering, adjust scroll position to maintain view
                                const afterHeight = continuousContainer.scrollHeight;
                                const heightDiff = afterHeight - beforeHeight;
                                if (heightDiff > 0) {
                                    viewerContainer.scrollTop = beforeScrollTop + heightDiff;
                                }
                            });
                        }
                    }
                    
                    // Periodically clean up off-screen pages to improve performance
                    // Use a counter to avoid doing this on every scroll event
                    if (!viewerContainer._cleanupCounter) viewerContainer._cleanupCounter = 0;
                    viewerContainer._cleanupCounter++;
                    
                    if (viewerContainer._cleanupCounter % 10 === 0) {
                        cleanupOffscreenPages();
                    }
                    
                    isScrolling = false;
                });
            }
            
            // Store the handler so we can remove it later
            viewerContainer._scrollHandler = handleScroll;
            
            // Add scroll event listener
            viewerContainer.addEventListener('scroll', viewerContainer._scrollHandler, { passive: true });
        }
        
        /**
         * Update current page based on scroll position
         */
        function updateCurrentPageFromScroll() {
            if (!continuousScrollMode) return;
            
            const viewerContainer = document.getElementById('viewerContainer');
            const pageWrappers = document.querySelectorAll('.page-wrapper');
            
            if (pageWrappers.length === 0) return;
            
            // Find the page most visible in the viewport
            let mostVisiblePage = null;
            let maxVisibleArea = 0;
            
            // Get viewport dimensions
            const viewportTop = viewerContainer.scrollTop;
            const viewportHeight = viewerContainer.clientHeight;
            const viewportBottom = viewportTop + viewportHeight;
            
            // Check each page's visibility
            pageWrappers.forEach(wrapper => {
                const rect = wrapper.getBoundingClientRect();
                const wrapperTop = wrapper.offsetTop - viewerContainer.offsetTop;
                const wrapperBottom = wrapperTop + wrapper.offsetHeight;
                
                // Skip if completely out of viewport
                if (wrapperBottom < viewportTop || wrapperTop > viewportBottom) return;
                
                // Calculate how much of this page is visible
                const visibleTop = Math.max(wrapperTop, viewportTop);
                const visibleBottom = Math.min(wrapperBottom, viewportBottom);
                const visibleHeight = Math.max(0, visibleBottom - visibleTop);
                const visibleArea = visibleHeight * rect.width;
                
                if (visibleArea > maxVisibleArea) {
                    maxVisibleArea = visibleArea;
                    mostVisiblePage = wrapper;
                }
            });
            
            if (mostVisiblePage) {
                const newPageNum = parseInt(mostVisiblePage.dataset.pageIndex);
                if (newPageNum !== pageNum) {
                    updateUIForCurrentPage(newPageNum);
                }
            }
        }
        
        /**
         * Render a specific page of the PDF
         */
        function renderPage(num) {
            pageRendering = true;
            
            console.log(`Rendering page ${num} in single page mode`);
            
            // Update UI page numbers
            pageNumDisplay.textContent = num;
            pageNumMobileDisplay.textContent = num;
            
            // Update progress bar
            updateProgressBar();
            
            // Update reading progress on the server
            saveProgress(num);
            
            // Get the page
            pdfDoc.getPage(num).then(function(page) {
                // Get device pixel ratio
                const devicePixelRatio = window.devicePixelRatio || 1;
                
                // Get the viewer container
                const viewerContainer = document.getElementById('viewerContainer');
                if (!viewerContainer) {
                    console.error('viewerContainer not found when rendering page');
                    return;
                }
                
                // Ensure canvas is in the DOM
                if (canvas && !viewerContainer.contains(canvas)) {
                    viewerContainer.appendChild(canvas);
                }
                
                // Adjust scale based on viewport
                const viewportWidth = viewerContainer.clientWidth - 20;
                const viewportHeight = viewerContainer.clientHeight - 20;
                
                console.log(`Viewport dimensions: ${viewportWidth} x ${viewportHeight}`);
                
                const originalViewport = page.getViewport({ scale: 1 });
                
                // Calculate scale to fit page within container
                const widthScale = viewportWidth / originalViewport.width;
                const heightScale = viewportHeight / originalViewport.height;
                const fitScale = Math.min(widthScale, heightScale);
                
                // Apply user scale on top of fit scale
                const viewport = page.getViewport({ scale: fitScale * scale });
                
                console.log(`Rendering with scale: ${fitScale * scale} (fit: ${fitScale}, user: ${scale})`);
                
                // Set canvas dimensions accounting for device pixel ratio for higher resolution
                canvas.width = Math.floor(viewport.width * devicePixelRatio);
                canvas.height = Math.floor(viewport.height * devicePixelRatio);
                canvas.style.width = Math.floor(viewport.width) + "px";
                canvas.style.height = Math.floor(viewport.height) + "px";
                
                // Reset context and prepare for high-quality rendering
                ctx.setTransform(1, 0, 0, 1, 0, 0);
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                
                // Apply current antialiasing setting
                ctx.imageSmoothingEnabled = useAntialiasing;
                ctx.imageSmoothingQuality = useAntialiasing ? 'high' : 'low';
                
                // Scale context to ensure correct rendering
                ctx.scale(devicePixelRatio, devicePixelRatio);
                
                // Render PDF page into canvas context with high quality settings
                const renderContext = {
                    canvasContext: ctx,
                    viewport: viewport,
                    enableWebGL: true,
                    renderInteractiveForms: true,
                    textLayer: true
                };
                
                const renderTask = page.render(renderContext);
                
                // Wait for rendering to finish
                renderTask.promise.then(function() {
                    console.log(`Page ${num} rendered successfully`);
                    pageRendering = false;
                    
                    // Check if there's a pending page
                    if (pageNumPending !== null) {
                        renderPage(pageNumPending);
                        pageNumPending = null;
                    }
                }).catch(function(error) {
                    console.error('Error rendering page:', error);
                    pageRendering = false;
                    errorMessage.style.display = 'flex';
                    errorText.textContent = 'Error rendering page: ' + error.message;
                });
            }).catch(function(error) {
                console.error('Error getting page:', error);
                pageRendering = false;
                errorMessage.style.display = 'flex';
                errorText.textContent = 'Error getting page: ' + error.message;
            });
        }
        
        /**
         * Display previous page
         */
        function showPrevPage() {
            if (pageNum <= 1) return;
            
            pageNum--;
            
            if (continuousScrollMode) {
                scrollToPageInContinuousMode(pageNum);
            } else {
                queueRenderPage(pageNum);
            }
        }
        
        /**
         * Display next page
         */
        function showNextPage() {
            if (pageNum >= pdfDoc.numPages) return;
            
            pageNum++;
            
            if (continuousScrollMode) {
                scrollToPageInContinuousMode(pageNum);
            } else {
                queueRenderPage(pageNum);
            }
        }
        
        /**
         * Scroll to a specific page in continuous mode
         */
        function scrollToPageInContinuousMode(targetPageNum) {
            // Find the wrapper for the target page
            const targetWrapper = document.querySelector(`.page-wrapper[data-page-index="${targetPageNum}"]`);
            const viewerContainer = document.getElementById('viewerContainer');
            const continuousContainer = document.getElementById('continuousContainer');
            
            // If page isn't in DOM yet, we need to render it and nearby pages
            if (!targetWrapper) {
                // If target page is ahead of current page
                if (targetPageNum > pageNum) {
                    // Calculate page range to render
                    const startPage = Math.max(1, pageNum);
                    const endPage = Math.min(pdfDoc.numPages, targetPageNum + 2);
                    
                    // Render pages leading to target
                    console.log(`Target page ${targetPageNum} not rendered yet, rendering pages ${startPage} to ${endPage}`);
                    
                    // Show loading indicator
                    const loadingMessage = document.createElement('div');
                    loadingMessage.className = 'continuous-loading jump-loading';
                    loadingMessage.innerHTML = '<div class="spinner-sm"></div> Loading page...';
                    continuousContainer.appendChild(loadingMessage);
                    
                    // Render pages sequentially until we reach target
                    const renderSequential = (index) => {
                        if (index > endPage) {
                            // All pages rendered
                            loadingMessage.remove();
                            
                            // Now try to scroll to the target page
                            setTimeout(() => {
                                const newTargetWrapper = document.querySelector(`.page-wrapper[data-page-index="${targetPageNum}"]`);
                                if (newTargetWrapper) {
                                    scrollToElement(newTargetWrapper);
                                    
                                    // Update UI elements
                                    updateUIForCurrentPage(targetPageNum);
                                }
                            }, 100);
                            
                            return;
                        }
                        
                        renderContinuousPage(index, continuousContainer).then(() => {
                            renderSequential(index + 1);
                        });
                    };
                    
                    renderSequential(startPage);
                    
                } else { // If target page is before current page
                    // Calculate page range to render
                    const startPage = Math.max(1, targetPageNum - 2);
                    const endPage = Math.min(pdfDoc.numPages, pageNum);
                    
                    // Render pages leading to target
                    console.log(`Target page ${targetPageNum} not rendered yet, rendering pages ${startPage} to ${endPage}`);
                    
                    // Show loading indicator
                    const loadingMessage = document.createElement('div');
                    loadingMessage.className = 'continuous-loading jump-loading';
                    loadingMessage.innerHTML = '<div class="spinner-sm"></div> Loading page...';
                    continuousContainer.appendChild(loadingMessage);
                    
                    // Remember current scroll position and height
                    const beforeHeight = continuousContainer.scrollHeight;
                    const beforeScrollTop = viewerContainer.scrollTop;
                    
                    // Render pages sequentially until we reach target
                    const renderSequential = (index) => {
                        if (index > endPage) {
                            // All pages rendered
                            loadingMessage.remove();
                            
                            // Now try to scroll to the target page
                            setTimeout(() => {
                                const newTargetWrapper = document.querySelector(`.page-wrapper[data-page-index="${targetPageNum}"]`);
                                if (newTargetWrapper) {
                                    scrollToElement(newTargetWrapper);
                                    
                                    // Update UI elements
                                    updateUIForCurrentPage(targetPageNum);
                                }
                            }, 100);
                            
                            return;
                        }
                        
                        renderContinuousPage(index, continuousContainer, true).then(() => {
                            // Adjust scroll position to maintain view when adding content at the top
                            const afterHeight = continuousContainer.scrollHeight;
                            const heightDiff = afterHeight - beforeHeight;
                            if (heightDiff > 0 && index < targetPageNum) {
                                viewerContainer.scrollTop = beforeScrollTop + heightDiff;
                            }
                            
                            renderSequential(index + 1);
                        });
                    };
                    
                    renderSequential(startPage);
                }
            } else {
                // If page is already in the DOM, just scroll to it
                scrollToElement(targetWrapper);
                
                // Update UI elements
                updateUIForCurrentPage(targetPageNum);
            }
        }
        
        /**
         * Smooth scroll to an element
         */
        function scrollToElement(element) {
            const viewerContainer = document.getElementById('viewerContainer');
            
            // Scroll with animation
            viewerContainer.scrollTo({
                top: element.offsetTop - viewerContainer.offsetTop,
                behavior: 'smooth'
            });
        }
        
        /**
         * Update UI elements to reflect the current page
         */
        function updateUIForCurrentPage(pageNumber) {
            pageNum = pageNumber;
            pageNumDisplay.textContent = pageNum;
            pageNumMobileDisplay.textContent = pageNum;
            
            // Update TOC highlighting
            updateTOCHighlight(pageNumber);
            
            // Update progress
            updateProgressBar();
            saveProgress(pageNum);
        }
        
        /**
         * Update highlighting in the table of contents
         */
        function updateTOCHighlight(pageNumber) {
            if (!outline || outline.length === 0) return;
            
            // Remove active class from all TOC links
            document.querySelectorAll('.toc-link').forEach(link => {
                link.classList.remove('active');
            });
            
            // Find the TOC item that corresponds to this page or the closest preceding page
            let closestItem = null;
            let closestPageNum = 0;
            
            // Function to search through the outline items recursively
            function findClosestTOCItem(items) {
                for (const item of items) {
                    if (item.dest) {
                        // Try to get the page number for this item
                        (function(tocItem) {
                            // Process the destination to get the page number
                            let dest = tocItem.dest;
                            if (typeof dest === 'string') {
                                pdfDoc.getDestination(dest).then(function(destArray) {
                                    processDestArray(destArray, tocItem);
                                });
                            } else if (Array.isArray(dest)) {
                                processDestArray(dest, tocItem);
                            }
                        })(item);
                    }
                    
                    // Recursively check nested items
                    if (item.items && item.items.length > 0) {
                        findClosestTOCItem(item.items);
                    }
                }
            }
            
            function processDestArray(destArray, tocItem) {
                if (!destArray || !Array.isArray(destArray) || destArray.length < 1) return;
                
                // Get the page reference
                const pageRef = destArray[0];
                
                // Get the page number for this reference
                pdfDoc.getPageIndex(pageRef).then(function(pageIndex) {
                    const itemPageNum = pageIndex + 1; // Convert to 1-based
                    
                    // If this item is for the current page or a preceding page that's closer to current
                    if (itemPageNum <= pageNumber && itemPageNum > closestPageNum) {
                        closestPageNum = itemPageNum;
                        closestItem = tocItem;
                        
                        // Find the link element for this item
                        const allLinks = document.querySelectorAll('.toc-link');
                        for (const link of allLinks) {
                            if (link.textContent === tocItem.title) {
                                // Mark this link as active
                                link.classList.add('active');
                                
                                // Scroll the TOC to make this visible if needed
                                const tocContent = document.getElementById('tocContent');
                                if (tocContent) {
                                    const linkTop = link.offsetTop;
                                    const tocScrollTop = tocContent.scrollTop;
                                    const tocHeight = tocContent.clientHeight;
                                    
                                    // If link is out of view, scroll to make it visible
                                    if (linkTop < tocScrollTop || linkTop > tocScrollTop + tocHeight) {
                                        tocContent.scrollTop = linkTop - (tocHeight / 2);
                                    }
                                }
                                
                                break;
                            }
                        }
                    }
                });
            }
            
            // Start the search
            findClosestTOCItem(outline);
        }
        
        /**
         * Queue a page for rendering
         */
        function queueRenderPage(num) {
            if (pageRendering) {
                pageNumPending = num;
            } else {
                renderPage(num);
            }
        }
        
        /**
         * Update the progress bar
         */
        function updateProgressBar() {
            if (!pdfDoc) return;
            
            const progress = (pageNum / pdfDoc.numPages) * 100;
            progressIndicator.style.width = progress + '%';
        }
        
        /**
         * Save reading progress to the server
         */
        function saveProgress(page) {
            fetch('/reading-session/update-progress', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    session_id: sessionId,
                    current_page: page,
                    is_completed: page === pdfDoc.numPages
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
            scale += 0.1;
            saveZoomLevel();
            showZoomIndicator();
            
            if (continuousScrollMode) {
                resetView();
                renderCurrentView();
            } else {
                queueRenderPage(pageNum);
            }
        }
        
        /**
         * Handle zoom out
         */
        function zoomOut() {
            if (scale <= 0.5) return;
            scale -= 0.1;
            saveZoomLevel();
            showZoomIndicator();
            
            if (continuousScrollMode) {
                resetView();
                renderCurrentView();
            } else {
                queueRenderPage(pageNum);
            }
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
         * Save zoom level to localStorage
         */
        function saveZoomLevel() {
            // Save mode-specific zoom level
            if (continuousScrollMode) {
                localStorage.setItem(`zoomLevel_continuous_${sessionId}`, scale.toString());
                localStorage.setItem('globalZoomLevel_continuous', scale.toString());
            } else {
                localStorage.setItem(`zoomLevel_single_${sessionId}`, scale.toString());
                localStorage.setItem('globalZoomLevel_single', scale.toString());
            }
        }
        
        /**
         * Load zoom level from localStorage
         */
        function loadZoomLevel() {
            let zoomKey, globalZoomKey;
            
            // Determine which zoom settings to load based on current mode
            if (continuousScrollMode) {
                zoomKey = `zoomLevel_continuous_${sessionId}`;
                globalZoomKey = 'globalZoomLevel_continuous';
            } else {
                zoomKey = `zoomLevel_single_${sessionId}`;
                globalZoomKey = 'globalZoomLevel_single';
            }
            
            // Try to load book-specific zoom level first
            const savedScale = localStorage.getItem(zoomKey);
            if (savedScale) {
                const parsedScale = parseFloat(savedScale);
                if (!isNaN(parsedScale) && parsedScale >= 0.5 && parsedScale <= 3.0) {
                    scale = parsedScale;
                    return;
                }
            }
            
            // Fall back to global zoom preference for the current mode
            const globalScale = localStorage.getItem(globalZoomKey);
            if (globalScale) {
                const parsedScale = parseFloat(globalScale);
                if (!isNaN(parsedScale) && parsedScale >= 0.5 && parsedScale <= 3.0) {
                    scale = parsedScale;
                }
            }
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
            
            // Re-render with new setting based on current mode
            if (continuousScrollMode) {
                // Show loading state
                loadingSpinner.style.display = 'flex';
                
                // Use setTimeout to allow the loading spinner to show before rendering
                setTimeout(() => {
                    // Reset and re-render all visible pages for continuous mode
                    resetView();
                    renderCurrentView();
                    
                    // Hide loading spinner
                    loadingSpinner.style.display = 'none';
                }, 50);
            } else {
                // Just re-render the current page for single page mode
                queueRenderPage(pageNum);
            }
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
         * Toggle continuous scroll mode
         */
        function toggleContinuousScroll() {
            // Prevent rapid toggling by adding a brief debounce
            if (window.isTogglingScrollMode) return;
            window.isTogglingScrollMode = true;
            
            // Show loading state
            loadingSpinner.style.display = 'flex';
            
            // Short delay to allow loading indicator to appear
            setTimeout(() => {
                // Toggle the mode
                continuousScrollMode = !continuousScrollMode;
                
                // Update button visual state
                if (continuousScrollMode) {
                    continuousScrollBtn.classList.add('active');
                } else {
                    continuousScrollBtn.classList.remove('active');
                }
                
                // Save preference
                localStorage.setItem('pdfContinuousScroll', continuousScrollMode ? 'true' : 'false');
                
                // Load zoom level appropriate for the current mode
                loadZoomLevel();
                
                // Reset the view and render with new mode
                resetView();
                renderCurrentView();
                
                // Hide loading spinner
                loadingSpinner.style.display = 'none';
                
                // Show indicator with current state
                const message = continuousScrollMode ? 'Continuous scroll: ON' : 'Continuous scroll: OFF';
                showMessage(message);
                
                // Clear toggle lock
                setTimeout(() => {
                    window.isTogglingScrollMode = false;
                }, 500);
            }, 100);
        }
        
        /**
         * Load continuous scroll setting from localStorage
         */
        function loadContinuousScrollSetting() {
            const savedSetting = localStorage.getItem('pdfContinuousScroll');
            if (savedSetting !== null) {
                continuousScrollMode = savedSetting === 'true';
            } else {
                // If no preference is saved, default to continuous mode
                continuousScrollMode = true;
            }
            
            // Update button visual state
            if (continuousScrollMode) {
                continuousScrollBtn.classList.add('active');
            } else {
                continuousScrollBtn.classList.remove('active');
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
        
        // Event listener for continuous scroll toggle
        continuousScrollBtn.addEventListener('click', toggleContinuousScroll);
        
        // Set up keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowRight') {
                showNextPage();
            } else if (e.key === 'ArrowLeft') {
                showPrevPage();
            }
        });
        
        // Handle window resize
        window.addEventListener('resize', function() {
            // Re-render current view to adjust to new size
            if (continuousScrollMode) {
                // Just re-render the current continuous view
                resetView();
                renderCurrentView();
            } else {
                // Just re-render the current page
                queueRenderPage(pageNum);
            }
        });

        /**
         * Clean up pages that are far outside the current viewport
         * to improve performance and reduce memory usage
         */
        function cleanupOffscreenPages() {
            if (!continuousScrollMode) return;
            
            const viewerContainer = document.getElementById('viewerContainer');
            const pageWrappers = document.querySelectorAll('.page-wrapper');
            
            if (pageWrappers.length <= 10) return; // Don't cleanup if we have few pages
            
            const viewportTop = viewerContainer.scrollTop;
            const viewportHeight = viewerContainer.clientHeight;
            const viewportBottom = viewportTop + viewportHeight;
            
            // Keep a buffer of pages before and after the viewport
            const bufferSize = viewportHeight * 2; // Keep pages within 2x viewport height
            const minKeepTop = viewportTop - bufferSize;
            const maxKeepBottom = viewportBottom + bufferSize;
            
            let pagesToRemove = [];
            
            // Find pages that are far outside the viewport
            pageWrappers.forEach(wrapper => {
                const pageIndex = parseInt(wrapper.dataset.pageIndex);
                const wrapperTop = wrapper.offsetTop - viewerContainer.offsetTop;
                const wrapperBottom = wrapperTop + wrapper.offsetHeight;
                
                // If the page is far outside the viewport, mark for removal
                if (wrapperBottom < minKeepTop || wrapperTop > maxKeepBottom) {
                    // Don't remove pages very close to the current page
                    if (Math.abs(pageIndex - pageNum) > 5) {
                        pagesToRemove.push({
                            wrapper: wrapper,
                            pageIndex: pageIndex
                        });
                    }
                }
            });
            
            // Sort so we keep pages closest to the current page
            pagesToRemove.sort((a, b) => {
                return Math.abs(b.pageIndex - pageNum) - Math.abs(a.pageIndex - pageNum);
            });
            
            // Only remove a few pages at a time to avoid jank
            const maxRemoveCount = Math.min(3, pagesToRemove.length);
            
            if (maxRemoveCount > 0) {
                console.log(`Cleaning up ${maxRemoveCount} offscreen pages`);
                
                for (let i = 0; i < maxRemoveCount; i++) {
                    const page = pagesToRemove[i];
                    page.wrapper.remove();
                    
                    // Remove from canvas tracking
                    const canvasElement = page.wrapper.querySelector('canvas');
                    if (canvasElement) {
                        const canvasIndex = pageCanvases.indexOf(canvasElement);
                        if (canvasIndex !== -1) {
                            pageCanvases.splice(canvasIndex, 1);
                        }
                    }
                    
                    // Remove from rendered pages tracking
                    renderedPages.delete(page.pageIndex);
                }
            }
        }

        // Initialize TOC toggle button
        toggleTOCBtn.addEventListener('click', function() {
            tocSidebar.classList.toggle('active');
        });

        // Initialize TOC close button
        closeTOCBtn.addEventListener('click', function() {
            tocSidebar.classList.remove('active');
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

.pdf-reader-content {
    display: flex;
    flex: 1;
    position: relative;
    overflow: hidden;
    height: calc(100vh - 60px); /* Adjust for navbar height */
}

.main-content-wrapper {
    flex: 1;
    display: flex;
    flex-direction: column;
    position: relative;
    overflow: hidden;
    height: 100%;
}

#viewerContainer {
    flex: 1;
    position: relative;
    overflow: auto;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    background-color: #444;
    padding-top: 20px;
    width: 100%;
    z-index: 1;
    height: calc(100% - 4px); /* Subtract progress bar height */
}

#pdfCanvas {
    box-shadow: 0 0 8px rgba(0, 0, 0, 0.15);
    margin-bottom: 20px;
    image-rendering: -webkit-optimize-contrast;
    image-rendering: crisp-edges;
    transform: translateZ(0);
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

.reading-progress-bar {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 4px;
    background-color: #dee2e6;
    width: 100%;
    z-index: 2;
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

.continuous-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 40px;
    width: 100%;
    padding: 20px;
    min-height: 100%;
    background-color: #444;
}

.continuous-loading {
    padding: 10px 20px;
    background-color: rgba(0, 0, 0, 0.7);
    color: white;
    border-radius: 20px;
    margin: 20px 0;
    position: sticky;
    top: 20px;
    z-index: 10;
    align-self: center;
    display: flex;
    align-items: center;
    gap: 10px;
}

.spinner-sm {
    width: 1.5rem;
    height: 1.5rem;
    border: 3px solid rgba(255, 255, 255, 0.2);
    border-left-color: #fff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

.page-wrapper {
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 100%;
    max-width: 100%;
    margin-bottom: 50px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    background-color: white;
    border-radius: 2px;
}

.pdf-page {
    display: block;
    background-color: white;
    max-width: 100%;
    height: auto;
    image-rendering: -webkit-optimize-contrast;
    image-rendering: crisp-edges;
    transform: translateZ(0);
}

.page-indicator {
    position: absolute;
    bottom: -25px;
    right: 0;
    background-color: rgba(0, 0, 0, 0.5);
    color: white;
    padding: 2px 8px;
    border-radius: 10px;
    font-size: 12px;
}

.page-error {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background-color: rgba(220, 53, 69, 0.8);
    color: white;
    padding: 10px 20px;
    border-radius: 5px;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.render-error {
    background-color: #f8d7da;
    border: 1px solid #f5c6cb;
    min-height: 200px;
    position: relative;
}

.jump-loading {
    position: fixed;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
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

/* Table of Contents Styles */
.toc-sidebar {
    width: 300px;
    background-color: #f8f9fa;
    border-right: 1px solid #dee2e6;
    display: flex;
    flex-direction: column;
    transition: transform 0.3s ease;
    transform: translateX(-100%);
    position: absolute;
    top: 0;
    left: 0;
    bottom: 0;
    z-index: 100;
    box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
}

.toc-sidebar.active {
    transform: translateX(0);
}

.toc-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem;
    border-bottom: 1px solid #dee2e6;
}

.toc-header h5 {
    margin: 0;
    font-size: 1.1rem;
}

.close-toc-btn {
    background: none;
    border: none;
    color: #6c757d;
    cursor: pointer;
    font-size: 1.2rem;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

.toc-content {
    flex: 1;
    overflow-y: auto;
    padding: 1rem 0;
}

.toc-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.toc-item {
    padding: 0;
    margin: 0;
}

.toc-link {
    display: block;
    padding: 0.5rem 1rem;
    color: #495057;
    text-decoration: none;
    cursor: pointer;
    transition: background-color 0.2s;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.toc-link:hover {
    background-color: #e9ecef;
}

.toc-link.active {
    background-color: #007bff;
    color: white;
}

.toc-list-nested {
    list-style: none;
    padding-left: 1.5rem;
}

.toc-loading, .toc-empty {
    padding: 1rem;
    text-align: center;
    color: #6c757d;
}

/* Small spinner for TOC loading */
.spinner-sm {
    width: 1.5rem;
    height: 1.5rem;
    border: 2px solid rgba(0, 0, 0, 0.1);
    border-left-color: #007bff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 1rem;
    display: block;
}

.toc-empty i {
    font-size: 2rem;
    margin-bottom: 1rem;
    color: #adb5bd;
}

/* Responsive styles */
@media (max-width: 767px) {
    .toc-sidebar {
        width: 250px;
    }
}
</style>

<?php
include $footerPath;
?> 