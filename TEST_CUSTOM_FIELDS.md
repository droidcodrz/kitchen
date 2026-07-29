# Custom Fields Testing Guide

## ✅ Fixes Applied

1. **Route Order Fixed** - Custom fields route moved before resource route
2. **Cache Cleared** - All Laravel caches cleared
3. **Inventory Types Fixed** - Now using dropdown options instead of hardcoded values

## 🧪 How to Test

### Step 1: Login
- URL: http://localhost:8000/login
- Email: `admin@kitchen.com`
- Password: `password`

### Step 2: Go to Add Product
- Click on **Products** menu
- Click **Add Product** button

### Step 3: Test Custom Fields

#### Test Case 1: Paint (Chemicals)
1. Select **Category**: Chemicals
2. Select **Material Type**: Paint
3. **Expected Result**: 4 custom fields should appear in BLUE box:
   - Color (text box)
   - Volume (oz/gal/ml) (text box)
   - Viscosity (text box)
   - Brand (text box)

#### Test Case 2: Steel Tube
1. Select **Category**: Tubes & Pipes
2. Select **Item Type**: Steel Tube
3. **Expected Result**: 3 custom fields appear:
   - Tube Type (dropdown: Stainless Steel/Black Iron/Galvanized)
   - Diameter (inches) (text box)
   - Length (feet) (number box)

#### Test Case 3: Gas Cylinder
1. Select **Category**: Gases
2. Select **Material Type**: Oxygen
3. **Expected Result**: 3 fields appear:
   - Cylinder Size (cu ft) (text box)
   - Gas Purity % (text box)
   - Pressure Rating (PSI) (text box)

#### Test Case 4: Welding Electrode
1. Select **Category**: Consumables
2. Select **Material Type**: Electrode
3. **Expected Result**: 4 fields appear:
   - Tip Size (text box)
   - Wire/Electrode Diameter (mm) (text box)
   - Material Specification (text box)
   - Pack Quantity (number box)

#### Test Case 5: Gas Valve
1. Select **Category**: Valves & Fittings
2. Select **Item Type**: Gas Valve
3. **Expected Result**: 3 fields appear:
   - Size (inches) (text box)
   - BTU Rating (text box)
   - Connection Type (text box)

## 🔍 Debugging

### If Custom Fields Are NOT Appearing:

#### Check 1: Browser Console
1. Open browser (Chrome/Firefox)
2. Press `F12` to open Developer Tools
3. Go to **Console** tab
4. Try selecting a category
5. Look for errors in red

**Common Errors:**
- `404 Not Found` on `/products/custom-fields/get` → Route issue
- `fetch failed` → Network issue
- `JSON parse error` → Response issue

#### Check 2: Network Tab
1. Open Developer Tools (`F12`)
2. Go to **Network** tab
3. Select a category
4. Look for request to `products/custom-fields/get`
5. Click on it and check:
   - **Status**: Should be `200 OK`
   - **Response**: Should show JSON with fields array

#### Check 3: Database
Run this in terminal:
```bash
php artisan tinker
```

Then run:
```php
// Check categories
\App\Models\Category::where('slug', 'chemicals')->first();

// Check custom fields for Chemicals category
$cat = \App\Models\Category::where('slug', 'chemicals')->first();
\App\Models\CustomFieldDefinition::where('category_id', $cat->id)->where('entity_type', 'product')->get(['field_label', 'field_type']);

// Check all categories with custom fields
\App\Models\CustomFieldDefinition::with('category')->where('entity_type', 'product')->get(['id', 'category_id', 'field_label'])->groupBy('category_id');
```

## 📸 Expected UI

### Before Selecting Category:
```
┌─────────────────────────────────────┐
│ Category: [Select Category ▼]      │
│ Material Type: [Select...     ▼]   │
│ Item Type: [Select...         ▼]   │
└─────────────────────────────────────┘
```

### After Selecting Category (e.g., Chemicals):
```
┌─────────────────────────────────────┐
│ Category: [Chemicals           ▼]   │
│ Material Type: [Paint          ▼]   │
│ Item Type: [Select...         ▼]    │
└─────────────────────────────────────┘

╔═════════════════════════════════════╗
║ Additional Fields for this Category ║
║ ─────────────────────────────────── ║
║ Color: [________________]           ║
║ Volume: [________________]          ║
║ Viscosity: [________________]       ║
║ Brand: [________________]           ║
╚═════════════════════════════════════╝
```

## 🐛 Still Not Working?

### Quick Fixes:

1. **Hard Refresh Browser**
   - Windows: `Ctrl + Shift + R`
   - Mac: `Cmd + Shift + R`

2. **Check if Alpine.js is loaded**
   - Open browser console
   - Type: `Alpine`
   - Should show Alpine object, not "undefined"

3. **Verify Route**
   ```bash
   php artisan route:list | grep custom-fields
   ```
   Should show:
   ```
   GET|HEAD  products/custom-fields/get .... products.custom-fields.get
   ```

4. **Re-seed if needed**
   ```bash
   php artisan db:seed --class=CustomFieldDefinitionSeeder
   ```

## 📞 Share Debug Info

If still not working, check:
1. Browser console errors (screenshot)
2. Network tab → custom-fields request → Response
3. Laravel logs: `storage/logs/laravel.log` (last 20 lines)

Share these 3 things for further debugging!
