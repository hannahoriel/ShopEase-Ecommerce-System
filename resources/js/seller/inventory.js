/* ShopEase Seller Inventory
 * Interactive behavior extracted from inventory.blade.php.
 */
(function () {
    'use strict';

    const configElement = document.getElementById('sellerInventoryConfig');

    if (!configElement) {
        return;
    }

    const inventoryConfig = JSON.parse(configElement.textContent);

document.addEventListener(
            'DOMContentLoaded',
            function () {


                /* =================================================
                   FAST SIDEBAR / PAGE ALIGNMENT
                   Matches the seller pages with quick response:
                   - Expanded sidebar = 270px
                   - Collapsed sidebar = 100px
                   - Mobile = 0px
                   - 200ms margin transition
                ================================================== */

                const inventoryPage =
                    document.getElementById(
                        'inventory-page'
                    );

                const inventorySidebar =
                    document.getElementById(
                        'sellerSidebar'
                    );


                function syncInventoryPageOffset() {

                    if (!inventoryPage) {
                        return;
                    }


                    if (window.innerWidth <= 760) {

                        inventoryPage.style.marginLeft =
                            '0px';

                        return;
                    }


                    const sidebarCollapsed =
                        inventorySidebar &&
                        inventorySidebar.classList.contains(
                            'seller-sidebar-collapsed'
                        );


                    inventoryPage.style.marginLeft =
                        sidebarCollapsed
                            ? '100px'
                            : '270px';

                }


                /*
                 * Initial alignment.
                 */
                syncInventoryPageOffset();


                /*
                 * Recalculate on viewport changes.
                 */
                window.addEventListener(
                    'resize',
                    syncInventoryPageOffset
                );


                /*
                 * Watch the exact class used by the seller sidebar
                 * when it collapses/expands so Inventory responds
                 * immediately instead of waiting for a layout measurement.
                 */
                if (
                    inventorySidebar &&
                    typeof MutationObserver !== 'undefined'
                ) {

                    const inventorySidebarObserver =
                        new MutationObserver(
                            function () {

                                requestAnimationFrame(
                                    syncInventoryPageOffset
                                );

                            }
                        );


                    inventorySidebarObserver.observe(
                        inventorySidebar,
                        {
                            attributes: true,
                            attributeFilter: ['class']
                        }
                    );

                }


                /*
                 * Compatibility with the seller navbar toggle event.
                 */
                document.body.addEventListener(
                    'toggle-seller-sidebar',
                    function () {

                        requestAnimationFrame(
                            syncInventoryPageOffset
                        );

                    }
                );




                /* =================================================
                   FLASH MESSAGE
                ================================================== */

                const inventoryFlashMessage =
                    document.getElementById(
                        'inventoryFlashMessage'
                    );

                const inventoryFlashText =
                    document.getElementById(
                        'inventoryFlashText'
                    );

                let inventoryFlashTimer = null;

                function showInventoryFlash(message) {

                    if (!inventoryFlashMessage || !inventoryFlashText) {
                        return;
                    }

                    if (inventoryFlashTimer) {
                        window.clearTimeout(inventoryFlashTimer);
                    }

                    inventoryFlashText.textContent = message;

                    inventoryFlashMessage.classList.remove('hidden');

                    void inventoryFlashMessage.offsetWidth;

                    inventoryFlashMessage.classList.add('flash-visible');

                    inventoryFlashTimer = window.setTimeout(function () {

                        inventoryFlashMessage.classList.remove('flash-visible');

                        window.setTimeout(function () {

                            inventoryFlashMessage.classList.add('hidden');

                        }, 200);

                    }, 3000);

                }

                /* =================================================
                   INVENTORY ELEMENTS
                ================================================== */

                const tabs =
                    document.querySelectorAll(
                        '.inventory-tab'
                    );


                const allHeader =
                    document.getElementById(
                        'allProductsHeader'
                    );


                const policyHeader =
                    document.getElementById(
                        'policyIssuesHeader'
                    );


                const allTable =
                    document.getElementById(
                        'allProductsTable'
                    );


                const policyTable =
                    document.getElementById(
                        'policyIssuesTable'
                    );


                const archivedTable =
                    document.getElementById(
                        'archivedItemsTable'
                    );


                const archivedEmpty =
                    document.getElementById(
                        'archivedItemsEmpty'
                    );


                const searchInput =
                    document.getElementById(
                        'productSearch'
                    );


                const categoryFilter =
                    document.getElementById(
                        'categoryFilter'
                    );


                const statusFilter =
                    document.getElementById(
                        'statusFilter'
                    );

                const addProductCategory =
                    document.getElementById(
                        'addProductCategory'
                    );

                const sellerRegisteredCategories =
                    inventoryConfig.sellerRegisteredCategories;


                const categoryChoicePills =
                    Array.from(
                        document.querySelectorAll(
                            '.create-category-pill[data-category-slug]'
                        )
                    );

                /*
                 * Category-specific specification library.
                 * All specification fields are optional and only the
                 * selected product category's fields are rendered.
                 */
                const commonProductSpecificationFields = [
                    {
                        label: 'Brand',
                        key: 'brand',
                        placeholder: 'Brand name'
                    },
                    {
                        label: 'Material',
                        key: 'material',
                        placeholder: 'Main material'
                    },
                    {
                        label: 'Quantity per Pack',
                        key: 'quantity_per_pack',
                        placeholder: 'e.g. 1 piece, 12 pcs'
                    },
                    {
                        label: 'Features',
                        key: 'features',
                        placeholder: 'Key features',
                        type: 'textarea'
                    },
                    {
                        label: 'Condition',
                        key: 'condition',
                        type: 'select',
                        options: [
                            'New',
                            'Like New',
                            'Used - Good',
                            'Used - Fair'
                        ]
                    },
                    {
                        label: 'Country of Origin',
                        key: 'country_of_origin',
                        placeholder: 'e.g. Philippines'
                    },
                    {
                        label: 'Ships From',
                        key: 'ships_from',
                        type: 'readonly',
                        value: inventoryConfig.sellerShipFromAddress
                    }
                ];


                const productSpecificationLibrary = {
                    "pet-supplies": {
                                        "label": "Pet Supplies",
                                        "generalFields": [
                                                            {
                                                                                "label": "Pet Type",
                                                                                "key": "pet_type",
                                                                                "placeholder": "e.g. Dog, Cat, Fish, Bird"
                                                            },
                                                            {
                                                                                "label": "Life Stage",
                                                                                "key": "life_stage",
                                                                                "placeholder": "e.g. Puppy, Adult, Senior"
                                                            },
                                                            {
                                                                                "label": "Pet Size",
                                                                                "key": "pet_size",
                                                                                "placeholder": "e.g. Small, Medium, Large"
                                                            },
                                                            {
                                                                                "label": "Net Weight / Volume",
                                                                                "key": "net_weight_volume",
                                                                                "placeholder": "e.g. 1 kg, 250 ml"
                                                            },
                                                            {
                                                                                "label": "Recommended Use",
                                                                                "key": "recommended_use",
                                                                                "placeholder": "General intended use"
                                                            }
                                        ],
                                        "subcategories": {
                                                            "Dog Food & Treats": [
                                                                                {
                                                                                                    "label": "Food Form",
                                                                                                    "key": "dog_food_form",
                                                                                                    "placeholder": "e.g. Dry, Wet, Treat"
                                                                                },
                                                                                {
                                                                                                    "label": "Flavor / Protein Source",
                                                                                                    "key": "dog_flavor",
                                                                                                    "placeholder": "e.g. Chicken, Beef"
                                                                                },
                                                                                {
                                                                                                    "label": "Breed Size",
                                                                                                    "key": "dog_breed_size",
                                                                                                    "placeholder": "e.g. Small Breed"
                                                                                },
                                                                                {
                                                                                                    "label": "Dietary Needs",
                                                                                                    "key": "dog_dietary_needs",
                                                                                                    "placeholder": "e.g. Grain-Free"
                                                                                },
                                                                                {
                                                                                                    "label": "Crude Protein",
                                                                                                    "key": "dog_crude_protein",
                                                                                                    "placeholder": "e.g. 24%"
                                                                                }
                                                            ],
                                                            "Cat Litter & Accessories": [
                                                                                {
                                                                                                    "label": "Litter Type",
                                                                                                    "key": "cat_litter_type",
                                                                                                    "placeholder": "e.g. Bentonite, Tofu"
                                                                                },
                                                                                {
                                                                                                    "label": "Clumping",
                                                                                                    "key": "cat_litter_clumping",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Clumping",
                                                                                                                        "Non-Clumping"
                                                                                                    ]
                                                                                },
                                                                                {
                                                                                                    "label": "Scent",
                                                                                                    "key": "cat_litter_scent",
                                                                                                    "placeholder": "e.g. Unscented, Lavender"
                                                                                },
                                                                                {
                                                                                                    "label": "Dust Level",
                                                                                                    "key": "cat_litter_dust",
                                                                                                    "placeholder": "e.g. Low Dust"
                                                                                },
                                                                                {
                                                                                                    "label": "Absorption / Odor Control",
                                                                                                    "key": "cat_litter_absorption",
                                                                                                    "placeholder": "Performance details"
                                                                                }
                                                            ],
                                                            "Aquariums & Fish Supplies": [
                                                                                {
                                                                                                    "label": "Water Type",
                                                                                                    "key": "aquarium_water_type",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Freshwater",
                                                                                                                        "Saltwater",
                                                                                                                        "Both"
                                                                                                    ]
                                                                                },
                                                                                {
                                                                                                    "label": "Tank Capacity",
                                                                                                    "key": "aquarium_capacity",
                                                                                                    "placeholder": "e.g. 20 L"
                                                                                },
                                                                                {
                                                                                                    "label": "Fish / Species Type",
                                                                                                    "key": "aquarium_species",
                                                                                                    "placeholder": "e.g. Tropical Fish"
                                                                                },
                                                                                {
                                                                                                    "label": "Equipment Type",
                                                                                                    "key": "aquarium_equipment_type",
                                                                                                    "placeholder": "e.g. Filter, Pump, Light"
                                                                                },
                                                                                {
                                                                                                    "label": "Dimensions",
                                                                                                    "key": "aquarium_dimensions",
                                                                                                    "placeholder": "L × W × H"
                                                                                }
                                                            ],
                                                            "Bird Feeders & Food": [
                                                                                {
                                                                                                    "label": "Bird Type",
                                                                                                    "key": "bird_type",
                                                                                                    "placeholder": "e.g. Parrot, Finch"
                                                                                },
                                                                                {
                                                                                                    "label": "Feed Type",
                                                                                                    "key": "bird_feed_type",
                                                                                                    "placeholder": "e.g. Seeds, Pellets"
                                                                                },
                                                                                {
                                                                                                    "label": "Feeder Capacity",
                                                                                                    "key": "bird_feeder_capacity",
                                                                                                    "placeholder": "e.g. 500 g"
                                                                                },
                                                                                {
                                                                                                    "label": "Mounting Type",
                                                                                                    "key": "bird_mounting_type",
                                                                                                    "placeholder": "e.g. Hanging"
                                                                                },
                                                                                {
                                                                                                    "label": "Weather Resistance",
                                                                                                    "key": "bird_weather_resistance",
                                                                                                    "placeholder": "e.g. Outdoor-safe"
                                                                                }
                                                            ],
                                                            "Pet Grooming Products": [
                                                                                {
                                                                                                    "label": "Grooming Type",
                                                                                                    "key": "pet_grooming_type",
                                                                                                    "placeholder": "e.g. Shampoo, Clipper, Brush"
                                                                                },
                                                                                {
                                                                                                    "label": "Coat / Hair Type",
                                                                                                    "key": "pet_coat_type",
                                                                                                    "placeholder": "e.g. Long Coat"
                                                                                },
                                                                                {
                                                                                                    "label": "Power Source",
                                                                                                    "key": "pet_grooming_power",
                                                                                                    "placeholder": "e.g. Rechargeable"
                                                                                },
                                                                                {
                                                                                                    "label": "Blade / Brush Material",
                                                                                                    "key": "pet_grooming_material",
                                                                                                    "placeholder": "e.g. Stainless Steel"
                                                                                },
                                                                                {
                                                                                                    "label": "Waterproof",
                                                                                                    "key": "pet_grooming_waterproof",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                }
                                                            ],
                                                            "Pet Health & Wellness": [
                                                                                {
                                                                                                    "label": "Wellness Type",
                                                                                                    "key": "pet_wellness_type",
                                                                                                    "placeholder": "e.g. Supplement, Dental"
                                                                                },
                                                                                {
                                                                                                    "label": "Health Concern",
                                                                                                    "key": "pet_health_concern",
                                                                                                    "placeholder": "e.g. Joint Support"
                                                                                },
                                                                                {
                                                                                                    "label": "Form",
                                                                                                    "key": "pet_health_form",
                                                                                                    "placeholder": "e.g. Tablet, Liquid"
                                                                                },
                                                                                {
                                                                                                    "label": "Active Ingredients",
                                                                                                    "key": "pet_active_ingredients",
                                                                                                    "placeholder": "Key active ingredients"
                                                                                },
                                                                                {
                                                                                                    "label": "Dosage / Usage",
                                                                                                    "key": "pet_dosage",
                                                                                                    "placeholder": "Suggested usage"
                                                                                }
                                                            ]
                                        }
                    },
                    "electronics-and-gadgets": {
                                        "label": "Electronics and Gadgets",
                                        "generalFields": [
                                                            {
                                                                                "label": "Model",
                                                                                "key": "electronics_model",
                                                                                "placeholder": "Product model"
                                                            },
                                                            {
                                                                                "label": "Compatibility",
                                                                                "key": "electronics_compatibility",
                                                                                "placeholder": "Compatible devices or systems"
                                                            },
                                                            {
                                                                                "label": "Connection Type",
                                                                                "key": "connection_type",
                                                                                "placeholder": "e.g. Bluetooth, USB-C, Wi-Fi"
                                                            },
                                                            {
                                                                                "label": "Power / Battery",
                                                                                "key": "power_battery",
                                                                                "placeholder": "Battery or power details"
                                                            },
                                                            {
                                                                                "label": "Warranty Duration",
                                                                                "key": "warranty_duration",
                                                                                "placeholder": "e.g. 12 months"
                                                            },
                                                            {
                                                                                "label": "Warranty Type",
                                                                                "key": "warranty_type",
                                                                                "placeholder": "e.g. Seller, Manufacturer"
                                                            }
                                        ],
                                        "subcategories": {
                                                            "Mobile Phones & Accessories": [
                                                                                {
                                                                                                    "label": "Device / Accessory Type",
                                                                                                    "key": "mobile_type",
                                                                                                    "placeholder": "e.g. Smartphone, Case, Charger"
                                                                                },
                                                                                {
                                                                                                    "label": "Operating System",
                                                                                                    "key": "mobile_os",
                                                                                                    "placeholder": "e.g. Android, iOS"
                                                                                },
                                                                                {
                                                                                                    "label": "Storage",
                                                                                                    "key": "mobile_storage",
                                                                                                    "placeholder": "e.g. 256 GB"
                                                                                },
                                                                                {
                                                                                                    "label": "RAM",
                                                                                                    "key": "mobile_ram",
                                                                                                    "placeholder": "e.g. 8 GB"
                                                                                },
                                                                                {
                                                                                                    "label": "Screen Size",
                                                                                                    "key": "mobile_screen_size",
                                                                                                    "placeholder": "e.g. 6.7 inches"
                                                                                },
                                                                                {
                                                                                                    "label": "Connector Type",
                                                                                                    "key": "mobile_connector",
                                                                                                    "placeholder": "e.g. USB-C"
                                                                                }
                                                            ],
                                                            "Laptops, Desktops & Monitors": [
                                                                                {
                                                                                                    "label": "Processor",
                                                                                                    "key": "computer_processor",
                                                                                                    "placeholder": "e.g. Intel Core i5"
                                                                                },
                                                                                {
                                                                                                    "label": "RAM",
                                                                                                    "key": "computer_ram",
                                                                                                    "placeholder": "e.g. 16 GB"
                                                                                },
                                                                                {
                                                                                                    "label": "Storage",
                                                                                                    "key": "computer_storage",
                                                                                                    "placeholder": "e.g. 512 GB SSD"
                                                                                },
                                                                                {
                                                                                                    "label": "Screen Size",
                                                                                                    "key": "computer_screen_size",
                                                                                                    "placeholder": "e.g. 15.6 inches"
                                                                                },
                                                                                {
                                                                                                    "label": "Resolution",
                                                                                                    "key": "computer_resolution",
                                                                                                    "placeholder": "e.g. 1920×1080"
                                                                                },
                                                                                {
                                                                                                    "label": "Graphics",
                                                                                                    "key": "computer_graphics",
                                                                                                    "placeholder": "GPU details"
                                                                                }
                                                            ],
                                                            "Audio & Video Equipment": [
                                                                                {
                                                                                                    "label": "Device / Headset Type",
                                                                                                    "key": "audio_device_type",
                                                                                                    "placeholder": "e.g. Earphone, Headphone, Speaker"
                                                                                },
                                                                                {
                                                                                                    "label": "Audio Compatibility",
                                                                                                    "key": "audio_compatibility",
                                                                                                    "placeholder": "e.g. Android, iOS, PC"
                                                                                },
                                                                                {
                                                                                                    "label": "Headphone Connection Type",
                                                                                                    "key": "headphone_connection_type",
                                                                                                    "placeholder": "e.g. Wired, Wireless"
                                                                                },
                                                                                {
                                                                                                    "label": "Gaming Focused",
                                                                                                    "key": "gaming_focused",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                },
                                                                                {
                                                                                                    "label": "Microphone",
                                                                                                    "key": "audio_microphone",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Built-in",
                                                                                                                        "Detachable",
                                                                                                                        "None"
                                                                                                    ]
                                                                                },
                                                                                {
                                                                                                    "label": "Frequency Response",
                                                                                                    "key": "frequency_response",
                                                                                                    "placeholder": "e.g. 20Hz–20kHz"
                                                                                }
                                                            ],
                                                            "Smart Home Devices": [
                                                                                {
                                                                                                    "label": "Device Type",
                                                                                                    "key": "smart_home_type",
                                                                                                    "placeholder": "e.g. Camera, Bulb, Plug"
                                                                                },
                                                                                {
                                                                                                    "label": "Smart Ecosystem",
                                                                                                    "key": "smart_ecosystem",
                                                                                                    "placeholder": "e.g. Google Home, Alexa"
                                                                                },
                                                                                {
                                                                                                    "label": "Wireless Protocol",
                                                                                                    "key": "smart_protocol",
                                                                                                    "placeholder": "e.g. Wi-Fi, Zigbee"
                                                                                },
                                                                                {
                                                                                                    "label": "Voice Assistant Support",
                                                                                                    "key": "voice_assistant",
                                                                                                    "placeholder": "e.g. Alexa"
                                                                                },
                                                                                {
                                                                                                    "label": "Power Source",
                                                                                                    "key": "smart_power_source",
                                                                                                    "placeholder": "e.g. USB, AC"
                                                                                }
                                                            ],
                                                            "Cameras & Photography": [
                                                                                {
                                                                                                    "label": "Camera Type",
                                                                                                    "key": "camera_type",
                                                                                                    "placeholder": "e.g. Mirrorless, Action Camera"
                                                                                },
                                                                                {
                                                                                                    "label": "Megapixels",
                                                                                                    "key": "camera_megapixels",
                                                                                                    "placeholder": "e.g. 24 MP"
                                                                                },
                                                                                {
                                                                                                    "label": "Sensor Size",
                                                                                                    "key": "camera_sensor",
                                                                                                    "placeholder": "e.g. APS-C"
                                                                                },
                                                                                {
                                                                                                    "label": "Lens Mount",
                                                                                                    "key": "camera_lens_mount",
                                                                                                    "placeholder": "e.g. E-mount"
                                                                                },
                                                                                {
                                                                                                    "label": "Video Resolution",
                                                                                                    "key": "camera_video_resolution",
                                                                                                    "placeholder": "e.g. 4K"
                                                                                },
                                                                                {
                                                                                                    "label": "Optical Zoom",
                                                                                                    "key": "camera_optical_zoom",
                                                                                                    "placeholder": "e.g. 5×"
                                                                                }
                                                            ],
                                                            "Wearable Technology": [
                                                                                {
                                                                                                    "label": "Wearable Type",
                                                                                                    "key": "wearable_type",
                                                                                                    "placeholder": "e.g. Smartwatch, Fitness Band"
                                                                                },
                                                                                {
                                                                                                    "label": "Display Type",
                                                                                                    "key": "wearable_display",
                                                                                                    "placeholder": "e.g. AMOLED"
                                                                                },
                                                                                {
                                                                                                    "label": "Battery Life",
                                                                                                    "key": "wearable_battery_life",
                                                                                                    "placeholder": "e.g. 7 days"
                                                                                },
                                                                                {
                                                                                                    "label": "Sensors",
                                                                                                    "key": "wearable_sensors",
                                                                                                    "placeholder": "e.g. Heart Rate, SpO2"
                                                                                },
                                                                                {
                                                                                                    "label": "Water Resistance",
                                                                                                    "key": "wearable_water_resistance",
                                                                                                    "placeholder": "e.g. 5 ATM"
                                                                                },
                                                                                {
                                                                                                    "label": "Supported OS",
                                                                                                    "key": "wearable_os",
                                                                                                    "placeholder": "e.g. Android / iOS"
                                                                                }
                                                            ]
                                        }
                    },
                    "womens-apparel": {
                                        "label": "Women's Apparel",
                                        "generalFields": [
                                                            {
                                                                                "label": "Fabric Type",
                                                                                "key": "women_fabric",
                                                                                "placeholder": "e.g. Cotton, Linen"
                                                            },
                                                            {
                                                                                "label": "Fit",
                                                                                "key": "women_fit",
                                                                                "placeholder": "e.g. Regular, Slim, Oversized"
                                                            },
                                                            {
                                                                                "label": "Pattern",
                                                                                "key": "women_pattern",
                                                                                "placeholder": "e.g. Solid, Floral"
                                                            },
                                                            {
                                                                                "label": "Occasion",
                                                                                "key": "women_occasion",
                                                                                "placeholder": "e.g. Casual, Formal"
                                                            },
                                                            {
                                                                                "label": "Care Instructions",
                                                                                "key": "women_care",
                                                                                "placeholder": "e.g. Machine wash cold"
                                                            }
                                        ],
                                        "subcategories": {
                                                            "Dresses & Skirts": [
                                                                                {
                                                                                                    "label": "Dress / Skirt Type",
                                                                                                    "key": "women_dress_type",
                                                                                                    "placeholder": "e.g. Maxi Dress, A-Line Skirt"
                                                                                },
                                                                                {
                                                                                                    "label": "Length",
                                                                                                    "key": "women_dress_length",
                                                                                                    "placeholder": "e.g. Mini, Midi, Maxi"
                                                                                },
                                                                                {
                                                                                                    "label": "Waist Rise",
                                                                                                    "key": "women_waist_rise",
                                                                                                    "placeholder": "e.g. High Rise"
                                                                                },
                                                                                {
                                                                                                    "label": "Closure",
                                                                                                    "key": "women_dress_closure",
                                                                                                    "placeholder": "e.g. Zipper"
                                                                                },
                                                                                {
                                                                                                    "label": "Lining",
                                                                                                    "key": "women_dress_lining",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Lined",
                                                                                                                        "Unlined"
                                                                                                    ]
                                                                                }
                                                            ],
                                                            "Tops & Blouses": [
                                                                                {
                                                                                                    "label": "Top Type",
                                                                                                    "key": "women_top_type",
                                                                                                    "placeholder": "e.g. Blouse, T-Shirt"
                                                                                },
                                                                                {
                                                                                                    "label": "Sleeve Length",
                                                                                                    "key": "women_top_sleeve",
                                                                                                    "placeholder": "e.g. Short Sleeve"
                                                                                },
                                                                                {
                                                                                                    "label": "Neckline",
                                                                                                    "key": "women_top_neckline",
                                                                                                    "placeholder": "e.g. V-Neck"
                                                                                },
                                                                                {
                                                                                                    "label": "Closure",
                                                                                                    "key": "women_top_closure",
                                                                                                    "placeholder": "e.g. Button"
                                                                                },
                                                                                {
                                                                                                    "label": "Top Length",
                                                                                                    "key": "women_top_length",
                                                                                                    "placeholder": "e.g. Cropped, Regular"
                                                                                }
                                                            ],
                                                            "Activewear & Yoga Pants": [
                                                                                {
                                                                                                    "label": "Activity",
                                                                                                    "key": "women_active_activity",
                                                                                                    "placeholder": "e.g. Yoga, Running"
                                                                                },
                                                                                {
                                                                                                    "label": "Support / Compression",
                                                                                                    "key": "women_active_support",
                                                                                                    "placeholder": "e.g. Medium Compression"
                                                                                },
                                                                                {
                                                                                                    "label": "Stretch Level",
                                                                                                    "key": "women_active_stretch",
                                                                                                    "placeholder": "e.g. 4-Way Stretch"
                                                                                },
                                                                                {
                                                                                                    "label": "Moisture Wicking",
                                                                                                    "key": "women_active_wicking",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                },
                                                                                {
                                                                                                    "label": "Waist Type",
                                                                                                    "key": "women_active_waist",
                                                                                                    "placeholder": "e.g. High Waist"
                                                                                }
                                                            ],
                                                            "Lingerie & Sleepwear": [
                                                                                {
                                                                                                    "label": "Product Type",
                                                                                                    "key": "women_lingerie_type",
                                                                                                    "placeholder": "e.g. Bra, Pajama Set"
                                                                                },
                                                                                {
                                                                                                    "label": "Support Level",
                                                                                                    "key": "women_lingerie_support",
                                                                                                    "placeholder": "e.g. Light, Medium"
                                                                                },
                                                                                {
                                                                                                    "label": "Padding",
                                                                                                    "key": "women_lingerie_padding",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Padded",
                                                                                                                        "Unpadded"
                                                                                                    ]
                                                                                },
                                                                                {
                                                                                                    "label": "Closure",
                                                                                                    "key": "women_lingerie_closure",
                                                                                                    "placeholder": "e.g. Hook & Eye"
                                                                                },
                                                                                {
                                                                                                    "label": "Cup / Coverage",
                                                                                                    "key": "women_lingerie_coverage",
                                                                                                    "placeholder": "e.g. Full Coverage"
                                                                                }
                                                            ],
                                                            "Jackets & Coats": [
                                                                                {
                                                                                                    "label": "Outerwear Type",
                                                                                                    "key": "women_jacket_type",
                                                                                                    "placeholder": "e.g. Trench Coat"
                                                                                },
                                                                                {
                                                                                                    "label": "Insulation",
                                                                                                    "key": "women_jacket_insulation",
                                                                                                    "placeholder": "e.g. Lightweight"
                                                                                },
                                                                                {
                                                                                                    "label": "Closure",
                                                                                                    "key": "women_jacket_closure",
                                                                                                    "placeholder": "e.g. Zipper"
                                                                                },
                                                                                {
                                                                                                    "label": "Hood",
                                                                                                    "key": "women_jacket_hood",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "With Hood",
                                                                                                                        "No Hood"
                                                                                                    ]
                                                                                },
                                                                                {
                                                                                                    "label": "Weather Resistance",
                                                                                                    "key": "women_jacket_weather",
                                                                                                    "placeholder": "e.g. Water Resistant"
                                                                                }
                                                            ],
                                                            "Shoes & Accessories": [
                                                                                {
                                                                                                    "label": "Product Type",
                                                                                                    "key": "women_shoe_accessory_type",
                                                                                                    "placeholder": "e.g. Sneakers, Bag, Belt"
                                                                                },
                                                                                {
                                                                                                    "label": "Shoe Size System",
                                                                                                    "key": "women_shoe_size_system",
                                                                                                    "placeholder": "e.g. US, EU"
                                                                                },
                                                                                {
                                                                                                    "label": "Heel Height",
                                                                                                    "key": "women_heel_height",
                                                                                                    "placeholder": "e.g. 5 cm"
                                                                                },
                                                                                {
                                                                                                    "label": "Closure",
                                                                                                    "key": "women_shoe_closure",
                                                                                                    "placeholder": "e.g. Lace-Up"
                                                                                },
                                                                                {
                                                                                                    "label": "Sole Material",
                                                                                                    "key": "women_sole_material",
                                                                                                    "placeholder": "e.g. Rubber"
                                                                                }
                                                            ]
                                        }
                    },
                    "mens-apparel": {
                                        "label": "Men's Apparel",
                                        "generalFields": [
                                                            {
                                                                                "label": "Fabric Type",
                                                                                "key": "men_fabric",
                                                                                "placeholder": "e.g. Cotton, Denim"
                                                            },
                                                            {
                                                                                "label": "Fit",
                                                                                "key": "men_fit",
                                                                                "placeholder": "e.g. Regular, Slim, Relaxed"
                                                            },
                                                            {
                                                                                "label": "Pattern",
                                                                                "key": "men_pattern",
                                                                                "placeholder": "e.g. Solid, Plaid"
                                                            },
                                                            {
                                                                                "label": "Occasion / Use",
                                                                                "key": "men_occasion",
                                                                                "placeholder": "e.g. Office, Casual"
                                                            },
                                                            {
                                                                                "label": "Care Instructions",
                                                                                "key": "men_care",
                                                                                "placeholder": "e.g. Machine wash cold"
                                                            }
                                        ],
                                        "subcategories": {
                                                            "Suits & Blazers": [
                                                                                {
                                                                                                    "label": "Suit / Blazer Type",
                                                                                                    "key": "men_suit_type",
                                                                                                    "placeholder": "e.g. Two-Piece Suit"
                                                                                },
                                                                                {
                                                                                                    "label": "Pieces Included",
                                                                                                    "key": "men_suit_pieces",
                                                                                                    "placeholder": "e.g. Jacket + Pants"
                                                                                },
                                                                                {
                                                                                                    "label": "Lapel Style",
                                                                                                    "key": "men_lapel",
                                                                                                    "placeholder": "e.g. Notch Lapel"
                                                                                },
                                                                                {
                                                                                                    "label": "Closure",
                                                                                                    "key": "men_suit_closure",
                                                                                                    "placeholder": "e.g. Two Button"
                                                                                },
                                                                                {
                                                                                                    "label": "Vents",
                                                                                                    "key": "men_suit_vents",
                                                                                                    "placeholder": "e.g. Double Vent"
                                                                                }
                                                            ],
                                                            "Casual Shirts & Pants": [
                                                                                {
                                                                                                    "label": "Garment Type",
                                                                                                    "key": "men_casual_type",
                                                                                                    "placeholder": "e.g. Polo, Chinos"
                                                                                },
                                                                                {
                                                                                                    "label": "Collar Type",
                                                                                                    "key": "men_collar",
                                                                                                    "placeholder": "e.g. Spread Collar"
                                                                                },
                                                                                {
                                                                                                    "label": "Waist Rise",
                                                                                                    "key": "men_pants_rise",
                                                                                                    "placeholder": "e.g. Mid Rise"
                                                                                },
                                                                                {
                                                                                                    "label": "Inseam",
                                                                                                    "key": "men_inseam",
                                                                                                    "placeholder": "e.g. 32 inches"
                                                                                },
                                                                                {
                                                                                                    "label": "Closure",
                                                                                                    "key": "men_casual_closure",
                                                                                                    "placeholder": "e.g. Button / Zipper"
                                                                                }
                                                            ],
                                                            "Outerwear & Jackets": [
                                                                                {
                                                                                                    "label": "Outerwear Type",
                                                                                                    "key": "men_outerwear_type",
                                                                                                    "placeholder": "e.g. Bomber Jacket"
                                                                                },
                                                                                {
                                                                                                    "label": "Insulation",
                                                                                                    "key": "men_outerwear_insulation",
                                                                                                    "placeholder": "e.g. Fleece Lined"
                                                                                },
                                                                                {
                                                                                                    "label": "Hood",
                                                                                                    "key": "men_outerwear_hood",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "With Hood",
                                                                                                                        "No Hood"
                                                                                                    ]
                                                                                },
                                                                                {
                                                                                                    "label": "Closure",
                                                                                                    "key": "men_outerwear_closure",
                                                                                                    "placeholder": "e.g. Zipper"
                                                                                },
                                                                                {
                                                                                                    "label": "Weather Resistance",
                                                                                                    "key": "men_outerwear_weather",
                                                                                                    "placeholder": "e.g. Windproof"
                                                                                }
                                                            ],
                                                            "Activewear & Fitness Gear": [
                                                                                {
                                                                                                    "label": "Activity",
                                                                                                    "key": "men_active_activity",
                                                                                                    "placeholder": "e.g. Gym, Running"
                                                                                },
                                                                                {
                                                                                                    "label": "Compression",
                                                                                                    "key": "men_active_compression",
                                                                                                    "placeholder": "e.g. Light Compression"
                                                                                },
                                                                                {
                                                                                                    "label": "Moisture Wicking",
                                                                                                    "key": "men_active_wicking",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                },
                                                                                {
                                                                                                    "label": "Stretch Level",
                                                                                                    "key": "men_active_stretch",
                                                                                                    "placeholder": "e.g. 4-Way Stretch"
                                                                                },
                                                                                {
                                                                                                    "label": "Pocket Type",
                                                                                                    "key": "men_active_pockets",
                                                                                                    "placeholder": "e.g. Zippered Pockets"
                                                                                }
                                                            ],
                                                            "Shoes & Accessories": [
                                                                                {
                                                                                                    "label": "Product Type",
                                                                                                    "key": "men_shoe_accessory_type",
                                                                                                    "placeholder": "e.g. Loafers, Belt, Wallet"
                                                                                },
                                                                                {
                                                                                                    "label": "Shoe Size System",
                                                                                                    "key": "men_shoe_size_system",
                                                                                                    "placeholder": "e.g. US, EU"
                                                                                },
                                                                                {
                                                                                                    "label": "Closure",
                                                                                                    "key": "men_shoe_closure",
                                                                                                    "placeholder": "e.g. Lace-Up"
                                                                                },
                                                                                {
                                                                                                    "label": "Sole Material",
                                                                                                    "key": "men_sole_material",
                                                                                                    "placeholder": "e.g. Rubber"
                                                                                },
                                                                                {
                                                                                                    "label": "Shaft / Heel Height",
                                                                                                    "key": "men_shoe_height",
                                                                                                    "placeholder": "If applicable"
                                                                                }
                                                            ],
                                                            "Grooming Products": [
                                                                                {
                                                                                                    "label": "Grooming Type",
                                                                                                    "key": "men_grooming_type",
                                                                                                    "placeholder": "e.g. Trimmer, Shaving Cream"
                                                                                },
                                                                                {
                                                                                                    "label": "Skin / Hair Type",
                                                                                                    "key": "men_grooming_skin_hair",
                                                                                                    "placeholder": "e.g. Sensitive Skin"
                                                                                },
                                                                                {
                                                                                                    "label": "Power Source",
                                                                                                    "key": "men_grooming_power",
                                                                                                    "placeholder": "e.g. Rechargeable"
                                                                                },
                                                                                {
                                                                                                    "label": "Blade / Head Material",
                                                                                                    "key": "men_grooming_blade",
                                                                                                    "placeholder": "e.g. Stainless Steel"
                                                                                },
                                                                                {
                                                                                                    "label": "Wet / Dry Use",
                                                                                                    "key": "men_grooming_wet_dry",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Wet & Dry",
                                                                                                                        "Dry Only"
                                                                                                    ]
                                                                                }
                                                            ]
                                        }
                    },
                    "kids-and-baby": {
                                        "label": "Kids and Baby",
                                        "generalFields": [
                                                            {
                                                                                "label": "Recommended Age",
                                                                                "key": "kids_age",
                                                                                "placeholder": "e.g. 0-6 months, 3-5 years"
                                                            },
                                                            {
                                                                                "label": "Age Group",
                                                                                "key": "kids_age_group",
                                                                                "placeholder": "e.g. Infant, Toddler"
                                                            },
                                                            {
                                                                                "label": "Gender",
                                                                                "key": "kids_gender",
                                                                                "placeholder": "e.g. Unisex"
                                                            },
                                                            {
                                                                                "label": "Safety Certification",
                                                                                "key": "kids_safety",
                                                                                "placeholder": "Safety standard if applicable"
                                                            },
                                                            {
                                                                                "label": "Care Instructions",
                                                                                "key": "kids_care",
                                                                                "placeholder": "Cleaning or care details"
                                                            }
                                        ],
                                        "subcategories": {
                                                            "Baby Clothes & Accessories": [
                                                                                {
                                                                                                    "label": "Clothing / Accessory Type",
                                                                                                    "key": "baby_clothing_type",
                                                                                                    "placeholder": "e.g. Onesie, Bib"
                                                                                },
                                                                                {
                                                                                                    "label": "Baby Size",
                                                                                                    "key": "baby_size",
                                                                                                    "placeholder": "e.g. 6-12 Months"
                                                                                },
                                                                                {
                                                                                                    "label": "Closure",
                                                                                                    "key": "baby_closure",
                                                                                                    "placeholder": "e.g. Snap Button"
                                                                                },
                                                                                {
                                                                                                    "label": "Hypoallergenic",
                                                                                                    "key": "baby_hypoallergenic",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                },
                                                                                {
                                                                                                    "label": "Season",
                                                                                                    "key": "baby_season",
                                                                                                    "placeholder": "e.g. All Season"
                                                                                }
                                                            ],
                                                            "Toys & Games": [
                                                                                {
                                                                                                    "label": "Toy Type",
                                                                                                    "key": "kids_toy_type",
                                                                                                    "placeholder": "e.g. Puzzle, Action Figure"
                                                                                },
                                                                                {
                                                                                                    "label": "Educational Benefit",
                                                                                                    "key": "kids_toy_benefit",
                                                                                                    "placeholder": "e.g. Motor Skills"
                                                                                },
                                                                                {
                                                                                                    "label": "Battery Required",
                                                                                                    "key": "kids_toy_battery",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                },
                                                                                {
                                                                                                    "label": "Number of Pieces",
                                                                                                    "key": "kids_toy_pieces",
                                                                                                    "placeholder": "e.g. 24 pieces"
                                                                                },
                                                                                {
                                                                                                    "label": "Safety Standard",
                                                                                                    "key": "kids_toy_standard",
                                                                                                    "placeholder": "e.g. Non-Toxic"
                                                                                }
                                                            ],
                                                            "Educational Materials": [
                                                                                {
                                                                                                    "label": "Subject",
                                                                                                    "key": "kids_education_subject",
                                                                                                    "placeholder": "e.g. Math, Reading"
                                                                                },
                                                                                {
                                                                                                    "label": "Grade / Learning Level",
                                                                                                    "key": "kids_education_level",
                                                                                                    "placeholder": "e.g. Grade 2"
                                                                                },
                                                                                {
                                                                                                    "label": "Format",
                                                                                                    "key": "kids_education_format",
                                                                                                    "placeholder": "e.g. Workbook, Flash Cards"
                                                                                },
                                                                                {
                                                                                                    "label": "Language",
                                                                                                    "key": "kids_education_language",
                                                                                                    "placeholder": "e.g. English"
                                                                                },
                                                                                {
                                                                                                    "label": "Page / Piece Count",
                                                                                                    "key": "kids_education_count",
                                                                                                    "placeholder": "e.g. 80 pages"
                                                                                }
                                                            ],
                                                            "Strollers & Gear": [
                                                                                {
                                                                                                    "label": "Gear Type",
                                                                                                    "key": "baby_gear_type",
                                                                                                    "placeholder": "e.g. Stroller, Carrier"
                                                                                },
                                                                                {
                                                                                                    "label": "Weight Capacity",
                                                                                                    "key": "baby_gear_capacity",
                                                                                                    "placeholder": "e.g. 22 kg"
                                                                                },
                                                                                {
                                                                                                    "label": "Foldable",
                                                                                                    "key": "baby_gear_foldable",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                },
                                                                                {
                                                                                                    "label": "Wheel Type",
                                                                                                    "key": "baby_gear_wheels",
                                                                                                    "placeholder": "e.g. All-Terrain"
                                                                                },
                                                                                {
                                                                                                    "label": "Harness Type",
                                                                                                    "key": "baby_gear_harness",
                                                                                                    "placeholder": "e.g. 5-Point Harness"
                                                                                }
                                                            ],
                                                            "Nursery Furniture": [
                                                                                {
                                                                                                    "label": "Furniture Type",
                                                                                                    "key": "nursery_type",
                                                                                                    "placeholder": "e.g. Crib, Changing Table"
                                                                                },
                                                                                {
                                                                                                    "label": "Dimensions",
                                                                                                    "key": "nursery_dimensions",
                                                                                                    "placeholder": "L × W × H"
                                                                                },
                                                                                {
                                                                                                    "label": "Weight Capacity",
                                                                                                    "key": "nursery_capacity",
                                                                                                    "placeholder": "e.g. 25 kg"
                                                                                },
                                                                                {
                                                                                                    "label": "Assembly Required",
                                                                                                    "key": "nursery_assembly",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                },
                                                                                {
                                                                                                    "label": "Safety Standard",
                                                                                                    "key": "nursery_safety_standard",
                                                                                                    "placeholder": "Certification if applicable"
                                                                                }
                                                            ],
                                                            "Safety and Health": [
                                                                                {
                                                                                                    "label": "Product Type",
                                                                                                    "key": "kids_health_type",
                                                                                                    "placeholder": "e.g. Thermometer, Safety Gate"
                                                                                },
                                                                                {
                                                                                                    "label": "Safety Feature",
                                                                                                    "key": "kids_health_feature",
                                                                                                    "placeholder": "Key safety feature"
                                                                                },
                                                                                {
                                                                                                    "label": "Certification",
                                                                                                    "key": "kids_health_certification",
                                                                                                    "placeholder": "Certification if applicable"
                                                                                },
                                                                                {
                                                                                                    "label": "Usage Location",
                                                                                                    "key": "kids_health_location",
                                                                                                    "placeholder": "e.g. Home, Car"
                                                                                },
                                                                                {
                                                                                                    "label": "Age Range",
                                                                                                    "key": "kids_health_age_range",
                                                                                                    "placeholder": "Recommended age range"
                                                                                }
                                                            ]
                                        }
                    },
                    "home-and-garden": {
                                        "label": "Home and Garden",
                                        "generalFields": [
                                                            {
                                                                                "label": "Room / Area",
                                                                                "key": "home_room_area",
                                                                                "placeholder": "e.g. Kitchen, Bedroom, Garden"
                                                            },
                                                            {
                                                                                "label": "Dimensions",
                                                                                "key": "home_dimensions",
                                                                                "placeholder": "L × W × H"
                                                            },
                                                            {
                                                                                "label": "Indoor / Outdoor",
                                                                                "key": "home_indoor_outdoor",
                                                                                "type": "select",
                                                                                "options": [
                                                                                                    "Indoor",
                                                                                                    "Outdoor",
                                                                                                    "Indoor & Outdoor"
                                                                                ]
                                                            },
                                                            {
                                                                                "label": "Assembly Required",
                                                                                "key": "home_assembly",
                                                                                "type": "select",
                                                                                "options": [
                                                                                                    "Yes",
                                                                                                    "No"
                                                                                ]
                                                            },
                                                            {
                                                                                "label": "Finish / Surface",
                                                                                "key": "home_finish",
                                                                                "placeholder": "e.g. Matte, Glossy, Wood Grain"
                                                            }
                                        ],
                                        "subcategories": {
                                                            "Kitchen Appliances": [
                                                                                {
                                                                                                    "label": "Appliance Type",
                                                                                                    "key": "kitchen_appliance_type",
                                                                                                    "placeholder": "e.g. Blender, Rice Cooker"
                                                                                },
                                                                                {
                                                                                                    "label": "Capacity",
                                                                                                    "key": "kitchen_capacity",
                                                                                                    "placeholder": "e.g. 1.8 L"
                                                                                },
                                                                                {
                                                                                                    "label": "Wattage",
                                                                                                    "key": "kitchen_wattage",
                                                                                                    "placeholder": "e.g. 700 W"
                                                                                },
                                                                                {
                                                                                                    "label": "Voltage",
                                                                                                    "key": "kitchen_voltage",
                                                                                                    "placeholder": "e.g. 220 V"
                                                                                },
                                                                                {
                                                                                                    "label": "Functions / Settings",
                                                                                                    "key": "kitchen_functions",
                                                                                                    "placeholder": "e.g. 3 Speed"
                                                                                }
                                                            ],
                                                            "Furniture & Decor": [
                                                                                {
                                                                                                    "label": "Furniture / Decor Type",
                                                                                                    "key": "home_furniture_type",
                                                                                                    "placeholder": "e.g. Chair, Vase"
                                                                                },
                                                                                {
                                                                                                    "label": "Style",
                                                                                                    "key": "home_decor_style",
                                                                                                    "placeholder": "e.g. Modern, Minimalist"
                                                                                },
                                                                                {
                                                                                                    "label": "Weight Capacity",
                                                                                                    "key": "home_furniture_capacity",
                                                                                                    "placeholder": "If applicable"
                                                                                },
                                                                                {
                                                                                                    "label": "Number of Pieces",
                                                                                                    "key": "home_furniture_pieces",
                                                                                                    "placeholder": "e.g. 4-piece set"
                                                                                },
                                                                                {
                                                                                                    "label": "Mounting Type",
                                                                                                    "key": "home_decor_mounting",
                                                                                                    "placeholder": "e.g. Freestanding, Wall-Mounted"
                                                                                }
                                                            ],
                                                            "Gardening Tools": [
                                                                                {
                                                                                                    "label": "Tool Type",
                                                                                                    "key": "garden_tool_type",
                                                                                                    "placeholder": "e.g. Pruner, Shovel"
                                                                                },
                                                                                {
                                                                                                    "label": "Power Source",
                                                                                                    "key": "garden_power_source",
                                                                                                    "placeholder": "e.g. Manual, Battery"
                                                                                },
                                                                                {
                                                                                                    "label": "Blade / Tool Material",
                                                                                                    "key": "garden_tool_material",
                                                                                                    "placeholder": "e.g. Carbon Steel"
                                                                                },
                                                                                {
                                                                                                    "label": "Handle Length",
                                                                                                    "key": "garden_handle_length",
                                                                                                    "placeholder": "e.g. 80 cm"
                                                                                },
                                                                                {
                                                                                                    "label": "Recommended Use",
                                                                                                    "key": "garden_tool_use",
                                                                                                    "placeholder": "e.g. Pruning, Digging"
                                                                                }
                                                            ],
                                                            "Outdoor Living": [
                                                                                {
                                                                                                    "label": "Outdoor Product Type",
                                                                                                    "key": "outdoor_living_type",
                                                                                                    "placeholder": "e.g. Patio Chair, Grill"
                                                                                },
                                                                                {
                                                                                                    "label": "Weather Resistance",
                                                                                                    "key": "outdoor_weather",
                                                                                                    "placeholder": "e.g. UV / Rain Resistant"
                                                                                },
                                                                                {
                                                                                                    "label": "Capacity",
                                                                                                    "key": "outdoor_capacity",
                                                                                                    "placeholder": "e.g. Seats 4"
                                                                                },
                                                                                {
                                                                                                    "label": "Foldable / Portable",
                                                                                                    "key": "outdoor_portable",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                },
                                                                                {
                                                                                                    "label": "Cover Included",
                                                                                                    "key": "outdoor_cover",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                }
                                                            ],
                                                            "Home Improvement Tools": [
                                                                                {
                                                                                                    "label": "Tool Type",
                                                                                                    "key": "home_tool_type",
                                                                                                    "placeholder": "e.g. Drill, Sander"
                                                                                },
                                                                                {
                                                                                                    "label": "Power Source",
                                                                                                    "key": "home_tool_power",
                                                                                                    "placeholder": "e.g. Corded, Battery"
                                                                                },
                                                                                {
                                                                                                    "label": "Voltage",
                                                                                                    "key": "home_tool_voltage",
                                                                                                    "placeholder": "e.g. 18 V"
                                                                                },
                                                                                {
                                                                                                    "label": "Speed / Torque",
                                                                                                    "key": "home_tool_performance",
                                                                                                    "placeholder": "e.g. 0-1500 RPM"
                                                                                },
                                                                                {
                                                                                                    "label": "Included Accessories",
                                                                                                    "key": "home_tool_accessories",
                                                                                                    "placeholder": "e.g. 10 Drill Bits"
                                                                                }
                                                            ],
                                                            "Bedding & Bath": [
                                                                                {
                                                                                                    "label": "Item Type",
                                                                                                    "key": "bedding_bath_type",
                                                                                                    "placeholder": "e.g. Bedsheet, Towel"
                                                                                },
                                                                                {
                                                                                                    "label": "Size",
                                                                                                    "key": "bedding_bath_size",
                                                                                                    "placeholder": "e.g. Queen, Bath Towel"
                                                                                },
                                                                                {
                                                                                                    "label": "Thread Count / GSM",
                                                                                                    "key": "bedding_gsm",
                                                                                                    "placeholder": "e.g. 300 TC, 500 GSM"
                                                                                },
                                                                                {
                                                                                                    "label": "Set Includes",
                                                                                                    "key": "bedding_set_includes",
                                                                                                    "placeholder": "e.g. 1 Sheet + 2 Pillowcases"
                                                                                },
                                                                                {
                                                                                                    "label": "Absorbency / Softness",
                                                                                                    "key": "bedding_quality",
                                                                                                    "placeholder": "Product feel or absorbency"
                                                                                }
                                                            ]
                                        }
                    },
                    "sports-and-outdoors": {
                                        "label": "Sports and Outdoors",
                                        "generalFields": [
                                                            {
                                                                                "label": "Sport / Activity",
                                                                                "key": "sports_activity",
                                                                                "placeholder": "e.g. Basketball, Hiking"
                                                            },
                                                            {
                                                                                "label": "Skill Level",
                                                                                "key": "sports_skill_level",
                                                                                "placeholder": "e.g. Beginner, Pro"
                                                            },
                                                            {
                                                                                "label": "Dimensions / Size",
                                                                                "key": "sports_dimensions",
                                                                                "placeholder": "Product dimensions"
                                                            },
                                                            {
                                                                                "label": "Product Weight",
                                                                                "key": "sports_weight",
                                                                                "placeholder": "e.g. 2.5 kg"
                                                            },
                                                            {
                                                                                "label": "Weather / Water Resistance",
                                                                                "key": "sports_weather",
                                                                                "placeholder": "e.g. Waterproof"
                                                            }
                                        ],
                                        "subcategories": {
                                                            "Fitness Equipment": [
                                                                                {
                                                                                                    "label": "Equipment Type",
                                                                                                    "key": "fitness_equipment_type",
                                                                                                    "placeholder": "e.g. Dumbbell, Treadmill"
                                                                                },
                                                                                {
                                                                                                    "label": "Resistance / Weight",
                                                                                                    "key": "fitness_resistance",
                                                                                                    "placeholder": "e.g. 20 kg"
                                                                                },
                                                                                {
                                                                                                    "label": "Max User Weight",
                                                                                                    "key": "fitness_user_weight",
                                                                                                    "placeholder": "e.g. 120 kg"
                                                                                },
                                                                                {
                                                                                                    "label": "Foldable",
                                                                                                    "key": "fitness_foldable",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                },
                                                                                {
                                                                                                    "label": "Target Muscle / Use",
                                                                                                    "key": "fitness_target",
                                                                                                    "placeholder": "e.g. Cardio, Core"
                                                                                }
                                                            ],
                                                            "Camping & Hiking Gear": [
                                                                                {
                                                                                                    "label": "Gear Type",
                                                                                                    "key": "camping_gear_type",
                                                                                                    "placeholder": "e.g. Tent, Backpack"
                                                                                },
                                                                                {
                                                                                                    "label": "Capacity",
                                                                                                    "key": "camping_capacity",
                                                                                                    "placeholder": "e.g. 4 persons, 40 L"
                                                                                },
                                                                                {
                                                                                                    "label": "Packed Size / Weight",
                                                                                                    "key": "camping_packed_size",
                                                                                                    "placeholder": "Packed dimensions or weight"
                                                                                },
                                                                                {
                                                                                                    "label": "Waterproof Rating",
                                                                                                    "key": "camping_waterproof",
                                                                                                    "placeholder": "e.g. 3000 mm"
                                                                                },
                                                                                {
                                                                                                    "label": "Season Rating",
                                                                                                    "key": "camping_season",
                                                                                                    "placeholder": "e.g. 3-Season"
                                                                                }
                                                            ],
                                                            "Sports Apparel": [
                                                                                {
                                                                                                    "label": "Apparel Type",
                                                                                                    "key": "sports_apparel_type",
                                                                                                    "placeholder": "e.g. Jersey, Shorts"
                                                                                },
                                                                                {
                                                                                                    "label": "Fit / Compression",
                                                                                                    "key": "sports_apparel_fit",
                                                                                                    "placeholder": "e.g. Compression Fit"
                                                                                },
                                                                                {
                                                                                                    "label": "Moisture Wicking",
                                                                                                    "key": "sports_apparel_wicking",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                },
                                                                                {
                                                                                                    "label": "UPF Rating",
                                                                                                    "key": "sports_apparel_upf",
                                                                                                    "placeholder": "e.g. UPF 50+"
                                                                                },
                                                                                {
                                                                                                    "label": "Reflective Details",
                                                                                                    "key": "sports_apparel_reflective",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                }
                                                            ],
                                                            "Cycling & Bikes": [
                                                                                {
                                                                                                    "label": "Bike / Accessory Type",
                                                                                                    "key": "cycling_type",
                                                                                                    "placeholder": "e.g. Mountain Bike, Helmet"
                                                                                },
                                                                                {
                                                                                                    "label": "Wheel Size",
                                                                                                    "key": "cycling_wheel_size",
                                                                                                    "placeholder": "e.g. 29 inches"
                                                                                },
                                                                                {
                                                                                                    "label": "Frame Size / Material",
                                                                                                    "key": "cycling_frame",
                                                                                                    "placeholder": "e.g. Medium / Aluminum"
                                                                                },
                                                                                {
                                                                                                    "label": "Gear Count",
                                                                                                    "key": "cycling_gears",
                                                                                                    "placeholder": "e.g. 21-Speed"
                                                                                },
                                                                                {
                                                                                                    "label": "Brake Type",
                                                                                                    "key": "cycling_brakes",
                                                                                                    "placeholder": "e.g. Hydraulic Disc"
                                                                                }
                                                            ],
                                                            "Water Sports": [
                                                                                {
                                                                                                    "label": "Equipment Type",
                                                                                                    "key": "water_sports_type",
                                                                                                    "placeholder": "e.g. Paddleboard, Life Vest"
                                                                                },
                                                                                {
                                                                                                    "label": "Buoyancy / Capacity",
                                                                                                    "key": "water_sports_capacity",
                                                                                                    "placeholder": "e.g. 100 kg"
                                                                                },
                                                                                {
                                                                                                    "label": "Board / Gear Size",
                                                                                                    "key": "water_sports_size",
                                                                                                    "placeholder": "Dimensions or size"
                                                                                },
                                                                                {
                                                                                                    "label": "Waterproof Rating",
                                                                                                    "key": "water_sports_waterproof",
                                                                                                    "placeholder": "If applicable"
                                                                                },
                                                                                {
                                                                                                    "label": "Recommended Skill Level",
                                                                                                    "key": "water_sports_skill",
                                                                                                    "placeholder": "e.g. Beginner"
                                                                                }
                                                            ],
                                                            "Team Sports Equipment": [
                                                                                {
                                                                                                    "label": "Sport",
                                                                                                    "key": "team_sport",
                                                                                                    "placeholder": "e.g. Basketball, Volleyball"
                                                                                },
                                                                                {
                                                                                                    "label": "Equipment Type",
                                                                                                    "key": "team_equipment_type",
                                                                                                    "placeholder": "e.g. Ball, Net"
                                                                                },
                                                                                {
                                                                                                    "label": "Official Size",
                                                                                                    "key": "team_equipment_size",
                                                                                                    "placeholder": "e.g. Size 7"
                                                                                },
                                                                                {
                                                                                                    "label": "League / Standard",
                                                                                                    "key": "team_standard",
                                                                                                    "placeholder": "e.g. FIBA Standard"
                                                                                },
                                                                                {
                                                                                                    "label": "Surface / Playing Area",
                                                                                                    "key": "team_surface",
                                                                                                    "placeholder": "e.g. Indoor / Outdoor"
                                                                                }
                                                            ]
                                        }
                    },
                    "health-and-beauty": {
                                        "label": "Health and Beauty",
                                        "generalFields": [
                                                            {
                                                                                "label": "Product Type",
                                                                                "key": "beauty_type",
                                                                                "placeholder": "e.g. Serum, Shampoo, Trimmer"
                                                            },
                                                            {
                                                                                "label": "Skin / Hair Type",
                                                                                "key": "beauty_skin_hair_type",
                                                                                "placeholder": "e.g. Oily Skin"
                                                            },
                                                            {
                                                                                "label": "Net Weight / Volume",
                                                                                "key": "beauty_net_weight",
                                                                                "placeholder": "e.g. 50 ml"
                                                            },
                                                            {
                                                                                "label": "Key Ingredients",
                                                                                "key": "beauty_ingredients",
                                                                                "placeholder": "Key or active ingredients"
                                                            },
                                                            {
                                                                                "label": "Shelf Life / Expiry",
                                                                                "key": "beauty_shelf_life",
                                                                                "placeholder": "Shelf life or expiry"
                                                            }
                                        ],
                                        "subcategories": {
                                                            "Skincare Products": [
                                                                                {
                                                                                                    "label": "Skin Concern",
                                                                                                    "key": "skincare_concern",
                                                                                                    "placeholder": "e.g. Acne, Dryness"
                                                                                },
                                                                                {
                                                                                                    "label": "Active Ingredients",
                                                                                                    "key": "skincare_actives",
                                                                                                    "placeholder": "e.g. Niacinamide"
                                                                                },
                                                                                {
                                                                                                    "label": "SPF",
                                                                                                    "key": "skincare_spf",
                                                                                                    "placeholder": "e.g. SPF 50"
                                                                                },
                                                                                {
                                                                                                    "label": "Formulation",
                                                                                                    "key": "skincare_formulation",
                                                                                                    "placeholder": "e.g. Gel, Cream"
                                                                                },
                                                                                {
                                                                                                    "label": "Fragrance Free",
                                                                                                    "key": "skincare_fragrance_free",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                }
                                                            ],
                                                            "Haircare Solutions": [
                                                                                {
                                                                                                    "label": "Hair Concern",
                                                                                                    "key": "haircare_concern",
                                                                                                    "placeholder": "e.g. Hair Fall, Dandruff"
                                                                                },
                                                                                {
                                                                                                    "label": "Hair Type",
                                                                                                    "key": "haircare_type",
                                                                                                    "placeholder": "e.g. Curly, Dry"
                                                                                },
                                                                                {
                                                                                                    "label": "Formulation",
                                                                                                    "key": "haircare_formulation",
                                                                                                    "placeholder": "e.g. Shampoo, Serum"
                                                                                },
                                                                                {
                                                                                                    "label": "Sulfate Free",
                                                                                                    "key": "haircare_sulfate_free",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                },
                                                                                {
                                                                                                    "label": "Usage Frequency",
                                                                                                    "key": "haircare_frequency",
                                                                                                    "placeholder": "e.g. Daily, Weekly"
                                                                                }
                                                            ],
                                                            "Makeup & Cosmetics": [
                                                                                {
                                                                                                    "label": "Makeup Type",
                                                                                                    "key": "makeup_type",
                                                                                                    "placeholder": "e.g. Foundation, Lipstick"
                                                                                },
                                                                                {
                                                                                                    "label": "Shade",
                                                                                                    "key": "makeup_shade",
                                                                                                    "placeholder": "e.g. Warm Beige"
                                                                                },
                                                                                {
                                                                                                    "label": "Finish",
                                                                                                    "key": "makeup_finish",
                                                                                                    "placeholder": "e.g. Matte, Dewy"
                                                                                },
                                                                                {
                                                                                                    "label": "Coverage",
                                                                                                    "key": "makeup_coverage",
                                                                                                    "placeholder": "e.g. Light, Full"
                                                                                },
                                                                                {
                                                                                                    "label": "Waterproof",
                                                                                                    "key": "makeup_waterproof",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                }
                                                            ],
                                                            "Personal Care Appliances": [
                                                                                {
                                                                                                    "label": "Appliance Type",
                                                                                                    "key": "care_appliance_type",
                                                                                                    "placeholder": "e.g. Hair Dryer, Shaver"
                                                                                },
                                                                                {
                                                                                                    "label": "Power Source",
                                                                                                    "key": "care_appliance_power",
                                                                                                    "placeholder": "e.g. Corded, Rechargeable"
                                                                                },
                                                                                {
                                                                                                    "label": "Heat / Speed Settings",
                                                                                                    "key": "care_appliance_settings",
                                                                                                    "placeholder": "e.g. 3 Speeds"
                                                                                },
                                                                                {
                                                                                                    "label": "Voltage",
                                                                                                    "key": "care_appliance_voltage",
                                                                                                    "placeholder": "e.g. 220 V"
                                                                                },
                                                                                {
                                                                                                    "label": "Attachments Included",
                                                                                                    "key": "care_appliance_attachments",
                                                                                                    "placeholder": "Included accessories"
                                                                                }
                                                            ],
                                                            "Men's Grooming": [
                                                                                {
                                                                                                    "label": "Grooming Type",
                                                                                                    "key": "mens_grooming_type",
                                                                                                    "placeholder": "e.g. Beard Trimmer"
                                                                                },
                                                                                {
                                                                                                    "label": "Suitable Skin / Hair Type",
                                                                                                    "key": "mens_grooming_suitable",
                                                                                                    "placeholder": "e.g. Sensitive Skin"
                                                                                },
                                                                                {
                                                                                                    "label": "Power Source",
                                                                                                    "key": "mens_grooming_power",
                                                                                                    "placeholder": "e.g. Rechargeable"
                                                                                },
                                                                                {
                                                                                                    "label": "Blade Material",
                                                                                                    "key": "mens_grooming_blade",
                                                                                                    "placeholder": "e.g. Stainless Steel"
                                                                                },
                                                                                {
                                                                                                    "label": "Wet / Dry Use",
                                                                                                    "key": "mens_grooming_wet_dry",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Wet & Dry",
                                                                                                                        "Dry Only"
                                                                                                    ]
                                                                                }
                                                            ],
                                                            "Health Supplements": [
                                                                                {
                                                                                                    "label": "Supplement Type",
                                                                                                    "key": "supplement_type",
                                                                                                    "placeholder": "e.g. Vitamin, Protein"
                                                                                },
                                                                                {
                                                                                                    "label": "Form",
                                                                                                    "key": "supplement_form",
                                                                                                    "placeholder": "e.g. Capsule, Powder"
                                                                                },
                                                                                {
                                                                                                    "label": "Serving Size",
                                                                                                    "key": "supplement_serving_size",
                                                                                                    "placeholder": "e.g. 2 Capsules"
                                                                                },
                                                                                {
                                                                                                    "label": "Servings per Container",
                                                                                                    "key": "supplement_servings",
                                                                                                    "placeholder": "e.g. 30"
                                                                                },
                                                                                {
                                                                                                    "label": "Active Nutrients",
                                                                                                    "key": "supplement_nutrients",
                                                                                                    "placeholder": "e.g. Vitamin C 500 mg"
                                                                                }
                                                            ]
                                        }
                    },
                    "books-and-media": {
                                        "label": "Books and Media",
                                        "generalFields": [
                                                            {
                                                                                "label": "Media Type",
                                                                                "key": "media_type",
                                                                                "placeholder": "e.g. Book, Vinyl, DVD, Game"
                                                            },
                                                            {
                                                                                "label": "Creator / Publisher",
                                                                                "key": "media_creator",
                                                                                "placeholder": "Author, artist, publisher"
                                                            },
                                                            {
                                                                                "label": "Language",
                                                                                "key": "media_language",
                                                                                "placeholder": "e.g. English"
                                                            },
                                                            {
                                                                                "label": "Release / Publication Year",
                                                                                "key": "media_release_year",
                                                                                "placeholder": "e.g. 2026"
                                                            },
                                                            {
                                                                                "label": "Edition / Format",
                                                                                "key": "media_format",
                                                                                "placeholder": "e.g. Paperback, Deluxe"
                                                            }
                                        ],
                                        "subcategories": {
                                                            "Fiction & Non-Fiction Books": [
                                                                                {
                                                                                                    "label": "Author",
                                                                                                    "key": "book_author",
                                                                                                    "placeholder": "Author name"
                                                                                },
                                                                                {
                                                                                                    "label": "Genre",
                                                                                                    "key": "book_genre",
                                                                                                    "placeholder": "e.g. Fiction, Biography"
                                                                                },
                                                                                {
                                                                                                    "label": "ISBN",
                                                                                                    "key": "book_isbn",
                                                                                                    "placeholder": "ISBN number"
                                                                                },
                                                                                {
                                                                                                    "label": "Binding",
                                                                                                    "key": "book_binding",
                                                                                                    "placeholder": "e.g. Hardcover"
                                                                                },
                                                                                {
                                                                                                    "label": "Page Count",
                                                                                                    "key": "book_pages",
                                                                                                    "placeholder": "e.g. 320 pages"
                                                                                }
                                                            ],
                                                            "Magazines & Periodicals": [
                                                                                {
                                                                                                    "label": "Publication",
                                                                                                    "key": "magazine_publication",
                                                                                                    "placeholder": "Magazine title"
                                                                                },
                                                                                {
                                                                                                    "label": "Issue / Volume",
                                                                                                    "key": "magazine_issue",
                                                                                                    "placeholder": "e.g. Vol. 12 No. 3"
                                                                                },
                                                                                {
                                                                                                    "label": "Frequency",
                                                                                                    "key": "magazine_frequency",
                                                                                                    "placeholder": "e.g. Monthly"
                                                                                },
                                                                                {
                                                                                                    "label": "Issue Date",
                                                                                                    "key": "magazine_issue_date",
                                                                                                    "placeholder": "Publication date"
                                                                                },
                                                                                {
                                                                                                    "label": "Page Count",
                                                                                                    "key": "magazine_pages",
                                                                                                    "placeholder": "e.g. 80 pages"
                                                                                }
                                                            ],
                                                            "Music CDs & Vinyl Records": [
                                                                                {
                                                                                                    "label": "Artist",
                                                                                                    "key": "music_artist",
                                                                                                    "placeholder": "Artist / band"
                                                                                },
                                                                                {
                                                                                                    "label": "Music Format",
                                                                                                    "key": "music_format",
                                                                                                    "placeholder": "e.g. CD, Vinyl"
                                                                                },
                                                                                {
                                                                                                    "label": "Genre",
                                                                                                    "key": "music_genre",
                                                                                                    "placeholder": "e.g. Pop, Rock"
                                                                                },
                                                                                {
                                                                                                    "label": "Track Count",
                                                                                                    "key": "music_tracks",
                                                                                                    "placeholder": "e.g. 12 tracks"
                                                                                },
                                                                                {
                                                                                                    "label": "Edition",
                                                                                                    "key": "music_edition",
                                                                                                    "placeholder": "e.g. Limited Edition"
                                                                                }
                                                            ],
                                                            "Movie DVDs & Blu-ray": [
                                                                                {
                                                                                                    "label": "Video Format",
                                                                                                    "key": "movie_format",
                                                                                                    "placeholder": "e.g. DVD, Blu-ray"
                                                                                },
                                                                                {
                                                                                                    "label": "Genre",
                                                                                                    "key": "movie_genre",
                                                                                                    "placeholder": "e.g. Action, Drama"
                                                                                },
                                                                                {
                                                                                                    "label": "Region Code",
                                                                                                    "key": "movie_region",
                                                                                                    "placeholder": "e.g. Region A"
                                                                                },
                                                                                {
                                                                                                    "label": "Runtime",
                                                                                                    "key": "movie_runtime",
                                                                                                    "placeholder": "e.g. 120 minutes"
                                                                                },
                                                                                {
                                                                                                    "label": "Audio / Subtitle",
                                                                                                    "key": "movie_audio_subtitle",
                                                                                                    "placeholder": "Languages available"
                                                                                }
                                                            ],
                                                            "Video Games & Consoles": [
                                                                                {
                                                                                                    "label": "Platform",
                                                                                                    "key": "game_platform",
                                                                                                    "placeholder": "e.g. PS5, Switch"
                                                                                },
                                                                                {
                                                                                                    "label": "Genre",
                                                                                                    "key": "game_genre",
                                                                                                    "placeholder": "e.g. RPG, Sports"
                                                                                },
                                                                                {
                                                                                                    "label": "Age Rating",
                                                                                                    "key": "game_rating",
                                                                                                    "placeholder": "e.g. ESRB T"
                                                                                },
                                                                                {
                                                                                                    "label": "Region",
                                                                                                    "key": "game_region",
                                                                                                    "placeholder": "e.g. Asia"
                                                                                },
                                                                                {
                                                                                                    "label": "Multiplayer",
                                                                                                    "key": "game_multiplayer",
                                                                                                    "placeholder": "e.g. Online / Local"
                                                                                }
                                                            ],
                                                            "Educational DVDs": [
                                                                                {
                                                                                                    "label": "Subject",
                                                                                                    "key": "edu_dvd_subject",
                                                                                                    "placeholder": "e.g. Science, Language"
                                                                                },
                                                                                {
                                                                                                    "label": "Target Audience",
                                                                                                    "key": "edu_dvd_audience",
                                                                                                    "placeholder": "e.g. Grade School"
                                                                                },
                                                                                {
                                                                                                    "label": "Runtime",
                                                                                                    "key": "edu_dvd_runtime",
                                                                                                    "placeholder": "e.g. 90 minutes"
                                                                                },
                                                                                {
                                                                                                    "label": "Language",
                                                                                                    "key": "edu_dvd_language",
                                                                                                    "placeholder": "e.g. English"
                                                                                },
                                                                                {
                                                                                                    "label": "Disc / Lesson Count",
                                                                                                    "key": "edu_dvd_count",
                                                                                                    "placeholder": "e.g. 3 Discs"
                                                                                }
                                                            ]
                                        }
                    },
                    "food-and-gourmet": {
                                        "label": "Food and Gourmet",
                                        "generalFields": [
                                                            {
                                                                                "label": "Food / Beverage Type",
                                                                                "key": "food_type",
                                                                                "placeholder": "e.g. Snack, Beverage"
                                                            },
                                                            {
                                                                                "label": "Net Weight / Volume",
                                                                                "key": "food_net_weight",
                                                                                "placeholder": "e.g. 500 g, 1 L"
                                                            },
                                                            {
                                                                                "label": "Ingredients",
                                                                                "key": "food_ingredients",
                                                                                "placeholder": "Main ingredients"
                                                            },
                                                            {
                                                                                "label": "Allergen Information",
                                                                                "key": "food_allergens",
                                                                                "placeholder": "e.g. Contains nuts"
                                                            },
                                                            {
                                                                                "label": "Expiration / Best Before",
                                                                                "key": "food_expiration",
                                                                                "placeholder": "Date or shelf life"
                                                            },
                                                            {
                                                                                "label": "Storage Instructions",
                                                                                "key": "food_storage",
                                                                                "placeholder": "e.g. Keep refrigerated"
                                                            }
                                        ],
                                        "subcategories": {
                                                            "Baking Supplies & Ingredients": [
                                                                                {
                                                                                                    "label": "Ingredient Type",
                                                                                                    "key": "baking_type",
                                                                                                    "placeholder": "e.g. Flour, Cocoa"
                                                                                },
                                                                                {
                                                                                                    "label": "Baking Use",
                                                                                                    "key": "baking_use",
                                                                                                    "placeholder": "e.g. Cakes, Bread"
                                                                                },
                                                                                {
                                                                                                    "label": "Dietary Information",
                                                                                                    "key": "baking_dietary",
                                                                                                    "placeholder": "e.g. Gluten-Free"
                                                                                },
                                                                                {
                                                                                                    "label": "Packaging Type",
                                                                                                    "key": "baking_packaging",
                                                                                                    "placeholder": "e.g. Resealable Pouch"
                                                                                },
                                                                                {
                                                                                                    "label": "Preparation Requirement",
                                                                                                    "key": "baking_preparation",
                                                                                                    "placeholder": "If applicable"
                                                                                }
                                                            ],
                                                            "Coffee, Tea & Beverages": [
                                                                                {
                                                                                                    "label": "Beverage Type",
                                                                                                    "key": "beverage_type",
                                                                                                    "placeholder": "e.g. Coffee, Tea"
                                                                                },
                                                                                {
                                                                                                    "label": "Roast / Flavor",
                                                                                                    "key": "beverage_roast_flavor",
                                                                                                    "placeholder": "e.g. Medium Roast"
                                                                                },
                                                                                {
                                                                                                    "label": "Caffeine Level",
                                                                                                    "key": "beverage_caffeine",
                                                                                                    "placeholder": "e.g. Caffeinated"
                                                                                },
                                                                                {
                                                                                                    "label": "Serving Size",
                                                                                                    "key": "beverage_serving",
                                                                                                    "placeholder": "e.g. 1 Sachet"
                                                                                },
                                                                                {
                                                                                                    "label": "Brewing / Preparation",
                                                                                                    "key": "beverage_preparation",
                                                                                                    "placeholder": "How to prepare"
                                                                                }
                                                            ],
                                                            "Snacks & Candy": [
                                                                                {
                                                                                                    "label": "Snack / Candy Type",
                                                                                                    "key": "snack_type",
                                                                                                    "placeholder": "e.g. Chips, Chocolate"
                                                                                },
                                                                                {
                                                                                                    "label": "Flavor",
                                                                                                    "key": "snack_flavor",
                                                                                                    "placeholder": "e.g. Cheese"
                                                                                },
                                                                                {
                                                                                                    "label": "Pieces / Servings",
                                                                                                    "key": "snack_servings",
                                                                                                    "placeholder": "e.g. 10 pieces"
                                                                                },
                                                                                {
                                                                                                    "label": "Dietary Information",
                                                                                                    "key": "snack_dietary",
                                                                                                    "placeholder": "e.g. Sugar-Free"
                                                                                },
                                                                                {
                                                                                                    "label": "Texture",
                                                                                                    "key": "snack_texture",
                                                                                                    "placeholder": "e.g. Crunchy"
                                                                                }
                                                            ],
                                                            "Specialty Foods & International Cuisine": [
                                                                                {
                                                                                                    "label": "Cuisine / Origin",
                                                                                                    "key": "specialty_cuisine",
                                                                                                    "placeholder": "e.g. Korean, Japanese"
                                                                                },
                                                                                {
                                                                                                    "label": "Food Type",
                                                                                                    "key": "specialty_food_type",
                                                                                                    "placeholder": "e.g. Sauce, Noodles"
                                                                                },
                                                                                {
                                                                                                    "label": "Spice Level",
                                                                                                    "key": "specialty_spice_level",
                                                                                                    "placeholder": "e.g. Mild, Spicy"
                                                                                },
                                                                                {
                                                                                                    "label": "Serving Suggestion",
                                                                                                    "key": "specialty_serving",
                                                                                                    "placeholder": "Serving suggestion"
                                                                                },
                                                                                {
                                                                                                    "label": "Preparation Method",
                                                                                                    "key": "specialty_preparation",
                                                                                                    "placeholder": "Cooking instructions"
                                                                                }
                                                            ],
                                                            "Organic and Health Foods": [
                                                                                {
                                                                                                    "label": "Certification",
                                                                                                    "key": "organic_certification",
                                                                                                    "placeholder": "e.g. Organic Certified"
                                                                                },
                                                                                {
                                                                                                    "label": "Dietary Type",
                                                                                                    "key": "organic_dietary",
                                                                                                    "placeholder": "e.g. Vegan, Keto"
                                                                                },
                                                                                {
                                                                                                    "label": "Nutrition Focus",
                                                                                                    "key": "organic_nutrition_focus",
                                                                                                    "placeholder": "e.g. High Protein"
                                                                                },
                                                                                {
                                                                                                    "label": "Organic Content",
                                                                                                    "key": "organic_content",
                                                                                                    "placeholder": "e.g. 100% Organic"
                                                                                },
                                                                                {
                                                                                                    "label": "Added Sugar",
                                                                                                    "key": "organic_added_sugar",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                }
                                                            ],
                                                            "Meal Kits & Prepped Foods": [
                                                                                {
                                                                                                    "label": "Cuisine",
                                                                                                    "key": "meal_kit_cuisine",
                                                                                                    "placeholder": "e.g. Filipino, Italian"
                                                                                },
                                                                                {
                                                                                                    "label": "Servings",
                                                                                                    "key": "meal_kit_servings",
                                                                                                    "placeholder": "e.g. Serves 2"
                                                                                },
                                                                                {
                                                                                                    "label": "Preparation Time",
                                                                                                    "key": "meal_kit_prep_time",
                                                                                                    "placeholder": "e.g. 15 minutes"
                                                                                },
                                                                                {
                                                                                                    "label": "Storage Requirement",
                                                                                                    "key": "meal_kit_storage",
                                                                                                    "placeholder": "e.g. Refrigerated"
                                                                                },
                                                                                {
                                                                                                    "label": "Included Components",
                                                                                                    "key": "meal_kit_components",
                                                                                                    "placeholder": "What's included"
                                                                                }
                                                            ]
                                        }
                    },
                    "automotive-motorcycle": {
                                        "label": "Automotive & Motorcycle",
                                        "generalFields": [
                                                            {
                                                                                "label": "Part / Accessory Type",
                                                                                "key": "auto_type",
                                                                                "placeholder": "e.g. Helmet, Mirror, Battery"
                                                            },
                                                            {
                                                                                "label": "Vehicle Compatibility",
                                                                                "key": "auto_compatibility",
                                                                                "placeholder": "Vehicle make and model"
                                                            },
                                                            {
                                                                                "label": "Model / Year",
                                                                                "key": "auto_model_year",
                                                                                "placeholder": "e.g. 2020-2026"
                                                            },
                                                            {
                                                                                "label": "Part Number",
                                                                                "key": "auto_part_number",
                                                                                "placeholder": "Manufacturer part number"
                                                            },
                                                            {
                                                                                "label": "Installation Type",
                                                                                "key": "auto_installation",
                                                                                "placeholder": "e.g. Bolt-On"
                                                            }
                                        ],
                                        "subcategories": {
                                                            "Protective Gear": [
                                                                                {
                                                                                                    "label": "Gear Type",
                                                                                                    "key": "auto_gear_type",
                                                                                                    "placeholder": "e.g. Helmet, Gloves"
                                                                                },
                                                                                {
                                                                                                    "label": "Size",
                                                                                                    "key": "auto_gear_size",
                                                                                                    "placeholder": "e.g. Medium"
                                                                                },
                                                                                {
                                                                                                    "label": "Safety Certification",
                                                                                                    "key": "auto_gear_certification",
                                                                                                    "placeholder": "e.g. DOT, ECE"
                                                                                },
                                                                                {
                                                                                                    "label": "Protection Level",
                                                                                                    "key": "auto_gear_protection",
                                                                                                    "placeholder": "Protection details"
                                                                                },
                                                                                {
                                                                                                    "label": "Closure Type",
                                                                                                    "key": "auto_gear_closure",
                                                                                                    "placeholder": "e.g. Double D-Ring"
                                                                                }
                                                            ],
                                                            "Maintenance & Repair Tools": [
                                                                                {
                                                                                                    "label": "Tool Type",
                                                                                                    "key": "auto_tool_type",
                                                                                                    "placeholder": "e.g. Wrench, Scanner"
                                                                                },
                                                                                {
                                                                                                    "label": "Drive / Tool Size",
                                                                                                    "key": "auto_tool_size",
                                                                                                    "placeholder": "e.g. 1/2 inch"
                                                                                },
                                                                                {
                                                                                                    "label": "Tool Material",
                                                                                                    "key": "auto_tool_material",
                                                                                                    "placeholder": "e.g. Chrome Vanadium"
                                                                                },
                                                                                {
                                                                                                    "label": "Power Source",
                                                                                                    "key": "auto_tool_power",
                                                                                                    "placeholder": "e.g. Manual, Battery"
                                                                                },
                                                                                {
                                                                                                    "label": "Set / Piece Count",
                                                                                                    "key": "auto_tool_count",
                                                                                                    "placeholder": "e.g. 24 pieces"
                                                                                }
                                                            ],
                                                            "Parts & Accessories": [
                                                                                {
                                                                                                    "label": "Part Type",
                                                                                                    "key": "auto_part_type_specific",
                                                                                                    "placeholder": "e.g. Mirror, Brake Pad"
                                                                                },
                                                                                {
                                                                                                    "label": "Placement",
                                                                                                    "key": "auto_part_placement",
                                                                                                    "placeholder": "e.g. Front Left"
                                                                                },
                                                                                {
                                                                                                    "label": "OEM / Aftermarket",
                                                                                                    "key": "auto_part_origin",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "OEM",
                                                                                                                        "Aftermarket"
                                                                                                    ]
                                                                                },
                                                                                {
                                                                                                    "label": "Fitment",
                                                                                                    "key": "auto_part_fitment",
                                                                                                    "placeholder": "Exact fitment details"
                                                                                },
                                                                                {
                                                                                                    "label": "Installation Difficulty",
                                                                                                    "key": "auto_part_installation_difficulty",
                                                                                                    "placeholder": "e.g. Easy, Professional"
                                                                                }
                                                            ],
                                                            "Electrical Components": [
                                                                                {
                                                                                                    "label": "Component Type",
                                                                                                    "key": "auto_electrical_type",
                                                                                                    "placeholder": "e.g. Relay, LED"
                                                                                },
                                                                                {
                                                                                                    "label": "Voltage",
                                                                                                    "key": "auto_electrical_voltage",
                                                                                                    "placeholder": "e.g. 12 V"
                                                                                },
                                                                                {
                                                                                                    "label": "Current / Wattage",
                                                                                                    "key": "auto_electrical_current",
                                                                                                    "placeholder": "e.g. 5 A"
                                                                                },
                                                                                {
                                                                                                    "label": "Connector Type",
                                                                                                    "key": "auto_electrical_connector",
                                                                                                    "placeholder": "e.g. Plug Type"
                                                                                },
                                                                                {
                                                                                                    "label": "Polarity",
                                                                                                    "key": "auto_electrical_polarity",
                                                                                                    "placeholder": "If applicable"
                                                                                }
                                                            ],
                                                            "Tires, Wheels, and Fluids": [
                                                                                {
                                                                                                    "label": "Product Type",
                                                                                                    "key": "auto_tire_fluid_type",
                                                                                                    "placeholder": "e.g. Tire, Wheel, Oil"
                                                                                },
                                                                                {
                                                                                                    "label": "Tire / Wheel Size",
                                                                                                    "key": "auto_tire_wheel_size",
                                                                                                    "placeholder": "e.g. 195/65 R15"
                                                                                },
                                                                                {
                                                                                                    "label": "Viscosity / Fluid Grade",
                                                                                                    "key": "auto_fluid_grade",
                                                                                                    "placeholder": "e.g. 5W-30"
                                                                                },
                                                                                {
                                                                                                    "label": "Volume",
                                                                                                    "key": "auto_fluid_volume",
                                                                                                    "placeholder": "e.g. 4 L"
                                                                                },
                                                                                {
                                                                                                    "label": "Load / Speed / Performance Rating",
                                                                                                    "key": "auto_rating",
                                                                                                    "placeholder": "Applicable rating"
                                                                                }
                                                            ]
                                        }
                    },
                    "furniture-and-office-equipment": {
                                        "label": "Furniture and Office Equipment",
                                        "generalFields": [
                                                            {
                                                                                "label": "Furniture / Equipment Type",
                                                                                "key": "furniture_type",
                                                                                "placeholder": "e.g. Chair, Desk"
                                                            },
                                                            {
                                                                                "label": "Dimensions",
                                                                                "key": "furniture_dimensions",
                                                                                "placeholder": "L × W × H"
                                                            },
                                                            {
                                                                                "label": "Assembly Required",
                                                                                "key": "furniture_assembly",
                                                                                "type": "select",
                                                                                "options": [
                                                                                                    "Yes",
                                                                                                    "No"
                                                                                ]
                                                            },
                                                            {
                                                                                "label": "Weight Capacity",
                                                                                "key": "furniture_capacity",
                                                                                "placeholder": "If applicable"
                                                            },
                                                            {
                                                                                "label": "Finish",
                                                                                "key": "furniture_finish",
                                                                                "placeholder": "e.g. Matte, Wood Grain"
                                                            }
                                        ],
                                        "subcategories": {
                                                            "Office Desks & Chairs": [
                                                                                {
                                                                                                    "label": "Item Type",
                                                                                                    "key": "office_desk_chair_type",
                                                                                                    "placeholder": "e.g. Desk, Ergonomic Chair"
                                                                                },
                                                                                {
                                                                                                    "label": "Adjustable Height",
                                                                                                    "key": "office_adjustable_height",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                },
                                                                                {
                                                                                                    "label": "Seat / Desk Width",
                                                                                                    "key": "office_desk_chair_width",
                                                                                                    "placeholder": "Relevant measurement"
                                                                                },
                                                                                {
                                                                                                    "label": "Ergonomic Support",
                                                                                                    "key": "office_ergonomic_support",
                                                                                                    "placeholder": "e.g. Lumbar Support"
                                                                                },
                                                                                {
                                                                                                    "label": "Caster / Base Type",
                                                                                                    "key": "office_base_type",
                                                                                                    "placeholder": "e.g. 5-Star Base"
                                                                                }
                                                            ],
                                                            "Storage Cabinets & Shelving": [
                                                                                {
                                                                                                    "label": "Storage Type",
                                                                                                    "key": "office_storage_type",
                                                                                                    "placeholder": "e.g. Cabinet, Shelf"
                                                                                },
                                                                                {
                                                                                                    "label": "Number of Shelves / Drawers",
                                                                                                    "key": "office_storage_count",
                                                                                                    "placeholder": "e.g. 4 shelves"
                                                                                },
                                                                                {
                                                                                                    "label": "Lockable",
                                                                                                    "key": "office_storage_lockable",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                },
                                                                                {
                                                                                                    "label": "Shelf Weight Capacity",
                                                                                                    "key": "office_storage_capacity",
                                                                                                    "placeholder": "e.g. 30 kg"
                                                                                },
                                                                                {
                                                                                                    "label": "Door Type",
                                                                                                    "key": "office_storage_door",
                                                                                                    "placeholder": "e.g. Sliding, Hinged"
                                                                                }
                                                            ],
                                                            "Conference & Meeting Furniture": [
                                                                                {
                                                                                                    "label": "Furniture Type",
                                                                                                    "key": "conference_furniture_type",
                                                                                                    "placeholder": "e.g. Conference Table"
                                                                                },
                                                                                {
                                                                                                    "label": "Seating Capacity",
                                                                                                    "key": "conference_capacity",
                                                                                                    "placeholder": "e.g. 8 persons"
                                                                                },
                                                                                {
                                                                                                    "label": "Table Shape",
                                                                                                    "key": "conference_shape",
                                                                                                    "placeholder": "e.g. Rectangular"
                                                                                },
                                                                                {
                                                                                                    "label": "Cable Management",
                                                                                                    "key": "conference_cable_management",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                },
                                                                                {
                                                                                                    "label": "Modular",
                                                                                                    "key": "conference_modular",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                }
                                                            ],
                                                            "Computer Tables & Workstations": [
                                                                                {
                                                                                                    "label": "Workstation Type",
                                                                                                    "key": "workstation_type",
                                                                                                    "placeholder": "e.g. L-Shaped Desk"
                                                                                },
                                                                                {
                                                                                                    "label": "Monitor Capacity",
                                                                                                    "key": "workstation_monitor_capacity",
                                                                                                    "placeholder": "e.g. 2 monitors"
                                                                                },
                                                                                {
                                                                                                    "label": "Cable Management",
                                                                                                    "key": "workstation_cable_management",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                },
                                                                                {
                                                                                                    "label": "Keyboard Tray",
                                                                                                    "key": "workstation_keyboard_tray",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                },
                                                                                {
                                                                                                    "label": "Height Adjustable",
                                                                                                    "key": "workstation_adjustable",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                }
                                                            ],
                                                            "Ergonomic Accessories": [
                                                                                {
                                                                                                    "label": "Accessory Type",
                                                                                                    "key": "ergonomic_accessory_type",
                                                                                                    "placeholder": "e.g. Footrest, Monitor Arm"
                                                                                },
                                                                                {
                                                                                                    "label": "Adjustment Type",
                                                                                                    "key": "ergonomic_adjustment",
                                                                                                    "placeholder": "e.g. Height / Tilt"
                                                                                },
                                                                                {
                                                                                                    "label": "Compatible Equipment",
                                                                                                    "key": "ergonomic_compatibility",
                                                                                                    "placeholder": "Compatible devices"
                                                                                },
                                                                                {
                                                                                                    "label": "Maximum Load",
                                                                                                    "key": "ergonomic_max_load",
                                                                                                    "placeholder": "e.g. 9 kg"
                                                                                },
                                                                                {
                                                                                                    "label": "Ergonomic Benefit",
                                                                                                    "key": "ergonomic_benefit",
                                                                                                    "placeholder": "e.g. Wrist Support"
                                                                                }
                                                            ],
                                                            "Office Lighting & Fixtures": [
                                                                                {
                                                                                                    "label": "Fixture Type",
                                                                                                    "key": "office_light_type",
                                                                                                    "placeholder": "e.g. Desk Lamp"
                                                                                },
                                                                                {
                                                                                                    "label": "Bulb / Light Source",
                                                                                                    "key": "office_light_source",
                                                                                                    "placeholder": "e.g. LED"
                                                                                },
                                                                                {
                                                                                                    "label": "Wattage",
                                                                                                    "key": "office_light_wattage",
                                                                                                    "placeholder": "e.g. 12 W"
                                                                                },
                                                                                {
                                                                                                    "label": "Color Temperature",
                                                                                                    "key": "office_light_temperature",
                                                                                                    "placeholder": "e.g. 4000K"
                                                                                },
                                                                                {
                                                                                                    "label": "Dimmable",
                                                                                                    "key": "office_light_dimmable",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                }
                                                            ]
                                        }
                    },
                    "jewelry-and-watches": {
                                        "label": "Jewelry and Watches",
                                        "generalFields": [
                                                            {
                                                                                "label": "Jewelry / Watch Type",
                                                                                "key": "jewelry_type",
                                                                                "placeholder": "e.g. Ring, Necklace, Watch"
                                                            },
                                                            {
                                                                                "label": "Metal / Finish",
                                                                                "key": "jewelry_metal_finish",
                                                                                "placeholder": "e.g. Stainless Steel"
                                                            },
                                                            {
                                                                                "label": "Size / Length",
                                                                                "key": "jewelry_size",
                                                                                "placeholder": "Ring size, length, case size"
                                                            },
                                                            {
                                                                                "label": "Plating",
                                                                                "key": "jewelry_plating",
                                                                                "placeholder": "e.g. 18K Gold Plated"
                                                            },
                                                            {
                                                                                "label": "Care Instructions",
                                                                                "key": "jewelry_care",
                                                                                "placeholder": "Care details"
                                                            }
                                        ],
                                        "subcategories": {
                                                            "Necklaces & Pendants": [
                                                                                {
                                                                                                    "label": "Chain Length",
                                                                                                    "key": "necklace_chain_length",
                                                                                                    "placeholder": "e.g. 45 cm"
                                                                                },
                                                                                {
                                                                                                    "label": "Pendant Size",
                                                                                                    "key": "necklace_pendant_size",
                                                                                                    "placeholder": "Pendant dimensions"
                                                                                },
                                                                                {
                                                                                                    "label": "Clasp Type",
                                                                                                    "key": "necklace_clasp",
                                                                                                    "placeholder": "e.g. Lobster Clasp"
                                                                                },
                                                                                {
                                                                                                    "label": "Gemstone / Stone",
                                                                                                    "key": "necklace_gemstone",
                                                                                                    "placeholder": "If applicable"
                                                                                },
                                                                                {
                                                                                                    "label": "Adjustable Length",
                                                                                                    "key": "necklace_adjustable",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                }
                                                            ],
                                                            "Rings & Earrings": [
                                                                                {
                                                                                                    "label": "Ring / Earring Type",
                                                                                                    "key": "ring_earring_type",
                                                                                                    "placeholder": "e.g. Stud, Band"
                                                                                },
                                                                                {
                                                                                                    "label": "Ring Size",
                                                                                                    "key": "ring_size",
                                                                                                    "placeholder": "If applicable"
                                                                                },
                                                                                {
                                                                                                    "label": "Stone / Setting",
                                                                                                    "key": "ring_earring_stone",
                                                                                                    "placeholder": "e.g. Cubic Zirconia"
                                                                                },
                                                                                {
                                                                                                    "label": "Earring Closure",
                                                                                                    "key": "earring_closure",
                                                                                                    "placeholder": "e.g. Push Back"
                                                                                },
                                                                                {
                                                                                                    "label": "Hypoallergenic",
                                                                                                    "key": "ring_earring_hypoallergenic",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                }
                                                            ],
                                                            "Bracelets & Bangles": [
                                                                                {
                                                                                                    "label": "Bracelet Type",
                                                                                                    "key": "bracelet_type",
                                                                                                    "placeholder": "e.g. Chain, Bangle"
                                                                                },
                                                                                {
                                                                                                    "label": "Length / Diameter",
                                                                                                    "key": "bracelet_length",
                                                                                                    "placeholder": "e.g. 18 cm"
                                                                                },
                                                                                {
                                                                                                    "label": "Closure Type",
                                                                                                    "key": "bracelet_closure",
                                                                                                    "placeholder": "e.g. Lobster Clasp"
                                                                                },
                                                                                {
                                                                                                    "label": "Adjustable",
                                                                                                    "key": "bracelet_adjustable",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                },
                                                                                {
                                                                                                    "label": "Charm / Stone Details",
                                                                                                    "key": "bracelet_charm_stone",
                                                                                                    "placeholder": "If applicable"
                                                                                }
                                                            ],
                                                            "Watches for Men & Women": [
                                                                                {
                                                                                                    "label": "Movement",
                                                                                                    "key": "watch_movement",
                                                                                                    "placeholder": "e.g. Quartz, Automatic"
                                                                                },
                                                                                {
                                                                                                    "label": "Case Size",
                                                                                                    "key": "watch_case_size",
                                                                                                    "placeholder": "e.g. 42 mm"
                                                                                },
                                                                                {
                                                                                                    "label": "Band Material",
                                                                                                    "key": "watch_band_material",
                                                                                                    "placeholder": "e.g. Leather"
                                                                                },
                                                                                {
                                                                                                    "label": "Water Resistance",
                                                                                                    "key": "watch_water_resistance",
                                                                                                    "placeholder": "e.g. 5 ATM"
                                                                                },
                                                                                {
                                                                                                    "label": "Display Type",
                                                                                                    "key": "watch_display",
                                                                                                    "placeholder": "e.g. Analog, Digital"
                                                                                }
                                                            ],
                                                            "Fashion Jewelry": [
                                                                                {
                                                                                                    "label": "Fashion Jewelry Type",
                                                                                                    "key": "fashion_jewelry_type",
                                                                                                    "placeholder": "e.g. Necklace, Ring"
                                                                                },
                                                                                {
                                                                                                    "label": "Style / Theme",
                                                                                                    "key": "fashion_jewelry_style",
                                                                                                    "placeholder": "e.g. Minimalist"
                                                                                },
                                                                                {
                                                                                                    "label": "Hypoallergenic",
                                                                                                    "key": "fashion_jewelry_hypoallergenic",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                },
                                                                                {
                                                                                                    "label": "Occasion",
                                                                                                    "key": "fashion_jewelry_occasion",
                                                                                                    "placeholder": "e.g. Daily, Party"
                                                                                },
                                                                                {
                                                                                                    "label": "Stone / Decorative Material",
                                                                                                    "key": "fashion_jewelry_stone",
                                                                                                    "placeholder": "If applicable"
                                                                                }
                                                            ],
                                                            "Jewelry Storage & Care": [
                                                                                {
                                                                                                    "label": "Storage / Care Type",
                                                                                                    "key": "jewelry_storage_type",
                                                                                                    "placeholder": "e.g. Box, Cleaner"
                                                                                },
                                                                                {
                                                                                                    "label": "Compartments",
                                                                                                    "key": "jewelry_storage_compartments",
                                                                                                    "placeholder": "e.g. 12 compartments"
                                                                                },
                                                                                {
                                                                                                    "label": "Lining Material",
                                                                                                    "key": "jewelry_storage_lining",
                                                                                                    "placeholder": "e.g. Velvet"
                                                                                },
                                                                                {
                                                                                                    "label": "Capacity",
                                                                                                    "key": "jewelry_storage_capacity",
                                                                                                    "placeholder": "Number of items"
                                                                                },
                                                                                {
                                                                                                    "label": "Care Use",
                                                                                                    "key": "jewelry_storage_use",
                                                                                                    "placeholder": "e.g. Polishing, Storage"
                                                                                }
                                                            ]
                                        }
                    },
                    "office-and-school-supplies": {
                                        "label": "Office and School Supplies",
                                        "generalFields": [
                                                            {
                                                                                "label": "Product Type",
                                                                                "key": "office_school_type",
                                                                                "placeholder": "e.g. Notebook, Pen, Printer Ink"
                                                            },
                                                            {
                                                                                "label": "Item Size",
                                                                                "key": "office_school_size",
                                                                                "placeholder": "e.g. A4"
                                                            },
                                                            {
                                                                                "label": "Pack Count",
                                                                                "key": "office_school_pack_count",
                                                                                "placeholder": "e.g. 12 pieces"
                                                            },
                                                            {
                                                                                "label": "Recommended Use",
                                                                                "key": "office_school_use",
                                                                                "placeholder": "e.g. School, Office"
                                                            },
                                                            {
                                                                                "label": "Reusable / Refillable",
                                                                                "key": "office_school_reusable",
                                                                                "type": "select",
                                                                                "options": [
                                                                                                    "Yes",
                                                                                                    "No",
                                                                                                    "Not Applicable"
                                                                                ]
                                                            }
                                        ],
                                        "subcategories": {
                                                            "Notebooks & Paper Products": [
                                                                                {
                                                                                                    "label": "Paper Size",
                                                                                                    "key": "paper_size",
                                                                                                    "placeholder": "e.g. A4, Letter"
                                                                                },
                                                                                {
                                                                                                    "label": "Page Count",
                                                                                                    "key": "paper_page_count",
                                                                                                    "placeholder": "e.g. 80 pages"
                                                                                },
                                                                                {
                                                                                                    "label": "Ruling",
                                                                                                    "key": "paper_ruling",
                                                                                                    "placeholder": "e.g. Ruled, Grid, Plain"
                                                                                },
                                                                                {
                                                                                                    "label": "Paper Weight",
                                                                                                    "key": "paper_gsm",
                                                                                                    "placeholder": "e.g. 80 GSM"
                                                                                },
                                                                                {
                                                                                                    "label": "Binding",
                                                                                                    "key": "paper_binding",
                                                                                                    "placeholder": "e.g. Spiral"
                                                                                }
                                                            ],
                                                            "Writing Instruments": [
                                                                                {
                                                                                                    "label": "Instrument Type",
                                                                                                    "key": "writing_type",
                                                                                                    "placeholder": "e.g. Ballpen, Pencil"
                                                                                },
                                                                                {
                                                                                                    "label": "Ink / Lead Color",
                                                                                                    "key": "writing_color",
                                                                                                    "placeholder": "e.g. Black"
                                                                                },
                                                                                {
                                                                                                    "label": "Point / Tip Size",
                                                                                                    "key": "writing_tip_size",
                                                                                                    "placeholder": "e.g. 0.5 mm"
                                                                                },
                                                                                {
                                                                                                    "label": "Refillable",
                                                                                                    "key": "writing_refillable",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                },
                                                                                {
                                                                                                    "label": "Grip Type",
                                                                                                    "key": "writing_grip",
                                                                                                    "placeholder": "e.g. Rubber Grip"
                                                                                }
                                                            ],
                                                            "Office Furniture": [
                                                                                {
                                                                                                    "label": "Furniture Type",
                                                                                                    "key": "school_office_furniture_type",
                                                                                                    "placeholder": "e.g. Desk, Chair"
                                                                                },
                                                                                {
                                                                                                    "label": "Dimensions",
                                                                                                    "key": "school_office_furniture_dimensions",
                                                                                                    "placeholder": "L × W × H"
                                                                                },
                                                                                {
                                                                                                    "label": "Assembly Required",
                                                                                                    "key": "school_office_furniture_assembly",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                },
                                                                                {
                                                                                                    "label": "Weight Capacity",
                                                                                                    "key": "school_office_furniture_capacity",
                                                                                                    "placeholder": "If applicable"
                                                                                },
                                                                                {
                                                                                                    "label": "Ergonomic Feature",
                                                                                                    "key": "school_office_furniture_ergonomic",
                                                                                                    "placeholder": "If applicable"
                                                                                }
                                                            ],
                                                            "Printers & Printing Supplies": [
                                                                                {
                                                                                                    "label": "Supply / Printer Type",
                                                                                                    "key": "printer_supply_type",
                                                                                                    "placeholder": "e.g. Ink, Toner, Printer"
                                                                                },
                                                                                {
                                                                                                    "label": "Printer Compatibility",
                                                                                                    "key": "printer_compatibility",
                                                                                                    "placeholder": "Compatible models"
                                                                                },
                                                                                {
                                                                                                    "label": "Page Yield",
                                                                                                    "key": "printer_page_yield",
                                                                                                    "placeholder": "e.g. 1,500 pages"
                                                                                },
                                                                                {
                                                                                                    "label": "Color",
                                                                                                    "key": "printer_color",
                                                                                                    "placeholder": "e.g. Black, CMYK"
                                                                                },
                                                                                {
                                                                                                    "label": "Cartridge / Model Code",
                                                                                                    "key": "printer_model_code",
                                                                                                    "placeholder": "e.g. 680 Black"
                                                                                }
                                                            ],
                                                            "School Bags & Backpacks": [
                                                                                {
                                                                                                    "label": "Bag Type",
                                                                                                    "key": "school_bag_type",
                                                                                                    "placeholder": "e.g. Backpack, Sling Bag"
                                                                                },
                                                                                {
                                                                                                    "label": "Capacity",
                                                                                                    "key": "school_bag_capacity",
                                                                                                    "placeholder": "e.g. 25 L"
                                                                                },
                                                                                {
                                                                                                    "label": "Dimensions",
                                                                                                    "key": "school_bag_dimensions",
                                                                                                    "placeholder": "L × W × H"
                                                                                },
                                                                                {
                                                                                                    "label": "Compartments",
                                                                                                    "key": "school_bag_compartments",
                                                                                                    "placeholder": "e.g. 3 compartments"
                                                                                },
                                                                                {
                                                                                                    "label": "Water Resistant",
                                                                                                    "key": "school_bag_water_resistant",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                }
                                                            ],
                                                            "Arts & Craft Materials": [
                                                                                {
                                                                                                    "label": "Material Type",
                                                                                                    "key": "art_material_type",
                                                                                                    "placeholder": "e.g. Paint, Marker, Paper"
                                                                                },
                                                                                {
                                                                                                    "label": "Color Count",
                                                                                                    "key": "art_color_count",
                                                                                                    "placeholder": "e.g. 24 colors"
                                                                                },
                                                                                {
                                                                                                    "label": "Medium",
                                                                                                    "key": "art_medium",
                                                                                                    "placeholder": "e.g. Acrylic, Watercolor"
                                                                                },
                                                                                {
                                                                                                    "label": "Quantity / Set Size",
                                                                                                    "key": "art_quantity",
                                                                                                    "placeholder": "e.g. 12-piece set"
                                                                                },
                                                                                {
                                                                                                    "label": "Non-Toxic",
                                                                                                    "key": "art_non_toxic",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                }
                                                            ]
                                        }
                    }
};


                const showingCount =
                    document.getElementById(
                        'showingCount'
                    );

                const totalEntriesCount =
                    document.getElementById(
                        'totalEntriesCount'
                    );

                const itemsPerPageSelect =
                    document.getElementById(
                        'itemsPerPage'
                    );

                const inventoryPageButtons =
                    document.querySelectorAll(
                        '.pagination-button[data-page]'
                    );

                const previousPage =
                    document.getElementById(
                        'previousPage'
                    );

                const nextPage =
                    document.getElementById(
                        'nextPage'
                    );

                let inventoryCurrentPage = 1;
                let inventoryItemsPerPage = Number(
                    itemsPerPageSelect?.value || 7
                );


                const noResults =
                    document.getElementById(
                        'inventoryNoResults'
                    );


                const refreshButton =
                    document.getElementById(
                        'refreshInventory'
                    );


                const refreshIcon =
                    document.getElementById(
                        'refreshIcon'
                    );



                /* =================================================
                   STATE
                ================================================== */

                let activeTab =
                    'all';



                /* =================================================
                   FILTER PRODUCTS
                ================================================== */

                function getInventoryRowsForCurrentTab() {

                    if (activeTab === 'policy') {
                        return Array.from(
                            document.querySelectorAll(
                                '#policyIssuesTable .policy-row'
                            )
                        );
                    }

                    if (activeTab === 'archived') {
                        return Array.from(
                            document.querySelectorAll(
                                '#archivedItemsTable .archived-row'
                            )
                        );
                    }

                    return Array.from(
                        document.querySelectorAll(
                            '#allProductsTable .inventory-row'
                        )
                    );

                }


                function updateInventoryPaginationControls(totalPages) {

                    if (!inventoryPageButtons.length) {
                        return;
                    }

                    inventoryPageButtons.forEach(
                        function (button) {

                            const pageNumber = Number(
                                button.dataset.page || 1
                            );

                            const isCurrentPage =
                                pageNumber === inventoryCurrentPage;

                            button.classList.toggle(
                                'current',
                                isCurrentPage
                            );

                            button.disabled = pageNumber > totalPages;

                        }
                    );

                    previousPage?.classList.toggle(
                        'disabled',
                        inventoryCurrentPage <= 1
                    );

                    nextPage?.classList.toggle(
                        'disabled',
                        inventoryCurrentPage >= totalPages
                    );

                }


                function filterProducts() {

                    const rows =
                        getInventoryRowsForCurrentTab();

                    const search =
                        (
                            searchInput?.value ||
                            ''
                        )
                        .trim()
                        .toLowerCase();


                    const category =
                        categoryFilter?.value ||
                        'all';


                    const status =
                        statusFilter?.value ||
                        'all';

                    let filteredRows = [];

                    rows.forEach(
                        function (row) {

                            const name =
                                (
                                    row.dataset.name ||
                                    ''
                                )
                                .toLowerCase();


                            const rowCategory =
                                row.dataset.category ||
                                '';


                            const rowStatus =
                                row.dataset.status ||
                                '';


                            const matchesSearch =
                                !search ||
                                name.includes(
                                    search
                                );


                            const matchesCategory =
                                category === 'all' ||
                                rowCategory === category;


                            const matchesStatus =
                                status === 'all' ||
                                rowStatus === status;


                            const shouldShow =
                                matchesSearch &&
                                matchesCategory &&
                                matchesStatus;

                            if (
                                activeTab === 'all' ||
                                activeTab === 'policy' ||
                                activeTab === 'archived'
                            ) {
                                row.classList.toggle(
                                    'hidden',
                                    !shouldShow
                                );
                            }

                            if (shouldShow || activeTab !== 'all') {
                                filteredRows.push({
                                    row,
                                    shouldShow
                                });
                            }

                        }
                    );

                    const totalVisible =
                        filteredRows.filter(function (item) {
                            return item.shouldShow;
                        }).length;

                    const totalPages =
                        Math.max(
                            1,
                            Math.ceil(
                                totalVisible / inventoryItemsPerPage
                            )
                        );

                    inventoryCurrentPage =
                        Math.min(
                            inventoryCurrentPage,
                            totalPages
                        );

                    const startIndex =
                        (inventoryCurrentPage - 1) * inventoryItemsPerPage;

                    const endIndex =
                        startIndex + inventoryItemsPerPage;

                    let shownOnPage = 0;

                    rows.forEach(
                        function (row) {

                            const isVisible =
                                activeTab === 'all' ||
                                activeTab === 'policy' ||
                                activeTab === 'archived'
                                    ? row.classList.contains('hidden') === false
                                    : false;

                            const matchIndex =
                                filteredRows.findIndex(function (item) {
                                    return item.row === row;
                                });

                            const isInCurrentPage =
                                matchIndex >= startIndex &&
                                matchIndex < endIndex &&
                                filteredRows[matchIndex]?.shouldShow;

                            if (
                                activeTab === 'all' ||
                                activeTab === 'policy' ||
                                activeTab === 'archived'
                            ) {
                                row.classList.toggle(
                                    'hidden',
                                    !(filteredRows[matchIndex]?.shouldShow && isInCurrentPage)
                                );
                            }

                            if (filteredRows[matchIndex]?.shouldShow && isInCurrentPage) {
                                shownOnPage++;
                            }

                        }
                    );

                    if (showingCount) {
                        showingCount.textContent =
                            activeTab === 'all'
                                ? shownOnPage
                                : totalVisible;
                    }

                    if (totalEntriesCount) {
                        totalEntriesCount.textContent = totalVisible;
                    }

                    updateInventoryPaginationControls(totalPages);

                    if (noResults) {

                        noResults.classList.toggle(
                            'hidden',
                            totalVisible > 0
                        );

                    }

                }



                /* =================================================
                   TAB VIEW
                ================================================== */

                function updateTabView() {

                    allHeader?.classList.add(
                        'hidden'
                    );

                    policyHeader?.classList.add(
                        'hidden'
                    );

                    allTable?.classList.add(
                        'hidden'
                    );

                    policyTable?.classList.add(
                        'hidden'
                    );

                    archivedTable?.classList.add(
                        'hidden'
                    );

                    archivedEmpty?.classList.add(
                        'hidden'
                    );

                    statusFilter?.classList.remove(
                        'hidden'
                    );

                    if (activeTab === 'policy') {

                        policyHeader?.classList.remove(
                            'hidden'
                        );

                        policyTable?.classList.remove(
                            'hidden'
                        );

                        statusFilter?.classList.add(
                            'hidden'
                        );

                        const policyRows =
                            document.querySelectorAll(
                                '#policyIssuesTable .policy-row'
                            );

                        if (showingCount) {
                            showingCount.textContent =
                                policyRows.length;
                        }

                        if (totalEntriesCount) {
                            totalEntriesCount.textContent =
                                policyRows.length;
                        }

                        noResults?.classList.add(
                            'hidden'
                        );

                        return;
                    }


                    if (activeTab === 'archived') {

                        archivedTable?.classList.remove(
                            'hidden'
                        );

                        const archivedRows =
                            document.querySelectorAll(
                                '#archivedItemsTable .archived-row'
                            );

                        if (archivedRows.length === 0) {

                            archivedEmpty?.classList.remove(
                                'hidden'
                            );

                        }

                        if (showingCount) {
                            showingCount.textContent =
                                archivedRows.length;
                        }

                        if (totalEntriesCount) {
                            totalEntriesCount.textContent =
                                archivedRows.length;
                        }

                        noResults?.classList.add(
                            'hidden'
                        );

                        return;
                    }


                    allHeader?.classList.remove(
                        'hidden'
                    );

                    allTable?.classList.remove(
                        'hidden'
                    );

                    filterProducts();

                }


                /* =================================================
                   TAB EVENTS
                ================================================== */

                tabs.forEach(
                    function (tab) {

                        tab.addEventListener(
                            'click',
                            function () {

                                tabs.forEach(
                                    function (item) {

                                        item.classList.remove(
                                            'active'
                                        );

                                    }
                                );


                                tab.classList.add(
                                    'active'
                                );


                                activeTab =
                                    tab.dataset.tab ||
                                    'all';


                                updateTabView();

                            }
                        );

                    }
                );



                /* =================================================
                   FILTER EVENTS
                ================================================== */

                searchInput?.addEventListener(
                    'input',
                    filterProducts
                );


                categoryFilter?.addEventListener(
                    'change',
                    filterProducts
                );


                statusFilter?.addEventListener(
                    'change',
                    filterProducts
                );

                itemsPerPageSelect?.addEventListener(
                    'change',
                    function () {
                        inventoryItemsPerPage = Number(
                            itemsPerPageSelect.value || 7
                        );
                        inventoryCurrentPage = 1;
                        filterProducts();
                    }
                );

                previousPage?.addEventListener(
                    'click',
                    function () {
                        if (inventoryCurrentPage <= 1) {
                            return;
                        }

                        inventoryCurrentPage--;
                        filterProducts();
                    }
                );

                nextPage?.addEventListener(
                    'click',
                    function () {
                        const rows = getInventoryRowsForCurrentTab();
                        const filtered = rows.filter(function (row) {
                            return row.dataset.name && row.dataset.name.toLowerCase().includes((searchInput?.value || '').trim().toLowerCase());
                        });
                        const totalPages = Math.max(1, Math.ceil(filtered.length / inventoryItemsPerPage));

                        if (inventoryCurrentPage >= totalPages) {
                            return;
                        }

                        inventoryCurrentPage++;
                        filterProducts();
                    }
                );

                inventoryPageButtons.forEach(
                    function (button) {

                        button.addEventListener(
                            'click',
                            function () {
                                inventoryCurrentPage = Number(
                                    button.dataset.page || 1
                                );
                                filterProducts();
                            }
                        );

                    }
                );


                /* =================================================
                   REFRESH
                ================================================== */

                refreshButton?.addEventListener(
                    'click',
                    function () {

                        refreshIcon?.classList.add(
                            'animate-spin'
                        );


                        setTimeout(
                            function () {

                                if (searchInput) {

                                    searchInput.value =
                                        '';

                                }


                                if (categoryFilter) {

                                    categoryFilter.value =
                                        'all';

                                }


                                if (statusFilter) {

                                    statusFilter.value =
                                        'all';

                                }


                                tabs.forEach(
                                    function (tab) {

                                        tab.classList.toggle(
                                            'active',

                                            tab.dataset.tab ===
                                                'all'
                                        );

                                    }
                                );


                                activeTab =
                                    'all';


                                updateTabView();


                                refreshIcon?.classList.remove(
                                    'animate-spin'
                                );

                            },
                            450
                        );

                    }
                );



                /* =================================================
                   PRODUCT DETAILS / POLICY / REMOVE MODALS
                ================================================== */

                const productDetailsModal =
                    document.getElementById(
                        'productDetailsModal'
                    );


                const productDetailsPanel =
                    document.getElementById(
                        'productDetailsModalPanel'
                    );


                const productRows =
                    document.querySelectorAll(
                        '#allProductsTable .product-clickable, #policyIssuesTable .policy-product-clickable'
                    );


                const cancelProductDetails =
                    document.getElementById(
                        'cancelProductDetails'
                    );


                const saveProductChanges =
                    document.getElementById(
                        'saveProductChanges'
                    );


                const removeProductButton =
                    document.getElementById(
                        'removeProductButton'
                    );


                const productDescription =
                    document.getElementById(
                        'productDescription'
                    );


                const descriptionCount =
                    document.getElementById(
                        'descriptionCount'
                    );


                const productPolicyWarning =
                    document.getElementById(
                        'productPolicyWarning'
                    );

                const productDetailsCategory =
                    document.getElementById(
                        'productDetailsCategory'
                    );

                const productDetailsName =
                    document.getElementById(
                        'productDetailsName'
                    );

                const productDetailsPrice =
                    document.getElementById(
                        'productDetailsPrice'
                    );

                const productDetailsStock =
                    document.getElementById(
                        'productDetailsStock'
                    );

                const productDetailsStatus =
                    document.getElementById(
                        'productDetailsStatus'
                    );

                const productDetailsUploaded =
                    document.getElementById(
                        'productDetailsUploaded'
                    );

                const productDetailsMainImage =
                    document.getElementById(
                        'productDetailsMainImage'
                    );

                const productDetailsImageFallback =
                    document.getElementById(
                        'productDetailsImageFallback'
                    );

                const productDetailsThumbnails =
                    document.getElementById(
                        'productDetailsThumbnails'
                    );

                const productDetailBrand =
                    document.getElementById(
                        'productDetailBrand'
                    );

                const productDetailMaterial =
                    document.getElementById(
                        'productDetailMaterial'
                    );

                const productDetailSizes =
                    document.getElementById(
                        'productDetailSizes'
                    );

                const productDetailColors =
                    document.getElementById(
                        'productDetailColors'
                    );

                const productDetailQuantity =
                    document.getElementById(
                        'productDetailQuantity'
                    );

                const productDetailCountry =
                    document.getElementById(
                        'productDetailCountry'
                    );

                const productDetailsBuyerOptionsSection =
                    document.getElementById(
                        'productDetailsBuyerOptionsSection'
                    );

                const productDetailsBuyerOptions =
                    document.getElementById(
                        'productDetailsBuyerOptions'
                    );

                const productDetailsSpecificationsSection =
                    document.getElementById(
                        'productDetailsSpecificationsSection'
                    );

                const productDetailsSpecifications =
                    document.getElementById(
                        'productDetailsSpecifications'
                    );


                const productPolicyIssueTitle =
                    document.getElementById(
                        'productPolicyIssueTitle'
                    );


                const productPolicyIssueDate =
                    document.getElementById(
                        'productPolicyIssueDate'
                    );

                const productPolicyIssueReason =
                    document.getElementById(
                        'productPolicyIssueReason'
                    );


                const removeProductModal =
                    document.getElementById(
                        'removeProductModal'
                    );


                const removeProductModalPanel =
                    document.getElementById(
                        'removeProductModalPanel'
                    );


                const cancelRemoveProduct =
                    document.getElementById(
                        'cancelRemoveProduct'
                    );


                const confirmRemoveProduct =
                    document.getElementById(
                        'confirmRemoveProduct'
                    );

                const removeProductModalTitle =
                    document.getElementById('removeProductModalTitle');

                const removeProductModalSubtitle =
                    document.getElementById('removeProductModalSubtitle');

                const removeReasonBlock =
                    document.getElementById('removeReasonBlock');

                const removeDetailsBlock =
                    document.getElementById('removeDetailsBlock');


                const removeAdditionalDetails =
                    document.getElementById(
                        'removeAdditionalDetails'
                    );


                const removeDetailsCount =
                    document.getElementById(
                        'removeDetailsCount'
                    );


                let currentProductRow = null;

                /*
                 * Editable Product Specifications draft for a created product.
                 * The draft is saved back to createdInventoryProducts only when
                 * the seller presses Save Changes.
                 */
                let currentCreatedSpecificationDraft = {};
                let currentCreatedSpecificationLabels = {};


                let closeDetailsTimer = null;


                let closeRemoveTimer = null;


                /* =================================================
                   FADE OPEN / CLOSE HELPERS
                ================================================== */

                function fadeOpenModal(modal, panel) {

                    if (!modal || !panel) {
                        return;
                    }

                    modal.classList.remove(
                        'hidden',
                        'modal-closing'
                    );

                    modal.classList.add(
                        'modal-open'
                    );

                    modal.setAttribute(
                        'aria-hidden',
                        'false'
                    );

                    panel.style.transform =
                        'none';

                }


                function fadeCloseModal(
                    modal,
                    panel,
                    onComplete = null
                ) {

                    if (!modal || !panel) {
                        return;
                    }

                    modal.classList.remove(
                        'modal-open'
                    );

                    modal.classList.add(
                        'modal-closing'
                    );

                    modal.setAttribute(
                        'aria-hidden',
                        'true'
                    );

                    panel.style.transform =
                        'none';

                    window.setTimeout(
                        function () {

                            modal.classList.remove(
                                'modal-closing'
                            );

                            modal.classList.add(
                                'hidden'
                            );

                            if (typeof onComplete === 'function') {
                                onComplete();
                            }

                        },
                        200
                    );

                }


                /* =================================================
                   UPDATE PRODUCT DETAILS MODAL CONTENT
                ================================================== */

                function getCreatedProductFromRow(
                    row
                ) {

                    const productId =
                        row?.dataset?.createdProductId ||
                        '';

                    if (!productId) {
                        return null;
                    }

                    return createdInventoryProducts.find(
                        function (product) {
                            return String(product.id) ===
                                String(productId);
                        }
                    ) || archivedInventoryProducts.find(
                        function (product) {
                            return String(product.id) ===
                                String(productId);
                        }
                    ) || null;

                }


                function formatCreatedProductDate(
                    value
                ) {

                    if (!value) {
                        return '—';
                    }

                    const date =
                        new Date(
                            value
                        );

                    if (
                        Number.isNaN(
                            date.getTime()
                        )
                    ) {
                        return value;
                    }

                    const datePart =
                        new Intl.DateTimeFormat(
                            'en-US',
                            {
                                month:
                                    'long',
                                day:
                                    'numeric',
                                year:
                                    'numeric'
                            }
                        ).format(
                            date
                        );

                    const timePart =
                        new Intl.DateTimeFormat(
                            'en-US',
                            {
                                hour:
                                    'numeric',
                                minute:
                                    '2-digit'
                            }
                        ).format(
                            date
                        );

                    return `${datePart}  ${timePart}`;

                }

                function formatPolicyIssueTitle(
                    value
                ) {
                    return String(
                        value || 'Issue Warning'
                    ).trim();
                }


                function createdProductLabelFromKey(
                    key
                ) {

                    return String(
                        key ||
                        ''
                    )
                    .replace(
                        /^category_specifications\[/,
                        ''
                    )
                    .replace(
                        /\]$/,
                        ''
                    )
                    .replaceAll(
                        '_',
                        ' '
                    )
                    .replace(
                        /\b\w/g,
                        function (letter) {
                            return letter.toUpperCase();
                        }
                    );

                }


                function normalizeOptionalProductValue(value) {

                    if (value === null || value === undefined) {
                        return '';
                    }

                    const text = String(value).trim();

                    if (!text || ['null', 'undefined'].includes(text.toLowerCase())) {
                        return '';
                    }

                    return text;

                }


                function createdProductSpecificationValue(
                    product,
                    target
                ) {

                    const entries =
                        product?.specificationDisplay ||
                        [];

                    const found =
                        entries.find(
                            function (item) {
                                return item.key ===
                                    target;
                            }
                        );

                    if (found?.value !== undefined && found?.value !== null) {
                        return normalizeOptionalProductValue(found.value);
                    }

                    const legacy =
                        product?.specifications?.[
                            `category_specifications[${target}]`
                        ];

                    return normalizeOptionalProductValue(legacy);

                }


                function makeCreatedDetailItem(
                    label,
                    value,
                    fullWidth = false
                ) {

                    const item =
                        document.createElement(
                            'div'
                        );

                    item.className =
                        fullWidth
                            ? 'product-created-detail-item is-full'
                            : 'product-created-detail-item';

                    const labelElement =
                        document.createElement(
                            'span'
                        );

                    labelElement.className =
                        'product-created-detail-label';

                    labelElement.textContent =
                        label;

                    const valueElement =
                        Array.isArray(value)
                            ? document.createElement('div')
                            : document.createElement('span');

                    valueElement.className =
                        'product-created-detail-value';

                    if (Array.isArray(value)) {
                        valueElement.classList.add(
                            'product-created-detail-list'
                        );

                        value.forEach(function (line) {
                            const lineElement =
                                document.createElement('div');

                            lineElement.className =
                                'product-created-detail-line';

                            lineElement.textContent = line;
                            valueElement.appendChild(lineElement);
                        });
                    } else {
                        const normalizedValue = normalizeOptionalProductValue(value);
                        valueElement.textContent = normalizedValue;
                    }

                    item.appendChild(
                        labelElement
                    );

                    item.appendChild(
                        valueElement
                    );

                    return item;

                }


                function renderCreatedProductPhotos(
                    product
                ) {

                    const photos =
                        (
                            Array.isArray(
                                product.photos
                            ) &&
                            product.photos.length
                        )
                            ? product.photos
                            : (
                                product.coverPhoto
                                    ? [
                                        product.coverPhoto
                                    ]
                                    : []
                            );

                    if (productDetailsMainImage) {

                        if (photos.length) {

                            productDetailsMainImage.src =
                                photos[0];

                            productDetailsMainImage.alt =
                                product.title ||
                                'Product image';

                            productDetailsMainImage.style.display =
                                'block';

                            if (productDetailsImageFallback) {
                                productDetailsImageFallback.style.display =
                                    'none';
                            }

                        } else {

                            productDetailsMainImage.style.display =
                                'none';

                            if (productDetailsImageFallback) {
                                productDetailsImageFallback.style.display =
                                    'flex';
                            }

                        }

                    }

                    if (!productDetailsThumbnails) {
                        return;
                    }

                    productDetailsThumbnails.innerHTML =
                        '';

                    photos.forEach(
                        function (photo, index) {

                            const thumb =
                                document.createElement(
                                    'button'
                                );

                            thumb.type =
                                'button';

                            thumb.className =
                                index === 0
                                    ? 'created-detail-thumb is-active'
                                    : 'created-detail-thumb';

                            const image =
                                document.createElement(
                                    'img'
                                );

                            image.src =
                                photo;

                            image.alt =
                                `${product.title || 'Product'} photo ${index + 1}`;

                            thumb.appendChild(
                                image
                            );

                            thumb.addEventListener(
                                'click',
                                function () {

                                    if (productDetailsMainImage) {
                                        productDetailsMainImage.src =
                                            photo;
                                    }

                                    productDetailsThumbnails
                                        .querySelectorAll(
                                            '.created-detail-thumb'
                                        )
                                        .forEach(
                                            function (item) {
                                                item.classList.remove(
                                                    'is-active'
                                                );
                                            }
                                        );

                                    thumb.classList.add(
                                        'is-active'
                                    );

                                }
                            );

                            productDetailsThumbnails.appendChild(
                                thumb
                            );

                        }
                    );

                }


                function renderCreatedBuyerOptions(
                    product
                ) {

                    if (
                        !productDetailsBuyerOptionsSection ||
                        !productDetailsBuyerOptions
                    ) {
                        return;
                    }

                    productDetailsBuyerOptions.innerHTML =
                        '';

                    const groups = [
                        {
                            label:
                                'Variations',
                            key:
                                'variations'
                        },
                        {
                            label:
                                'Colors',
                            key:
                                'colors'
                        },
                        {
                            label:
                                'Sizes',
                            key:
                                'sizes'
                        }
                    ];

                    let hasAny =
                        false;

                    groups.forEach(
                        function (group) {

                            const values =
                                Array.isArray(
                                    product[group.key]
                                )
                                    ? product[group.key]
                                    : [];

                            if (!values.length) {
                                return;
                            }

                            hasAny =
                                true;

                            const rows =
                                values.map(
                                    function (entry) {

                                        const numericPrice =
                                            Number(
                                                entry.price ||
                                                0
                                            );

                                        const priceText =
                                            product.pricingMode ===
                                                'varies'
                                                ? (
                                                    entry.priceType ===
                                                        'base'
                                                        ? formatMoney(
                                                            numericPrice
                                                        )
                                                        : `+${formatMoney(
                                                            numericPrice
                                                        )}`
                                                )
                                                : '';

                                        const photoNote =
                                            entry.photoData || entry.photo
                                                ? ' 📷'
                                                : '';

                                        const stockText =
                                            `Stock: ${Number(entry.stock || 0)}`;

                                        return priceText
                                            ? `${entry.name}${photoNote} — ${priceText} — ${stockText}`
                                            : `${entry.name}${photoNote} — ${stockText}`;

                                    }
                                );

                            productDetailsBuyerOptions.appendChild(
                                makeCreatedDetailItem(
                                    group.label,
                                    rows,
                                    true
                                )
                            );

                        }
                    );

                    productDetailsBuyerOptionsSection.classList.toggle(
                        'hidden',
                        !hasAny
                    );

                }


                function buildCreatedSpecificationDraft(
                    product
                ) {

                    const draft =
                        {};

                    const labels =
                        {};

                    const source =
                        Array.isArray(
                            product?.specificationDisplay
                        )
                            ? product.specificationDisplay
                            : [];

                    source.forEach(
                        function (item) {

                            if (!item?.key) {
                                return;
                            }

                            draft[item.key] =
                                normalizeOptionalProductValue(
                                    item.value
                                );

                            labels[item.key] =
                                item.label ||
                                createdProductLabelFromKey(
                                    item.key
                                );

                        }
                    );

                    Object.entries(
                        product?.specifications ||
                        {}
                    ).forEach(
                        function ([
                            rawKey,
                            value
                        ]) {

                            const key =
                                String(
                                    rawKey
                                )
                                .replace(
                                    /^category_specifications\[/,
                                    ''
                                )
                                .replace(
                                    /\]$/,
                                    ''
                                );

                            if (
                                draft[key] ===
                                undefined
                            ) {
                                draft[key] =
                                    normalizeOptionalProductValue(
                                        value
                                    );
                            }

                            if (!labels[key]) {
                                labels[key] =
                                    createdProductLabelFromKey(
                                        key
                                    );
                            }

                        }
                    );

                    return {
                        draft,
                        labels
                    };

                }


                function makeCreatedSpecificationHeading(
                    title,
                    subtitle = ''
                ) {

                    const heading =
                        document.createElement(
                            'div'
                        );

                    heading.className =
                        'product-created-spec-heading';

                    heading.textContent =
                        title;

                    if (subtitle) {

                        const small =
                            document.createElement(
                                'small'
                            );

                        small.textContent =
                            subtitle;

                        heading.appendChild(
                            small
                        );

                    }

                    return heading;

                }


                function makeCreatedSpecificationField(
                    field,
                    value = ''
                ) {

                    const type =
                        field.type ||
                        'text';

                    const fullWidth =
                        type === 'textarea';

                    const wrapper =
                        document.createElement(
                            'div'
                        );

                    wrapper.className =
                        fullWidth
                            ? 'product-created-spec-field is-full'
                            : 'product-created-spec-field';

                    const label =
                        document.createElement(
                            'label'
                        );

                    label.htmlFor =
                        `detailsSpec_${field.key}`;

                    label.textContent =
                        field.label ||
                        createdProductLabelFromKey(
                            field.key
                        );

                    let control;

                    if (type === 'select') {

                        control =
                            document.createElement(
                                'select'
                            );

                        control.className =
                            'product-created-spec-select';

                        const empty =
                            document.createElement(
                                'option'
                            );

                        empty.value =
                            '';

                        empty.textContent =
                            `Select ${String(field.label || '').toLowerCase()}`;

                        control.appendChild(
                            empty
                        );

                        (
                            field.options ||
                            []
                        ).forEach(
                            function (optionLabel) {

                                const option =
                                    document.createElement(
                                        'option'
                                    );

                                option.value =
                                    optionLabel;

                                option.textContent =
                                    optionLabel;

                                control.appendChild(
                                    option
                                );

                            }
                        );

                    } else if (type === 'textarea') {

                        control =
                            document.createElement(
                                'textarea'
                            );

                        control.className =
                            'product-created-spec-textarea';

                        control.rows =
                            2;

                    } else {

                        control =
                            document.createElement(
                                'input'
                            );

                        control.type =
                            'text';

                        control.className =
                            'product-created-spec-input';

                    }

                    control.id =
                        `detailsSpec_${field.key}`;

                    control.dataset.createdSpecKey =
                        field.key;

                    control.dataset.createdSpecLabel =
                        field.label ||
                        createdProductLabelFromKey(
                            field.key
                        );

                    if (field.placeholder) {
                        control.placeholder =
                            field.placeholder;
                    }

                    if (type === 'readonly') {

                        control.readOnly =
                            true;

                        control.classList.add(
                            'product-created-spec-readonly'
                        );

                    }

                    control.value =
                        normalizeOptionalProductValue(
                            value
                        );

                    const updateDraft =
                        function () {

                            currentCreatedSpecificationDraft[
                                field.key
                            ] =
                                control.value;

                            currentCreatedSpecificationLabels[
                                field.key
                            ] =
                                field.label ||
                                createdProductLabelFromKey(
                                    field.key
                                );

                        };

                    control.addEventListener(
                        'input',
                        updateDraft
                    );

                    control.addEventListener(
                        'change',
                        updateDraft
                    );

                    wrapper.appendChild(
                        label
                    );

                    wrapper.appendChild(
                        control
                    );

                    if (field.help) {

                        const help =
                            document.createElement(
                                'small'
                            );

                        help.className =
                            'product-created-spec-help';

                        help.textContent =
                            field.help;

                        wrapper.appendChild(
                            help
                        );

                    }

                    return {
                        wrapper,
                        control
                    };

                }


                function makeCreatedSubcategoryField(
                    product,
                    config
                ) {

                    const wrapper =
                        document.createElement(
                            'div'
                        );

                    wrapper.className =
                        'product-created-spec-field';

                    const label =
                        document.createElement(
                            'label'
                        );

                    label.htmlFor =
                        'detailsSpec_subcategory';

                    label.textContent =
                        'Subcategory';

                    const select =
                        document.createElement(
                            'select'
                        );

                    select.id =
                        'detailsSpec_subcategory';

                    select.className =
                        'product-created-spec-select';

                    select.dataset.createdSpecKey =
                        'subcategory';

                    select.dataset.createdSpecLabel =
                        'Subcategory';

                    const empty =
                        document.createElement(
                            'option'
                        );

                    empty.value =
                        '';

                    empty.textContent =
                        'Select subcategory';

                    select.appendChild(
                        empty
                    );

                    Object.keys(
                        config?.subcategories ||
                        {}
                    ).forEach(
                        function (subcategory) {

                            const option =
                                document.createElement(
                                    'option'
                                );

                            option.value =
                                subcategory;

                            option.textContent =
                                subcategory;

                            select.appendChild(
                                option
                            );

                        }
                    );

                    select.value =
                        currentCreatedSpecificationDraft
                            .subcategory ||
                        '';

                    select.addEventListener(
                        'change',
                        function () {

                            /*
                             * First collect any unsaved values from the current
                             * dynamic specification fields, then re-render so
                             * the newly selected subcategory gets its own fields.
                             */
                            collectCreatedSpecificationInputs();

                            currentCreatedSpecificationDraft
                                .subcategory =
                                select.value;

                            currentCreatedSpecificationLabels
                                .subcategory =
                                'Subcategory';

                            renderCreatedSpecifications(
                                product,
                                false
                            );

                        }
                    );

                    wrapper.appendChild(
                        label
                    );

                    wrapper.appendChild(
                        select
                    );

                    return wrapper;

                }


                function collectCreatedSpecificationInputs() {

                    if (!productDetailsSpecifications) {
                        return;
                    }

                    productDetailsSpecifications
                        .querySelectorAll(
                            '[data-created-spec-key]'
                        )
                        .forEach(
                            function (control) {

                                const key =
                                    control.dataset
                                        .createdSpecKey;

                                if (!key) {
                                    return;
                                }

                                currentCreatedSpecificationDraft[
                                    key
                                ] =
                                    control.value ?? '';

                                currentCreatedSpecificationLabels[
                                    key
                                ] =
                                    control.dataset
                                        .createdSpecLabel ||
                                    createdProductLabelFromKey(
                                        key
                                    );

                            }
                        );

                }


                function renderCreatedSpecifications(
                    product,
                    initializeDraft = true
                ) {

                    if (
                        !productDetailsSpecificationsSection ||
                        !productDetailsSpecifications
                    ) {
                        return;
                    }

                    const config =
                        productSpecificationLibrary[
                            product?.category
                        ] ||
                        null;

                    if (initializeDraft) {

                        const prepared =
                            buildCreatedSpecificationDraft(
                                product
                            );

                        currentCreatedSpecificationDraft =
                            prepared.draft;

                        currentCreatedSpecificationLabels =
                            prepared.labels;

                    }

                    productDetailsSpecifications.innerHTML =
                        '';

                    if (!config) {

                        productDetailsSpecificationsSection
                            .classList.add(
                                'hidden'
                            );

                        return;
                    }

                    productDetailsSpecificationsSection
                        .classList.remove(
                            'hidden'
                        );

                    productDetailsSpecifications.appendChild(
                        makeCreatedSpecificationHeading(
                            `General ${config.label} Specifications`,
                            'You can still edit these values before or after product approval.'
                        )
                    );

                    /*
                     * Subcategory remains editable. Changing it updates the
                     * category-specific fields shown underneath.
                     */
                    productDetailsSpecifications.appendChild(
                        makeCreatedSubcategoryField(
                            product,
                            config
                        )
                    );

                    /*
                     * Brand / Material / Quantity per Pack / Country of Origin
                     * already have editable fields in Product Details above,
                     * so they are not duplicated here.
                     */
                    const duplicateCommonKeys =
                        new Set([
                            'brand',
                            'material',
                            'quantity_per_pack',
                            'country_of_origin'
                        ]);

                    commonProductSpecificationFields.forEach(
                        function (field) {

                            if (
                                duplicateCommonKeys.has(
                                    field.key
                                )
                            ) {
                                return;
                            }

                            const result =
                                makeCreatedSpecificationField(
                                    field,
                                    currentCreatedSpecificationDraft[
                                        field.key
                                    ] ??
                                    field.value ??
                                    ''
                                );

                            productDetailsSpecifications.appendChild(
                                result.wrapper
                            );

                        }
                    );

                    (
                        config.generalFields ||
                        []
                    ).forEach(
                        function (field) {

                            const result =
                                makeCreatedSpecificationField(
                                    field,
                                    currentCreatedSpecificationDraft[
                                        field.key
                                    ] ??
                                    ''
                                );

                            productDetailsSpecifications.appendChild(
                                result.wrapper
                            );

                        }
                    );

                    const selectedSubcategory =
                        currentCreatedSpecificationDraft
                            .subcategory ||
                        '';

                    const subcategoryFields =
                        config.subcategories?.[
                            selectedSubcategory
                        ] ||
                        [];

                    if (
                        selectedSubcategory &&
                        subcategoryFields.length
                    ) {

                        productDetailsSpecifications.appendChild(
                            makeCreatedSpecificationHeading(
                                `${selectedSubcategory} Specifications`,
                                'These fields are specific to the selected subcategory.'
                            )
                        );

                        subcategoryFields.forEach(
                            function (field) {

                                const result =
                                    makeCreatedSpecificationField(
                                        field,
                                        currentCreatedSpecificationDraft[
                                            field.key
                                        ] ??
                                        ''
                                    );

                                productDetailsSpecifications.appendChild(
                                    result.wrapper
                                );

                            }
                        );

                    }

                }


                function saveCreatedProductSpecificationChanges(
                    product
                ) {

                    if (!product) {
                        return;
                    }

                    collectCreatedSpecificationInputs();

                    /*
                     * Sync the editable common Product Details fields into the
                     * same specification model used by Create Product.
                     */
                    currentCreatedSpecificationDraft.brand =
                        productDetailBrand?.value?.trim() ||
                        '';

                    currentCreatedSpecificationLabels.brand =
                        'Brand';

                    currentCreatedSpecificationDraft.material =
                        productDetailMaterial?.value?.trim() ||
                        '';

                    currentCreatedSpecificationLabels.material =
                        'Material';

                    currentCreatedSpecificationDraft
                        .quantity_per_pack =
                        productDetailQuantity?.value?.trim() ||
                        '';

                    currentCreatedSpecificationLabels
                        .quantity_per_pack =
                        'Quantity per Pack';

                    currentCreatedSpecificationDraft
                        .country_of_origin =
                        productDetailCountry?.value?.trim() ||
                        '';

                    currentCreatedSpecificationLabels
                        .country_of_origin =
                        'Country of Origin';

                    /*
                     * Keep category and stock as system values rather than
                     * seller-editable specification fields.
                     */
                    currentCreatedSpecificationDraft
                        .selected_category =
                        product.categoryLabel ||
                        productSpecificationLibrary[
                            product.category
                        ]?.label ||
                        product.category ||
                        '';

                    currentCreatedSpecificationLabels
                        .selected_category =
                        'Category';

                    currentCreatedSpecificationDraft
                        .stock_display =
                        String(
                            product.stock ??
                            0
                        );

                    currentCreatedSpecificationLabels
                        .stock_display =
                        'Stock';

                    const specificationDisplay =
                        Object.entries(
                            currentCreatedSpecificationDraft
                        )
                        .filter(
                            function ([
                                key,
                                value
                            ]) {

                                return (
                                    key &&
                                    value !== '' &&
                                    value !== null &&
                                    value !== undefined
                                );

                            }
                        )
                        .map(
                            function ([
                                key,
                                value
                            ]) {

                                return {
                                    key,
                                    label:
                                        currentCreatedSpecificationLabels[
                                            key
                                        ] ||
                                        createdProductLabelFromKey(
                                            key
                                        ),
                                    value:
                                        String(
                                            value
                                        )
                                };

                            }
                        );

                    const specifications =
                        {};

                    specificationDisplay.forEach(
                        function (item) {

                            specifications[
                                `category_specifications[${item.key}]`
                            ] =
                                item.value;

                        }
                    );

                    product.specificationDisplay =
                        specificationDisplay;

                    product.specifications =
                        specifications;

                    product.description =
                        productDescription?.value?.trim() ||
                        '';

                    /*
                     * Product Details already exposes Colors and Sizes as
                     * editable comma-separated fields. Keep those edits too
                     * while retaining their previous price values where names
                     * still match.
                     */
                    const syncNamedOptions =
                        function (
                            rawValue,
                            existing,
                            group
                        ) {

                            const names =
                                String(
                                    rawValue ||
                                    ''
                                )
                                .split(',')
                                .map(
                                    function (value) {
                                        return value.trim();
                                    }
                                )
                                .filter(Boolean);

                            return names.map(
                                function (name) {

                                    const previous =
                                        (
                                            existing ||
                                            []
                                        )
                                        .find(
                                            function (item) {
                                                return (
                                                    String(
                                                        item.name ||
                                                        ''
                                                    ).toLowerCase() ===
                                                    name.toLowerCase()
                                                );
                                            }
                                        );

                                    return {
                                        name,
                                        price:
                                            previous?.price ||
                                            0,
                                        priceType:
                                            product.pricingMode ===
                                                'varies' &&
                                            product.pricingSource ===
                                                group
                                                ? 'base'
                                                : 'addon'
                                    };

                                }
                            );

                        };

                    product.colors =
                        syncNamedOptions(
                            productDetailColors?.value,
                            product.colors,
                            'colors'
                        );

                    product.sizes =
                        syncNamedOptions(
                            productDetailSizes?.value,
                            product.sizes,
                            'sizes'
                        );

                    saveCreatedProducts();

                }


                function updateProductDetailsModal(row) {

                    if (!row) {
                        return;
                    }

                    const createdProduct =
                        getCreatedProductFromRow(
                            row
                        );

                    if (saveProductChanges) {
                        const archivedByAdmin = Boolean(
                            (createdProduct && (createdProduct.archivedByAdmin || createdProduct.archived_by_admin)) ||
                            row.dataset.archivedByAdmin === 'true'
                        );

                        saveProductChanges.textContent =
                            row.classList.contains('archived-row')
                                ? (archivedByAdmin ? 'Removed by Admin' : 'Unarchive')
                                : 'Save Changes';

                        saveProductChanges.disabled = archivedByAdmin && row.classList.contains('archived-row');
                    }

                    const isPolicy =
                        row.dataset.policy === 'true' ||
                        row.classList.contains(
                            'policy-product-clickable'
                        );


                    /*
                     * CREATED PRODUCT
                     * Use the exact values entered in Create Product.
                     */
                    if (createdProduct) {

                        const category =
                            createdProduct.categoryLabel ||
                            productSpecificationLibrary[
                                createdProduct.category
                            ]?.label ||
                            createdProduct.category ||
                            '—';

                        if (productDetailsName) {
                            productDetailsName.textContent =
                                createdProduct.title ||
                                'Product';
                        }

                        if (productDetailsPrice) {
                            productDetailsPrice.textContent =
                                calculateCreatedProductPrice(
                                    createdProduct
                                );
                        }

                        if (productDetailsStock) {
                            productDetailsStock.textContent =
                                `${createdProduct.stock ?? 0} pieces`;
                        }

                        if (productDetailsStatus) {
                            const createdStatus =
                                getCreatedProductStatus(
                                    createdProduct.stock,
                                    createdProduct.approvalStatus
                                );

                            productDetailsStatus.textContent =
                                createdStatus.label;

                            productDetailsStatus.className =
                                `status-badge mt-[6px] ${createdStatus.className}`;
                        }

                        if (productDetailsUploaded) {
                            productDetailsUploaded.textContent =
                                formatCreatedProductDate(
                                    createdProduct.createdAt
                                );
                        }

                        if (productDetailsCategory) {

                            productDetailsCategory.textContent =
                                category;

                            productDetailsCategory.className =
                                `inline-flex items-center mt-[7px] rounded-full px-[13px] py-[4px] text-[11px] font-medium category-badge ${categoryBadgeClass(category)}`;

                        }

                        if (productDescription) {
                            productDescription.value =
                                createdProduct.description ||
                                '';
                        }

                        const archiveReason =
                            createdProduct.archiveReason ||
                            createdProduct.archive_reason ||
                            '';

                        const archivedByAdmin = Boolean(
                            createdProduct.archivedByAdmin ||
                            createdProduct.archived_by_admin
                        );

                        const warningReason =
                            createdProduct.warningReason ||
                            createdProduct.warning_reason ||
                            archiveReason ||
                            '';

                        const warningDetails =
                            createdProduct.warningDetails ||
                            createdProduct.warning_details ||
                            (archivedByAdmin
                                ? (archiveReason
                                    ? `This product was removed by an administrator because ${archiveReason}.`
                                    : 'This product was removed by an administrator.')
                                : '') ||
                            '';

                        const hasWarningIssue =
                            String(createdProduct.approvalStatus || '').toLowerCase() === 'warning' ||
                            Boolean(warningReason) ||
                            archivedByAdmin;

                        if (productPolicyWarning) {
                            productPolicyWarning.classList.toggle(
                                'hidden',
                                !hasWarningIssue
                            );
                        }

                        if (hasWarningIssue && productPolicyIssueTitle) {
                            productPolicyIssueTitle.textContent =
                                warningReason || 'Issue Warning';
                        }

                        if (hasWarningIssue && productPolicyIssueDate) {
                            productPolicyIssueDate.textContent =
                                formatCreatedProductDate(
                                    createdProduct.updatedAt ||
                                    createdProduct.createdAt
                                );
                        }

                        if (hasWarningIssue && productPolicyIssueReason) {
                            productPolicyIssueReason.textContent =
                                warningDetails ||
                                'This product has a policy warning that requires seller attention.';
                        }

                        if (productDetailBrand) {
                            productDetailBrand.value =
                                normalizeOptionalProductValue(
                                    createdProductSpecificationValue(
                                        createdProduct,
                                        'brand'
                                    )
                                );
                        }

                        if (productDetailMaterial) {
                            productDetailMaterial.value =
                                normalizeOptionalProductValue(
                                    createdProductSpecificationValue(
                                        createdProduct,
                                        'material'
                                    )
                                );
                        }

                        if (productDetailSizes) {
                            productDetailSizes.value =
                                (
                                    createdProduct.sizes ||
                                    []
                                )
                                .map(
                                    function (item) {
                                        return item.name;
                                    }
                                )
                                .join(
                                    ', '
                                );
                        }

                        if (productDetailColors) {
                            productDetailColors.value =
                                (
                                    createdProduct.colors ||
                                    []
                                )
                                .map(
                                    function (item) {
                                        return item.name;
                                    }
                                )
                                .join(
                                    ', '
                                );
                        }

                        if (productDetailQuantity) {
                            productDetailQuantity.value =
                                normalizeOptionalProductValue(
                                    createdProductSpecificationValue(
                                        createdProduct,
                                        'quantity_per_pack'
                                    )
                                );
                        }

                        if (productDetailCountry) {
                            productDetailCountry.value =
                                normalizeOptionalProductValue(
                                    createdProductSpecificationValue(
                                        createdProduct,
                                        'country_of_origin'
                                    )
                                );
                        }

                        renderCreatedProductPhotos(
                            createdProduct
                        );

                        renderCreatedBuyerOptions(
                            createdProduct
                        );

                        renderCreatedSpecifications(
                            createdProduct
                        );

                        updateDescriptionCount();

                        return;

                    }


                    /*
                     * EXISTING DEMO PRODUCT
                     * Keep the existing behavior, but update its summary
                     * from the clicked table row where possible.
                     */
                    const rowCategoryBadge =
                        row.querySelector(
                            '.category-badge'
                        );

                    const category =
                        rowCategoryBadge?.textContent?.trim() ||
                        '—';

                    const rowNumbers =
                        row.querySelectorAll(
                            '.product-number'
                        );

                    if (productDetailsName) {
                        productDetailsName.textContent =
                            row.dataset.name ||
                            'Product';
                    }

                    if (productDetailsPrice) {
                        productDetailsPrice.textContent =
                            rowNumbers[0]?.textContent?.trim() ||
                            '—';
                    }

                    if (productDetailsStock) {
                        productDetailsStock.textContent =
                            `${rowNumbers[1]?.textContent?.trim() || '0'} pieces`;
                    }

                    if (productDetailsStatus) {

                        const statusBadge =
                            row.querySelector(
                                '.status-badge'
                            );

                        const statusText =
                            statusBadge?.textContent?.trim() ||
                            '—';

                        productDetailsStatus.textContent =
                            statusText;

                        productDetailsStatus.className =
                            `status-badge mt-[6px] ${
                                row.dataset.status === 'low-stock'
                                    ? 'status-low-stock'
                                    : row.dataset.status === 'out-of-stock'
                                        ? 'status-out-stock'
                                        : row.dataset.status === 'pending'
                                            ? 'status-pending'
                                            : 'status-in-stock'
                            }`;

                    }

                    if (productDetailsCategory) {

                        productDetailsCategory.textContent =
                            category;

                        productDetailsCategory.className =
                            `inline-flex items-center mt-[7px] rounded-full px-[13px] py-[4px] text-[11px] font-medium category-badge ${categoryBadgeClass(category)}`;

                    }

                    productDetailsBuyerOptionsSection?.classList.add(
                        'hidden'
                    );

                    productDetailsSpecificationsSection?.classList.add(
                        'hidden'
                    );

                    if (productPolicyWarning) {

                        productPolicyWarning.classList.toggle(
                            'hidden',
                            !isPolicy
                        );

                    }

                    if (isPolicy) {

                        if (productPolicyIssueTitle) {

                            productPolicyIssueTitle.textContent =
                                row.dataset.issueTitle ||
                                'Policy issue';

                        }

                        if (productPolicyIssueDate) {

                            productPolicyIssueDate.textContent =
                                row.dataset.issueDate ||
                                'Review required';

                        }

                        if (productPolicyIssueReason) {

                            productPolicyIssueReason.textContent =
                                row.dataset.issueReason ||
                                'The product has a policy issue that needs review.';

                        }

                    }

                    updateDescriptionCount();

                }


                /* =================================================
                   OPEN PRODUCT DETAILS
                ================================================== */

                function openProductDetails(row) {

                    if (!productDetailsModal) {
                        return;
                    }

                    currentProductRow =
                        row ||
                        null;

                    updateProductDetailsModal(
                        currentProductRow
                    );

                    if (closeDetailsTimer) {
                        clearTimeout(closeDetailsTimer);
                        closeDetailsTimer = null;
                    }

                    fadeOpenModal(
                        productDetailsModal,
                        productDetailsPanel
                    );

                    document.body.classList.add(
                        'overflow-hidden'
                    );

                }


                /* =================================================
                   CLOSE PRODUCT DETAILS
                ================================================== */

                function closeProductDetails(
                    callback = null
                ) {

                    if (!productDetailsModal) {
                        return;
                    }

                    if (closeDetailsTimer) {
                        clearTimeout(closeDetailsTimer);
                    }

                    productDetailsModal.classList.remove(
                        'modal-open'
                    );

                    productDetailsModal.classList.add(
                        'modal-closing'
                    );

                    productDetailsModal.setAttribute(
                        'aria-hidden',
                        'true'
                    );

                    if (productDetailsPanel) {
                        productDetailsPanel.style.transform =
                            'none';
                    }

                    closeDetailsTimer =
                        window.setTimeout(
                            function () {

                                productDetailsModal.classList.remove(
                                    'modal-closing'
                                );

                                productDetailsModal.classList.add(
                                    'hidden'
                                );

                                document.body.classList.remove(
                                    'overflow-hidden'
                                );

                                currentProductRow =
                                    null;

                                if (typeof callback === 'function') {
                                    callback();
                                }

                            },
                            200
                        );

                }


                /* =================================================
                   PRODUCT ROW CLICK
                ================================================== */

                function bindProductRow(
                    row
                ) {

                    if (
                        !row ||
                        row.dataset.productRowBound ===
                            'true'
                    ) {
                        return;
                    }

                    row.dataset.productRowBound =
                        'true';

                    row.addEventListener(
                        'click',
                        function (event) {

                            if (
                                event.target.closest(
                                    'button, input, select, textarea, a'
                                )
                            ) {
                                return;
                            }

                            openProductDetails(
                                row
                            );

                        }
                    );

                    row.addEventListener(
                        'keydown',
                        function (event) {

                            if (
                                event.key === 'Enter' ||
                                event.key === ' '
                            ) {

                                event.preventDefault();

                                openProductDetails(
                                    row
                                );

                            }

                        }
                    );

                }


                productRows.forEach(
                    bindProductRow
                );


                /* =================================================
                   CLOSE PRODUCT DETAILS
                ================================================== */

                cancelProductDetails?.addEventListener(
                    'click',
                    function () {
                        closeProductDetails();
                    }
                );


                /* =================================================
                   CLICK OUTSIDE PRODUCT DETAILS
                ================================================== */

                productDetailsModal?.addEventListener(
                    'click',
                    function (event) {

                        if (
                            event.target ===
                            productDetailsModal
                        ) {

                            closeProductDetails();

                        }

                    }
                );


                /* =================================================
                   PRODUCT DETAILS ESC
                ================================================== */

                document.addEventListener(
                    'keydown',
                    function (event) {

                        if (event.key !== 'Escape') {
                            return;
                        }

                        if (
                            removeProductModal?.classList.contains(
                                'modal-open'
                            )
                        ) {

                            closeRemoveProductModal();
                            return;

                        }

                        if (
                            productDetailsModal?.classList.contains(
                                'modal-open'
                            )
                        ) {

                            closeProductDetails();

                        }

                    }
                );


                /* =================================================
                   DESCRIPTION COUNTER
                ================================================== */

                function updateDescriptionCount() {

                    if (
                        !productDescription ||
                        !descriptionCount
                    ) {

                        return;

                    }

                    descriptionCount.textContent =
                        `${productDescription.value.length}/300`;

                }


                productDescription?.addEventListener(
                    'input',
                    updateDescriptionCount
                );


                updateDescriptionCount();


                /* =================================================
                   SAVE CHANGES
                ================================================== */

                function persistArchiveState(productId, archived) {
                    if (!productId || !/^\d+$/.test(String(productId))) {
                        return Promise.resolve(true);
                    }

                    const endpoint =
                        `${inventoryConfig.inventoryProductsUrl}/${productId}/${archived ? 'archive' : 'unarchive'}`;

                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

                    return fetch(endpoint, {
                        method: 'PATCH',
                        credentials: 'same-origin',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'X-XSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    }).then(function (response) {
                        if (!response.ok) {
                            throw new Error('Unable to update the product archive state.');
                        }

                        return response.json();
                    });
                }

                function unarchiveCurrentProduct(product) {
                    if (!product || !currentProductRow) {
                        return;
                    }

                    const wasRemovedByAdmin = Boolean(
                        product.archivedByAdmin ||
                        product.archived_by_admin
                    );

                    if (wasRemovedByAdmin) {
                        window.alert('This product was removed by an administrator and cannot be restored by the seller.');
                        return;
                    }

                    if (!currentProductRow.dataset.archivePersisted) {
                        persistArchiveState(product.id, false)
                            .then(function () {
                                currentProductRow.dataset.archivePersisted = 'true';
                                unarchiveCurrentProduct(product);
                            })
                            .catch(function (error) {
                                window.alert(error.message);
                            });
                        return;
                    }

                    archivedInventoryProducts =
                        archivedInventoryProducts.filter(function (item) {
                            return String(item.id) !== String(product.id);
                        });

                    createdInventoryProducts = [
                        product,
                        ...createdInventoryProducts.filter(function (item) {
                            return String(item.id) !== String(product.id);
                        })
                    ];

                    saveArchivedProducts();
                    saveCreatedProducts();

                    const restoredRow = buildCreatedProductRow(product, true);

                    if (restoredRow) {
                        allTable.prepend(restoredRow);
                    }

                    currentProductRow.remove();
                    activeTab = 'all';

                    tabs.forEach(function (tab) {
                        tab.classList.toggle(
                            'active',
                            tab.dataset.tab === 'all'
                        );
                    });

                    updateTabView();
                }

                saveProductChanges?.addEventListener(
                    'click',
                    function () {

                        const originalText =
                            saveProductChanges.textContent;

                        const createdProduct =
                            getCreatedProductFromRow(
                                currentProductRow
                            );

                        const isArchived =
                            currentProductRow?.classList.contains(
                                'archived-row'
                            );

                        saveProductChanges.disabled =
                            true;

                        saveProductChanges.textContent =
                            isArchived
                                ? 'Unarchiving...'
                                : 'Saving...';

                        if (isArchived) {
                            const wasRemovedByAdmin = Boolean(
                                createdProduct && (createdProduct.archivedByAdmin || createdProduct.archived_by_admin)
                            );

                            if (wasRemovedByAdmin) {
                                saveProductChanges.disabled = true;
                                saveProductChanges.textContent = 'Removed by Admin';
                                window.alert('This product was removed by an administrator and cannot be restored by the seller.');
                                return;
                            }

                            unarchiveCurrentProduct(createdProduct);

                            window.setTimeout(function () {
                                saveProductChanges.disabled = false;
                                saveProductChanges.textContent = 'Save Changes';
                                closeProductDetails(function () {
                                    showInventoryFlash(
                                        'Product restored to All Products.'
                                    );
                                });
                            }, 450);

                            return;
                        }

                        /*
                         * Created products are truly editable in the current
                         * Inventory UI. Save the Product Specifications,
                         * Product Description, Colors, Sizes, and common
                         * Product Details back to local persistence.
                         */
                        if (createdProduct) {

                            saveCreatedProductSpecificationChanges(
                                createdProduct
                            );

                            /*
                             * Re-render the dynamic Product Specifications
                             * immediately from the values that were saved.
                             */
                            renderCreatedSpecifications(
                                createdProduct,
                                true
                            );

                        }

                        window.setTimeout(
                            function () {

                                saveProductChanges.disabled =
                                    false;

                                saveProductChanges.textContent =
                                    originalText;

                                closeProductDetails(
                                    function () {

                                        window.setTimeout(
                                            function () {

                                                if (
                                                    typeof showInventoryFlash ===
                                                    'function'
                                                ) {

                                                    showInventoryFlash(
                                                        createdProduct
                                                            ? 'Product details and specifications updated successfully.'
                                                            : 'Product changes saved successfully.'
                                                    );

                                                } else {

                                                    window.alert(
                                                        createdProduct
                                                            ? 'Product details and specifications updated successfully.'
                                                            : 'Product changes saved successfully.'
                                                    );

                                                }

                                            },
                                            60
                                        );

                                    }
                                );

                            },
                            450
                        );

                    }
                );


                /* =================================================
                   OPEN REMOVE PRODUCT REASON MODAL
                ================================================== */

                function openRemoveProductModal() {

                    if (
                        !removeProductModal ||
                        !removeProductModalPanel
                    ) {
                        return;
                    }

                    document.querySelectorAll(
                        'input[name="remove_reason"]'
                    ).forEach(
                        function (radio) {
                            radio.checked = false;
                        }
                    );

                    if (removeAdditionalDetails) {
                        removeAdditionalDetails.value = '';
                    }

                    const isArchived =
                        currentProductRow?.classList.contains('archived-row');

                    if (removeProductModalTitle) {
                        removeProductModalTitle.textContent = isArchived
                            ? 'Permanently Remove Product'
                            : 'Remove Product';
                    }

                    if (removeProductModalSubtitle) {
                        removeProductModalSubtitle.textContent = isArchived
                            ? 'Are you sure you want to permanently remove the products? This action cannot be undone.'
                            : 'You are about to remove your product.';
                    }

                    removeReasonBlock?.classList.toggle('hidden', isArchived);
                    removeDetailsBlock?.classList.toggle('hidden', isArchived);

                    if (confirmRemoveProduct) {
                        confirmRemoveProduct.textContent = isArchived
                            ? 'Yes, Permanently Remove'
                            : 'Remove';
                    }

                    updateRemoveDetailsCount();

                    fadeOpenModal(
                        removeProductModal,
                        removeProductModalPanel
                    );

                }


                /* =================================================
                   CLOSE REMOVE PRODUCT REASON MODAL
                ================================================== */

                function closeRemoveProductModal() {

                    if (
                        !removeProductModal ||
                        !removeProductModalPanel
                    ) {
                        return;
                    }

                    if (closeRemoveTimer) {
                        clearTimeout(closeRemoveTimer);
                    }

                    removeProductModal.classList.remove(
                        'modal-open'
                    );

                    removeProductModal.classList.add(
                        'modal-closing'
                    );

                    removeProductModal.setAttribute(
                        'aria-hidden',
                        'true'
                    );

                    removeProductModalPanel.style.transform =
                        'none';

                    closeRemoveTimer =
                        window.setTimeout(
                            function () {

                                removeProductModal.classList.remove(
                                    'modal-closing'
                                );

                                removeProductModal.classList.add(
                                    'hidden'
                                );

                            },
                            200
                        );

                }


                /* =================================================
                   REMOVE BUTTON -> REASON MODAL
                ================================================== */

                removeProductButton?.addEventListener(
                    'click',
                    function () {

                        if (!currentProductRow) {
                            return;
                        }

                        openRemoveProductModal();

                    }
                );


                /* =================================================
                   REMOVE DETAILS COUNTER
                ================================================== */

                function updateRemoveDetailsCount() {

                    if (
                        !removeAdditionalDetails ||
                        !removeDetailsCount
                    ) {
                        return;
                    }

                    removeDetailsCount.textContent =
                        `${removeAdditionalDetails.value.length}/300`;

                }


                removeAdditionalDetails?.addEventListener(
                    'input',
                    updateRemoveDetailsCount
                );

                updateRemoveDetailsCount();


                /* =================================================
                   CANCEL REMOVE REASON MODAL
                ================================================== */

                cancelRemoveProduct?.addEventListener(
                    'click',
                    closeRemoveProductModal
                );


                removeProductModal?.addEventListener(
                    'click',
                    function (event) {

                        if (
                            event.target ===
                            removeProductModal
                        ) {
                            closeRemoveProductModal();
                        }

                    }
                );


                /* =================================================
                   MOVE REMOVED PRODUCT TO ARCHIVED TAB
                ================================================== */


                /* =================================================
                   CATEGORY BADGE CLASS HELPER
                ================================================== */

                function categoryBadgeClass(category) {

                    const map = {
                        'pet-supplies': 'category-pet-supplies',
                        'Pet Supplies': 'category-pet-supplies',
                        'electronics-and-gadgets': 'category-electronics-and-gadgets',
                        'Electronics and Gadgets': 'category-electronics-and-gadgets',
                        'Electronics & Gadgets': 'category-electronics-and-gadgets',
                        'womens-apparel': 'category-womens-apparel',
                        "Women's Apparel": 'category-womens-apparel',
                        "Women’s Apparel": 'category-womens-apparel',
                        'mens-apparel': 'category-mens-apparel',
                        "Men's Apparel": 'category-mens-apparel',
                        "Men’s Apparel": 'category-mens-apparel',
                        'kids-and-baby': 'category-kids-and-baby',
                        'Kids and Baby': 'category-kids-and-baby',
                        'home-and-garden': 'category-home-and-garden',
                        'Home and Garden': 'category-home-and-garden',
                        'sports-and-outdoors': 'category-sports-and-outdoors',
                        'Sports and Outdoors': 'category-sports-and-outdoors',
                        'health-and-beauty': 'category-health-and-beauty',
                        'Health and Beauty': 'category-health-and-beauty',
                        'books-and-media': 'category-books-and-media',
                        'Books and Media': 'category-books-and-media',
                        'food-and-gourmet': 'category-food-and-gourmet',
                        'Food and Gourmet': 'category-food-and-gourmet',
                        'automotive-motorcycle': 'category-automotive-motorcycle',
                        'Automotive & Motorcycle': 'category-automotive-motorcycle',
                        'Automotive and Motorcycle': 'category-automotive-motorcycle',
                        'furniture-and-office-equipment': 'category-furniture-and-office-equipment',
                        'Furniture and Office Equipment': 'category-furniture-and-office-equipment',
                        'jewelry-and-watches': 'category-jewelry-and-watches',
                        'Jewelry and Watches': 'category-jewelry-and-watches',
                        'office-and-school-supplies': 'category-office-and-school-supplies',
                        'Office and School Supplies': 'category-office-and-school-supplies'
                    };

                    return map[category] || 'category-default';
                }


                function archiveCurrentProduct(reason, details) {

                    if (!currentProductRow) {
                        return;
                    }

                    const row = currentProductRow;

                    if (
                        row.dataset.createdProductId &&
                        /^\d+$/.test(String(row.dataset.createdProductId)) &&
                        !row.dataset.archivePersisted
                    ) {
                        persistArchiveState(row.dataset.createdProductId, true)
                            .then(function () {
                                row.dataset.archivePersisted = 'true';
                                archiveCurrentProduct(reason, details);
                            })
                            .catch(function (error) {
                                window.alert(error.message);
                            });
                        return;
                    }

                    const archivedProductId =
                        row.dataset.createdProductId ||
                        `row-${Date.now()}-${Math.random().toString(36).slice(2, 8)}`;

                    const sourceProduct =
                        createdInventoryProducts.find(function (product) {
                            return String(product.id) === String(archivedProductId);
                        }) || {};

                    const name =
                        sourceProduct.name ||
                        sourceProduct.title ||
                        row.dataset.name ||
                        'Product';

                    const category =
                        row.querySelector('.category-badge')?.textContent?.trim() ||
                        '—';

                    const price =
                        row.querySelector('.product-number')?.textContent?.trim() ||
                        '—';

                    const stock =
                        row.querySelectorAll('.product-number')[1]?.textContent?.trim() ||
                        '—';

                    const archivedTable =
                        document.getElementById(
                            'archivedItemsTable'
                        );

                    const archivedEmpty =
                        document.getElementById(
                            'archivedItemsEmpty'
                        );

                    if (!archivedTable) {
                        return;
                    }

                    const archivedRow =
                        document.createElement(
                            'article'
                        );

                    archivedRow.className =
                        'archived-row product-clickable grid grid-cols-[2.3fr_1.65fr_1fr_0.9fr_1fr_0.18fr] items-center min-h-[90px] px-[20px] border-b border-[#DDD9D7]';

                    archivedRow.dataset.createdProductId = archivedProductId;
                    archivedRow.tabIndex = 0;
                    archivedRow.setAttribute('role', 'button');

                    const archivedCoverPhoto = resolveProductCoverPhoto(sourceProduct);
                    const archivedCover = archivedCoverPhoto
                        ? `<img src="${escapeHtml(archivedCoverPhoto)}" alt="${escapeHtml(name)}" class="created-product-cover">`
                        : '<div class="product-bag"></div>';

                    archivedRow.innerHTML = `
                        <div class="product-main">
                            <div class="product-thumb">
                                ${archivedCover}
                            </div>
                            <div class="min-w-0">
                                <h3 class="product-name">${escapeHtml(name)}</h3>
                                <p class="product-sold">${escapeHtml(details || reasonLabel(reason))}</p>
                            </div>
                        </div>

                        <div>
                            <span class="category-badge ${categoryBadgeClass(category)}">${escapeHtml(category)}</span>
                        </div>

                        <div class="product-number">${escapeHtml(price)}</div>

                        <div class="product-number">${escapeHtml(stock)}</div>

                        <div>
                            <span class="status-badge status-pending">Archived</span>
                        </div>

                        <div></div>
                    `;

                    archivedTable.appendChild(
                        archivedRow
                    );

                    bindProductRow(archivedRow);

                    archivedInventoryProducts =
                        archivedInventoryProducts.filter(function (product) {
                            return String(product.id) !== String(archivedProductId);
                        });

                    archivedInventoryProducts.unshift({
                        ...sourceProduct,
                        id: archivedProductId,
                        name,
                        title: name,
                        category,
                        price,
                        stock,
                        reason: details || reasonLabel(reason),
                        archivedAt: new Date().toISOString()
                    });

                    saveArchivedProducts();

                    archivedTable.classList.remove(
                        'hidden'
                    );

                    archivedEmpty?.classList.add(
                        'hidden'
                    );

                    if (
                        row.dataset.createdProductId
                    ) {

                        removeCreatedProductFromStorage(
                            row.dataset.createdProductId
                        );

                    }

                    row.remove();

                    updateTabView();

                }


                function reasonLabel(reason) {

                    const labels = {
                        'no-longer-selling': 'No longer selling this product',
                        'updating-listing': 'Updating or replacing the product listing',
                        'pricing-changes': 'Pricing or cost changes',
                        'supplier-changes': 'Supplier or sourcing changes',
                        'low-demand': 'Product has low customer demand',
                        'other': 'Other',
                    };

                    return labels[reason] || 'Archived product';

                }

                function resolveProductCoverPhoto(product) {
                    if (!product) {
                        return '';
                    }

                    const photos = Array.isArray(product.photos)
                        ? product.photos
                        : [];

                    const firstPhoto = photos.find(function (photo) {
                        return Boolean(photo && String(photo).trim());
                    });

                    return firstPhoto || product.coverPhoto || product.image_url || '';
                }

                function renderArchivedProductRow(product) {
                    if (!archivedTable || !product) {
                        return;
                    }

                    const archiveReason = product.archiveReason || product.archive_reason || product.reason || '';
                    const archivedByAdmin = Boolean(product.archivedByAdmin || product.archived_by_admin);
                    const activeReason = archivedByAdmin
                        ? (archiveReason ? `Removed by admin because ${archiveReason}` : 'Removed by admin')
                        : (product.reason || 'Archived product');
                    const productName = product.name || product.title || 'Product';

                    const archivedRow = document.createElement('article');
                    archivedRow.className =
                        'archived-row product-clickable grid grid-cols-[2.3fr_1.65fr_1fr_0.9fr_1fr_0.18fr] items-center min-h-[90px] px-[20px] border-b border-[#DDD9D7]';

                    archivedRow.dataset.createdProductId = product.id || '';
                    archivedRow.tabIndex = 0;
                    archivedRow.setAttribute('role', 'button');

                    const archivedCoverPhoto = resolveProductCoverPhoto(product);
                    const archivedCover = archivedCoverPhoto
                        ? `<img src="${escapeHtml(archivedCoverPhoto)}" alt="${escapeHtml(productName)}" class="created-product-cover">`
                        : '<div class="product-bag"></div>';

                    const categoryLabel = categoryDisplayLabel(product.categoryLabel || product.category || '');
                    const priceText = calculateCreatedProductPrice(product) || '—';

                    archivedRow.innerHTML = `
                        <div class="product-main">
                            <div class="product-thumb">
                                ${archivedCover}
                            </div>
                            <div class="min-w-0">
                                <h3 class="product-name">${escapeHtml(productName)}</h3>
                                <p class="product-sold">${escapeHtml(activeReason)}</p>
                            </div>
                        </div>
                        <div>
                            <span class="category-badge ${categoryBadgeClass(categoryLabel)}">
                                ${escapeHtml(categoryLabel)}
                            </span>
                        </div>
                        <div class="product-number">${escapeHtml(priceText)}</div>
                        <div class="product-number">${escapeHtml(product.stock ?? 0)}</div>
                        <div>
                            <span class="status-badge status-pending">Archived</span>
                        </div>

                        <div></div>
                    `;

                    archivedTable.appendChild(archivedRow);

                    bindProductRow(archivedRow);
                }


                function escapeHtml(value) {

                    return String(value)
                        .replaceAll('&', '&amp;')
                        .replaceAll('<', '&lt;')
                        .replaceAll('>', '&gt;')
                        .replaceAll('"', '&quot;')
                        .replaceAll("'", '&#039;');

                }

                function categoryDisplayLabel(category) {
                    const value = String(category || '').trim();

                    if (!value) {
                        return '—';
                    }

                    const definition = Object.values(
                        productSpecificationLibrary || {}
                    ).find(function (item) {
                        return item.slug === value;
                    });

                    if (definition?.label) {
                        return definition.label;
                    }

                    const labels = {
                        'pet-supplies': 'Pet Supplies',
                        'electronics-and-gadgets': 'Electronics and Gadgets',
                        'womens-apparel': "Women's Apparel",
                        'mens-apparel': "Men's Apparel",
                        'kids-and-baby': 'Kids and Baby',
                        'home-and-garden': 'Home and Garden',
                        'sports-and-outdoors': 'Sports and Outdoors',
                        'health-and-beauty': 'Health and Beauty',
                        'books-and-media': 'Books and Media',
                        'food-and-gourmet': 'Food and Gourmet',
                        'automotive-motorcycle': 'Automotive & Motorcycle',
                        'furniture-and-office-equipment': 'Furniture and Office Equipment',
                        'jewelry-and-watches': 'Jewelry and Watches',
                        'office-and-school-supplies': 'Office and School Supplies'
                    };

                    return labels[value] || value;
                }


                /* =================================================
                   CREATED PRODUCT LIST
                   Front-end persistence for the current Inventory UI.
                ================================================== */

                const createdProductsStorageKey =
                    'shopease_seller_inventory_created_products_v1';

                const archivedProductsStorageKey =
                    'shopease_seller_inventory_archived_products_v1';


                function loadCreatedProducts() {

                    try {

                        const stored =
                            JSON.parse(
                                localStorage.getItem(
                                    createdProductsStorageKey
                                ) ||
                                '[]'
                            );

                        return Array.isArray(
                            stored
                        )
                            ? stored
                            : [];

                    } catch (error) {

                        console.warn(
                            'Unable to read created products:',
                            error
                        );

                        return [];

                    }

                }

                function loadArchivedProducts() {
                    try {
                        const stored = JSON.parse(
                            localStorage.getItem(archivedProductsStorageKey) ||
                            '[]'
                        );

                        return Array.isArray(stored) ? stored : [];
                    } catch (error) {
                        console.warn('Unable to read archived products:', error);
                        return [];
                    }
                }

                let archivedInventoryProducts = loadArchivedProducts();

                function saveArchivedProducts() {
                    try {
                        localStorage.setItem(
                            archivedProductsStorageKey,
                            JSON.stringify(archivedInventoryProducts)
                        );
                    } catch (error) {
                        console.warn('Unable to save archived products:', error);
                    }
                }


                function normalizePersistedProduct(product) {
                    const specifications = product.specifications || {};
                    const specificationDisplay = Object.entries(specifications)
                        .map(function ([key, value]) {
                            const cleanKey = String(key)
                                .replace(/^category_specifications\[/, '')
                                .replace(/\]$/, '');

                            return {
                                key: cleanKey,
                                label: createdProductLabelFromKey(cleanKey),
                                value: String(value ?? '')
                            };
                        })
                        .filter(function (item) {
                            return item.value !== '';
                        });

                    const storageBase = String(inventoryConfig.storageUrl || '').replace(/\/+$/, '');
                    const photos = Array.isArray(product.photos)
                        ? product.photos.map(function (photo) {
                            const value = String(photo || '').trim();

                            if (!value) {
                                return '';
                            }

                            if (value.startsWith('http') || value.startsWith('data:')) {
                                return value;
                            }

                            const relative = value.replace(/^\/+/, '').replace(/^storage\//, '');
                            return storageBase ? `${storageBase}/${relative}` : `/${relative}`;
                        }).filter(Boolean)
                        : [];

                    const archiveReason = product.archive_reason || product.archiveReason || product.reason || '';
                    const archivedByAdmin = Boolean(product.archived_by_admin || product.archivedByAdmin);
                    const displayReason = archivedByAdmin
                        ? (archiveReason ? `Removed by admin because ${archiveReason}` : 'Removed by admin')
                        : (product.reason || 'Archived product');
                    const warningReason = product.warning_reason || product.warningReason || (archivedByAdmin ? archiveReason : '');
                    const warningDetails = product.warning_details || product.warningDetails || (archivedByAdmin
                        ? (archiveReason ? `This product was removed by an administrator because ${archiveReason}.` : 'This product was removed by an administrator.')
                        : '');

                    const resolvedName = product.name || product.title || 'New Product';

                    return {
                        id: String(product.id),
                        name: resolvedName,
                        title: resolvedName,
                        category: product.category || '',
                        categoryLabel: categoryDisplayLabel(
                            product.categoryLabel || product.category
                        ),
                        pricingMode: product.pricingMode || product.pricing_mode || 'fixed',
                        pricingSource: product.pricingSource || product.pricing_source || '',
                        basePrice: product.basePrice ?? product.price ?? 0,
                        stock: product.stock ?? product.stock_quantity ?? 0,
                        variations: Array.isArray(product.variations) ? product.variations : [],
                        colors: Array.isArray(product.colors) ? product.colors : [],
                        sizes: Array.isArray(product.sizes) ? product.sizes : [],
                        specifications: specifications,
                        specificationDisplay: specificationDisplay,
                        description: product.description || '',
                        sku: product.sku || '',
                        photos: photos,
                        coverPhoto: photos[0] || '',
                        approvalStatus: product.status || 'pending',
                        createdAt: product.created_at || new Date().toISOString(),
                        updatedAt: product.updated_at || product.updatedAt || product.created_at || new Date().toISOString(),
                        warningReason: warningReason,
                        warningDetails: warningDetails,
                        archivedByAdmin: archivedByAdmin,
                        archiveReason: archiveReason,
                        reason: displayReason,
                    };
                }

                const serverInventoryProducts = inventoryConfig.inventoryProducts;
                const serverCreatedProducts = serverInventoryProducts.map(
                    normalizePersistedProduct
                );
                const serverArchivedProducts = inventoryConfig.archivedProducts
                    .map(normalizePersistedProduct);

                archivedInventoryProducts = [
                    ...serverArchivedProducts,
                    ...archivedInventoryProducts.filter(function (localProduct) {
                        return !serverArchivedProducts.some(function (serverProduct) {
                            return String(serverProduct.id) === String(localProduct.id);
                        });
                    })
                ];

                let createdInventoryProducts = [
                    ...serverCreatedProducts,
                    ...loadCreatedProducts().filter(function (localProduct) {
                        return !serverCreatedProducts.some(function (serverProduct) {
                            return serverProduct.id === String(localProduct.id);
                        }) && !archivedInventoryProducts.some(function (archivedProduct) {
                            return String(archivedProduct.id) === String(localProduct.id);
                        });
                    })
                ].filter(function (product) {
                    return !archivedInventoryProducts.some(function (archivedProduct) {
                        return String(archivedProduct.id) === String(product.id);
                    });
                });


                function saveCreatedProducts() {

                    try {

                        localStorage.setItem(
                            createdProductsStorageKey,
                            JSON.stringify(
                                createdInventoryProducts
                            )
                        );

                    } catch (error) {

                        console.warn(
                            'Unable to save created products locally:',
                            error
                        );

                    }

                }


                function updateInventoryTotalCount() {

                    if (!totalEntriesCount) {
                        return;
                    }

                    const baseTotal =
                        Number(
                            totalEntriesCount.dataset.baseTotal ||
                            378
                        );

                    totalEntriesCount.textContent =
                        baseTotal +
                        createdInventoryProducts.length;

                }


                function removeCreatedProductFromStorage(
                    productId
                ) {

                    if (!productId) {
                        return;
                    }

                    createdInventoryProducts =
                        createdInventoryProducts.filter(
                            function (product) {
                                return product.id !==
                                    productId;
                            }
                        );

                    saveCreatedProducts();
                    updateInventoryTotalCount();

                }


                function formatMoney(
                    value
                ) {

                    return new Intl.NumberFormat(
                        'en-PH',
                        {
                            style:
                                'currency',

                            currency:
                                'PHP',

                            minimumFractionDigits:
                                2,

                            maximumFractionDigits:
                                2
                        }
                    ).format(
                        Number(
                            value ||
                            0
                        )
                    );

                }


                function calculateCreatedProductPrice(
                    product
                ) {

                    if (
                        product.pricingMode !==
                        'varies'
                    ) {
                        return formatMoney(
                            product.basePrice
                        );
                    }

                    const groups = {
                        variations:
                            product.variations ||
                            [],

                        colors:
                            product.colors ||
                            [],

                        sizes:
                            product.sizes ||
                            []
                    };

                    const primary =
                        groups[
                            product.pricingSource
                        ] ||
                        [];

                    if (!primary.length) {
                        return 'Price varies';
                    }

                    const basePrices =
                        primary.map(
                            function (item) {
                                return Number(
                                    item.price ||
                                    0
                                );
                            }
                        );

                    let minimum =
                        Math.min(
                            ...basePrices
                        );

                    let maximum =
                        Math.max(
                            ...basePrices
                        );

                    Object.entries(
                        groups
                    ).forEach(
                        function ([
                            group,
                            items
                        ]) {

                            if (
                                group ===
                                    product.pricingSource ||
                                !items.length
                            ) {
                                return;
                            }

                            const additions =
                                items.map(
                                    function (item) {
                                        return Number(
                                            item.price ||
                                            0
                                        );
                                    }
                                );

                            minimum +=
                                Math.min(
                                    ...additions
                                );

                            maximum +=
                                Math.max(
                                    ...additions
                                );

                        }
                    );

                    if (
                        Math.abs(
                            maximum -
                            minimum
                        ) <
                        0.005
                    ) {
                        return formatMoney(
                            minimum
                        );
                    }

                    return `${formatMoney(minimum)} – ${formatMoney(maximum)}`;

                }


                function getCreatedProductStatus(
                    stock,
                    approvalStatus = 'pending'
                ) {
                    if (['pending', 'under_review', 'review'].includes(String(approvalStatus).toLowerCase())) {
                        return {
                            slug: 'pending',
                            label: 'Pending Review',
                            className: 'status-pending'
                        };
                    }

                    const quantity = Number(stock || 0);

                    if (quantity <= 0) {
                        return {
                            slug: 'out-of-stock',
                            label: 'Out of Stock',
                            className: 'status-out-stock'
                        };
                    }

                    if (quantity <= 5) {
                        return {
                            slug: 'low-stock',
                            label: 'Low Stock',
                            className: 'status-low-stock'
                        };
                    }

                    return {
                        slug: 'in-stock',
                        label: 'In Stock',
                        className: 'status-in-stock'
                    };

                }


                function buildCreatedProductRow(
                    product,
                    animate = false
                ) {

                    if (!allTable) {
                        return null;
                    }

                    const status =
                        getCreatedProductStatus(
                            product.stock,
                            product.approvalStatus
                        );

                    const archiveReason =
                        product.archiveReason ||
                        product.archive_reason ||
                        '';

                    const archivedByAdmin = Boolean(
                        product.archivedByAdmin ||
                        product.archived_by_admin
                    );

                    const warningReason =
                        product.warningReason ||
                        product.warning_reason ||
                        (archivedByAdmin ? archiveReason : '') ||
                        '';

                    const hasWarningIssue =
                        String(product.approvalStatus || '').toLowerCase() === 'warning' ||
                        Boolean(warningReason) ||
                        archivedByAdmin;

                    const categoryLabel =
                        categoryDisplayLabel(
                            product.categoryLabel ||
                        productSpecificationLibrary[
                            product.category
                        ]?.label ||
                        product.category
                        );

                    const row =
                        document.createElement(
                            'article'
                        );

                    row.className =
                        [
                            'inventory-row',
                            'product-clickable',
                            'grid',
                            'grid-cols-[2.3fr_1.65fr_1fr_0.9fr_1fr_0.18fr]',
                            'items-center',
                            'min-h-[90px]',
                            'px-[20px]',
                            'border-b',
                            'border-[#DDD9D7]',
                            'transition-all',
                            'duration-200',
                            'hover:bg-[#FFFBF9]',
                            animate
                                ? 'inventory-new-product-row'
                                : ''
                        ]
                        .filter(
                            Boolean
                        )
                        .join(
                            ' '
                        );

                    row.dataset.name =
                        product.title ||
                        'New Product';

                    row.dataset.category =
                        product.category ||
                        '';

                    row.dataset.status =
                        status.slug;

                    row.dataset.createdProductId =
                        product.id ||
                        '';

                    row.tabIndex =
                        0;

                    row.setAttribute(
                        'role',
                        'button'
                    );

                    const coverMarkup =
                        product.coverPhoto
                            ? `
                                <img
                                    src="${escapeHtml(product.coverPhoto)}"
                                    alt="${escapeHtml(product.title || 'Product')}"
                                    class="created-product-cover"
                                >
                            `
                            : `
                                <div class="product-bag"></div>
                            `;

                    row.innerHTML = `
                        <div class="product-main">

                            <div class="product-thumb">
                                ${coverMarkup}
                            </div>

                            <div class="min-w-0">

                                <h3 class="product-name">
                                    ${escapeHtml(product.title || 'New Product')}
                                </h3>

                                <p class="product-sold">
                                    0 sold
                                </p>

                            </div>

                        </div>

                        <div>
                            <span class="category-badge ${categoryBadgeClass(categoryLabel)}">
                                ${escapeHtml(categoryLabel)}
                            </span>
                        </div>

                        <div class="product-number">
                            ${escapeHtml(calculateCreatedProductPrice(product))}
                        </div>

                        <div class="product-number">
                            ${escapeHtml(product.stock ?? 0)}
                        </div>

                        <div>
                            <span class="status-badge ${status.className}">
                                ${escapeHtml(status.label)}
                            </span>
                        </div>

                        <div class="flex justify-center">
                            ${hasWarningIssue ? '<span class="stock-warning" aria-label="Warning">!</span>' : ''}
                        </div>
                    `;

                    bindProductRow(
                        row
                    );

                    return row;

                }


                function buildPolicyIssueRow(
                    product
                ) {
                    const archiveReason =
                        product.archiveReason ||
                        product.archive_reason ||
                        '';

                    const archivedByAdmin = Boolean(
                        product.archivedByAdmin ||
                        product.archived_by_admin
                    );

                    const warningReason =
                        product.warningReason ||
                        product.warning_reason ||
                        (archivedByAdmin ? (archiveReason || 'Removed by admin') : 'Issue Warning');

                    const warningDetails =
                        product.warningDetails ||
                        product.warning_details ||
                        (archivedByAdmin
                            ? (archiveReason
                                ? `This product was removed by an administrator because ${archiveReason}.`
                                : 'This product was removed by an administrator.')
                            : 'This product has a policy warning that requires seller attention.');

                    const issueTitle =
                        formatPolicyIssueTitle(
                            warningReason
                        );

                    const dateText =
                        formatCreatedProductDate(
                            product.updatedAt ||
                            product.createdAt
                        );

                    const row =
                        document.createElement(
                            'article'
                        );

                    row.className =
                        'policy-row policy-product-clickable policy-dynamic-row grid grid-cols-[2.3fr_1.65fr_1fr_0.9fr_1.1fr] items-center min-h-[90px] cursor-pointer px-[20px] border-b border-[#DDD9D7] transition-all duration-200 hover:bg-[#FFFBF9]';

                    row.dataset.policy = 'true';
                    row.dataset.name = product.title || 'New Product';
                    row.dataset.category = product.category || '';
                    row.dataset.createdProductId = product.id || '';
                    row.dataset.status = getCreatedProductStatus(product.stock, product.approvalStatus).slug;
                    row.dataset.issueTitle = warningReason;
                    row.dataset.issueDate = dateText;
                    row.dataset.issueReason = warningDetails;
                    row.tabIndex = 0;
                    row.setAttribute('role', 'button');

                    const coverMarkup = product.coverPhoto
                        ? `<img src="${escapeHtml(product.coverPhoto)}" alt="${escapeHtml(product.title || 'Product')}" class="created-product-cover">`
                        : '<div class="product-bag"></div>';

                    row.innerHTML = `
                        <div class="product-main">
                            <div class="product-thumb">
                                ${coverMarkup}
                            </div>
                            <div class="min-w-0">
                                <h3 class="product-name">${escapeHtml(product.title || 'New Product')}</h3>
                                <p class="product-sold">0 sold</p>
                            </div>
                        </div>
                        <div>
                            <span class="category-badge ${categoryBadgeClass(categoryDisplayLabel(product.categoryLabel || product.category || ''))}">
                                ${escapeHtml(categoryDisplayLabel(product.categoryLabel || product.category || ''))}
                            </span>
                        </div>
                        <div class="product-number">${escapeHtml(calculateCreatedProductPrice(product))}</div>
                        <div class="product-number">${escapeHtml(product.stock ?? 0)}</div>
                        <div class="policy-issue">
                            <span class="stock-warning">!</span>
                            <div>
                                <p class="issue-title">${escapeHtml(issueTitle)}</p>
                                <p class="issue-date">${escapeHtml(dateText)}</p>
                            </div>
                        </div>
                    `;

                    bindProductRow(row);
                    return row;
                }

                function renderWarningPolicyRows() {
                    const policyTable =
                        document.getElementById(
                            'policyIssuesTable'
                        );

                    if (!policyTable) {
                        return;
                    }

                    policyTable
                        .querySelectorAll(
                            '.policy-dynamic-row'
                        )
                        .forEach(
                            function (row) {
                                row.remove();
                            }
                        );

                    createdInventoryProducts
                        .filter(
                            function (product) {
                                const archiveReason = product.archiveReason || product.archive_reason || '';
                                const archivedByAdmin = Boolean(product.archivedByAdmin || product.archived_by_admin);
                                const warningStatus = String(product.approvalStatus || '').toLowerCase() === 'warning';
                                const hasWarningText = Boolean(
                                    product.warningReason ||
                                    product.warning_reason ||
                                    product.warningDetails ||
                                    product.warning_details ||
                                    archiveReason ||
                                    archivedByAdmin
                                );

                                return warningStatus || hasWarningText;
                            }
                        )
                        .forEach(
                            function (product) {
                                policyTable.appendChild(
                                    buildPolicyIssueRow(product)
                                );
                            }
                        );
                }

                function insertCreatedProductRow(
                    product,
                    animate = true
                ) {

                    const row =
                        buildCreatedProductRow(
                            product,
                            animate
                        );

                    if (!row) {
                        return;
                    }

                    allTable.prepend(
                        row
                    );

                    renderWarningPolicyRows();

                    activeTab =
                        'all';

                    tabs.forEach(
                        function (tab) {

                            tab.classList.toggle(
                                'active',
                                tab.dataset.tab ===
                                    'all'
                            );

                        }
                    );

                    if (searchInput) {
                        searchInput.value =
                            '';
                    }

                    if (categoryFilter) {
                        categoryFilter.value =
                            'all';
                    }

                    if (statusFilter) {
                        statusFilter.value =
                            'all';
                    }

                    updateTabView();
                    filterProducts();

                }


                function restoreCreatedProductRows() {

                    createdInventoryProducts
                        .slice()
                        .reverse()
                        .forEach(
                            function (product) {

                                const row =
                                    buildCreatedProductRow(
                                        product,
                                        false
                                    );

                                if (row) {
                                    allTable.prepend(
                                        row
                                    );
                                }

                            }
                        );

                    updateInventoryTotalCount();
                    filterProducts();

                }

                function restoreArchivedProductRows() {
                    if (!archivedTable) {
                        return;
                    }

                    archivedTable.innerHTML = '';

                    archivedInventoryProducts
                        .slice()
                        .reverse()
                        .forEach(renderArchivedProductRow);
                }


                function createCompactCoverDataUrl(
                    file
                ) {

                    return new Promise(
                        function (resolve) {

                            if (!file) {
                                resolve('');
                                return;
                            }

                            const reader =
                                new FileReader();

                            reader.onload =
                                function () {

                                    const image =
                                        new Image();

                                    image.onload =
                                        function () {

                                            const maxSize =
                                                320;

                                            const ratio =
                                                Math.min(
                                                    1,
                                                    maxSize /
                                                    Math.max(
                                                        image.width,
                                                        image.height
                                                    )
                                                );

                                            const canvas =
                                                document.createElement(
                                                    'canvas'
                                                );

                                            canvas.width =
                                                Math.max(
                                                    1,
                                                    Math.round(
                                                        image.width *
                                                        ratio
                                                    )
                                                );

                                            canvas.height =
                                                Math.max(
                                                    1,
                                                    Math.round(
                                                        image.height *
                                                        ratio
                                                    )
                                                );

                                            const context =
                                                canvas.getContext(
                                                    '2d'
                                                );

                                            if (!context) {
                                                resolve('');
                                                return;
                                            }

                                            context.drawImage(
                                                image,
                                                0,
                                                0,
                                                canvas.width,
                                                canvas.height
                                            );

                                            resolve(
                                                canvas.toDataURL(
                                                    'image/jpeg',
                                                    0.72
                                                )
                                            );

                                        };

                                    image.onerror =
                                        function () {
                                            resolve('');
                                        };

                                    image.src =
                                        reader.result;

                                };

                            reader.onerror =
                                function () {
                                    resolve('');
                                };

                            reader.readAsDataURL(
                                file
                            );

                        }
                    );

                }


                restoreCreatedProductRows();
                renderWarningPolicyRows();
                restoreArchivedProductRows();

                async function permanentlyDeleteCurrentProduct() {
                    const product = getCreatedProductFromRow(currentProductRow);
                    const productId = currentProductRow?.dataset.createdProductId;

                    if (!productId || !/^\d+$/.test(String(productId))) {
                        return;
                    }

                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

                    const response = await fetch(
                        `${inventoryConfig.inventoryProductsUrl}/${productId}`,
                        {
                            method: 'DELETE',
                            credentials: 'same-origin',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'X-XSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            }
                        }
                    );

                    if (!response.ok) {
                        throw new Error('Unable to permanently remove the product.');
                    }

                    archivedInventoryProducts = archivedInventoryProducts.filter(function (item) {
                        return String(item.id) !== String(productId);
                    });

                    createdInventoryProducts = createdInventoryProducts.filter(function (item) {
                        return String(item.id) !== String(productId);
                    });

                    saveArchivedProducts();
                    saveCreatedProducts();
                    currentProductRow.remove();
                    updateTabView();
                }


                /* =================================================
                   CONFIRM REMOVE
                ================================================== */

                confirmRemoveProduct?.addEventListener(
                    'click',
                    async function () {

                        const isArchived =
                            currentProductRow?.classList.contains('archived-row');

                        if (isArchived) {
                            confirmRemoveProduct.disabled = true;
                            confirmRemoveProduct.textContent = 'Removing...';

                            try {
                                await permanentlyDeleteCurrentProduct();
                                closeRemoveProductModal();
                                closeProductDetails(function () {
                                    showInventoryFlash('Product permanently removed.');
                                });
                            } catch (error) {
                                window.alert(error.message);
                                confirmRemoveProduct.disabled = false;
                                confirmRemoveProduct.textContent = 'Yes, Permanently Remove';
                            }

                            return;
                        }

                        const selectedReason =
                            document.querySelector(
                                'input[name="remove_reason"]:checked'
                            );

                        if (!selectedReason) {

                            alert(
                                'Please select a reason for removing the product.'
                            );

                            return;

                        }

                        const reason =
                            selectedReason.value;

                        const details =
                            removeAdditionalDetails?.value?.trim() ||
                            '';

                        confirmRemoveProduct.disabled =
                            true;

                        confirmRemoveProduct.textContent =
                            'Removing...';

                        window.setTimeout(
                            function () {

                                archiveCurrentProduct(
                                    reason,
                                    details
                                );

                                confirmRemoveProduct.disabled =
                                    false;

                                confirmRemoveProduct.textContent =
                                    'Remove';

                                closeRemoveProductModal();

                                closeProductDetails(
                                    function () {

                                        currentProductRow =
                                            null;

                                        window.setTimeout(
                                            function () {

                                                showInventoryFlash(
                                                    'Product archived successfully and moved to Archived Items.'
                                                );

                                            },
                                            60
                                        );

                                    }
                                );

                            },
                            450
                        );

                    }
                );

                /* =================================================
                   CREATE PRODUCT MODAL
                ================================================== */

                const addProductModal =
                    document.getElementById(
                        'addProductModal'
                    );

                const addProductModalPanel =
                    document.getElementById(
                        'addProductModalPanel'
                    );

                const openAddProductModal =
                    document.getElementById(
                        'openAddProductModal'
                    );

                const closeAddProductModal =
                    document.getElementById(
                        'closeAddProductModal'
                    );

                const cancelAddProduct =
                    document.getElementById(
                        'cancelAddProduct'
                    );

                const addProductForm =
                    document.getElementById(
                        'addProductForm'
                    );

                const productPhotosInput =
                    document.getElementById(
                        'productPhotosInput'
                    );

                const productPhotosDropZone =
                    document.getElementById(
                        'productPhotosDropZone'
                    );

                const productPhotoPreviewGrid =
                    document.getElementById(
                        'productPhotoPreviewGrid'
                    );

                const productPhotoCount =
                    document.getElementById(
                        'productPhotoCount'
                    );

                const productVariationEntry =
                    document.getElementById(
                        'productVariationEntry'
                    );

                const productVariationPhotoInput =
                    document.getElementById(
                        'productVariationPhotoInput'
                    );

                const variationPhotoPickerLabel =
                    document.getElementById(
                        'variationPhotoPickerLabel'
                    );

                const variationPhotoPickerText =
                    document.getElementById(
                        'variationPhotoPickerText'
                    );

                const addProductVariationButton =
                    document.getElementById(
                        'addProductVariationButton'
                    );

                const productVariationsContainer =
                    document.getElementById(
                        'productVariationsContainer'
                    );

                const productColorEntry =
                    document.getElementById(
                        'productColorEntry'
                    );

                const addProductColorButton =
                    document.getElementById(
                        'addProductColorButton'
                    );

                const productColorsContainer =
                    document.getElementById(
                        'productColorsContainer'
                    );

                const productSizeEntry =
                    document.getElementById(
                        'productSizeEntry'
                    );

                const productColorPhotoInput =
                    document.getElementById('productColorPhotoInput');

                const productSizePhotoInput =
                    document.getElementById('productSizePhotoInput');

                const colorPhotoPickerText =
                    document.getElementById('colorPhotoPickerText');

                const sizePhotoPickerText =
                    document.getElementById('sizePhotoPickerText');

                const addProductSizeButton =
                    document.getElementById(
                        'addProductSizeButton'
                    );

                const productSizesContainer =
                    document.getElementById(
                        'productSizesContainer'
                    );

                const productSpecificationsSection =
                    document.getElementById(
                        'productSpecificationsSection'
                    );

                const categorySpecificationsTitle =
                    document.getElementById(
                        'categorySpecificationsTitle'
                    );

                const categorySpecificationsFields =
                    document.getElementById(
                        'categorySpecificationsFields'
                    );

                const createProductStock =
                    document.getElementById(
                        'createProductStock'
                    );

                const createProductPrice =
                    document.getElementById(
                        'createProductPrice'
                    );

                const productPricingMode =
                    document.getElementById(
                        'productPricingMode'
                    );

                const fixedPricePanel =
                    document.getElementById(
                        'fixedPricePanel'
                    );

                const variablePricePanel =
                    document.getElementById(
                        'variablePricePanel'
                    );

                const variablePricingSetup =
                    document.getElementById(
                        'variablePricingSetup'
                    );

                const productPricingSource =
                    document.getElementById(
                        'productPricingSource'
                    );

                const pricingModeButtons =
                    Array.from(
                        document.querySelectorAll(
                            '.create-pricing-mode-button[data-pricing-mode]'
                        )
                    );

                const pricingSourceButtons =
                    Array.from(
                        document.querySelectorAll(
                            '.create-pricing-source-button[data-pricing-source]'
                        )
                    );

                const variablePricingRule =
                    document.getElementById(
                        'variablePricingRule'
                    );

                const createProductDescription =
                    document.getElementById(
                        'createProductDescription'
                    );

                const createProductDescriptionCount =
                    document.getElementById(
                        'createProductDescriptionCount'
                    );


                let selectedProductPhotos = [];
                let selectedVariationPhoto = null;
                let selectedVariationPhotoUrl = null;
                let selectedColorPhoto = null;
                let selectedColorPhotoUrl = null;
                let selectedSizePhoto = null;
                let selectedSizePhotoUrl = null;
                let productVariations = [];
                let productColors = [];
                let productSizes = [];
                let pricingMode = 'fixed';
                let pricingSource = '';


                /* =================================================
                   PRODUCT PHOTOS
                ================================================== */

                function syncProductPhotoInput() {

                    if (
                        !productPhotosInput ||
                        typeof DataTransfer === 'undefined'
                    ) {
                        return;
                    }

                    const transfer =
                        new DataTransfer();

                    selectedProductPhotos.forEach(
                        function (entry) {
                            transfer.items.add(
                                entry.file
                            );
                        }
                    );

                    productPhotosInput.files =
                        transfer.files;

                }


                function revokeProductPhotoUrls() {

                    selectedProductPhotos.forEach(
                        function (entry) {
                            if (entry.url) {
                                URL.revokeObjectURL(
                                    entry.url
                                );
                            }
                        }
                    );

                }


                function renderProductPhotos() {

                    if (
                        !productPhotoPreviewGrid ||
                        !productPhotoCount
                    ) {
                        return;
                    }

                    productPhotoCount.textContent =
                        `${selectedProductPhotos.length} ${selectedProductPhotos.length === 1 ? 'photo' : 'photos'}`;

                    productPhotoPreviewGrid.innerHTML =
                        '';

                    productPhotoPreviewGrid.classList.toggle(
                        'hidden',
                        selectedProductPhotos.length === 0
                    );

                    selectedProductPhotos.forEach(
                        function (entry, index) {

                            const item =
                                document.createElement(
                                    'div'
                                );

                            item.className =
                                'create-product-photo';

                            const image =
                                document.createElement(
                                    'img'
                                );

                            image.src =
                                entry.url;

                            image.alt =
                                `Product photo ${index + 1}`;

                            const remove =
                                document.createElement(
                                    'button'
                                );

                            remove.type =
                                'button';

                            remove.className =
                                'create-product-photo-remove';

                            remove.setAttribute(
                                'aria-label',
                                `Remove product photo ${index + 1}`
                            );

                            remove.textContent =
                                '×';

                            remove.addEventListener(
                                'click',
                                function () {

                                    URL.revokeObjectURL(
                                        entry.url
                                    );

                                    selectedProductPhotos.splice(
                                        index,
                                        1
                                    );

                                    syncProductPhotoInput();
                                    renderProductPhotos();

                                }
                            );

                            item.appendChild(
                                image
                            );

                            item.appendChild(
                                remove
                            );

                            if (index === 0) {

                                const cover =
                                    document.createElement(
                                        'span'
                                    );

                                cover.className =
                                    'create-product-photo-cover';

                                cover.textContent =
                                    'Cover';

                                item.appendChild(
                                    cover
                                );

                            }

                            productPhotoPreviewGrid.appendChild(
                                item
                            );

                        }
                    );

                }


                function addProductPhotoFiles(fileList) {

                    const incoming =
                        Array.from(
                            fileList || []
                        );

                    const valid =
                        incoming.filter(
                            function (file) {
                                return [
                                    'image/jpeg',
                                    'image/png',
                                    'image/webp'
                                ].includes(
                                    file.type
                                );
                            }
                        );

                    valid.forEach(
                        function (file) {

                            selectedProductPhotos.push({
                                file,
                                url:
                                    URL.createObjectURL(
                                        file
                                    )
                            });

                        }
                    );

                    syncProductPhotoInput();
                    renderProductPhotos();

                }


                productPhotosInput?.addEventListener(
                    'change',
                    function () {
                        addProductPhotoFiles(
                            productPhotosInput.files
                        );
                    }
                );


                productPhotosDropZone?.addEventListener(
                    'dragover',
                    function (event) {

                        event.preventDefault();

                        productPhotosDropZone.classList.add(
                            'is-dragging'
                        );

                    }
                );


                productPhotosDropZone?.addEventListener(
                    'dragleave',
                    function () {
                        productPhotosDropZone.classList.remove(
                            'is-dragging'
                        );
                    }
                );


                productPhotosDropZone?.addEventListener(
                    'drop',
                    function (event) {

                        event.preventDefault();

                        productPhotosDropZone.classList.remove(
                            'is-dragging'
                        );

                        addProductPhotoFiles(
                            event.dataTransfer?.files
                        );

                    }
                );


                /* =================================================
                   PRODUCT PRICING
                ================================================== */

                function getPricingGroupItems(group) {
                    if (group === 'variations') return productVariations;
                    if (group === 'colors') return productColors;
                    if (group === 'sizes') return productSizes;
                    return [];
                }

                function getPricingGroupLabel(group) {
                    if (group === 'variations') return 'Variations';
                    if (group === 'colors') return 'Colors';
                    if (group === 'sizes') return 'Sizes';
                    return '';
                }

                function findFirstAvailablePricingSource() {
                    return ['variations', 'colors', 'sizes'].find(
                        function (group) {
                            return getPricingGroupItems(group).length > 0;
                        }
                    ) || '';
                }

                function setPricingSource(source, rerender = true) {
                    pricingSource = source || '';

                    if (productPricingSource) {
                        productPricingSource.value = pricingSource;
                    }

                    pricingSourceButtons.forEach(
                        function (button) {
                            button.classList.toggle(
                                'is-selected',
                                button.dataset.pricingSource === pricingSource
                            );
                        }
                    );

                    if (variablePricingRule) {
                        variablePricingRule.textContent =
                            pricingSource
                                ? `${getPricingGroupLabel(pricingSource)} use actual item prices. Other buyer options add extra charges (+₱).`
                                : 'Select which buyer option carries the actual item price. Other groups will use additional price (+₱).';
                    }

                    if (rerender) {
                        renderAllBuyerOptions();
                    }
                }

                function ensurePricingSource(preferredGroup = '') {
                    if (pricingMode !== 'varies') return;

                    if (
                        pricingSource &&
                        getPricingGroupItems(pricingSource).length > 0
                    ) {
                        return;
                    }

                    if (
                        preferredGroup &&
                        getPricingGroupItems(preferredGroup).length > 0
                    ) {
                        setPricingSource(preferredGroup, false);
                        return;
                    }

                    setPricingSource(
                        findFirstAvailablePricingSource(),
                        false
                    );
                }

                function getOptionPriceMeta(group) {
                    const isPrimary =
                        pricingMode !== 'varies' ||
                        pricingSource === group;

                    return {
                        isPrimary,
                        label:
                            pricingMode === 'fixed'
                                ? 'Item Price'
                                : isPrimary
                                ? 'Item Price'
                                : 'Additional',
                        prefix:
                            isPrimary
                                ? '₱'
                                : '+₱',
                        placeholder:
                            pricingMode === 'fixed'
                                ? 'Base price'
                                : isPrimary
                                ? 'Price'
                                : '0.00'
                    };
                }

                function createOptionPriceControl(group, item) {
                    const meta =
                        getOptionPriceMeta(group);

                    if (!meta) {
                        return null;
                    }

                    const wrap =
                        document.createElement('div');

                    wrap.className =
                        'create-option-price-wrap';

                    const label =
                        document.createElement('small');

                    label.className =
                        'create-option-price-label';

                    label.textContent =
                        meta.label;

                    const box =
                        document.createElement('div');

                    box.className =
                        'create-option-price-box';

                    const prefix =
                        document.createElement('span');

                    prefix.textContent =
                        meta.prefix;

                    const input =
                        document.createElement('input');

                    input.type =
                        'number';

                    input.min =
                        '0';

                    input.step =
                        '0.01';

                    input.placeholder =
                        meta.placeholder;

                    input.className =
                        'create-option-price-input';

                    if (pricingMode === 'fixed') {
                        input.disabled = true;
                    }

                    if (meta.isPrimary) {
                        input.classList.add(
                            'is-primary-price'
                        );
                        input.required = true;
                    }

                    input.value =
                        pricingMode === 'fixed'
                            ? createProductPrice?.value || ''
                            : item.price ?? '';

                    input.addEventListener(
                        'input',
                        function () {
                            if (pricingMode !== 'fixed') {
                                item.price = input.value;
                            }
                        }
                    );

                    box.appendChild(prefix);
                    box.appendChild(input);

                    wrap.appendChild(label);
                    wrap.appendChild(box);

                    return wrap;
                }

                function createOptionStockControl(item) {
                    const wrap = document.createElement('div');
                    wrap.className = 'create-option-price-wrap';

                    const label = document.createElement('small');
                    label.className = 'create-option-price-label';
                    label.textContent = 'Stock';

                    const box = document.createElement('div');
                    box.className = 'create-option-price-box';

                    const input = document.createElement('input');
                    input.type = 'number';
                    input.min = '0';
                    input.step = '1';
                    input.required = true;
                    input.placeholder = '0';
                    input.className = 'create-option-price-input';
                    input.value = item.stock ?? '';

                    input.addEventListener('input', function () {
                        item.stock = input.value;
                    });

                    box.appendChild(input);
                    wrap.appendChild(label);
                    wrap.appendChild(box);

                    return wrap;
                }

                function renderAllBuyerOptions() {
                    renderVariations();

                    renderOptionChips(
                        productColorsContainer,
                        productColors,
                        'colors'
                    );

                    renderOptionChips(
                        productSizesContainer,
                        productSizes,
                        'sizes'
                    );
                }

                function applyPricingMode(mode) {
                    pricingMode =
                        mode === 'varies'
                            ? 'varies'
                            : 'fixed';

                    if (productPricingMode) {
                        productPricingMode.value =
                            pricingMode;
                    }

                    pricingModeButtons.forEach(
                        function (button) {
                            button.classList.toggle(
                                'is-selected',
                                button.dataset.pricingMode === pricingMode
                            );
                        }
                    );

                    const isVariable =
                        pricingMode === 'varies';

                    fixedPricePanel?.classList.toggle(
                        'hidden',
                        isVariable
                    );

                    variablePricePanel?.classList.toggle(
                        'hidden',
                        !isVariable
                    );

                    variablePricingSetup?.classList.toggle(
                        'hidden',
                        !isVariable
                    );

                    if (createProductPrice) {
                        createProductPrice.disabled =
                            isVariable;

                        createProductPrice.required =
                            !isVariable;

                        if (isVariable) {
                            createProductPrice.value =
                                '';
                        }
                    }

                    if (isVariable) {
                        ensurePricingSource();
                    } else {
                        setPricingSource('', false);
                    }

                    renderAllBuyerOptions();
                }

                pricingModeButtons.forEach(
                    function (button) {
                        button.addEventListener(
                            'click',
                            function () {
                                applyPricingMode(
                                    button.dataset.pricingMode
                                );
                            }
                        );
                    }
                );

                pricingSourceButtons.forEach(
                    function (button) {
                        button.addEventListener(
                            'click',
                            function () {
                                if (pricingMode !== 'varies') {
                                    return;
                                }

                                setPricingSource(
                                    button.dataset.pricingSource
                                );
                            }
                        );
                    }
                );

                createProductPrice?.addEventListener(
                    'input',
                    function () {
                        if (pricingMode === 'fixed') {
                            renderAllBuyerOptions();
                        }
                    }
                );


                /* =================================================
                   VARIATIONS — CHIP STYLE + OPTIONAL PHOTO
                ================================================== */

                function clearPendingVariationPhoto() {

                    if (selectedVariationPhotoUrl) {
                        URL.revokeObjectURL(
                            selectedVariationPhotoUrl
                        );
                    }

                    selectedVariationPhoto =
                        null;

                    selectedVariationPhotoUrl =
                        null;

                    if (productVariationPhotoInput) {
                        productVariationPhotoInput.value =
                            '';
                    }

                    if (variationPhotoPickerText) {
                        variationPhotoPickerText.textContent =
                            'Photo';
                    }

                    variationPhotoPickerLabel?.classList.remove(
                        'has-photo'
                    );

                }


                productVariationPhotoInput?.addEventListener(
                    'change',
                    function () {

                        clearPendingVariationPhoto();

                        const file =
                            productVariationPhotoInput.files?.[0];

                        if (!file) {
                            return;
                        }

                        if (
                            ![
                                'image/jpeg',
                                'image/png',
                                'image/webp'
                            ].includes(
                                file.type
                            )
                        ) {
                            productVariationPhotoInput.value =
                                '';

                            return;
                        }

                        selectedVariationPhoto =
                            file;

                        selectedVariationPhotoUrl =
                            URL.createObjectURL(
                                file
                            );

                        if (variationPhotoPickerText) {
                            variationPhotoPickerText.textContent =
                                'Added';
                        }

                        variationPhotoPickerLabel?.classList.add(
                            'has-photo'
                        );

                    }
                );

                function bindOptionPhotoPicker(input, setPhoto, setUrl, textElement) {
                    input?.addEventListener('change', function () {
                        const file = input.files?.[0];

                        if (!file || !['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
                            input.value = '';
                            return;
                        }

                        setPhoto(file);
                        setUrl(URL.createObjectURL(file));
                        if (textElement) textElement.textContent = 'Added';
                    });
                }

                bindOptionPhotoPicker(
                    productColorPhotoInput,
                    function (file) { selectedColorPhoto = file; },
                    function (url) { selectedColorPhotoUrl = url; },
                    colorPhotoPickerText
                );

                bindOptionPhotoPicker(
                    productSizePhotoInput,
                    function (file) { selectedSizePhoto = file; },
                    function (url) { selectedSizePhotoUrl = url; },
                    sizePhotoPickerText
                );


                function revokeVariationUrls() {

                    productVariations.forEach(
                        function (variation) {
                            if (variation.photoUrl) {
                                URL.revokeObjectURL(
                                    variation.photoUrl
                                );
                            }
                        }
                    );

                    if (selectedVariationPhotoUrl) {
                        URL.revokeObjectURL(
                            selectedVariationPhotoUrl
                        );
                    }

                }


                function renderVariations() {
                    if (!productVariationsContainer) {
                        return;
                    }

                    productVariationsContainer.innerHTML = '';

                    productVariations.forEach(
                        function (variation, index) {
                            const item =
                                document.createElement('div');

                            const identity =
                                document.createElement('div');

                            identity.className =
                                'create-variation-identity';

                            const hasPhoto =
                                Boolean(variation.photoUrl);

                            const hasOptionPrice =
                                pricingMode === 'varies';

                            item.className =
                                [
                                    'create-variation-chip',
                                    hasPhoto ? '' : 'no-photo',
                                    hasOptionPrice ? 'has-option-price' : ''
                                ]
                                .filter(Boolean)
                                .join(' ');

                            if (variation.photoUrl) {
                                const thumb =
                                    document.createElement('div');

                                thumb.className =
                                    'create-variation-thumb';

                                const image =
                                    document.createElement('img');

                                image.src =
                                    variation.photoUrl;

                                image.alt =
                                    variation.name;

                                thumb.appendChild(image);
                                identity.appendChild(thumb);
                            }

                            const label =
                                document.createElement('span');

                            label.className =
                                'create-variation-chip-name';

                            label.textContent =
                                variation.name;

                            identity.appendChild(label);
                            item.appendChild(identity);

                            const hidden =
                                document.createElement('input');

                            hidden.type =
                                'hidden';

                            hidden.name =
                                'variations[]';

                            hidden.value =
                                variation.name;

                            item.appendChild(hidden);

                            const priceControl =
                                createOptionPriceControl(
                                    'variations',
                                    variation
                                );

                            if (priceControl) {
                                item.appendChild(
                                    priceControl
                                );
                            }

                            item.appendChild(
                                createOptionStockControl(variation)
                            );

                            const remove =
                                document.createElement('button');

                            remove.type =
                                'button';

                            remove.className =
                                'create-variation-chip-remove';

                            remove.textContent =
                                '×';

                            remove.setAttribute(
                                'aria-label',
                                `Remove ${variation.name}`
                            );

                            remove.addEventListener(
                                'click',
                                function () {
                                    if (
                                        productVariations[index]
                                            ?.photoUrl
                                    ) {
                                        URL.revokeObjectURL(
                                            productVariations[index]
                                                .photoUrl
                                        );
                                    }

                                    productVariations.splice(
                                        index,
                                        1
                                    );

                                    ensurePricingSource();
                                    renderAllBuyerOptions();
                                }
                            );

                            item.appendChild(remove);

                            productVariationsContainer.appendChild(
                                item
                            );
                        }
                    );
                }


                function addVariationValue() {

                    if (!productVariationEntry) {
                        return;
                    }

                    const values = productVariationEntry.value
                        .split(',')
                        .map(function (value) {
                            return value.trim();
                        })
                        .filter(Boolean);

                    if (!values.length) {
                        productVariationEntry.focus();
                        return;
                    }

                    values.forEach(function (value, valueIndex) {
                        const alreadyExists = productVariations.some(
                            function (variation) {
                                return variation.name.toLowerCase() === value.toLowerCase();
                            }
                        );

                        if (alreadyExists) {
                            return;
                        }

                        productVariations.push({
                            name: value,
                            photo: valueIndex === 0 ? selectedVariationPhoto : null,
                            photoUrl: valueIndex === 0 ? selectedVariationPhotoUrl : null,
                            price: '',
                            stock: ''
                        });
                    });

                    selectedVariationPhoto =
                        null;

                    selectedVariationPhotoUrl =
                        null;

                    productVariationEntry.value =
                        '';

                    if (productVariationPhotoInput) {
                        productVariationPhotoInput.value =
                            '';
                    }

                    if (variationPhotoPickerText) {
                        variationPhotoPickerText.textContent =
                            'Photo';
                    }

                    variationPhotoPickerLabel?.classList.remove(
                        'has-photo'
                    );

                    ensurePricingSource(
                        'variations'
                    );

                    renderAllBuyerOptions();

                    productVariationEntry.focus();

                }


                addProductVariationButton?.addEventListener(
                    'click',
                    addVariationValue
                );


                productVariationEntry?.addEventListener(
                    'keydown',
                    function (event) {

                        if (event.key === 'Enter') {

                            event.preventDefault();

                            addVariationValue();

                        }

                    }
                );


                /* =================================================
                   COLOR / SIZE CHIPS
                ================================================== */

                function renderOptionChips(
                    container,
                    values,
                    group
                ) {
                    if (!container) {
                        return;
                    }

                    container.innerHTML = '';

                    container.classList.toggle(
                        'variable-pricing-list',
                        pricingMode === 'varies'
                    );

                    values.forEach(
                        function (item, index) {
                            const row =
                                document.createElement('div');

                            const identity = document.createElement('div');
                            identity.className = 'create-variation-identity';

                            row.className =
                                pricingMode === 'varies'
                                    ? 'create-priced-option-row'
                                    : 'create-priced-option-row no-option-price';

                            const label =
                                document.createElement('span');

                            label.className =
                                'create-option-value-name';

                            label.textContent =
                                item.name;

                            if (item.photoUrl) {
                                const thumb = document.createElement('div');
                                thumb.className = 'create-variation-thumb';
                                const image = document.createElement('img');
                                image.src = item.photoUrl;
                                image.alt = item.name;
                                thumb.appendChild(image);
                                identity.appendChild(thumb);
                            }

                            identity.appendChild(label);

                            const hidden =
                                document.createElement('input');

                            hidden.type =
                                'hidden';

                            hidden.name =
                                `${group}[]`;

                            hidden.value =
                                item.name;

                            row.appendChild(identity);
                            row.appendChild(hidden);

                            const priceControl =
                                createOptionPriceControl(
                                    group,
                                    item
                                );

                            if (priceControl) {
                                row.appendChild(
                                    priceControl
                                );
                            }

                            row.appendChild(
                                createOptionStockControl(item)
                            );

                            const remove =
                                document.createElement('button');

                            remove.type =
                                'button';

                            remove.className =
                                'create-option-remove';

                            remove.textContent =
                                '×';

                            remove.setAttribute(
                                'aria-label',
                                `Remove ${item.name}`
                            );

                            remove.addEventListener(
                                'click',
                                function () {
                                    values.splice(
                                        index,
                                        1
                                    );

                                    ensurePricingSource();
                                    renderAllBuyerOptions();
                                }
                            );

                            row.appendChild(remove);
                            container.appendChild(row);
                        }
                    );
                }


                function addChipValue(
                    entry,
                    values,
                    container,
                    group,
                    pendingPhoto,
                    pendingPhotoUrl
                ) {
                    if (!entry) {
                        return;
                    }

                    const names = entry.value
                        .split(',')
                        .map(function (value) {
                            return value.trim();
                        })
                        .filter(Boolean);

                    if (!names.length) {
                        return;
                    }

                    names.forEach(function (name) {
                        const alreadyExists = values.some(function (existing) {
                            return existing.name.toLowerCase() === name.toLowerCase();
                        });

                        if (!alreadyExists) {
                            values.push({
                                name,
                                photo: pendingPhoto,
                                photoUrl: pendingPhotoUrl,
                                price: '',
                                stock: ''
                            });
                        }
                    });

                    entry.value = '';

                    ensurePricingSource(group);
                    renderAllBuyerOptions();
                    entry.focus();
                }


                addProductColorButton?.addEventListener(
                    'click',
                    function () {

                        addChipValue(
                            productColorEntry,
                            productColors,
                            productColorsContainer,
                            'colors',
                            selectedColorPhoto,
                            selectedColorPhotoUrl
                        );

                        selectedColorPhoto = null;
                        selectedColorPhotoUrl = null;
                        if (productColorPhotoInput) productColorPhotoInput.value = '';
                        if (colorPhotoPickerText) colorPhotoPickerText.textContent = 'Photo';

                    }
                );


                addProductSizeButton?.addEventListener(
                    'click',
                    function () {

                        addChipValue(
                            productSizeEntry,
                            productSizes,
                            productSizesContainer,
                            'sizes',
                            selectedSizePhoto,
                            selectedSizePhotoUrl
                        );

                        selectedSizePhoto = null;
                        selectedSizePhotoUrl = null;
                        if (productSizePhotoInput) productSizePhotoInput.value = '';
                        if (sizePhotoPickerText) sizePhotoPickerText.textContent = 'Photo';

                    }
                );


                productColorEntry?.addEventListener(
                    'keydown',
                    function (event) {

                        if (event.key === 'Enter') {

                            event.preventDefault();

                            addProductColorButton?.click();

                        }

                    }
                );


                productSizeEntry?.addEventListener(
                    'keydown',
                    function (event) {

                        if (event.key === 'Enter') {

                            event.preventDefault();

                            addProductSizeButton?.click();

                        }

                    }
                );


                /* =================================================
                   CATEGORY-AWARE PRODUCT SPECIFICATIONS
                ================================================== */

                function createSpecificationWrapper(
                    labelText,
                    key,
                    fullWidth = false
                ) {

                    const wrapper =
                        document.createElement(
                            'div'
                        );

                    wrapper.className =
                        fullWidth
                            ? 'create-field category-spec-full'
                            : 'create-field';

                    const label =
                        document.createElement(
                            'label'
                        );

                    label.htmlFor =
                        `categorySpec_${key}`;

                    label.textContent =
                        labelText;

                    wrapper.appendChild(
                        label
                    );

                    return {
                        wrapper,
                        label
                    };

                }


                function syncCreateCategoryPills() {

                    const selectedCategory =
                        addProductCategory?.value ||
                        '';

                    categoryChoicePills.forEach(
                        function (pill) {

                            pill.classList.toggle(
                                'is-selected',
                                pill.dataset.categorySlug ===
                                    selectedCategory
                            );

                            pill.setAttribute(
                                'aria-pressed',
                                pill.dataset.categorySlug ===
                                    selectedCategory
                                    ? 'true'
                                    : 'false'
                            );

                        }
                    );

                }


                categoryChoicePills.forEach(
                    function (pill) {

                        pill.setAttribute(
                            'aria-pressed',
                            'false'
                        );

                        pill.addEventListener(
                            'click',
                            function () {

                                if (!addProductCategory) {
                                    return;
                                }

                                addProductCategory.value =
                                    pill.dataset.categorySlug ||
                                    '';

                                syncCreateCategoryPills();

                                addProductCategory.dispatchEvent(
                                    new Event(
                                        'change',
                                        {
                                            bubbles: true
                                        }
                                    )
                                );

                            }
                        );

                    }
                );


                function createSpecificationWrapper(
                    labelText,
                    key,
                    fullWidth = false
                ) {

                    const wrapper =
                        document.createElement(
                            'div'
                        );

                    wrapper.className =
                        fullWidth
                            ? 'create-field category-spec-full'
                            : 'create-field';

                    const label =
                        document.createElement(
                            'label'
                        );

                    label.htmlFor =
                        `categorySpec_${key}`;

                    label.textContent =
                        labelText;

                    wrapper.appendChild(
                        label
                    );

                    return {
                        wrapper,
                        label
                    };

                }


                function buildSpecificationField(
                    field
                ) {

                    const type =
                        field.type ||
                        'text';

                    const fullWidth =
                        type === 'textarea';

                    const {
                        wrapper
                    } =
                        createSpecificationWrapper(
                            field.label,
                            field.key,
                            fullWidth
                        );

                    let control;

                    if (type === 'select') {

                        control =
                            document.createElement(
                                'select'
                            );

                        const empty =
                            document.createElement(
                                'option'
                            );

                        empty.value =
                            '';

                        empty.textContent =
                            `Select ${field.label.toLowerCase()}`;

                        control.appendChild(
                            empty
                        );

                        (
                            field.options ||
                            []
                        ).forEach(
                            function (optionLabel) {

                                const option =
                                    document.createElement(
                                        'option'
                                    );

                                option.value =
                                    optionLabel;

                                option.textContent =
                                    optionLabel;

                                control.appendChild(
                                    option
                                );

                            }
                        );

                    } else if (type === 'textarea') {

                        control =
                            document.createElement(
                                'textarea'
                            );

                        control.rows =
                            2;

                    } else {

                        control =
                            document.createElement(
                                'input'
                            );

                        control.type =
                            'text';

                    }

                    control.id =
                        `categorySpec_${field.key}`;

                    control.name =
                        `category_specifications[${field.key}]`;

                    if (field.placeholder) {
                        control.placeholder =
                            field.placeholder;
                    }

                    if (type === 'readonly') {

                        control.value =
                            field.value ||
                            '';

                        control.readOnly =
                            true;

                        control.classList.add(
                            'category-spec-readonly'
                        );

                    }

                    wrapper.appendChild(
                        control
                    );

                    return wrapper;

                }


                function buildReadonlySpecification(
                    label,
                    key,
                    value
                ) {

                    return buildSpecificationField({
                        label,
                        key,
                        type:
                            'readonly',
                        value
                    });

                }


                function buildSpecificationGroupTitle(
                    title,
                    subtitle = ''
                ) {

                    const heading =
                        document.createElement(
                            'div'
                        );

                    heading.className =
                        'category-spec-group-title';

                    heading.textContent =
                        title;

                    if (subtitle) {

                        const small =
                            document.createElement(
                                'small'
                            );

                        small.textContent =
                            subtitle;

                        heading.appendChild(
                            small
                        );

                    }

                    return heading;

                }


                function buildSubcategorySelect(
                    config
                ) {

                    const {
                        wrapper
                    } =
                        createSpecificationWrapper(
                            'Subcategory',
                            'subcategory'
                        );

                    const select =
                        document.createElement(
                            'select'
                        );

                    select.id =
                        'categorySpec_subcategory';

                    select.name =
                        'category_specifications[subcategory]';

                    const empty =
                        document.createElement(
                            'option'
                        );

                    empty.value =
                        '';

                    empty.textContent =
                        'Select subcategory';

                    select.appendChild(
                        empty
                    );

                    Object.keys(
                        config.subcategories ||
                        {}
                    ).forEach(
                        function (subcategory) {

                            const option =
                                document.createElement(
                                    'option'
                                );

                            option.value =
                                subcategory;

                            option.textContent =
                                subcategory;

                            select.appendChild(
                                option
                            );

                        }
                    );

                    wrapper.appendChild(
                        select
                    );

                    return {
                        wrapper,
                        select
                    };

                }


                function renderSubcategorySpecifications(
                    config,
                    subcategory
                ) {

                    const container =
                        document.getElementById(
                            'subcategorySpecificFields'
                        );

                    const title =
                        document.getElementById(
                            'subcategorySpecificTitle'
                        );

                    if (
                        !container ||
                        !title
                    ) {
                        return;
                    }

                    container.innerHTML =
                        '';

                    const fields =
                        config.subcategories?.[
                            subcategory
                        ] ||
                        [];

                    const hasFields =
                        Boolean(
                            subcategory &&
                            fields.length
                        );

                    title.classList.toggle(
                        'hidden',
                        !hasFields
                    );

                    container.classList.toggle(
                        'hidden',
                        !hasFields
                    );

                    if (!hasFields) {
                        return;
                    }

                    title.firstChild.textContent =
                        `${subcategory} Specifications`;

                    fields.forEach(
                        function (field) {

                            container.appendChild(
                                buildSpecificationField(
                                    field
                                )
                            );

                        }
                    );

                }


                function renderCategorySpecifications() {

                    if (
                        !addProductCategory ||
                        !productSpecificationsSection ||
                        !categorySpecificationsFields
                    ) {
                        return;
                    }

                    syncCreateCategoryPills();

                    const category =
                        addProductCategory.value;

                    const config =
                        productSpecificationLibrary[
                            category
                        ];

                    categorySpecificationsFields.innerHTML =
                        '';

                    const hasConfig =
                        Boolean(
                            category &&
                            config
                        );

                    productSpecificationsSection.classList.toggle(
                        'hidden',
                        !hasConfig
                    );

                    if (!hasConfig) {

                        if (categorySpecificationsTitle) {
                            categorySpecificationsTitle.textContent =
                                'Product Specifications';
                        }

                        return;

                    }

                    if (categorySpecificationsTitle) {

                        categorySpecificationsTitle.textContent =
                            `${config.label} Product Specifications`;

                    }

                    categorySpecificationsFields.appendChild(
                        buildReadonlySpecification(
                            'Category',
                            'selected_category',
                            config.label
                        )
                    );

                    categorySpecificationsFields.appendChild(
                        buildReadonlySpecification(
                            'Stock',
                            'stock_display',
                            createProductStock?.value ||
                            ''
                        )
                    );

                    const subcategorySelectData =
                        buildSubcategorySelect(
                            config
                        );

                    categorySpecificationsFields.appendChild(
                        subcategorySelectData.wrapper
                    );

                    categorySpecificationsFields.appendChild(
                        buildSpecificationGroupTitle(
                            `General ${config.label} Specifications`,
                            'These fields apply generally to products under this category.'
                        )
                    );

                    commonProductSpecificationFields.forEach(
                        function (field) {

                            categorySpecificationsFields.appendChild(
                                buildSpecificationField(
                                    field
                                )
                            );

                        }
                    );

                    (
                        config.generalFields ||
                        []
                    ).forEach(
                        function (field) {

                            categorySpecificationsFields.appendChild(
                                buildSpecificationField(
                                    field
                                )
                            );

                        }
                    );

                    const subcategoryTitle =
                        buildSpecificationGroupTitle(
                            'Subcategory Specifications',
                            'Choose a subcategory to show more specific product fields.'
                        );

                    subcategoryTitle.id =
                        'subcategorySpecificTitle';

                    subcategoryTitle.classList.add(
                        'hidden'
                    );

                    const subcategoryContainer =
                        document.createElement(
                            'div'
                        );

                    subcategoryContainer.id =
                        'subcategorySpecificFields';

                    subcategoryContainer.className =
                        'subcategory-specifications-wrap hidden';

                    categorySpecificationsFields.appendChild(
                        subcategoryTitle
                    );

                    categorySpecificationsFields.appendChild(
                        subcategoryContainer
                    );

                    subcategorySelectData.select.addEventListener(
                        'change',
                        function () {

                            renderSubcategorySpecifications(
                                config,
                                subcategorySelectData.select.value
                            );

                        }
                    );

                }


                addProductCategory?.addEventListener(
                    'change',
                    renderCategorySpecifications
                );


                createProductStock?.addEventListener(
                    'input',
                    function () {

                        const stockDisplay =
                            document.getElementById(
                                'categorySpec_stock_display'
                            );

                        if (stockDisplay) {
                            stockDisplay.value =
                                createProductStock.value;
                        }

                    }
                );


                /* =================================================
                   DESCRIPTION COUNTER
                ================================================== */

                function updateCreateDescriptionCount() {

                    if (
                        !createProductDescription ||
                        !createProductDescriptionCount
                    ) {
                        return;
                    }

                    createProductDescriptionCount.textContent =
                        `${createProductDescription.value.length}/2000`;

                }


                createProductDescription?.addEventListener(
                    'input',
                    updateCreateDescriptionCount
                );


                /* =================================================
                   RESET CREATE PRODUCT MODAL
                ================================================== */

                function resetCreateProductModal() {

                    revokeProductPhotoUrls();
                    revokeVariationUrls();

                    selectedProductPhotos =
                        [];

                    selectedVariationPhoto =
                        null;

                    selectedVariationPhotoUrl =
                        null;

                    productVariations =
                        [];

                    productColors =
                        [];

                    productSizes =
                        [];

                    pricingMode =
                        'fixed';

                    pricingSource =
                        '';

                    addProductForm?.reset();

                    if (productPricingMode) {
                        productPricingMode.value =
                            'fixed';
                    }

                    if (productPricingSource) {
                        productPricingSource.value =
                            '';
                    }

                    if (productVariationsContainer) {
                        productVariationsContainer.innerHTML =
                            '';
                    }

                    if (productColorsContainer) {
                        productColorsContainer.innerHTML =
                            '';
                    }

                    if (productSizesContainer) {
                        productSizesContainer.innerHTML =
                            '';
                    }

                    if (productPhotoPreviewGrid) {
                        productPhotoPreviewGrid.innerHTML =
                            '';
                    }

                    if (variationPhotoPickerText) {
                        variationPhotoPickerText.textContent =
                            'Photo';
                    }

                    variationPhotoPickerLabel?.classList.remove(
                        'has-photo'
                    );

                    selectedColorPhoto = null;
                    selectedColorPhotoUrl = null;
                    selectedSizePhoto = null;
                    selectedSizePhotoUrl = null;

                    if (productColorPhotoInput) productColorPhotoInput.value = '';
                    if (productSizePhotoInput) productSizePhotoInput.value = '';
                    if (colorPhotoPickerText) colorPhotoPickerText.textContent = 'Photo';
                    if (sizePhotoPickerText) sizePhotoPickerText.textContent = 'Photo';

                    syncProductPhotoInput();
                    renderProductPhotos();

                    applyPricingMode(
                        'fixed'
                    );

                    renderCategorySpecifications();
                    updateCreateDescriptionCount();

                }


                /* =================================================
                   OPEN / CLOSE CREATE PRODUCT
                ================================================== */

                function openAddModal() {

                    if (!addProductModal) {
                        return;
                    }

                    resetCreateProductModal();

                    addProductModal.classList.remove(
                        'hidden'
                    );

                    addProductModal.classList.add(
                        'modal-open'
                    );

                    addProductModal.setAttribute(
                        'aria-hidden',
                        'false'
                    );

                    document.body.classList.add(
                        'overflow-hidden'
                    );

                    window.setTimeout(
                        function () {

                            document.getElementById(
                                'createProductTitle'
                            )?.focus();

                        },
                        80
                    );

                }


                function closeAddModal() {

                    if (
                        !addProductModal ||
                        !addProductModalPanel
                    ) {
                        return;
                    }

                    fadeCloseModal(
                        addProductModal,
                        addProductModalPanel,
                        function () {

                            document.body.classList.remove(
                                'overflow-hidden'
                            );

                            resetCreateProductModal();

                        }
                    );

                }


                openAddProductModal?.addEventListener(
                    'click',
                    openAddModal
                );


                closeAddProductModal?.addEventListener(
                    'click',
                    closeAddModal
                );


                cancelAddProduct?.addEventListener(
                    'click',
                    closeAddModal
                );


                addProductModal?.addEventListener(
                    'click',
                    function (event) {

                        if (
                            event.target ===
                            addProductModal
                        ) {

                            closeAddModal();

                        }

                    }
                );


                document.addEventListener(
                    'keydown',
                    function (event) {

                        if (
                            event.key === 'Escape' &&
                            addProductModal?.classList.contains(
                                'modal-open'
                            )
                        ) {

                            closeAddModal();

                        }

                    }
                );


                /* =================================================
                   CREATE PRODUCT SUBMIT
                   Still front-end/demo until Product persistence is wired.
                ================================================== */

                addProductForm?.addEventListener(
                    'submit',
                    async function (event) {

                        event.preventDefault();

                        if (
                            !sellerRegisteredCategories.length
                        ) {

                            window.alert(
                                'No registered product categories are available for this seller.'
                            );

                            return;

                        }

                        if (
                            !addProductCategory ||
                            !sellerRegisteredCategories.includes(
                                addProductCategory.value
                            )
                        ) {

                            window.alert(
                                'Please choose one of your registered product categories.'
                            );

                            document
                                .getElementById(
                                    'createProductCategoryPills'
                                )
                                ?.scrollIntoView({
                                    behavior: 'smooth',
                                    block: 'center'
                                });

                            return;

                        }

                        if (
                            selectedProductPhotos.length === 0
                        ) {

                            window.alert(
                                'Please upload at least one product photo.'
                            );

                            productPhotosDropZone?.scrollIntoView({
                                behavior: 'smooth',
                                block: 'center'
                            });

                            return;

                        }

                        if (
                            pricingMode ===
                            'varies'
                        ) {

                            if (!pricingSource) {

                                window.alert(
                                    'Choose which buyer option sets the actual product price.'
                                );

                                variablePricingSetup?.scrollIntoView({
                                    behavior: 'smooth',
                                    block: 'center'
                                });

                                return;
                            }

                            const primaryItems =
                                getPricingGroupItems(
                                    pricingSource
                                );

                            if (
                                primaryItems.length === 0
                            ) {

                                window.alert(
                                    `Add at least one ${getPricingGroupLabel(pricingSource).toLowerCase()} option for variable pricing.`
                                );

                                variablePricingSetup?.scrollIntoView({
                                    behavior: 'smooth',
                                    block: 'center'
                                });

                                return;
                            }

                            const missingPrimaryPrice =
                                primaryItems.some(
                                    function (item) {
                                        return (
                                            item.price === '' ||
                                            item.price === null ||
                                            item.price === undefined ||
                                            Number.isNaN(
                                                Number(item.price)
                                            )
                                        );
                                    }
                                );

                            if (missingPrimaryPrice) {

                                window.alert(
                                    `Enter an item price for every ${getPricingGroupLabel(pricingSource).toLowerCase()} option.`
                                );

                                variablePricingSetup?.scrollIntoView({
                                    behavior: 'smooth',
                                    block: 'center'
                                });

                                return;
                            }
                        }

                        if (
                            !addProductForm.checkValidity()
                        ) {

                            addProductForm.reportValidity();
                            return;

                        }

                        const submitButton =
                            addProductForm.querySelector(
                                'button[type="submit"]'
                            );

                        if (submitButton) {

                            submitButton.disabled =
                                true;

                            submitButton.textContent =
                                'Adding...';

                        }

                        const formData =
                            new FormData(
                                addProductForm
                            );

                        if (!formData.has('name')) {
                            formData.append('name', formData.get('title') || '');
                        }

                        if (!formData.has('stock_quantity')) {
                            formData.append('stock_quantity', formData.get('stock') || '0');
                        }

                        if (!formData.has('status')) {
                            formData.append('status', 'pending');
                        }

                        productVariations.forEach(
                            function (variation, index) {

                                formData.append(
                                    `variation_items[${index}][name]`,
                                    variation.name
                                );

                                formData.append(
                                    `variation_items[${index}][price]`,
                                    pricingMode === 'fixed'
                                        ? createProductPrice?.value || '0'
                                        : variation.price || '0'
                                );

                                formData.append(
                                    `variation_items[${index}][price_type]`,
                                    pricingMode === 'varies' &&
                                    pricingSource === 'variations'
                                        ? 'base'
                                        : 'addon'
                                );

                                formData.append(
                                    `variation_items[${index}][stock]`,
                                    variation.stock || '0'
                                );

                                if (variation.photo) {

                                    formData.append(
                                        `variation_items[${index}][photo]`,
                                        variation.photo
                                    );

                                }

                            }
                        );

                        productColors.forEach(
                            function (color, index) {

                                formData.append(
                                    `color_items[${index}][name]`,
                                    color.name
                                );

                                formData.append(
                                    `color_items[${index}][price]`,
                                    pricingMode === 'fixed'
                                        ? createProductPrice?.value || '0'
                                        : color.price || '0'
                                );

                                formData.append(
                                    `color_items[${index}][price_type]`,
                                    pricingMode === 'varies' &&
                                    pricingSource === 'colors'
                                        ? 'base'
                                        : 'addon'
                                );

                                formData.append(
                                    `color_items[${index}][stock]`,
                                    color.stock || '0'
                                );

                                if (color.photo) {
                                    formData.append(
                                        `color_items[${index}][photo]`,
                                        color.photo
                                    );
                                }

                            }
                        );

                        productSizes.forEach(
                            function (size, index) {

                                formData.append(
                                    `size_items[${index}][name]`,
                                    size.name
                                );

                                formData.append(
                                    `size_items[${index}][price]`,
                                    pricingMode === 'fixed'
                                        ? createProductPrice?.value || '0'
                                        : size.price || '0'
                                );

                                formData.append(
                                    `size_items[${index}][price_type]`,
                                    pricingMode === 'varies' &&
                                    pricingSource === 'sizes'
                                        ? 'base'
                                        : 'addon'
                                );

                                formData.append(
                                    `size_items[${index}][stock]`,
                                    size.stock || '0'
                                );

                                if (size.photo) {
                                    formData.append(
                                        `size_items[${index}][photo]`,
                                        size.photo
                                    );
                                }

                            }
                        );

                        const previewPayload = {
                            title:
                                formData.get(
                                    'title'
                                ),

                            category:
                                formData.get(
                                    'category'
                                ),

                            pricingMode:
                                pricingMode,

                            pricingSource:
                                pricingSource,

                            basePrice:
                                pricingMode === 'fixed'
                                    ? formData.get(
                                        'price'
                                    )
                                    : null,

                            stock:
                                formData.get(
                                    'stock'
                                ),

                            variations:
                                productVariations.map(
                                    function (variation) {
                                        return {
                                            name:
                                                variation.name,

                                            hasPhoto:
                                                Boolean(
                                                    variation.photo
                                                ),

                                            price:
                                                Number(
                                                    pricingMode === 'fixed'
                                                        ? formData.get('price') || 0
                                                        : variation.price || 0
                                                ),

                                            stock:
                                                Number(
                                                    variation.stock ||
                                                    0
                                                ),

                                            priceType:
                                                pricingMode === 'varies' &&
                                                pricingSource === 'variations'
                                                    ? 'base'
                                                    : 'addon'
                                        };
                                    }
                                ),

                            colors:
                                productColors.map(
                                    function (color) {
                                        return {
                                            name:
                                                color.name,

                                            price:
                                                Number(
                                                    pricingMode === 'fixed'
                                                        ? formData.get('price') || 0
                                                        : color.price || 0
                                                ),

                                            stock:
                                                Number(
                                                    color.stock ||
                                                    0
                                                ),

                                            priceType:
                                                pricingMode === 'varies' &&
                                                pricingSource === 'colors'
                                                    ? 'base'
                                                    : 'addon'
                                        };
                                    }
                                ),

                            sizes:
                                productSizes.map(
                                    function (size) {
                                        return {
                                            name:
                                                size.name,

                                            price:
                                                Number(
                                                    pricingMode === 'fixed'
                                                        ? formData.get('price') || 0
                                                        : size.price || 0
                                                ),

                                            stock:
                                                Number(
                                                    size.stock ||
                                                    0
                                                ),

                                            priceType:
                                                pricingMode === 'varies' &&
                                                pricingSource === 'sizes'
                                                    ? 'base'
                                                    : 'addon'
                                        };
                                    }
                                ),

                            pricingFormula:
                                pricingMode === 'fixed'
                                    ? 'fixed_price'
                                    : 'selected_base_option_price + selected_addon_prices',

                            specifications:
                                Object.fromEntries(
                                    Array.from(
                                        formData.entries()
                                    )
                                    .filter(
                                        function ([key]) {
                                            return key.startsWith(
                                                'category_specifications['
                                            );
                                        }
                                    )
                                )
                        };


                        const compactProductPhotos =
                            (
                                await Promise.all(
                                    selectedProductPhotos
                                        .slice(
                                            0,
                                            8
                                        )
                                        .map(
                                            function (entry) {
                                                return createCompactCoverDataUrl(
                                                    entry.file
                                                );
                                            }
                                        )
                                )
                            )
                            .filter(
                                Boolean
                            );

                        if (compactProductPhotos.length) {
                            compactProductPhotos.forEach(
                                function (photo) {
                                    formData.append(
                                        'photos[]',
                                        photo
                                    );
                                }
                            );
                        }


                        const compactVariationPhotos =
                            await Promise.all(
                                productVariations.map(
                                    function (variation) {

                                        return createCompactCoverDataUrl(
                                            variation.photo
                                        );

                                    }
                                )
                            );


                        const specificationDisplay =
                            Array.from(
                                categorySpecificationsFields
                                    ?.querySelectorAll(
                                        '.create-field'
                                    ) ||
                                []
                            )
                            .map(
                                function (wrapper) {

                                    const control =
                                        wrapper.querySelector(
                                            'input, select, textarea'
                                        );

                                    const label =
                                        wrapper.querySelector(
                                            'label'
                                        );

                                    if (!control) {
                                        return null;
                                    }

                                    const rawName =
                                        control.name ||
                                        '';

                                    const key =
                                        rawName
                                            .replace(
                                                /^category_specifications\[/,
                                                ''
                                            )
                                            .replace(
                                                /\]$/,
                                                ''
                                            );

                                    return {
                                        key:
                                            key,

                                        label:
                                            label?.textContent?.trim() ||
                                            createdProductLabelFromKey(
                                                key
                                            ),

                                        value:
                                            control.value?.trim() ||
                                            ''
                                    };

                                }
                            )
                            .filter(
                                function (item) {
                                    return (
                                        item &&
                                        item.value !== ''
                                    );
                                }
                            );


                        let persistedProduct;

                        try {
                            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

                            const response = await fetch(
                                inventoryConfig.storeProductUrl,
                                {
                                    method: 'POST',
                                    credentials: 'same-origin',
                                    headers: {
                                        'X-CSRF-TOKEN': csrfToken,
                                        'X-XSRF-TOKEN': csrfToken,
                                        'Accept': 'application/json'
                                    },
                                    body: formData
                                }
                            );

                            if (!response.ok) {
                                const errorPayload = await response.json().catch(() => ({}));
                                const firstError = Object.values(errorPayload.errors || {})[0]?.[0];
                                throw new Error(firstError || 'Product could not be saved.');
                            }

                            persistedProduct = await response.json();
                        } catch (error) {
                            if (submitButton) {
                                submitButton.disabled = false;
                                submitButton.textContent = 'Add Product';
                            }

                            window.alert(error.message || 'Product could not be saved.');
                            return;
                        }

                        const createdProduct = {
                            id:
                                persistedProduct.id,

                            title:
                                previewPayload.title,

                            category:
                                previewPayload.category,

                            categoryLabel:
                                productSpecificationLibrary[
                                    previewPayload.category
                                ]?.label ||
                                previewPayload.category,

                            approvalStatus:
                                persistedProduct.status || 'pending',

                            pricingMode:
                                previewPayload.pricingMode,

                            pricingSource:
                                previewPayload.pricingSource,

                            basePrice:
                                previewPayload.basePrice,

                            stock:
                                Number(
                                    previewPayload.stock ||
                                    0
                                ),

                            variations:
                                previewPayload.variations.map(
                                    function (variation, index) {
                                        return {
                                            ...variation,

                                            photoData:
                                                compactVariationPhotos[
                                                    index
                                                ] ||
                                                ''
                                        };
                                    }
                                ),

                            colors:
                                previewPayload.colors,

                            sizes:
                                previewPayload.sizes,

                            specifications:
                                previewPayload.specifications,

                            specificationDisplay:
                                specificationDisplay,

                            description:
                                formData.get(
                                    'description'
                                ) ||
                                '',

                            sku:
                                formData.get(
                                    'sku'
                                ) ||
                                '',

                            photos:
                                compactProductPhotos,

                            coverPhoto:
                                compactProductPhotos[
                                    0
                                ] ||
                                '',

                            createdAt:
                                persistedProduct.created_at || new Date().toISOString()
                        };
                        createdInventoryProducts.unshift(
                            createdProduct
                        );

                        saveCreatedProducts();
                        updateInventoryTotalCount();

                        insertCreatedProductRow(
                            createdProduct,
                            true
                        );


                        console.log(
                            'Create Product payload:',
                            previewPayload
                        );

                        window.setTimeout(
                            function () {

                                if (submitButton) {

                                    submitButton.disabled =
                                        false;

                                    submitButton.textContent =
                                        'Add Product';

                                }

                                closeAddModal();

                                window.setTimeout(
                                    function () {

                                        if (
                                            typeof showInventoryFlash ===
                                            'function'
                                        ) {

                                            showInventoryFlash(
                                                'Product created and added to your inventory.'
                                            );

                                        } else {

                                            window.alert(
                                                'Product created and added to your inventory.'
                                            );

                                        }

                                    },
                                    220
                                );

                            },
                            550
                        );

                    }
                );


                applyPricingMode(
                    'fixed'
                );

                renderCategorySpecifications();
                updateCreateDescriptionCount();



                /* =================================================
                   PAGINATION
                ================================================== */

                const paginationButtons =
                    document.querySelectorAll(
                        '.pagination-button[data-page]'
                    );


                let currentPage =
                    1;


                paginationButtons.forEach(
                    function (button) {

                        button.addEventListener(
                            'click',
                            function () {

                                currentPage =
                                    parseInt(
                                        button.dataset.page,
                                        10
                                    ) || 1;


                                paginationButtons.forEach(
                                    function (item) {

                                        item.classList.toggle(
                                            'current',

                                            parseInt(
                                                item.dataset.page,
                                                10
                                            ) ===
                                            currentPage
                                        );

                                    }
                                );

                            }
                        );

                    }
                );


                previousPage?.addEventListener(
                    'click',
                    function () {

                        if (
                            currentPage <= 1
                        ) {

                            return;

                        }


                        currentPage--;


                        paginationButtons.forEach(
                            function (item) {

                                item.classList.toggle(
                                    'current',

                                    parseInt(
                                        item.dataset.page,
                                        10
                                    ) ===
                                    currentPage
                                );

                            }
                        );

                    }
                );


                nextPage?.addEventListener(
                    'click',
                    function () {

                        if (
                            currentPage >= 3
                        ) {

                            return;

                        }


                        currentPage++;


                        paginationButtons.forEach(
                            function (item) {

                                item.classList.toggle(
                                    'current',

                                    parseInt(
                                        item.dataset.page,
                                        10
                                    ) ===
                                    currentPage
                                );

                            }
                        );

                    }
                );



                /* =================================================
                   INITIALIZE
                ================================================== */

                updateTabView();

            }
        );

})();
