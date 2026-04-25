(function () {
    var config = window.UltraClarityConfig || {};
    if (!config.endpoint || Math.random() > (config.sampleRate || 1)) return;
    if (config.gdpr && localStorage.getItem('ultraclarity:optout') === '1') return;

    var visitorId = localStorage.getItem('ultraclarity:visitor');
    if (!visitorId) {
        visitorId = crypto.randomUUID ? crypto.randomUUID() : String(Date.now()) + Math.random();
        localStorage.setItem('ultraclarity:visitor', visitorId);
    }

    var sessionId = sessionStorage.getItem('ultraclarity:session');
    if (!sessionId) {
        sessionId = crypto.randomUUID ? crypto.randomUUID() : String(Date.now()) + Math.random();
        sessionStorage.setItem('ultraclarity:session', sessionId);
    }

    var started = Date.now();
    var events = [];
    var recording = [];
    var maxScroll = 0;
    var lastMove = 0;
    var lastScroll = 0;
    var lastInput = 0;

    function selectorFor(el) {
        if (!el || !el.tagName) return null;
        if (el.id) return '#' + el.id;
        var selector = el.tagName.toLowerCase();
        if (el.className && typeof el.className === 'string') {
            selector += '.' + el.className.trim().split(/\s+/).slice(0, 2).join('.');
        }
        return selector;
    }

    function push(list, item, limit) {
        if (list.length > limit) list.shift();
        list.push(item);
    }

    function scrollDepth() {
        var doc = document.documentElement;
        var height = Math.max(doc.scrollHeight - window.innerHeight, 1);
        return Math.min(100, Math.round((window.scrollY / height) * 100));
    }

    function browserName() {
        var ua = navigator.userAgent;
        if (ua.indexOf('Edg') > -1) return 'Edge';
        if (ua.indexOf('Chrome') > -1) return 'Chrome';
        if (ua.indexOf('Safari') > -1) return 'Safari';
        if (ua.indexOf('Firefox') > -1) return 'Firefox';
        return 'Other';
    }

    function capture(item) {
        item.t = Date.now() - started;
        push(events, item, 500);
        push(recording, item, 1200);
    }

    document.addEventListener('click', function (event) {
        capture({
            type: 'click',
            x: event.pageX,
            y: event.pageY,
            selector: selectorFor(event.target),
            text: config.maskInputs ? '' : (event.target.innerText || '').slice(0, 120)
        });
    }, { passive: true });

    document.addEventListener('mousemove', function (event) {
        var now = Date.now();
        if (now - lastMove < 220) return;
        lastMove = now;
        push(recording, { type: 'move', x: event.pageX, y: event.pageY, t: now - started }, 1200);
    }, { passive: true });

    document.addEventListener('scroll', function () {
        var now = Date.now();
        maxScroll = Math.max(maxScroll, scrollDepth());
        if (now - lastScroll < 700) return;
        lastScroll = now;
        capture({ type: 'scroll', scroll: maxScroll });
    }, { passive: true });

    document.addEventListener('submit', function (event) {
        capture({ type: 'form_submit', selector: selectorFor(event.target), formAction: event.target.action || null });
    }, true);

    document.addEventListener('input', function (event) {
        var now = Date.now();
        if (now - lastInput < 1200) return;
        lastInput = now;
        capture({
            type: 'typing',
            selector: selectorFor(event.target),
            inputType: event.target && event.target.type ? event.target.type : 'text'
        });
    }, true);

    document.addEventListener('play', function (event) {
        if (event.target && event.target.tagName === 'VIDEO') {
            capture({ type: 'video_play', selector: selectorFor(event.target), currentTime: Math.round(event.target.currentTime || 0) });
        }
    }, true);

    function authPayload() {
        var meta = document.querySelector('meta[name="ultraclarity-user"]');
        if (!meta) return { loggedIn: false };
        return { loggedIn: true, id: meta.getAttribute('content') };
    }

    function payload(flushAll) {
        return {
            siteId: config.siteId,
            visitorId: visitorId,
            sessionId: sessionId,
            page: {
                url: location.href,
                title: document.title,
                referrer: document.referrer,
                timeOnPage: Math.round((Date.now() - started) / 1000),
                scrollDepth: Math.max(maxScroll, scrollDepth()),
                viewport: { w: window.innerWidth, h: window.innerHeight }
            },
            events: events.splice(0, flushAll ? events.length : Math.min(events.length, 80)),
            recording: recording.splice(0, flushAll ? recording.length : Math.min(recording.length, 160)),
            auth: authPayload(),
            device: {
                type: window.innerWidth < 640 ? 'mobile' : (window.innerWidth < 1024 ? 'tablet' : 'desktop'),
                browserName: browserName(),
                browser: navigator.userAgent,
                os: navigator.platform
            }
        };
    }

    function send(flushAll) {
        var body = JSON.stringify(payload(flushAll));
        if (navigator.sendBeacon && flushAll) {
            navigator.sendBeacon(config.endpoint, new Blob([body], { type: 'application/json' }));
            return;
        }
        fetch(config.endpoint, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: body, keepalive: true }).catch(function () {});
    }

    var prismPathApi = {
        optOut: function () { localStorage.setItem('ultraclarity:optout', '1'); },
        optIn: function () { localStorage.removeItem('ultraclarity:optout'); },
        event: function (name, properties) {
            capture({ type: 'custom', name: name, properties: properties || {} });
            fetch(config.endpoint.replace('/collect', '/event'), {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ sessionId: sessionId, name: name, path: location.pathname, properties: properties || {} })
            }).catch(function () {});
        }
    };

    window.PrismPath = prismPathApi;
    window.UltraClarity = prismPathApi;

    setInterval(function () { send(false); }, 5000);
    addEventListener('pagehide', function () { send(true); });
})();

