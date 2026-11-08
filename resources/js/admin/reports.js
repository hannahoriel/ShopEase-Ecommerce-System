(() => {
  const root = document.getElementById('admin-reports');
  if (!root) return;
  const form = document.getElementById('ar-period');
  const content = document.getElementById('ar-report-content');
  const message = document.getElementById('ar-message');
  const exportLink = document.getElementById('ar-export');
  const formatMoney = value => '₱' + Number(value).toLocaleString('en-PH', {maximumFractionDigits:0});
  let currentRequest = null;
  let lastChartWidth = 0;
  let chartAnimating = false;
  let pendingResize = false;
  let activeAnimationId = 0;

  // Render against the actual card dimensions, not a fixed-height SVG.
  const drawChart = (animateOnDraw = true) => {
    const target = document.getElementById('ar-trend-chart');
    if (!target) return;
    let days = [];
    try { days = JSON.parse(target.dataset.trend || '[]'); } catch (_) { /* invalid chart data */ }
    if (!days.length) {
      target.textContent = 'No chart data available.';
      return;
    }

    // Keep long date ranges readable by combining consecutive days into buckets.
    const span = Math.max(1, Math.ceil(days.length / 15));
    const points = [];
    for (let i = 0; i < days.length; i += span) {
      const batch = days.slice(i, i + span);
      points.push({
        label: batch.length === 1 ? batch[0].label : `${batch[0].label} – ${batch[batch.length - 1].label}`,
        shortLabel: batch[0].label,
        value: batch.reduce((total, day) => total + Number(day.sales || 0), 0)
      });
    }

    const currentWidth = Math.round(target.getBoundingClientRect().width || target.clientWidth || 780);
    lastChartWidth = currentWidth;
    const w = Math.max(340, currentWidth);
    const h = Math.max(320, Math.round(target.clientHeight || 365));
    const left = w < 450 ? 55 : 59;
    const right = 8;
    const top = 16;
    const bottom = 48;
    const width = w - left - right;
    const plotHeight = h - top - bottom;
    const maxValue = Math.max(1, ...points.map(point => point.value));
    const axisMax = maxValue * 1.06;
    const svgNS = 'http://www.w3.org/2000/svg';
    const svg = document.createElementNS(svgNS, 'svg');
    svg.setAttribute('class', 'ar-chart-svg');
    svg.setAttribute('viewBox', `0 0 ${w} ${h}`);
    svg.setAttribute('preserveAspectRatio', 'none');
    svg.setAttribute('role', 'img');
    svg.setAttribute('aria-label', 'Completed merchandise value by date. Hover or focus on a bar for its value.');

    const add = (tag, attributes, value) => {
      const element = document.createElementNS(svgNS, tag);
      Object.entries(attributes).forEach(([key, val]) => element.setAttribute(key, String(val)));
      if (value !== undefined) element.textContent = value;
      svg.appendChild(element);
      return element;
    };
    [0, .25, .5, .75, 1].forEach(fraction => {
      const y = top + plotHeight * (1 - fraction);
      add('line', {x1:left, y1:y, x2:w-right, y2:y, stroke:'#eaedf0', 'stroke-dasharray':'4 5'});
      add('text', {x:left-10, y:y+4, 'text-anchor':'end', fill:'#9ca3af', 'font-size':10}, formatMoney(axisMax*fraction));
    });

    const slot = width / points.length;
    const barWidth = Math.min(64, slot * .84);
    const tooltip = document.createElement('div');
    tooltip.className = 'ar-bar-tooltip';
    tooltip.setAttribute('role', 'status');
    tooltip.hidden = true;

    const showTooltip = (point, x, y) => {
      tooltip.replaceChildren();
      const label = document.createElement('span');
      label.className = 'ar-bar-tooltip-date';
      label.textContent = point.label;
      const amount = document.createElement('strong');
      amount.textContent = '₱' + Number(point.value).toLocaleString('en-PH', {
        minimumFractionDigits:2, maximumFractionDigits:2
      });
      tooltip.append(label, amount);
      tooltip.hidden = false;
      // Clamp within chart so tooltips never disappear beyond its edges.
      const tooltipWidth = tooltip.offsetWidth;
      const tooltipHeight = tooltip.offsetHeight;
      tooltip.style.left = `${Math.max(tooltipWidth/2 + 7, Math.min(w - tooltipWidth/2 - 7, x))}px`;
      tooltip.style.top = `${Math.max(tooltipHeight + 10, y - 10)}px`;
    };
    const hideTooltip = () => { tooltip.hidden = true; };

    const animatedBars = [];
    points.forEach((point, i) => {
      const x = left + slot * i + (slot - barWidth)/2;
      const barHeight = Math.max(0, point.value / axisMax * plotHeight);
      const y = top + plotHeight - barHeight;
      const rect = add('rect', {
        x, y, width:barWidth, height:barHeight, rx:4,
        fill:'#a21c41', class:'ar-trend-bar', tabindex:0,
        'aria-label':`${point.label}: ₱${Number(point.value).toLocaleString('en-PH',{minimumFractionDigits:2,maximumFractionDigits:2})}`
      });
      animatedBars.push({ rect, finalY: y, finalHeight: barHeight });
      // Native tooltip is a fallback for keyboard/accessibility and older browsers.
      const nativeTitle = document.createElementNS(svgNS,'title');
      nativeTitle.textContent = `${point.label}: ₱${Number(point.value).toLocaleString('en-PH',{minimumFractionDigits:2,maximumFractionDigits:2})}`;
      rect.appendChild(nativeTitle);
      const show = () => showTooltip(point, x + barWidth/2, y);
      rect.addEventListener('mouseenter', show);
      rect.addEventListener('mousemove', show);
      rect.addEventListener('mouseleave', hideTooltip);
      rect.addEventListener('focus', show);
      rect.addEventListener('blur', hideTooltip);
      add('text', {x:x+barWidth/2, y:h-16, 'text-anchor':'middle', fill:'#9ca3af', 'font-size':w<500?9:10}, point.shortLabel);
    });

    target.replaceChildren(svg, tooltip);

    // Every initial page load and date-filter update gets a smooth rising animation.
    // Resizing only redraws the chart; it does not repeatedly restart the animation.
    const reducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    activeAnimationId += 1;
    const thisAnimationId = activeAnimationId;
    chartAnimating = false;
    if (!animateOnDraw || reducedMotion) return;
    chartAnimating = true;

    const baseline = top + plotHeight;
    const duration = 980;      // milliseconds for each bar to rise
    const stagger = 75;        // milliseconds between each bar
    let startTime = null;
    let animationFrame = 0;
    const easeOutCubic = t => 1 - Math.pow(1 - t, 3);

    animatedBars.forEach(({ rect }) => {
      rect.setAttribute('y', baseline);
      rect.setAttribute('height', 0);
    });

    const frame = timestamp => {
      if (thisAnimationId !== activeAnimationId || !target.contains(svg)) return; // chart replaced
      if (startTime === null) startTime = timestamp;
      const elapsed = timestamp - startTime;
      let allFinished = true;
      animatedBars.forEach(({ rect, finalY, finalHeight }, index) => {
        const progress = Math.min(1, Math.max(0, (elapsed - index * stagger) / duration));
        const eased = easeOutCubic(progress);
        rect.setAttribute('y', baseline - finalHeight * eased);
        rect.setAttribute('height', finalHeight * eased);
        if (progress < 1) allFinished = false;
      });
      if (!allFinished) {
        animationFrame = requestAnimationFrame(frame);
      } else {
        chartAnimating = false;
        if (pendingResize) {
          pendingResize = false;
          resizeChart();
        }
      }
    };
    animationFrame = requestAnimationFrame(frame);
  };

  // IMPORTANT: ResizeObserver immediately calls its callback on observe().
  // The earlier implementation redrew without animation on that first callback,
  // so the bars appeared already at their full height on page load.
  // Only redraw when the *real measured width* has changed.
  let resizeFrame = 0;
  const resizeChart = () => {
    const target = document.getElementById('ar-trend-chart');
    if (!target) return;
    const actualWidth = Math.round(target.getBoundingClientRect().width || target.clientWidth || 0);
    if (!actualWidth || Math.abs(actualWidth - lastChartWidth) < 3) return;
    if (chartAnimating) { pendingResize = true; return; }
    cancelAnimationFrame(resizeFrame);
    resizeFrame = requestAnimationFrame(() => {
      const el = document.getElementById('ar-trend-chart');
      const latestWidth = el ? Math.round(el.getBoundingClientRect().width || el.clientWidth || 0) : 0;
      if (latestWidth && Math.abs(latestWidth - lastChartWidth) >= 3) drawChart(false);
    });
  };
  if (typeof ResizeObserver !== 'undefined') {
    const observer = new ResizeObserver(resizeChart);
    const chartTarget = document.getElementById('ar-trend-chart');
    if (chartTarget) observer.observe(chartTarget);
  } else {
    window.addEventListener('resize', resizeChart);
  }

  const update = async () => {
    const from = form.elements.from.value, to = form.elements.to.value;
    if (!from || !to || from > to) { message.textContent = 'Select a valid date range. The From date cannot be later than To.'; message.hidden = false; return; }
    if (currentRequest) currentRequest.abort();
    const controller = new AbortController(); currentRequest = controller;
    message.hidden = true; content.classList.add('ar-loading');
    const params = new URLSearchParams({from,to});
    try {
      const response = await fetch(root.dataset.apiUrl + '?' + params, {headers:{'Accept':'application/json'},credentials:'same-origin',signal:controller.signal});
      if (!response.ok) {
        const error = await response.json().catch(() => ({}));
        throw new Error(response.status === 422 ? (error.message || 'Invalid range. Maximum 366 days, and no future dates.') : (error.message || 'Unable to load report data.'));
      }
      const payload = await response.json();
      if (currentRequest !== controller) return;
      const doc = new DOMParser().parseFromString(payload.html || '', 'text/html');
      const updatedContent = doc.getElementById('ar-report-content');
      if (!updatedContent) throw new Error('Report content is missing from the response.');
      content.innerHTML = updatedContent.innerHTML;
      if (exportLink) exportLink.href = `${root.dataset.url}/export?${new URLSearchParams({from,to}).toString()}`;
      window.history.replaceState(null,'',`${root.dataset.url}?${params.toString()}`);
      drawChart();
    } catch (error) {
      if (error.name !== 'AbortError') { message.textContent = error.message || 'An error occurred.'; message.hidden = false; }
    } finally {
      if (currentRequest === controller) {content.classList.remove('ar-loading'); currentRequest = null;}
    }
  };
  form.addEventListener('submit', event => {event.preventDefault(); update();});
  document.getElementById('ar-last30').addEventListener('click', () => {
    const today = new Date(); const local = new Date(today.getTime()-today.getTimezoneOffset()*60000);
    const end = local.toISOString().slice(0,10); local.setDate(local.getDate()-29);
    form.elements.from.value = local.toISOString().slice(0,10); form.elements.to.value = end;
    update();
  });
  drawChart();
})();