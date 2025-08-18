// ATTEMPT 11: Use exact legacy DOM manipulation approach within poster service

class GenMapperPoster {
    constructor() {
        this.initialized = false;
        this.genMapperCSS = null;
        this.init();
    }
    
    init() {
        console.log('GenMapperPoster: Initializing with legacy DOM manipulation...');
        this.extractGenMapperCSS();
        document.addEventListener('generatePoster', this.handleGeneratePoster.bind(this));
        this.initialized = true;
        console.log('GenMapperPoster: Poster service ready with legacy DOM approach');
    }
    
    extractGenMapperCSS() {
        try {
            console.log('GenMapperPoster: Extracting GenMapper CSS styling...');
            
            let extractedCSS = '';
            
            for (let stylesheet of document.styleSheets) {
                try {
                    let rules = stylesheet.cssRules || stylesheet.rules;
                    if (rules) {
                        for (let rule of rules) {
                            if (rule.type === CSSRule.STYLE_RULE) {
                                if (this.isGenMapperRule(rule.selectorText)) {
                                    extractedCSS += rule.cssText + '\n';
                                }
                            }
                        }
                    }
                } catch (e) {
                    console.log('GenMapperPoster: Skipped inaccessible stylesheet');
                }
            }
            
            if (!extractedCSS || extractedCSS.length < 100) {
                console.log('GenMapperPoster: Using fallback CSS styles');
                extractedCSS = this.getFallbackGenMapperCSS();
            }
            
            this.genMapperCSS = extractedCSS;
            console.log('GenMapperPoster: Extracted CSS length:', extractedCSS.length);
            
        } catch (error) {
            console.error('GenMapperPoster: Error extracting CSS:', error);
            this.genMapperCSS = this.getFallbackGenMapperCSS();
        }
    }
    
    isGenMapperRule(selectorText) {
        if (!selectorText) return false;
        
        const genMapperSelectors = [
            '.node', '.link', '.addNode', '.removeNode',
            '.node text', '.node:hover', '.node--active', '.node--inactive',
            '.link-text', '.group-links', '.group-nodes',
            'rect', 'line', 'circle', 'svg', '#genmapper-graph-svg'
        ];
        
        return genMapperSelectors.some(selector => 
            selectorText.includes(selector) || 
            selectorText.includes('genmapper') ||
            selectorText.includes('svg')
        );
    }
    
    getFallbackGenMapperCSS() {
        return `
            .node text {
                font: 15px sans-serif;
                text-anchor: middle;
                fill: #000;
                stroke: none;
            }
            
            .node:hover > rect, .node:hover > line {
                fill: lightskyblue;
            }
            
            .node--active text, .link-text--active {
                stroke: black;
                fill: black;
            }
            
            .node--inactive text, .link-text--inactive {
                stroke: #000;
                fill: #000;
                opacity: 0.4;
            }
            
            .node--inactive image {
                opacity: 0.4;
            }
            
            .node--inactive > rect, .node--inactive > line {
                stroke: #ddd;
            }
            
            .link {
                fill: none;
                stroke: #ccc;
                stroke-width: 2px;
            }
            
            .link-text {
                text-anchor: middle;
                font: 15px sans-serif;
            }
            
            rect {
                fill: white;
                stroke: #000;
                stroke-width: 1px;
            }
            
            .addNode rect {
                fill: #b3ffb3;
            }
            
            .addNode:hover rect {
                fill: lime;
            }
            
            .removeNode rect {
                fill: lightsalmon;
            }
            
            .removeNode:hover rect {
                fill: red;
            }
            
            .invisible-rect {
                fill-opacity: 0;
                stroke-opacity: 0;
            }
            
            svg {
                background: white;
            }
        `;
    }
    
    handleGeneratePoster(event) {
        const { printType, sourceElementId } = event.detail;
        console.log('GenMapperPoster: Using legacy DOM manipulation approach -', printType);
        
        try {
            const svgElement = document.getElementById(sourceElementId || 'genmapper-graph-svg');
            if (!svgElement) {
                console.error('GenMapperPoster: SVG element not found');
                this.showAlert('Error: Map display not found');
                return;
            }
            
            // ATTEMPT 11 FIX: Use exact legacy DOM manipulation approach
            const posterHtml = this.generatePosterWithLegacyDOMApproach(svgElement, printType);
            
            PrintDialog.openPrintDialog(posterHtml);
            
        } catch (error) {
            console.error('GenMapperPoster: Error generating legacy DOM poster:', error);
            this.showAlert('Error generating poster: ' + error.message);
        }
    }
    
    generatePosterWithLegacyDOMApproach(originalSvgElement, printType) {
        console.log('GenMapperPoster: Starting legacy DOM manipulation approach...');
        
        try {
            // ATTEMPT 11 FIX: Clone SVG and apply exact legacy transformations
            const svgClone = originalSvgElement.cloneNode(true);
            
            // Get node data exactly like legacy
            const nodeElements = svgClone.querySelectorAll('.node');
            const legacyDimensions = this.calculateLegacyMapDimensions(nodeElements);
            
            // Apply exact legacy DOM manipulations
            this.applyLegacyTransformations(svgClone, legacyDimensions, printType);
            
            // Generate minimal HTML wrapper (no complex layout)
            const posterHtml = this.generateMinimalHTMLWrapper(svgClone, printType);
            
            console.log('GenMapperPoster: Legacy DOM manipulation complete');
            return posterHtml;
            
        } catch (error) {
            console.error('GenMapperPoster: Error in legacy DOM approach:', error);
            throw new Error('Failed to generate poster with legacy DOM approach: ' + error.message);
        }
    }
    
