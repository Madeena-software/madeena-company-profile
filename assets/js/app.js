// Core interactivity rewritten in vanilla ES6

// toggle mobile menu and submenus
const mobileMenuButton = document.getElementById('mobile-menu-button');
const mobileMenu = document.getElementById('mobile-menu');

if (mobileMenuButton && mobileMenu) {
    mobileMenuButton.addEventListener('click', () => {
        mobileMenu.classList.toggle('hidden');
    });

    // submenu toggles
    mobileMenu.querySelectorAll('[data-toggle="submenu"]').forEach(btn => {
        btn.addEventListener('click', () => {
            const arrow = btn.querySelector('svg');
            const name = btn.textContent.trim();
            const submenu = mobileMenu.querySelector(`[data-menu="${name}"]`);
            if (submenu) {
                submenu.classList.toggle('hidden');
                arrow.classList.toggle('rotate-180');
            }
        });
    });
}

// close mobile menu when a link is clicked (small screens)
document.querySelectorAll('#mobile-menu a[href^="#"]').forEach(link => {
    link.addEventListener('click', () => {
        if (window.innerWidth < 768 && mobileMenu) {
            mobileMenu.classList.add('hidden');
        }
    });
});

// video fallback logic (moved from inline script)
document.addEventListener('DOMContentLoaded', () => {
    const heroVideo = document.querySelector('#one .image.main video');
    const heroIframe = document.querySelector('#one .image.main iframe');
    const videoSources = heroVideo ? heroVideo.querySelectorAll('source') : [];
    const localVideoSrc = 'videos/Dispaly Video.mp4';
    let hasTriedLocalSource = false;

    if (!heroVideo) return;

    const useIframeFallback = () => {
        if (!heroIframe) return;
        heroVideo.style.display = 'none';
        heroIframe.style.display = 'block';
    };

    const tryLocalVideoFallback = () => {
        if (hasTriedLocalSource) return false;
        hasTriedLocalSource = true;
        if (videoSources.length > 0) {
            videoSources[0].src = localVideoSrc;
        } else {
            heroVideo.src = localVideoSrc;
        }
        heroVideo.load();
        const playPromise = heroVideo.play();
        if (playPromise && typeof playPromise.catch === 'function') {
            playPromise.catch(() => useIframeFallback());
        }
        return true;
    };

    heroVideo.muted = true;
    heroVideo.addEventListener('error', () => {
        if (!tryLocalVideoFallback()) useIframeFallback();
    }, { once: true });

    const playPromise = heroVideo.play();
    if (playPromise && typeof playPromise.catch === 'function') {
        playPromise.catch(() => {
            if (!tryLocalVideoFallback()) useIframeFallback();
        });
    }
});
