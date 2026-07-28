'use strict';

/* ═══════════════════════════════════════════════════════════
   Ledger — app.js
   Vanilla JS expense tracker with Chart.js and localStorage.
   Single-class app pattern, no framework, no build step.
   ═══════════════════════════════════════════════════════════ */

const LS_KEY = 'ledger.expenses.v1';

/* ── Category → color mapping (monochromatic moss) ────────── */
const CATEGORIES = {
  'Groceries':     { color: '#1e4a2f' },
  'Rent & Bills':  { color: '#2b5a3d' },
  'Transport':     { color: '#3f7051' },
  'Eating out':    { color: '#578870' },
  'Entertainment': { color: '#7a9f88' },
  'Health':        { color: '#9ebca7' },
  'Shopping':      { color: '#b8d0c0' },
  'Other':         { color: '#c2d5c9' },
};

/* ── Formatters ─────────────────────────────────────────── */
const eurFmt = new Intl.NumberFormat('en-GB', {
  minimumFractionDigits: 2,
  maximumFractionDigits: 2,
});

const dateFmt = new Intl.DateTimeFormat('en-GB', {
  day:   '2-digit',
  month: 'short',
  year:  'numeric',
});

const monthFmt = new Intl.DateTimeFormat('en-GB', {
  month: 'long',
  year:  'numeric',
});

/* Splits a formatted number into main + cents parts */
function splitAmount(n) {
  const [main, cents] = eurFmt.format(n).split('.');
  return { main: main.replace(',', ','), cents: '.' + cents };
}

/* ── Sample seed data — first-time visitors see a populated app */
function seedData() {
  const now = new Date();
  const daysAgo = d => {
    const dt = new Date(now);
    dt.setDate(dt.getDate() - d);
    return dt.toISOString().split('T')[0];
  };
  return [
    { amount: 320.00, category: 'Rent & Bills',  desc: 'Electricity + internet',    date: daysAgo(2)  },
    { amount:  47.80, category: 'Groceries',     desc: 'Weekly market',              date: daysAgo(3)  },
    { amount:  32.00, category: 'Eating out',    desc: 'Dinner at Sela',             date: daysAgo(4)  },
    { amount:  12.50, category: 'Transport',     desc: 'Metro pass',                 date: daysAgo(5)  },
    { amount:  85.20, category: 'Groceries',     desc: 'Monthly stock-up',           date: daysAgo(8)  },
    { amount:  18.00, category: 'Entertainment', desc: 'Cinema',                     date: daysAgo(9)  },
    { amount:  22.40, category: 'Eating out',    desc: 'Lunch with V.',              date: daysAgo(11) },
    { amount:   9.50, category: 'Transport',     desc: 'Taxi home',                  date: daysAgo(12) },
    { amount:  56.00, category: 'Shopping',      desc: 'Books & stationery',         date: daysAgo(14) },
    { amount:  34.20, category: 'Groceries',     desc: 'Bakery + fruit',             date: daysAgo(16) },
    { amount:  14.00, category: 'Health',        desc: 'Pharmacy',                   date: daysAgo(18) },
    { amount:  28.00, category: 'Eating out',    desc: 'Coffee & brunch',            date: daysAgo(20) },
    { amount:  95.00, category: 'Rent & Bills',  desc: 'Mobile + insurance',         date: daysAgo(22) },
    { amount:  17.30, category: 'Transport',     desc: 'Weekend bus',                date: daysAgo(24) },
    { amount:  42.00, category: 'Other',         desc: 'Gift for K.',                date: daysAgo(27) },
  ].map(e => ({
    ...e,
    id: crypto.randomUUID?.() ?? String(Date.now() + Math.random()),
    created: Date.now(),
  }));
}

/* ═══════════════════════════════════════════════════════════
   LEDGER APP CLASS
   ═══════════════════════════════════════════════════════════ */
class Ledger {

