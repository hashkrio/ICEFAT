# Unseen Functionality: Backend Calculation Logic

The core value of this application lies in its "unseen" calculation logic within `App\Controllers\CommonController.php`. While the UI is simple, the backend performs complex regression-based estimates to determine crate weight and carbon footprint.

## Core Logic Breakdown

### 1. Dimension Calculation (`getDimensionValues`)
-   **Input**: Height, Width, Depth (in cm or inches).
-   **Normalization**: Converts all inputs to **Meters**.
-   **Derived Metrics**:
    -   Object Volume ($V = H \times W \times D$)
    -   Object Surface Area ($Area = 2(HW + HD + WD)$)
    -   Inner Package Dimensions (calculated via linear regression based on object dimensions).
    -   Crate Outer Dimensions (calculated via linear regression).
    -   Crate Volume, Surface Area, Front Area, Footprint Area.

### 2. Regression-Based Modeling (`getCalculatedValues`)
The application uses a database-driven regression model (`DesignRegressionModel`) to estimate the dimensions and weight of various crate components based on the object's properties.

-   **Linear Regression Formula**: $y = mx + b$
    -   $y$: Target Variable (e.g., Inner Package Height).
    -   $x$: Independent Variable (e.g., Object Height).
    -   $m$: Slope (stored in `calculate_m`).
    -   $b$: Intercept (stored in `calculate_b`).

-   **Dynamic Component Calculation**:
    The system iterates through a list of "Main Types" (components) like:
    -   Object Wrap
    -   2D Tray Panel
    -   Foam Cushion Pads
    -   Plywood Crate Walls
    -   ...and 20+ others.

    For each component, it selects the appropriate material (e.g., "PE film", "Plywood", "Pine lumber") based on the **Crate Desgin** (Economy, Mid-Range, Museum).

### 3. Weight and Carbon Calculation
For each component:
1.  **Calculate Weight ($W$)**:
    -   The code identifies the relevant geometric property ($x$) for the component (e.g., *Surface Area* for Plywood Walls, *Volume* for Handles).
    -   Applies the regression: $W = m \cdot x + b$.
2.  **Calculate Carbon Footprint ($CO_2e$)**:
    -   Retrieves the **Embodied Carbon Factor** ($E_f$) for the specific material from `MaterialByQuantityModel`.
    -   $CO_2e = W \times E_f$.
3.  **Aggregation**:
    -   Sums up $W$ and $CO_2e$ for all components to get the total Crate Weight and Total Embodied Carbon.

### 4. Transportation Emission (`carbonFootPrintValue`)
-   Calculates emissions for transport based on shipment method (Air, Road, Sea).
-   Logic includes specific multipliers for each mode:
    -   **Air Passenger**: Fixed rate per passenger? (Seems to use a direct multiplier).
    -   **Air Freight**: $Weight \times Distance \times Factor$.
    -   **Road/Sea Freight**: Includes a base emission + overhead (e.g., $+5\%$ if freight > 0).

## Key Database Models
-   **DesignRegressionModel**: Stores the $m$ and $b$ values for each component relation.
-   **DesignRegressionVariableModel**: Maps variables (e.g., "Inner package height") to their text representation.
-   **MaterialByQuantityModel**: Stores the Carbon Factors ($E_f$) for materials.

## Summary
The "unseen" functionality is a flexible, data-driven engineering model that allows the application to estimate complex physical crate properties and environmental impact from just three simple input dimensions.