    calculateLegacyMapDimensions(nodeElements) {
        console.log('GenMapperPoster: Calculating dimensions using exact legacy method...');
        
        let minX = 0, maxX = 0, minY = 0, maxY = 0;
        
        // ATTEMPT 11 FIX: Exact legacy node iteration
        nodeElements.forEach(node => {
            const transform = node.getAttribute('transform');
            if (transform) {
                const match = transform.match(/translate\((-?\d+\.?\d*),\s*(-?\d+\.?\d*)\)/);
                if (match) {
                    const x = parseFloat(match[1]);
                    const y = parseFloat(match[2]);
                    minX = Math.min(minX, x);
                    maxX = Math.max(maxX, x);
                    minY = Math.min(minY, y);
                    maxY = Math.max(maxY, y);
                }
            }
        });
        
        // ATTEMPT 11 FIX: Exact legacy constants and calculations
        const boxHeight = 80; // From legacy code
        const marginTop = 50;  // From legacy code
        
        const totalHeight = Math.max(600, marginTop + (maxY - minY) + boxHeight + marginTop);
        const totalWidthLeft = Math.max(500, -minX + boxHeight * 1.5 / 2 + 20);
        const totalWidthRight = Math.max(500, maxX + boxHeight * 1.5 / 2 + 20);
        
        console.log('GenMapperPoster: Legacy dimensions calculated -', {
            minX, maxX, minY, maxY,
            totalHeight, totalWidthLeft, totalWidthRight,
            totalMapWidth: totalWidthLeft + totalWidthRight,
            nodeCount: nodeElements.length
        });
        
        return {
            minX, maxX, minY, maxY,
            totalHeight, totalWidthLeft, totalWidthRight,
            totalMapWidth: totalWidthLeft + totalWidthRight,
            boxHeight, marginTop,
            nodeCount: nodeElements.length
        };
    }
    
    applyLegacyTransformations(svgClone, dimensions, printType) {
        console.log('GenMapperPoster: Applying exact legacy transformations...');
        
        const mainGroup = svgClone.querySelector('#maingroup');
        if (!mainGroup) {
            console.error('GenMapperPoster: Main group not found in SVG');
            return;
        }
        
        let translateX, translateY, svgWidth, svgHeight;
        
        if (printType === 'horizontal') {
            // ATTEMPT 11 FIX: Exact legacy horizontal calculations
            const printHeight = 700;
            const printWidth = 1200;
            
            svgWidth = printWidth;
            svgHeight = printHeight;
            
            const printScale = Math.min(1, 
                printWidth / dimensions.totalMapWidth, 
                printHeight / dimensions.totalHeight);
            
            translateX = dimensions.totalWidthLeft * printScale;
            translateY = dimensions.marginTop * printScale;
            
            // Apply exact legacy transform
            const legacyTransform = `translate(${translateX}, ${translateY}) scale(${printScale})`;
            mainGroup.setAttribute('transform', legacyTransform);
            
            console.log('GenMapperPoster: Legacy horizontal transform applied -', {
                printScale, translateX, translateY, 
                svgDimensions: `${svgWidth}x${svgHeight}`,
                transform: legacyTransform
            });
            
        } else {
            // ATTEMPT 11 FIX: Exact legacy vertical calculations
            svgWidth = dimensions.totalHeight;
            svgHeight = dimensions.totalMapWidth;
            
            translateX = dimensions.totalHeight - dimensions.marginTop;
            translateY = dimensions.totalWidthLeft;
            
            // Apply exact legacy transform for vertical
            const legacyTransform = `translate(${translateX}, ${translateY}) rotate(90)`;
            mainGroup.setAttribute('transform', legacyTransform);
            
            console.log('GenMapperPoster: Legacy vertical transform applied -', {
                translateX, translateY,
                svgDimensions: `${svgWidth}x${svgHeight}`,
                transform: legacyTransform
            });
        }
        
        // ATTEMPT 11 FIX: Set SVG attributes exactly like legacy
        svgClone.setAttribute('width', svgWidth);
        svgClone.setAttribute('height', svgHeight);
        svgClone.style.background = 'white';
        
        // Remove any existing styles that might interfere
        svgClone.removeAttribute('style');
        svgClone.style.background = 'white';
    }
    
    // ATTEMPT 13: Add proper top margin while maintaining horizontal centering
    
