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
});

window.adminLayoutState = () => ({
    sidebarState: 'expanded',
    sidebarHover: false,
    mobileSidebarOpen: false,

    init() {
        const savedState = window.localStorage.getItem('wayna.admin.sidebar.state');

        if (savedState !== null && ['expanded', 'icons', 'hidden'].includes(savedState)) {
            this.sidebarState = savedState;
        }
    },

    get sidebarEffective() {
        if (this.sidebarState === 'expanded') return 'expanded';
        if (this.sidebarState === 'icons' && this.sidebarHover) return 'expanded';
        return this.sidebarState;
    },

    get sidebarWidth() {
        if (this.sidebarState === 'expanded') return 'md:pl-[260px]';
        if (this.sidebarState === 'icons') return 'md:pl-0';
        return 'md:pl-0';
    },

    get sidebarWidthEffective() {
        const eff = this.sidebarEffective;
        if (eff === 'expanded') return 'md:pl-[260px]';
        if (this.sidebarState === 'icons') return 'md:pl-[88px]';
        return 'md:pl-0';
    },

    toggleSidebar() {
        if (this.sidebarState === 'expanded') {
            this.sidebarState = 'icons';
        } else if (this.sidebarState === 'icons') {
            this.sidebarState = 'hidden';
        } else {
            this.sidebarState = 'expanded';
        }
        window.localStorage.setItem('wayna.admin.sidebar.state', this.sidebarState);
    },

    closeMobileSidebar() {
        this.mobileSidebarOpen = false;
    },
});
