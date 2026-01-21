# Romanian Insurance Aggregator API

A PHP-based insurance aggregator connecting multiple providers to generate real-time offers and policies. The system features strict OpenAPI data validation, nomenclature lookups (vehicle, company, address), and PDF management, optimized for the Romanian insurance market (RCA).

## 🚀 Key Features

* **Multi-Provider Aggregation:** Unified interface to fetch quotes from major insurers.
* **Strict Validation:** Enforces Romanian specific formats for CNP, CUI, VIN, and CIV series.
* **Nomenclature System:** Dedicated endpoints for normalizing addresses (County/City codes), vehicle types, and company details.
* **End-to-End Flow:** Handles the complete lifecycle from data validation &rarr; Offer generation &rarr; Policy issuance (PDF).

## 🔌 Supported Providers

Currently integrated with the following insurers:

* Allianz-Tiriac
* Asirom
* Axeria
* Generali
* Groupama
* Hellas Direct
* Omniasig
* Grawe
* Eazy Insure
* DallBogg
