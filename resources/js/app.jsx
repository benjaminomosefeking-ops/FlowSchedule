
import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

import React from 'react';
import ReactDOM from 'react-dom/client';
import ExcalidrawBoard from './components/ExcalidrawBoard.jsx';
import TurnosCallendar from './components/TurnosCallendar.jsx';

console.log('FlowSchedule: Initializing JS Bundle...');

const mountBoard = () => {
    const element = document.getElementById('excalidraw-root');

    if (element) {
        console.log('Found #excalidraw-root, mounting board...');
        try {
            const rawInitialData = element.dataset.initialData ?? '{}';
            const parsedInitialData = (() => {
                try {
                    const value = JSON.parse(rawInitialData);
                    return typeof value === 'string' ? JSON.parse(value) : value;
                } catch {
                    return {};
                }
            })();

            const root = ReactDOM.createRoot(element);
            root.render(
                <React.StrictMode>
                    <ExcalidrawBoard
                        boardId={element.dataset.boardId}
                        initialData={parsedInitialData}
                        csrfToken={element.dataset.csrfToken}
                        saveUrl={element.dataset.saveUrl}
                        contentUrl={element.dataset.contentUrl}
                        canEdit={element.dataset.canEdit === 'true'}
                        canManagePermissions={element.dataset.canManagePermissions === 'true'}
                    />
                </React.StrictMode>
            );
            console.log(' Board rendered successfully');
        } catch (error) {
            console.error(' Critical error during ReactDOM mount:', error);
        }
    } else {
        console.log('No #excalidraw-root found on this page.');
    }
};

const mountCalendar = () => {
    const element = document.getElementById('calendar-root');

    if (element) {
        console.log('Found #calendar-root, mounting calendar...');
        try {
            const shifts = JSON.parse(element.dataset.shifts ?? '[]');
            const root = ReactDOM.createRoot(element);
            root.render(
                <React.StrictMode>
                    <TurnosCallendar shifts={shifts} />
                </React.StrictMode>
            );
            console.log(' Calendar rendered successfully');
        } catch (error) {
            console.error(' Critical error during Calendar mount:', error);
        }
    } else {
        console.log('No #calendar-root found on this page.');
    }
};

// Run on load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        mountBoard();
        mountCalendar();

        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenuSidebar = document.getElementById('mobile-menu-sidebar');
        const mobileMenuBackdrop = document.getElementById('mobile-menu-backdrop');

        if (mobileMenuButton && mobileMenuSidebar && mobileMenuBackdrop) {
            mobileMenuButton.addEventListener('click', () => {
                const isOpen = mobileMenuButton.getAttribute('aria-expanded') === 'true';
                mobileMenuButton.setAttribute('aria-expanded', String(!isOpen));
                mobileMenuSidebar.classList.toggle('-translate-x-full');
                mobileMenuSidebar.classList.toggle('pointer-events-none');
                mobileMenuBackdrop.classList.toggle('opacity-0');
                mobileMenuBackdrop.classList.toggle('pointer-events-none');
            });

            mobileMenuBackdrop.addEventListener('click', () => {
                mobileMenuButton.setAttribute('aria-expanded', 'false');
                mobileMenuSidebar.classList.add('-translate-x-full');
                mobileMenuSidebar.classList.add('pointer-events-none');
                mobileMenuBackdrop.classList.add('opacity-0');
                mobileMenuBackdrop.classList.add('pointer-events-none');
            });

            const mobileMenuLinks = mobileMenuSidebar.querySelectorAll('a, button');
            mobileMenuLinks.forEach(link => {
                link.addEventListener('click', () => {
                    mobileMenuButton.setAttribute('aria-expanded', 'false');
                    mobileMenuSidebar.classList.add('-translate-x-full');
                    mobileMenuSidebar.classList.add('pointer-events-none');
                    mobileMenuBackdrop.classList.add('opacity-0');
                    mobileMenuBackdrop.classList.add('pointer-events-none');
                });
            });
        }
    });
} else {
    mountBoard();
    mountCalendar();

    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenuSidebar = document.getElementById('mobile-menu-sidebar');
    const mobileMenuBackdrop = document.getElementById('mobile-menu-backdrop');

    if (mobileMenuButton && mobileMenuSidebar && mobileMenuBackdrop) {
       
        mobileMenuButton.addEventListener('click', () => {
            const isOpen = mobileMenuButton.getAttribute('aria-expanded') === 'true';
            mobileMenuButton.setAttribute('aria-expanded', String(!isOpen));
            mobileMenuSidebar.classList.toggle('-translate-x-full');
            mobileMenuSidebar.classList.toggle('pointer-events-none');
            mobileMenuBackdrop.classList.toggle('opacity-0');
            mobileMenuBackdrop.classList.toggle('pointer-events-none');
        });

        mobileMenuBackdrop.addEventListener('click', () => {
            mobileMenuButton.setAttribute('aria-expanded', 'false');
            mobileMenuSidebar.classList.add('-translate-x-full');
            mobileMenuSidebar.classList.add('pointer-events-none');
            mobileMenuBackdrop.classList.add('opacity-0');
            mobileMenuBackdrop.classList.add('pointer-events-none');
        });

        const mobileMenuLinks = mobileMenuSidebar.querySelectorAll('a, button');
        mobileMenuLinks.forEach(link => {
            link.addEventListener('click', () => {
                mobileMenuButton.setAttribute('aria-expanded', 'false');
                mobileMenuSidebar.classList.add('-translate-x-full');
                mobileMenuSidebar.classList.add('pointer-events-none');
                mobileMenuBackdrop.classList.add('opacity-0');
                mobileMenuBackdrop.classList.add('pointer-events-none');
            });
        });
    }
}