  constructor() {
    this.state = {
      expenses: this.load(),
      filter:   'month',   // 'month' | '30days' | 'all'
    };
    this.charts = { doughnut: null, bar: null };
    this.$ = this.cacheDom();
    this.bindEvents();
    this.render();
  }

  /* ── DOM cache ───────────────────────────────────────── */
  cacheDom() {
    const $ = sel => document.querySelector(sel);
    const $$ = sel => document.querySelectorAll(sel);
    return {
      // Summary
      sumTotalMain:  $('#sumTotalMain'),
      sumTotalCents: $('#sumTotalCents'),
      sumTotalSub:   $('#sumTotalSub'),
      sumAvgMain:    $('#sumAvgMain'),
      sumAvgCents:   $('#sumAvgCents'),
      sumAvgSub:     $('#sumAvgSub'),
      sumCount:      $('#sumCount'),
      sumCountSub:   $('#sumCountSub'),
      // Notes
      periodNote:    $('#periodNote'),
      chartsNote:    $('#chartsNote'),
      txnCount:      $('#txnCount'),
      // Chart canvases
      doughnutEl:    $('#doughnutChart'),
      barEl:         $('#barChart'),
      categoryList:  $('#categoryList'),
      // Form
      form:          $('#expenseForm'),
      amount:        $('#e_amount'),
      category:      $('#e_category'),
      desc:          $('#e_desc'),
      date:          $('#e_date'),
      formError:     $('#formError'),
      // List
      txnList:       $('#txnList'),
      // Controls
      filterTabs:    $$('.filter-tab'),
      resetBtn:      $('#resetBtn'),
    };
  }

  /* ── Persistence ─────────────────────────────────────── */
  load() {
    try {
      const raw = localStorage.getItem(LS_KEY);
      if (raw === null) {
        // First visit — seed with sample data
        const seeded = seedData();
        localStorage.setItem(LS_KEY, JSON.stringify(seeded));
        return seeded;
      }
      return JSON.parse(raw) || [];
    } catch { return []; }
  }

  save() {
    try { localStorage.setItem(LS_KEY, JSON.stringify(this.state.expenses)); }
    catch (e) { console.warn('[ledger] save failed:', e); }
  }

  /* ── Events ──────────────────────────────────────────── */
  bindEvents() {
    // Default date = today
    this.$.date.value = new Date().toISOString().split('T')[0];

    // Form submit
    this.$.form.addEventListener('submit', e => this.handleAdd(e));

    // Filter tabs
    this.$.filterTabs.forEach(tab => {
      tab.addEventListener('click', () => {
        this.$.filterTabs.forEach(t => {
          t.classList.remove('active');
          t.setAttribute('aria-selected', 'false');
        });
        tab.classList.add('active');
        tab.setAttribute('aria-selected', 'true');
        this.state.filter = tab.dataset.filter;
        this.render();
      });
    });

    // Reset
    this.$.resetBtn.addEventListener('click', () => {
      if (confirm('Clear all expenses? This cannot be undone.')) {
        this.state.expenses = [];
        this.save();
        this.render();
      }
    });
  }

  /* ── Actions ─────────────────────────────────────────── */
  handleAdd(e) {
    e.preventDefault();
    this.$.formError.classList.remove('show');

    const amount   = parseFloat(this.$.amount.value);
    const category = this.$.category.value;
    const desc     = this.$.desc.value.trim();
    const date     = this.$.date.value;

    if (isNaN(amount) || amount <= 0 || !category || !date) {
      this.$.formError.classList.add('show');
      return;
    }

    const expense = {
      id:       crypto.randomUUID?.() ?? String(Date.now() + Math.random()),
      amount,
      category,
      desc,
      date,
      created:  Date.now(),
      _new:     true,   // flag for entering animation
    };

    this.state.expenses.push(expense);
    this.save();

    // Reset form
    this.$.form.reset();
    this.$.date.value = new Date().toISOString().split('T')[0];
    this.$.amount.focus();

    this.render();
  }

  removeExpense(id) {
    this.state.expenses = this.state.expenses.filter(e => e.id !== id);
    this.save();
    this.render();
  }

