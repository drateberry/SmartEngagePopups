# SmartEngage Popups

A comprehensive WordPress plugin for creating and managing behavior-triggered popups with powerful targeting options.

## Features

- Create slide-in and full-screen popups with customizable content
- Multiple trigger conditions (time on page, scroll depth, exit intent, etc.)
- Advanced targeting options (device type, user roles, referrer URL, etc.)
- Flexible frequency controls to manage how often popups appear
- Built-in analytics to track impressions and conversions
- Clean, modern and accessible design
- No dependencies on external libraries or services

## Installation

1. Download the ZIP file of the plugin
2. Go to WordPress admin panel -> Plugins -> Add New
3. Click "Upload Plugin" and select the ZIP file
4. Activate the plugin

## Folder Structure

```
smartengage-popups/
├── admin/
│   ├── class-smartengage-admin.php
│   ├── class-smartengage-metabox.php
│   └── views/
│       ├── analytics-dashboard.php
│       ├── metabox-display-rules.php
│       ├── metabox-frequency.php
│       ├── metabox-popup-options.php
│       ├── metabox-preview.php
│       ├── metabox-stats.php
│       ├── metabox-targeting.php
│       └── settings-page.php
├── assets/
│   ├── css/
│   │   ├── admin.css
│   │   └── frontend.css
│   └── js/
│       ├── admin.js
│       ├── chart.min.js
│       ├── frontend.js
│       └── sidebar.js
├── includes/
│   ├── class-smartengage-analytics.php
│   ├── class-smartengage-frontend.php
│   ├── class-smartengage-loader.php
│   └── class-smartengage-post-types.php
├── languages/
│   └── smartengage-popups.pot
├── index.php
├── README.md
└── smartengage-popups.php
```

## Usage

1. Go to SmartEngage > Add New to create a new popup
2. Set up the title, content, and optional image
3. Configure trigger conditions and targeting options
4. Save and activate your popup
5. Track performance in SmartEngage > Analytics

## License

GPLv2 or later
