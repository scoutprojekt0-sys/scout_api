<!-- Google Analytics 4 -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'G-XXXXXXXXXX', {
    'send_page_view': false // We'll send manually
  });
</script>

<!-- Custom Analytics Tracking -->
<script>
  // NextScout Analytics
  const NextScoutAnalytics = {
    // Track page view
    trackPageView(pageData) {
      // Send to backend
      fetch('/api/analytics/pageview', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
          page_type: pageData.type,
          page_id: pageData.id,
          page_title: document.title,
          session_id: this.getSessionId()
        })
      });

      // Send to Google Analytics
      gtag('event', 'page_view', {
        page_title: document.title,
        page_location: window.location.href,
        page_path: window.location.pathname
      });
    },

    // Track custom event
    trackEvent(eventName, eventData = {}) {
      // Send to backend
      fetch('/api/analytics/event', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
          event_name: eventName,
          ...eventData,
          session_id: this.getSessionId()
        })
      });

      // Send to Google Analytics
      gtag('event', eventName, eventData);
    },

    // Track button click
    trackButtonClick(buttonName, category = 'engagement') {
      this.trackEvent('button_click', {
        category: category,
        action: 'click',
        label: buttonName
      });
    },

    // Track profile view
    trackProfileView(profileType, profileId) {
      this.trackEvent('profile_view', {
        category: 'engagement',
        profile_type: profileType,
        profile_id: profileId
      });
    },

    // Track video play
    trackVideoPlay(videoId, videoTitle) {
      this.trackEvent('video_play', {
        category: 'engagement',
        video_id: videoId,
        video_title: videoTitle
      });
    },

    // Track search
    trackSearch(searchQuery, resultsCount) {
      this.trackEvent('search', {
        search_term: searchQuery,
        results_count: resultsCount
      });
    },

    // Track conversion
    trackConversion(conversionType, value = null) {
      this.trackEvent('conversion', {
        category: 'conversion',
        conversion_type: conversionType,
        value: value
      });
    },

    // Track error
    trackError(errorMessage, errorType = 'js_error') {
      fetch('/api/analytics/error', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
          error_type: errorType,
          error_message: errorMessage,
          stack_trace: new Error().stack,
          url: window.location.href
        })
      });
    },

    // Get or create session ID
    getSessionId() {
      let sessionId = sessionStorage.getItem('nextscout_session_id');
      if (!sessionId) {
        sessionId = 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        sessionStorage.setItem('nextscout_session_id', sessionId);
      }
      return sessionId;
    },

    // Track time on page
    trackTimeOnPage() {
      const startTime = Date.now();

      window.addEventListener('beforeunload', () => {
        const timeSpent = Math.floor((Date.now() - startTime) / 1000); // seconds

        navigator.sendBeacon('/api/analytics/time-spent', JSON.stringify({
          session_id: this.getSessionId(),
          time_spent: timeSpent,
          page_url: window.location.href
        }));
      });
    },

    // Initialize
    init() {
      // Track initial page view
      this.trackPageView({
        type: document.body.dataset.pageType || 'general',
        id: document.body.dataset.pageId || null
      });

      // Track time on page
      this.trackTimeOnPage();

      // Global error handler
      window.addEventListener('error', (event) => {
        this.trackError(event.message, 'js_error');
      });

      // Track all button clicks automatically
      document.addEventListener('click', (event) => {
        const button = event.target.closest('button, a[role="button"], .btn');
        if (button) {
          const buttonName = button.textContent.trim() || button.getAttribute('aria-label') || 'Unknown Button';
          this.trackButtonClick(buttonName);
        }
      });
    }
  };

  // Auto-initialize on page load
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => NextScoutAnalytics.init());
  } else {
    NextScoutAnalytics.init();
  }

  // Make it globally available
  window.NSAnalytics = NextScoutAnalytics;
</script>

<!-- Performance Monitoring -->
<script>
  // Track page performance
  window.addEventListener('load', () => {
    setTimeout(() => {
      const perfData = performance.timing;
      const pageLoadTime = perfData.loadEventEnd - perfData.navigationStart;
      const domReadyTime = perfData.domContentLoadedEventEnd - perfData.navigationStart;
      const firstPaintTime = performance.getEntriesByType('paint')[0]?.startTime || 0;

      fetch('/api/analytics/performance', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
          metric_type: 'page_load',
          duration: pageLoadTime,
          url: window.location.href,
          additional_data: {
            dom_ready: domReadyTime,
            first_paint: firstPaintTime
          }
        })
      });
    }, 0);
  });
</script>

<!-- Facebook Pixel (Optional) -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', 'YOUR_PIXEL_ID');
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=YOUR_PIXEL_ID&ev=PageView&noscript=1"
/></noscript>