  /* ── Derived data ────────────────────────────────────── */
  getFilteredExpenses() {
    const { filter, expenses } = this.state;
    if (filter === 'all') return [...expenses];

    const now = new Date();
    const cutoff = new Date();

    if (filter === 'month') {
      // First day of current month
      cutoff.setFullYear(now.getFullYear(), now.getMonth(), 1);
      cutoff.setHours(0, 0, 0, 0);
    } else if (filter === '30days') {
      cutoff.setDate(now.getDate() - 30);
      cutoff.setHours(0, 0, 0, 0);
    }

    return expenses.filter(e => new Date(e.date) >= cutoff);
  }

  getSortedExpenses() {
    // Sort by date desc, then created desc
    return this.getFilteredExpenses().sort((a, b) => {
      if (a.date !== b.date) return a.date < b.date ? 1 : -1;
      return b.created - a.created;
    });
  }

  computeSummary(items) {
    const total = items.reduce((s, e) => s + e.amount, 0);
    const count = items.length;

    // Distinct days that had expenses
    const days = new Set(items.map(e => e.date)).size;
    const avgPerDay = days > 0 ? total / days : 0;

    return { total, count, days, avgPerDay };
  }

  computeByCategory(items) {
    const map = new Map();
    items.forEach(e => {
      map.set(e.category, (map.get(e.category) || 0) + e.amount);
    });
    return [...map.entries()]
      .map(([category, amount]) => ({
        category,
        amount,
        color: CATEGORIES[category]?.color ?? '#7a9f88',
      }))
      .sort((a, b) => b.amount - a.amount);
  }

  computeByDay(items) {
    // Determine date range
    const range = this.state.filter === 'month' ? this.currentMonthDays() : 30;
    const now = new Date();
    const days = [];

    for (let i = range - 1; i >= 0; i--) {
      const d = new Date(now);
      d.setDate(now.getDate() - i);
      const iso = d.toISOString().split('T')[0];
      days.push({ date: iso, label: d.getDate(), amount: 0 });
    }

    // Map expenses onto days
    const dayMap = new Map(days.map(d => [d.date, d]));
    items.forEach(e => {
      if (dayMap.has(e.date)) dayMap.get(e.date).amount += e.amount;
    });

    return days;
  }

  currentMonthDays() {
    const now = new Date();
    return now.getDate();
  }

  periodLabel() {
    if (this.state.filter === 'month')  return `Showing ${monthFmt.format(new Date())}`;
    if (this.state.filter === '30days') return 'Showing last 30 days';
    return 'Showing all time';
  }

  /* ═══════════════════════════════════════════════════════
     RENDER
     ═══════════════════════════════════════════════════════ */
  render() {
    const items    = this.getFilteredExpenses();
    const summary  = this.computeSummary(items);
    const byCat    = this.computeByCategory(items);
    const byDay    = this.computeByDay(items);

    this.renderSummary(summary);
    this.renderNotes();
    this.renderCharts(byCat, byDay);
    this.renderCategoryList(byCat, summary.total);
    this.renderTransactions();
  }

  renderSummary({ total, count, days, avgPerDay }) {
    // Total
    const t = splitAmount(total);
    this.$.sumTotalMain.textContent  = t.main;
    this.$.sumTotalCents.textContent = t.cents;
    this.$.sumTotalSub.textContent   = `Across ${count} ${count === 1 ? 'transaction' : 'transactions'}`;

    // Average
    const a = splitAmount(avgPerDay);
    this.$.sumAvgMain.textContent  = a.main;
    this.$.sumAvgCents.textContent = a.cents;
    this.$.sumAvgSub.textContent   = days === 1 ? 'Over one day' : `Over ${days} days`;

    // Count
    this.$.sumCount.textContent    = String(count);
    this.$.sumCountSub.textContent = this.state.filter === 'all'
      ? 'Since first entry'
      : this.state.filter === 'month'
        ? `In ${monthFmt.format(new Date())}`
        : 'In the last 30 days';
  }

