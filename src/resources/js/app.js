import Chart from 'chart.js/auto';

const adminChartRegistry = new WeakMap();

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
});

document.addEventListener('livewire:navigated', () => {
    destroyDetachedAdminCharts();
    initializeAdminCharts();
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
