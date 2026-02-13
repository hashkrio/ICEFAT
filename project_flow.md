# Project Flow Documentation

## Overview
This project is a **Carbon Footprint Calculator** for crate packaging, built with **CodeIgniter 4**. It allows users to calculate the carbon footprint of different crate designs based on dimensions, materials, and transport methods.

## Application Flow

### 1. Authentication
- **Login**: Users access the application via `/login`.
    - Controller: `LoginController`
    - View: `login.php`
    - Auth Library: `CodeIgniter Shield`
- **Redirect**: Upon successful login, users are redirected to the dashboard (`/home` or `/`).

### 2. Dashboard & Calculator (Main Interface)
- **Route**: `/home`, `/`
- **Controller**: `Home::index`
- **View**: `dashboard-list-3.php` (Main Interface), `dashboard_3_script.php` (JS Logic)
- **Functionality**:
    1.  **Input**: User selects units (Metric/Imperial), enters dimensions (Height, Width, Depth), and selects a Crate Design.
    2.  **Real-time Calculation**:
        -   Triggered by input changes.
        -   AJAX Request: `POST /filter/get_values_by_crate_type`
        -   Controller Method: `CommonController::getValuesByCrateType`
        -   Returns: `weight_in_kg`, `embodied_carbon_factor_in_kg`.
    3.  **Transport Calculation**:
        -   User enters "Number of one-way trips".
        -   JS calculates `per_one_way_trip` emission.
    4.  **Save/Add Crate**:
        -   User clicks "Add Crate".
        -   AJAX Request: `POST /filter/save_new`
        -   Controller Method: `CommonController::saveNewCalculations`
        -   Data stored in `DataList` model.

### 3. Data Tables & Visualization
-   **List View**: The dashboard displays a list of calculated crates.
    -   Data Source: `ajax.calculation.all.data.list` (`CommonController::allDataList`).
-   **Visualization**: A Donut chart shows the distribution of carbon footprint by crate type (ECONOMY, MID-RANGE, MUSEUM).
-   **Export**: Users can export data to Excel or PDF.
    -   Routes: `/filter/export/excel`, `/filter/export/pdf`.

### 4. Admin/Management (Hidden/Secondary Flows)
-   **User Management**: `/users` -> `Home::users` -> `user-list.php`.
-   **Material & Emission Factors**:
    -   Material Quantity: `/material/quantity`
    -   Crate Design: `/crate/design`
    -   Regression Models: `/design/regression`
    -   Transportation Emission: `/transportation/emission`
    -   Crate Types: `/crate/type`
    -   Model Options: `/model/option`
    -   These routes allow managing the constants and factors used in calculations.

## Architectural Components

-   **Controllers**:
    -   `App\Controllers\Home`: Handles Page views.
    -   `App\Controllers\CommonController`: Handles AJAX requests, business logic, calculations, and data export.
    -   `App\Controllers\LoginController`: Handles Authentication.

-   **Models**:
    -   `CrateTypeModel`: Storage for crate types (Economy, Museum, etc.).
    -   `DataList`: Stores user saved calculations.
    -   `DesignRegressionModel` & `DesignRegressionVariableModel`: Stores coefficients (M and B values) for regression-based weight calculations.
    -   `MaterialByQuantityModel`, `MaterialByCrateDesignModel`: Storess material properties.
    -   `TransportationEmissionFactorModel`: Stores emission factors for different shipment methods.
    -   `UserModel`: User management.

-   **Views**:
    -   Layouts: `app/Views/layouts/` (Master layout).
    -   Pages: `dashboard-list-3.php`, `login.php`, etc.

-   **Database**: MySQL (implied by CodeIgniter models).
