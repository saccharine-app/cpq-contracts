# **Saccharine CPQ Engine**

## **Overview**

Saccharine CPQ is a headless-first, domain-agnostic Configure-Price-Quote (CPQ) engine for Laravel.  
Unlike traditional e-commerce packages that tightly couple products to shopping carts, this package is designed for complex, service-based modular monoliths. It separates strict financial accounting from flexible frontend presentations, allowing complex quote building, future price scheduling, and domain-specific operational fulfillment.

*Status: v0.1.0-alpha. This package is currently in active early development. The database schema, APIs, and scaffolding commands are subject to change without notice. It is not yet recommended for production environments*

## **Core Architecture**

> * **Dual-Taxonomy Catalog:** Separates universal CatalogItems (your strict Chart of Accounts and central SKUs) from CatalogOfferings (local, polymorphic aliases with custom display names).  
> * **Temporal Pricing:** Prices are scheduled using effective_at and ends_at dates. Future price changes can be staged seamlessly without background cron jobs, and historical quotes remain perfectly accurate.  
> * **Ephemeral Quote State:** Active draft quotes are stored as JSON blobs (configurator_state). This allows real-time, offline-capable frontend calculation without constantly thrashing the relational database.  
> * **Inverted Fulfillment:** The CPQ engine treats locked contract line items strictly as financial ledgers. Operational tasks (like ordering merchandise or scheduling a service) are handled by the host application via polymorphic hooks.

## **UI-Agnostic Design**

This package acts purely as a headless API (CompileQuoteManifestAction and SaveQuoteDraftAction). It does not force a specific frontend stack on your host application.  
Instead, it provides scaffolding commands to publish a modern, Vue 3 Composition API UI directly into your host app.

## **Quick Start (Demo)**

Once installed in your Laravel host application, you can publish the configuration and database migrations:  
php artisan vendor:publish --tag=cpq-config  
php artisan migrate

### **Scaffolding the UI**

To generate the out-of-the-box Vue 3 Selector and seamlessly mount it inside a Filament Admin panel, run:  
php artisan cpq:scaffold  
php artisan vendor:publish --tag=cpq-views

*(Remember to import the published mount.js script in your host application's app.js and run your Vite build step).*

### **Seeding Demo Data**

To test the UI immediately, you can seed a domain-agnostic Event Planning catalog (complete with services, merchandise, and tax-exempt disbursements):  
php artisan cpq:seed-events  
