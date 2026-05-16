import Chart from 'chart.js/auto';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import markerIcon2x from 'leaflet/dist/images/marker-icon-2x.png';
import markerIcon from 'leaflet/dist/images/marker-icon.png';
import markerShadow from 'leaflet/dist/images/marker-shadow.png';

delete L.Icon.Default.prototype._getIconUrl;

L.Icon.Default.mergeOptions({
    iconRetinaUrl: markerIcon2x,
    iconUrl: markerIcon,
    shadowUrl: markerShadow,
});

const adminChartRegistry = new WeakMap();
const businessMapRegistry = new Map();
const businessMapDefaultCenter = [-16.4897, -68.1193];

const parseBusinessCoordinate = (value) => {
    const parsed = Number.parseFloat(value ?? '');

    return Number.isFinite(parsed) ? parsed : null;
};

const syncBusinessMapInput = (input, value) => {
    if (!input) {
        return;
    }

    input.value = value;
    input.dispatchEvent(new Event('input', { bubbles: true }));
    input.dispatchEvent(new Event('change', { bubbles: true }));
};

const updateBusinessMapDisplays = (root, latitude, longitude) => {
    const latDisplay = root.querySelector('[data-map-lat-display]');
    const lngDisplay = root.querySelector('[data-map-lng-display]');

    if (latDisplay) {
        latDisplay.textContent = latitude ?? 'Sin marcar';
    }

    if (lngDisplay) {
        lngDisplay.textContent = longitude ?? 'Sin marcar';
    }
};

const cleanupBusinessMaps = () => {
    businessMapRegistry.forEach((instance, element) => {
        if (element.isConnected) {
            return;
        }

        instance.map.remove();
        businessMapRegistry.delete(element);
    });
};

const initializeBusinessMaps = () => {
    cleanupBusinessMaps();

    document.querySelectorAll('[data-business-location-root]').forEach((root) => {
        const mapElement = root.querySelector('[data-business-map]');

        if (!mapElement || businessMapRegistry.has(mapElement)) {
            return;
        }

        const latInput = root.querySelector('[data-map-lat]');
        const lngInput = root.querySelector('[data-map-lng]');
        const initialLatitude = parseBusinessCoordinate(latInput?.value ?? mapElement.dataset.lat);
        const initialLongitude = parseBusinessCoordinate(lngInput?.value ?? mapElement.dataset.lng);

        const map = L.map(mapElement, {
            scrollWheelZoom: false,
        }).setView(
            initialLatitude !== null && initialLongitude !== null
                ? [initialLatitude, initialLongitude]
                : businessMapDefaultCenter,
            initialLatitude !== null && initialLongitude !== null ? 16 : 12
        );

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
            maxZoom: 19,
        }).addTo(map);

        let marker = null;

        const setMarker = (latitude, longitude, options = {}) => {
            const nextLatitude = Number.parseFloat(latitude.toFixed(7));
            const nextLongitude = Number.parseFloat(longitude.toFixed(7));
            const coords = [nextLatitude, nextLongitude];

            if (!marker) {
                marker = L.marker(coords, { draggable: true }).addTo(map);
                marker.on('dragend', () => {
                    const markerPosition = marker.getLatLng();
                    setMarker(markerPosition.lat, markerPosition.lng, { pan: false });
                });
            } else {
                marker.setLatLng(coords);
            }

            if (options.pan !== false) {
                map.setView(coords, Math.max(map.getZoom(), 16));
            }

            syncBusinessMapInput(latInput, nextLatitude.toFixed(7));
            syncBusinessMapInput(lngInput, nextLongitude.toFixed(7));
            updateBusinessMapDisplays(root, nextLatitude.toFixed(7), nextLongitude.toFixed(7));
        };

        if (initialLatitude !== null && initialLongitude !== null) {
            setMarker(initialLatitude, initialLongitude, { pan: false });
        } else {
            updateBusinessMapDisplays(root, null, null);
        }

        map.on('click', (event) => {
            setMarker(event.latlng.lat, event.latlng.lng);
        });

        map.whenReady(() => {
            setTimeout(() => map.invalidateSize(), 0);
        });

        businessMapRegistry.set(mapElement, { map });
    });
};

const buildAdminChart = (canvas) => {
    const rawConfig = canvas.dataset.adminChart;

    if (!rawConfig) {
        return;
    }

    const existingChart = adminChartRegistry.get(canvas);

    if (existingChart) {
        existingChart.destroy();
        adminChartRegistry.delete(canvas);
    }

    let config;

    try {
        config = JSON.parse(rawConfig);
    } catch {
        return;
    }

    const context = canvas.getContext('2d');

    if (!context) {
        return;
    }

    const chart = new Chart(context, config);
    adminChartRegistry.set(canvas, chart);
};

const initializeAdminCharts = () => {
    document.querySelectorAll('[data-admin-chart]').forEach((canvas) => {
        buildAdminChart(canvas);
    });
};

const destroyDetachedAdminCharts = () => {
    document.querySelectorAll('[data-admin-chart]').forEach((canvas) => {
        if (!canvas.isConnected) {
            const chart = adminChartRegistry.get(canvas);

            if (chart) {
                chart.destroy();
                adminChartRegistry.delete(canvas);
            }
        }
    });
};

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-scroll-nav]').forEach((nav) => {
        const links = Array.from(nav.querySelectorAll('[data-nav-link]'));
        const items = links
            .map((link) => {
                const href = link.getAttribute('href');
                const target = href ? document.querySelector(href) : null;

                if (!target) {
                    return null;
                }

                return { link, target };
            })
            .filter(Boolean);

        if (items.length === 0) {
            return;
        }

        const setActive = (id) => {
            items.forEach(({ link, target }) => {
                link.dataset.active = target.id === id ? 'true' : 'false';
            });
        };

        const getActiveSection = () => {
            const offset = window.innerHeight * 0.35;
            let active = items[0].target.id;

            items.forEach(({ target }) => {
                if (target.getBoundingClientRect().top - offset <= 0) {
                    active = target.id;
                }
            });

            return active;
        };

        links.forEach((link) => {
            link.addEventListener('click', () => {
                const targetId = link.getAttribute('href')?.replace('#', '');

                if (targetId) {
                    setActive(targetId);
                }
            });
        });

        const syncActiveLink = () => setActive(getActiveSection());

        syncActiveLink();
        window.addEventListener('scroll', syncActiveLink, { passive: true });
        window.addEventListener('resize', syncActiveLink);
    });

    initializeAdminCharts();
    initializeBusinessMaps();
});

document.addEventListener('livewire:navigated', () => {
    destroyDetachedAdminCharts();
    initializeAdminCharts();
    initializeBusinessMaps();
});

window.Chart = Chart;

window.adminLayoutState = () => ({
    sidebarHover: false,
    mobileSidebarOpen: false,

    init() {},

    get sidebarEffective() {
        if (this.mobileSidebarOpen || this.sidebarHover) return 'expanded';

        return 'icons';
    },

    get desktopSidebarWidth() {
        return this.sidebarEffective === 'expanded' ? 260 : 88;
    },

    get desktopGridStyle() {
        return `grid-template-columns: ${this.desktopSidebarWidth}px minmax(0, 1fr);`;
    },

    handleSidebarEnter() {
        if (window.innerWidth >= 768) {
            this.sidebarHover = true;
        }
    },

    handleSidebarLeave() {
        if (window.innerWidth >= 768) {
            this.sidebarHover = false;
        }
    },

    openMobileSidebar() {
        this.mobileSidebarOpen = true;
    },

    closeMobileSidebar() {
        this.mobileSidebarOpen = false;
    },
});
