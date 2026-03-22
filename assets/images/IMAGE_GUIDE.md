# Image Resources Guide for BISU R.I.T.E.S

## Image Directory Structure

```
assets/images/
├── login-cover.jpg          # Login page cover image (recommended size: 1920x1080+)
├── register-cover.jpg       # Register page cover image (optional)
├── dashboard-banner.jpg     # Dashboard header banner
├── logos/
│   ├── bisu-logo.png       # BISU official logo
│   ├── bisu-logo-white.png # White variant
│   └── rites-logo.png      # R.I.T.E.S logo
├── backgrounds/
│   ├── gradient-overlay.png
│   └── pattern-bg.png
└── icons/
    ├── research-icon.svg
    ├── innovation-icon.svg
    └── extension-icon.svg
```

## Image Specifications

### Login Cover Image
- **File**: `login-cover.jpg`
- **Size**: 1920x1080 pixels minimum
- **Format**: JPG (compressed for web)
- **Purpose**: Background for the left side of login page
- **Features**: 
  - Professional academic/research atmosphere
  - BISU branding or campus imagery
  - Should work well with blue gradient overlay (#1e3c72 / #2a5298)
  - Recommended: Laboratory, computer lab, or campus building

### Register Cover Image
- **File**: `register-cover.jpg`
- **Size**: Same as login cover
- **Format**: JPG
- **Purpose**: Background for register page (currently using same as login)

## Getting BISU Images

### Option 1: From BISU Website
Visit: https://bisucandijay.edu.ph/
- Look for header images or gallery sections
- Right-click → Save image as → Save to `assets/images/`
- Optimize the image size using tools like TinyJPG or ImageOptimizer

### Option 2: Create Custom Images
- Use professional photography from BISU campus
- Use design tools like Canva (https://canva.com) to create branded backgrounds
- Use placeholder services temporarily:
  - [Unsplash](https://unsplash.com) - Search "university campus", "laboratory", "research"
  - [Pexels](https://pexels.com) - Similar search terms
  - [Pixabay](https://pixabay.com) - Free high-quality images

### Option 3: Design Services
- Hire a graphic designer to create custom branded backgrounds
- Use Adobe Stock or similar services for professional images

## Adding Images to Login Page

The login page currently uses:
```html
<div class="login-cover" style="background-image: url('./assets/images/login-cover.jpg');">
```

To change the image:
1. Place your image in `assets/images/login-cover.jpg`
2. The image will automatically appear behind the blue gradient overlay
3. The overlay ensures text remains readable

## Image Optimization Tips

### For Web Performance
1. **Reduce File Size**: Use TinyJPG.com to compress images
   - Target: < 500KB per image
   - Recommended: 200-400KB for web use

2. **Use Correct Format**:
   - JPG: Photographs and complex images
   - PNG: Images with transparency
   - SVG: Icons and logos

3. **Responsive Images**: Consider creating multiple sizes
   - Full: 1920x1080 (desktop)
   - Tablet: 1280x720
   - Mobile: 768x432

## CSS Image Properties

The login page applies these effects to images:
- **Blue Gradient Overlay**: `rgba(30, 60, 114, 0.7)` for text readability
- **Floating Radial Gradient**: `rgba(59, 130, 246, 0.1)` for visual depth
- **Background Position**: Center positioning for responsive display

## Troubleshooting

### Image Not Showing
- Check file exists: `assets/images/login-cover.jpg`
- Verify correct path in HTML
- Clear browser cache (Ctrl+Shift+Del)
- Check image file size (some servers limit uploads)

### Image Quality Issues
- Image too blurry: Use higher resolution source
- Image distorted: Check aspect ratio (recommend 16:9)
- Image too dark/light: Adjust CSS overlay opacity

### Performance Issues
- Image takes too long to load: Compress using TinyJPG
- Page feels slow: Consider lazy loading or CDN for images

## Future Enhancements

Consider these improvements:
1. Create multiple themed login pages (dark mode, light mode)
2. Add seasonal variations to cover images
3. Implement image carousel or slideshow
4. Add animated overlays or parallax effects
5. Create separate images for different user roles

## Contact & Support

For image-related questions or professional design services:
- Contact BISU Marketing/Communications Office
- Email: design@bisu.edu
- Website: https://bisucandijay.edu.ph/
