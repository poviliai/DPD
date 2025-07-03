# DPD
OpenCart 4 module for DPD shipping integration. Supports automatic shipment number generation, PDF label printing, and a secure API endpoint for receiving status updates from DPD's Tracking Push Service.

# DPD Shipping Module for OpenCart 4

This module provides seamless integration of DPD Web Services into OpenCart 4.

## Features

- ✅ Automatic creation of DPD shipments from orders
- 🏷️ Generation of parcel numbers and printable PDF labels (A4/A6 format)
- 🔄 Server-side API endpoint for receiving real-time tracking updates via DPD's **Tracking Push Service**
- 🔐 Secure authentication using DelisID and AuthToken
- 🧾 Supports multiple parcels per shipment and advanced label options
- 🛠️ Admin interface for configuration and manual shipment creation

## Requirements

- OpenCart 4.1+
- Valid DPD Web Connect access (DelisID, Auth credentials)
- SOAP support enabled on your server

## Installation

1. Upload the module to your OpenCart installation.
2. Navigate to **Extensions > Shipping** and install **DPD Shipping**.
3. Configure API credentials and options in the settings panel.
4. Start creating shipments directly from order view in admin.

## Tracking Push Service

The module exposes a secured endpoint to handle tracking updates sent by DPD (Tracking Push Service).  
You can configure this endpoint in your DPD business portal under:

https://your-store.com/index.php?route=extension/shipping/dpd/trackingPush


Make sure to whitelist DPD's IP range and set a secret token in the module settings.

## License

This module is developed for **TELENORMA Holding AG** and distributed under proprietary license.

---

© TELENORMA Holding AG  
Johannes-Brahms-Str. 4, 72461 Albstadt, Germany  
Developer: Vika Poviliai <v.poviliai@gmail.com>