  renderNotes() {
    this.$.periodNote.textContent = this.periodLabel();
    this.$.chartsNote.textContent = 'By category · ' + (
      this.state.filter === 'month'  ? 'this month'  :
      this.state.filter === '30days' ? 'last 30 days': 'all time'
    );
    const c = this.state.expenses.length;
    this.$.txnCount.textContent = `${c} ${c === 1 ? 'entry' : 'entries'} total`;
  }

  renderCharts(byCat, byDay) {
    this.renderDoughnut(byCat);
    this.renderBar(byDay);
  }

  renderDoughnut(byCat) {
    if (this.charts.doughnut) this.charts.doughnut.destroy();

    if (byCat.length === 0) {
      // Empty state — draw placeholder
      const ctx = this.$.doughnutEl.getContext('2d');
      ctx.clearRect(0, 0, this.$.doughnutEl.width, this.$.doughnutEl.height);
      return;
    }

    const total = byCat.reduce((s, c) => s + c.amount, 0);

    // Center-text plugin
    const centerPlugin = {
      id: 'centerText',
      afterDatasetsDraw(chart) {
        const { ctx, chartArea } = chart;
        if (!chartArea) return;
        const cx = (chartArea.left + chartArea.right)  / 2;
        const cy = (chartArea.top  + chartArea.bottom) / 2;

        ctx.save();

        ctx.font         = '500 10px "JetBrains Mono", monospace';
        ctx.fillStyle    = '#6b675e';
        ctx.textAlign    = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText('TOTAL', cx, cy - 22);

        ctx.font      = '400 28px Newsreader, serif';
        ctx.fillStyle = '#0d0d0c';
        ctx.fillText('€ ' + eurFmt.format(total), cx, cy + 4);

        ctx.font      = '400 10px "JetBrains Mono", monospace';
        ctx.fillStyle = '#a8a49a';
        ctx.fillText(byCat.length + ' CATEGORIES', cx, cy + 22);

        ctx.restore();
      },
    };

    this.charts.doughnut = new Chart(this.$.doughnutEl, {
      type: 'doughnut',
      data: {
        labels:   byCat.map(c => c.category),
        datasets: [{
          data:            byCat.map(c => c.amount),
          backgroundColor: byCat.map(c => c.color),
          borderWidth:     2,
          borderColor:     '#f6f3ed',
          hoverOffset:     6,
        }],
      },
      options: {
        responsive:          true,
        maintainAspectRatio: false,
        cutout:              '68%',
        plugins: {
          legend:  { display: false },
          tooltip: {
            backgroundColor: '#0d0d0c',
            titleFont:  { family: 'Inter', size: 12, weight: '500' },
            bodyFont:   { family: 'Newsreader', size: 14 },
            padding:    12,
            cornerRadius: 4,
            displayColors: true,
            boxWidth:   10,
            boxHeight:  10,
            callbacks: {
              label: ctx => ` € ${eurFmt.format(ctx.parsed)}  (${((ctx.parsed / total) * 100).toFixed(1)}%)`,
            },
          },
        },
        animation: { duration: 500, easing: 'easeOutQuart' },
      },
      plugins: [centerPlugin],
    });
  }

