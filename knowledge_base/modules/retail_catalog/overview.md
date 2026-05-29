# Retail Catalog Module Overview

## Scope

The **Retail Catalog** module is the foundational master data management system for the retail POS and inventory operations. It defines the core attributes of products (metals, stones, purity), organization of inventory (categories, sub-categories), and the physical layout of the store (floors, counters).

## Key Responsibilities

- **Master Data Management**: defining Metals, Purity, Color, Cut, Clarity, Stones, and UOM.
- **Product Classification**: Managing Categories and Sub-categories.
- **Store Organization**: Managing Floors and Counters for inventory tracking.
- **Pricing Drivers**: Managing Making Types, Themes, and Material Rates.

## Boundary

- **Included**: All `ret_*` master tables, `product` entity configuration, and store layout (Floors/Counters).
- **Excluded**: E-commerce specific attributes (handled in `admin_catalog`), Transactional inventory (Stock), and Sales.

## Context

This module acts as the **source of truth** for all product definitions used in Purchase, Stock, and Sales modules.