    generateMinimalHTMLWrapper(transformedSvg, printType) {
        console.log('GenMapperPoster: Generating minimal HTML wrapper with horizontal centering and top margin...');
        
        // ATTEMPT 13 FIX: Preserve orientation logic from previous attempts
        const orientation = printType === 'horizontal' ? 'landscape' : 'portrait';
        const pageWidth = printType === 'horizontal' ? '17in' : '11in';
        const pageHeight = printType === 'horizontal' ? '11in' : '17in';
        
        const minimalHtml = `
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <title>GenMapper Poster - Centered with Margin</title>
                <style>
                    /* ATTEMPT 13 FIX: Add top margin while preserving horizontal centering */
                    * {
                        margin: 0;
                        padding: 0;
                        box-sizing: border-box;
                    }
                    
                    body {
                        background: white;
                        overflow: hidden;
                        /* ATTEMPT 13 FIX: Center horizontally and add proper top margin */
                        display: flex;
                        justify-content: center;
                        align-items: flex-start;
                        min-height: 100vh;
                        padding-top: 0.5in;
                    }
                    
                    /* ATTEMPT 13 FIX: Ensure SVG is centered within flex container */
                    svg {
                        background: white;
                        display: block;
                    }
                    
                    /* Include extracted GenMapper CSS */
                    ${this.genMapperCSS}
                    
                    /* Print-specific styling */
                    @media print {
                        @page {
                            size: ${pageWidth} ${pageHeight};
                            margin: 0;
                        }
                        
                        body {
                            margin: 0 !important;
                            /* ATTEMPT 13 FIX: Maintain centering and top margin in print mode */
                            display: flex !important;
                            justify-content: center !important;
                            align-items: flex-start !important;
                            padding-top: 0.5in !important;
                        }
                        
                        svg {
                            background: white !important;
                        }
                        
                        * {
                            -webkit-print-color-adjust: exact !important;
                            color-adjust: exact !important;
                            print-color-adjust: exact !important;
                        }
                    }
                </style>
            </head>
            <body>
                ${transformedSvg.outerHTML}
            </body>
            </html>
        `;
        
        console.log('GenMapperPoster: Minimal HTML wrapper with horizontal centering and top margin generated for', orientation);
        return minimalHtml;
    }
    
    showAlert(message) {
        if (window.genmapper && typeof window.genmapper.displayAlert === 'function') {
            window.genmapper.displayAlert(message);
        } else {
            alert(message);
        }
    }
}

// Enhanced PrintDialog with better orientation support
class PrintDialog {
    static init() {
        console.log('PrintDialog: Initializing with legacy DOM support...');
        document.addEventListener('openPrintDialog', this.handleOpenPrintDialog.bind(this));
        return this;
    }
    
    static detectTemplateType(htmlContent) {
        try {
            // Detect based on SVG dimensions in content
            const svgWidthMatch = htmlContent.match(/width="(\d+)"/);
            const svgHeightMatch = htmlContent.match(/height="(\d+)"/);
            
            if (svgWidthMatch && svgHeightMatch) {
                const width = parseInt(svgWidthMatch[1]);
                const height = parseInt(svgHeightMatch[1]);
                
                if (width > height) {
                    // Landscape orientation
                    return {
                        type: 'large_landscape',
                        width: '17in',
                        height: '11in',
                        orientation: 'landscape'
                    };
                } else {
                    // Portrait orientation
                    return {
                        type: 'large_portrait',
                        width: '11in',
                        height: '17in',
                        orientation: 'portrait'
                    };
                }
            }
            
            // Fallback
            return {
                type: 'large_landscape',
                width: '17in',
                height: '11in',
                orientation: 'landscape'
            };
        } catch (error) {
            console.error('PrintDialog: Error detecting template type:', error);
            return {
                type: 'large_landscape',
                width: '17in',
                height: '11in',
                orientation: 'landscape'
            };
        }
    }
    
    static openPrintDialog(content, options = {}) {
        console.log('PrintDialog: Opening print dialog with legacy DOM support');
        
        try {
            const iframe = document.createElement('iframe');
            iframe.style.position = 'fixed';
            iframe.style.width = '0';
            iframe.style.height = '0';
            iframe.style.opacity = '0';
            iframe.style.border = 'none';
            
            document.body.appendChild(iframe);
            
            const iframeDoc = iframe.contentDocument;
            iframeDoc.open();
            iframeDoc.write(content);
            iframeDoc.close();
            
            // Print with shorter delay
            setTimeout(() => {
                iframe.contentWindow.print();
                setTimeout(() => {
                    if (document.body.contains(iframe)) {
                        document.body.removeChild(iframe);
                    }
                }, 2000);
            }, 500);
            
            return true;
        } catch (error) {
            console.error('PrintDialog: Error opening print dialog:', error);
            return false;
        }
    }
    
    static handleOpenPrintDialog(event) {
        const { content, options } = event.detail;
        this.openPrintDialog(content, options);
    }
}


// ATTEMPT 3 FIX: Add global window reference at the end of the file - add this to the very end of GenMapperPoster.js

// Initialize services
PrintDialog.init();
const genMapperPoster = new GenMapperPoster();

// ATTEMPT 3 FIX: Expose GenMapperPoster globally for other scripts to access
window.GenMapperPoster = genMapperPoster;

console.log('GenMapperPoster: Legacy DOM manipulation approach loaded and globally available');