  renderBar(byDay) {
    if (this.charts.bar) this.charts.bar.destroy();
    if (byDay.length === 0) return;

    this.charts.bar = new Chart(this.$.barEl, {
      type: 'bar',
      data: {
        labels: byDay.map(d => d.label),
        datasets: [{
          data:            byDay.map(d => d.amount),
          backgroundColor: byDay.map(d => d.amount > 0 ? '#2b5a3d' : '#e6e2d8'),
          borderRadius:    2,
          barThickness:    'flex',
          maxBarThickness: 22,
        }],
      },
      options: {
        responsive:          true,
        maintainAspectRatio: false,
        plugins: {
          legend:  { display: false },
          tooltip: {
            backgroundColor: '#0d0d0c',
            titleFont: { family: 'Inter', size: 11 },
            bodyFont:  { family: 'Newsreader', size: 14 },
            padding: 10,
            cornerRadius: 4,
            displayColors: false,
            callbacks: {
              title: ctx => byDay[ctx[0].dataIndex].date,
              label: ctx => ' € ' + eurFmt.format(ctx.parsed.y),
            },
          },
        },
        scales: {
          x: {
            grid:   { display: false },
            border: { color: '#a8a49a', width: 1 },
            ticks:  {
              font:  { family: 'JetBrains Mono', size: 9 },
              color: '#a8a49a',
              autoSkip: true,
              maxRotation: 0,
              maxTicksLimit: 10,
            },
          },
          y: {
            grid:   { color: 'rgba(13,13,12,0.05)' },
            border: { display: false },
            ticks:  {
              font:  { family: 'JetBrains Mono', size: 9 },
              color: '#a8a49a',
              callback: v => '€ ' + v,
              maxTicksLimit: 4,
            },
          },
        },
        animation: { duration: 400, easing: 'easeOutQuart' },
      },
    });
  }

  renderCategoryList(byCat, total) {
    if (byCat.length === 0) {
      this.$.categoryList.innerHTML = '';
      return;
    }

    this.$.categoryList.innerHTML = byCat.map(c => {
      const pct = total > 0 ? ((c.amount / total) * 100).toFixed(1) : '0.0';
      return `
        <li class="category-row">
          <span class="category-swatch" style="background:${c.color}"></span>
          <span class="category-name">${escape(c.category)}</span>
          <span class="category-pct tabular">${pct}%</span>
          <span class="category-amt tabular"><span class="cur">€</span>${eurFmt.format(c.amount)}</span>
        </li>`;
    }).join('');
  }

  renderTransactions() {
    // Always show all entries in the book (regardless of filter)
    const sorted = [...this.state.expenses].sort((a, b) => {
      if (a.date !== b.date) return a.date < b.date ? 1 : -1;
      return b.created - a.created;
    });

    if (sorted.length === 0) {
      this.$.txnList.innerHTML = `
        <div class="empty-state">
          <p class="empty-state-title">The book is empty.</p>
          <p class="empty-state-sub">Add your first expense above, or start with a set of sample data to see how it looks.</p>
          <div class="empty-state-actions">
            <button class="btn-seed" id="seedBtn">Load sample data</button>
          </div>
        </div>`;
      document.getElementById('seedBtn')?.addEventListener('click', () => {
        this.state.expenses = seedData();
        this.save();
        this.render();
      });
      return;
    }

    this.$.txnList.innerHTML = sorted.map(e => {
      const color = CATEGORIES[e.category]?.color ?? '#7a9f88';
      const isNew = e._new ? ' entering' : '';
      if (e._new) delete e._new;
      return `
        <div class="txn-row${isNew}" data-id="${e.id}">
          <span class="txn-date tabular">${dateFmt.format(new Date(e.date))}</span>
          <span class="txn-category">
            <span class="txn-category-dot" style="background:${color}"></span>
            ${escape(e.category)}
          </span>
          <span class="txn-desc ${e.desc ? '' : 'empty'}">${escape(e.desc || 'no note')}</span>
          <span class="txn-amount tabular"><span class="cur">€</span>${eurFmt.format(e.amount)}</span>
          <button class="txn-delete" data-del="${e.id}" aria-label="Delete transaction">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
          </button>
        </div>`;
    }).join('');

    // Bind delete
    this.$.txnList.querySelectorAll('[data-del]').forEach(btn => {
      btn.addEventListener('click', () => this.removeExpense(btn.dataset.del));
    });
  }
}

/* ── Utility ─────────────────────────────────────────────── */
function escape(s) {
  return String(s)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;');
}

/* ── Boot ──────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
  // Set Chart.js global defaults
  if (typeof Chart !== 'undefined') {
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#6b675e';
  }
  new Ledger();
});
